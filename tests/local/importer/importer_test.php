<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Checklist importer tests.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

use advanced_testcase;
use gradingform_checklist_controller;

global $CFG;
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

/**
 * Checklist importer tests.
 */
class importer_test extends advanced_testcase {

    /**
     * JSON imports are normalised to the canonical structure.
     */
    public function test_json_importer_maps_example_to_canonical_data(): void {
        $json = json_encode(canonical_import_data::json_example());
        $result = (new json_importer())->parse($json);

        $this->assertFalse($result->has_errors());
        $data = $result->get_data();
        $this->assertSame(canonical_import_data::FORMAT, $data['format']);
        $this->assertSame(canonical_import_data::VERSION, $data['version']);
        $this->assertSame('Research Brief Checklist', $data['name']);
        $this->assertSame(gradingform_checklist_controller::get_default_options(), $data['settings']);
        $this->assertCount(1, $data['groups']);
        $this->assertSame('Submission Requirements', $data['groups'][0]['description']);
        $this->assertSame('Submitted by the due date', $data['groups'][0]['items'][0]['definition']);
        $this->assertSame(1.0, $data['groups'][0]['items'][0]['score']);
    }

    /**
     * Invalid JSON fails without throwing parser notices.
     */
    public function test_json_importer_reports_invalid_json(): void {
        $result = (new json_importer())->parse('{bad json');

        $this->assertTrue($result->has_errors());
        $this->assertStringContainsString('JSON', implode(' ', $result->get_errors()));
    }

    /**
     * JSON benchmark file payloads are out of scope for v1 imports.
     */
    public function test_json_importer_ignores_benchmark_files(): void {
        $raw = canonical_import_data::json_example();
        $raw['benchmark']['files'][] = [
            'filename' => 'image.png',
            'content' => base64_encode('image'),
            'encoding' => 'base64',
        ];

        $result = (new json_importer())->parse(json_encode($raw));

        $this->assertFalse($result->has_errors());
        $this->assertSame([], $result->get_data()['benchmark']['files']);
        $this->assertNotEmpty($result->get_warnings());
    }

    /**
     * Shared validation ignores blank template rows but keeps valid groups/items.
     */
    public function test_validator_ignores_blank_groups_and_items(): void {
        $result = import_validator::validate([
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => 'Checklist',
            'groups' => [
                [
                    'description' => '',
                    'items' => [
                        ['definition' => '', 'score' => ''],
                    ],
                ],
                [
                    'description' => 'Group',
                    'items' => [
                        ['definition' => '', 'score' => ''],
                        ['definition' => 'Item', 'score' => '2'],
                    ],
                ],
            ],
        ]);

        $this->assertFalse($result->has_errors());
        $this->assertCount(1, $result->get_data()['groups']);
        $this->assertSame('Group', $result->get_data()['groups'][0]['description']);
        $this->assertCount(1, $result->get_data()['groups'][0]['items']);
    }

    /**
     * Comment requirement options are disabled when their parent remark setting is off.
     */
    public function test_validator_applies_comment_option_dependencies(): void {
        $result = import_validator::validate([
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => 'Checklist',
            'settings' => [
                'enableitemremarks' => 0,
                'requireitemcommentschecked' => 1,
                'requireatleastoneitemcomment' => 1,
            ],
            'groups' => [
                [
                    'description' => 'Group',
                    'items' => [
                        ['definition' => 'Item', 'score' => 1],
                    ],
                ],
            ],
        ]);

        $this->assertFalse($result->has_errors());
        $settings = $result->get_data()['settings'];
        $this->assertSame(0, $settings['requireitemcommentschecked']);
        $this->assertSame(0, $settings['requireatleastoneitemcomment']);
    }

    /**
     * Invalid scores and observation enum values are fatal.
     */
    public function test_validator_reports_invalid_scores_and_observation_values(): void {
        $result = import_validator::validate([
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => 'Checklist',
            'settings' => [
                'observationmode' => 'tomorrow-ish',
                'observationdefault' => 'sometimes',
            ],
            'groups' => [
                [
                    'description' => 'Group',
                    'items' => [
                        ['definition' => 'Item', 'score' => 1001],
                    ],
                ],
            ],
        ]);

        $this->assertTrue($result->has_errors());
        $errors = implode(' ', $result->get_errors());
        $this->assertStringContainsString('Observation date selector', $errors);
        $this->assertStringContainsString('Default observation date', $errors);
        $this->assertStringContainsString('points', $errors);
    }

    /**
     * The bundled DOCX template remains empty above the reference section.
     */
    public function test_docx_template_stops_before_reference_samples(): void {
        if (!class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for DOCX parsing.');
        }

        $template = dirname(__DIR__, 3) . '/docs/checklist-import-template.docx';
        $result = (new docx_importer())->parse($template);
        $data = $result->get_data();

        $this->assertTrue($result->has_errors());
        $this->assertSame('', $data['name']);
        $this->assertSame([], $data['groups']);
        $this->assertStringNotContainsString('Submission Requirements', json_encode($data));
    }

    /**
     * JSON Schema exposes the stable format and version fields.
     */
    public function test_json_schema_exposes_format_and_version(): void {
        $schema = canonical_import_data::json_schema();

        $this->assertSame(canonical_import_data::FORMAT, $schema['properties']['format']['const']);
        $this->assertSame(canonical_import_data::VERSION, $schema['properties']['version']['const']);
        $this->assertArrayHasKey('observationmode', $schema['properties']['settings']['properties']);
        $this->assertArrayHasKey('benchmark', $schema['properties']);
    }
}
