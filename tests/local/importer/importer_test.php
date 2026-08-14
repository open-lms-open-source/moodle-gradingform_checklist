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
use gradingform_checklist\external\import_definition;
use gradingform_checklist\local\config;
use gradingform_checklist_controller;

global $CFG;
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

/**
 * Checklist importer tests.
 */
class importer_test extends advanced_testcase {

    /**
     * Administrator limits are used by both import validation and the schema.
     */
    public function test_configured_limits_are_used_by_import_validation_and_schema(): void {
        $this->resetAfterTest(true);
        set_config('groupdescriptionmaxchars', 12, 'gradingform_checklist');
        set_config('itemdefinitionmaxchars', 13, 'gradingform_checklist');

        $raw = [
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => 'Checklist',
            'groups' => [[
                'description' => str_repeat('G', 12),
                'items' => [['definition' => str_repeat('I', 13), 'score' => 1]],
            ]],
        ];
        $this->assertFalse(import_validator::validate($raw)->has_errors());

        $raw['groups'][0]['items'][0]['definition'] .= 'I';
        $this->assertTrue(import_validator::validate($raw)->has_errors());

        $schema = canonical_import_data::json_schema();
        $this->assertSame(12, $schema['properties']['groups']['items']['properties']['description']['maxLength']);
        $this->assertSame(13, $schema['properties']['groups']['items']['properties']['items']['items']
            ['properties']['definition']['maxLength']);
    }

    /**
     * Administrator defaults affect new definitions while stored option values remain explicit.
     */
    public function test_configured_defaults_are_used_for_new_checklists(): void {
        $this->resetAfterTest(true);
        set_config('enablebulkcheck', 1, 'gradingform_checklist');
        set_config('showitempointseval', 1, 'gradingform_checklist');

        $defaults = gradingform_checklist_controller::get_default_options();
        $this->assertSame(1, $defaults['enablebulkcheck']);
        $this->assertSame(1, $defaults['showitempointseval']);
    }

    /**
     * The JSON import web service honours its administrator feature switch.
     */
    public function test_external_import_can_be_disabled_by_administrator(): void {
        $this->resetAfterTest(true);
        set_config('enablejsonwebservice', 0, 'gradingform_checklist');

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('disabled');
        import_definition::execute(0, '{}', 'draft');
    }

    /**
     * The JSON import web service is disabled unless explicitly enabled.
     */
    public function test_external_import_is_disabled_by_default(): void {
        $this->resetAfterTest(true);

        $this->assertFalse(config::enabled('enablejsonwebservice'));
    }

