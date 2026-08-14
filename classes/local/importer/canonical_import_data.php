<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Canonical checklist import data.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

defined('MOODLE_INTERNAL') || die();

/**
 * Value object for parsed checklist imports.
 */
class canonical_import_data {
    /** Import format identifier. */
    public const FORMAT = 'gradingform_checklist_import';

    /** Supported import schema version. */
    public const VERSION = 1;

    /** @var array Canonical import payload. */
    protected array $data;

    /** @var array Non-fatal parse/import warnings. */
    protected array $warnings;

    /** @var array Fatal validation or parse errors. */
    protected array $errors;

    /**
     * Constructor.
     *
     * @param array $data canonical import payload
     * @param array $warnings warning messages
     * @param array $errors fatal error messages
     */
    public function __construct(array $data = [], array $warnings = [], array $errors = []) {
        $this->data = $data;
        $this->warnings = $warnings;
        $this->errors = $errors;
    }

    /**
     * Returns canonical import data.
     *
     * @return array
     */
    public function get_data(): array {
        return $this->data;
    }

    /**
     * Returns import warnings.
     *
     * @return array
     */
    public function get_warnings(): array {
        return $this->warnings;
    }

    /**
     * Returns import errors.
     *
     * @return array
     */
    public function get_errors(): array {
        return $this->errors;
    }

    /**
     * Whether the import has fatal errors.
     *
     * @return bool
     */
    public function has_errors(): bool {
        return !empty($this->errors);
    }

    /**
     * Returns data encoded for hidden form transport.
     *
     * @return string
     */
    public function encode(): string {
        $encoded = json_encode(
            $this->data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
        return $encoded === false ? '' : $encoded;
    }

    /**
     * Creates a result from encoded hidden form data.
     *
     * @param string $encoded encoded JSON
     * @param bool $allowfiles whether benchmark file payloads are allowed
     * @return self
     */
    public static function decode(string $encoded, bool $allowfiles = false): self {
        $decoded = json_decode($encoded, true);
        if (!is_array($decoded)) {
            return new self([], [], [get_string('importerrorinvalidjson', 'gradingform_checklist')]);
        }
        return import_validator::validate($decoded, $allowfiles);
    }

    /**
     * Returns a ready-to-edit JSON example.
     *
     * @return array
     */
    public static function json_example(): array {
        return [
            'format' => self::FORMAT,
            'version' => self::VERSION,
            'name' => 'Research Brief Checklist',
            'description' => 'Assessment checklist for the research brief activity.',
            'settings' => \gradingform_checklist_controller::get_default_options(),
            'benchmark' => [
                'enabled' => true,
                'buttonlabel' => get_string('benchmarkbuttondefault', 'gradingform_checklist'),
                'buttonicon' => \gradingform_checklist_controller::DEFAULT_BENCHMARK_BUTTON_ICON,
                'html' => '<table><tr><td>Use this benchmark guidance when evaluating the submission.</td></tr></table>',
                'files' => [],
            ],
            'groups' => [
                [
                    'description' => 'Submission Requirements',
                    'items' => [
                        [
                            'definition' => 'Submitted by the due date',
                            'score' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the JSON Schema for checklist imports.
     *
     * @return array
     */
    public static function json_schema(): array {
        $settings = [];
        foreach (array_keys(\gradingform_checklist_controller::get_default_options()) as $setting) {
            $settings[$setting] = [
                'description' => 'Boolean checklist setting. Values are emitted as 0 or 1 in canonical data.',
                'oneOf' => [
                    ['type' => 'boolean'],
                    ['type' => 'integer', 'enum' => [0, 1]],
                ],
            ];
        }
        $settings['groupremarkheading'] = [
            'description' => 'Heading displayed above group-level remarks.',
            'type' => 'string',
            'maxLength' => 255,
        ];
        $settings['observationmode'] = [
            'description' => 'Observation date selector mode.',
            'type' => 'string',
            'enum' => [
                \gradingform_checklist_controller::OBSERVATION_MODE_DISABLED,
                \gradingform_checklist_controller::OBSERVATION_MODE_DATE,
                \gradingform_checklist_controller::OBSERVATION_MODE_DATETIME,
            ],
        ];
        $settings['observationdefault'] = [
            'description' => 'Default value for a newly created observation date.',
            'type' => 'string',
            'enum' => [
                \gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
                \gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
            ],
        ];
        return [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            '$id' => 'https://moodle.org/plugins/gradingform_checklist/import.schema.json',
            'title' => 'Moodle gradingform_checklist import',
            'description' => 'Canonical version 1 checklist definition import payload.',
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['format', 'version', 'name', 'groups'],
            'properties' => [
                'format' => [
                    'type' => 'string',
                    'description' => 'Stable import format identifier.',
                    'const' => self::FORMAT,
                ],
                'version' => [
                    'type' => 'integer',
                    'description' => 'Stable import format version.',
                    'const' => self::VERSION,
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'Checklist name.',
                    'minLength' => 1,
                    'maxLength' => 255,
                ],
                'description' => [
                    'type' => 'string',
                    'description' => 'Optional checklist description. HTML is permitted.',
                ],
                'settings' => [
                    'type' => 'object',
                    'description' => 'Optional checklist settings. Omitted settings use plugin defaults.',
                    'additionalProperties' => false,
                    'properties' => $settings,
                ],
                'benchmark' => [
                    'type' => 'object',
                    'description' => 'Optional teacher-only benchmark guidance.',
                    'additionalProperties' => false,
                    'properties' => [
                        'enabled' => [
                            'type' => 'boolean',
                            'description' => 'Whether benchmark guidance is enabled.',
                        ],
                        'buttonlabel' => [
                            'type' => 'string',
                            'description' => 'Label used for the benchmark button.',
                            'maxLength' => 255,
                        ],
                        'buttonicon' => [
                            'type' => 'string',
                            'description' => 'Font Awesome icon classes for the benchmark button.',
                            'maxLength' => 255,
                        ],
                        'html' => [
                            'type' => 'string',
                            'description' => 'HTML benchmark guidance shown to graders.',
                        ],
                        'files' => [
                            'type' => 'array',
                            'description' => 'Reserved for future JSON embedded-file support; version 1 accepts no JSON files.',
                            'maxItems' => 0,
                        ],
                    ],
                ],
                'groups' => [
                    'type' => 'array',
                    'description' => 'Checklist groups in display order.',
                    'minItems' => 1,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['description', 'items'],
                        'properties' => [
                            'description' => [
                                'type' => 'string',
                                'description' => 'Group description.',
                                'minLength' => 1,
                                'maxLength' => \MoodleQuickForm_checklisteditor::get_group_description_max_length(),
                            ],
                            'items' => [
                                'type' => 'array',
                                'description' => 'Items in display order.',
                                'minItems' => 1,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['definition', 'score'],
                                    'properties' => [
                                        'definition' => [
                                            'type' => 'string',
                                            'description' => 'Item definition shown to graders.',
                                            'minLength' => 1,
                                            'maxLength' => \MoodleQuickForm_checklisteditor::get_item_definition_max_length(),
                                        ],
                                        'score' => [
                                            'type' => 'number',
                                            'description' => 'Non-negative points awarded when the item is checked.',
                                            'minimum' => 0,
                                            'maximum' => 1000,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