    /**
     * The import web service rejects status values outside its public contract.
     */
    public function test_external_import_rejects_unknown_status(): void {
        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage('The import status must be draft or ready.');

        import_definition::execute(0, '{}', 'published');
    }

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
     * Benchmark icon classes are cleaned during JSON validation.
     */
    public function test_json_importer_cleans_benchmark_button_icon(): void {
        $raw = canonical_import_data::json_example();
        $raw['benchmark']['buttonicon'] = 'fa-solid" onclick="alert(1)';

        $result = (new json_importer())->parse(json_encode($raw));

        $this->assertFalse($result->has_errors(), implode(' ', $result->get_errors()));
        $this->assertSame(
            gradingform_checklist_controller::DEFAULT_BENCHMARK_BUTTON_ICON,
            $result->get_data()['benchmark']['buttonicon']
        );
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
     * Session transport encoding remains safe when imported text contains invalid UTF-8.
     */
    public function test_canonical_encode_substitutes_invalid_utf8(): void {
        $result = new canonical_import_data([
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => "Checklist \xB1",
            'groups' => [
                [
                    'description' => 'Group',
                    'items' => [
                        [
                            'definition' => "Item \xB1",
                            'score' => 1,
                        ],
                    ],
                ],
            ],
        ]);

        $encoded = $result->encode();
        $decoded = json_decode($encoded, true);

        $this->assertNotSame('', $encoded);
        $this->assertIsArray($decoded);
        $this->assertSame(JSON_ERROR_NONE, json_last_error());
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
     * The import confirmation preview renders the incoming checklist structure.
     */
    public function test_import_preview_renders_groups_items_and_scores(): void {
        $this->resetAfterTest(true);
        $GLOBALS['PAGE']->set_context(\context_system::instance());

        $data = canonical_import_data::json_example();
        $data['settings']['showitempointseval'] = 1;
        $data['groups'] = [
            [
                'description' => 'Submission Requirements',
                'items' => [
                    ['definition' => 'Submitted by the due date', 'score' => 1],
                    ['definition' => 'Includes reference list', 'score' => 2],
                ],
            ],
            [
                'description' => 'Academic Writing',
                'items' => [
                    ['definition' => 'Uses discipline terminology', 'score' => 3],
                ],
            ],
        ];

        $html = import_preview::render($data, $GLOBALS['PAGE']);

        $this->assertStringContainsString('Submission Requirements', $html);
        $this->assertStringContainsString('Academic Writing', $html);
        $this->assertStringContainsString('Submitted by the due date', $html);
        $this->assertStringContainsString('Includes reference list', $html);
        $this->assertStringContainsString('Uses discipline terminology', $html);
        $this->assertStringContainsString('>2<', $html);
        $this->assertStringContainsString('>3<', $html);
    }

    /**
     * The import confirmation preview includes benchmark guidance when enabled.
     */
    public function test_import_preview_renders_enabled_benchmark(): void {
        $this->resetAfterTest(true);
        $GLOBALS['PAGE']->set_context(\context_system::instance());

        $data = canonical_import_data::json_example();
        $data['benchmark'] = [
            'enabled' => true,
            'buttonlabel' => 'Open benchmark notes',
            'buttonicon' => 'fa fa-check',
            'html' => '<p>Compare against the exemplar.</p>',
            'files' => [],
        ];

        $html = import_preview::render($data, $GLOBALS['PAGE']);

        $this->assertStringContainsString('gradingform-checklist-import-preview-benchmark', $html);
        $this->assertStringContainsString(get_string('benchmark', 'gradingform_checklist'), $html);
        $this->assertStringContainsString('Compare against the exemplar.', $html);
        $this->assertStringNotContainsString('class="benchmark-toggle', $html);
        $this->assertStringNotContainsString('data-benchmark-content="import-preview"', $html);
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
        $this->assertSame('string', $schema['properties']['format']['type']);
        $this->assertSame('integer', $schema['properties']['version']['type']);
        $this->assertSame(0, $schema['properties']['benchmark']['properties']['files']['maxItems']);
        $this->assertArrayNotHasKey('items', $schema['properties']['benchmark']['properties']['files']);
        $this->assertArrayHasKey('observationmode', $schema['properties']['settings']['properties']);
        $this->assertArrayHasKey('benchmark', $schema['properties']);
    }

    /**
     * The bundled template is a real DOCX archive rather than a path string.
     */
    public function test_docx_template_is_a_valid_archive(): void {
        if (!class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive extension is required for DOCX validation.');
        }

        $template = dirname(__DIR__, 3) . '/docs/checklist-import-template.docx';
        $this->assertSame('PK', file_get_contents($template, false, null, 0, 2));

        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($template) === true);
        $this->assertNotFalse($zip->getFromName('word/document.xml'));
        $this->assertNotFalse($zip->getFromName('[Content_Types].xml'));
        $this->assertNotFalse($zip->getFromName('word/_rels/document.xml.rels'));
        $this->assertSame(0, $zip->status);
        $zip->close();
    }

    /**
     * The Word download endpoint passes the template path, not its path text, to send_file().
     */
    public function test_docx_download_endpoint_uses_template_path(): void {
        $source = file_get_contents(dirname(__DIR__, 3) . '/template.php');
        $this->assertMatchesRegularExpression('/0,\s*0,\s*false,\s*true,/', $source);
    }

    /**
     * The published JSON example is valid according to the shared importer contract.
     */
    public function test_json_example_is_valid_import_payload(): void {
        $result = (new json_importer())->parse(json_encode(canonical_import_data::json_example(),
            JSON_THROW_ON_ERROR));

        $this->assertFalse($result->has_errors(), implode(' ', $result->get_errors()));
        $this->assertSame([], $result->get_data()['benchmark']['files']);
    }

    /**
     * JSON download endpoints use Moodle's UTF-8 file download path.
     */
    public function test_json_download_endpoints_use_utf8_downloads(): void {
        foreach (['jsonexample.php', 'jsonschema.php'] as $endpoint) {
            $source = file_get_contents(dirname(__DIR__, 3) . '/' . $endpoint);
            $this->assertStringContainsString('JSON_THROW_ON_ERROR', $source);
            $this->assertStringContainsString('send_file(', $source);
            $this->assertStringContainsString('charset=utf-8', $source);
            $this->assertMatchesRegularExpression('/0,\s*0,\s*true,\s*true,/', $source);
        }
    }
}
