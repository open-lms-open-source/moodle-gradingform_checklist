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
        return json_encode($this->data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
                'buttonicon' => 'fa-solid fa-file-circle-check',
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
                'oneOf' => [
                    ['type' => 'boolean'],
                    ['enum' => [0, 1]],
                ],
            ];
        }
        $settings['groupremarkheading'] = [
            'type' => 'string',
            'maxLength' => 255,
        ];
        $settings['observationmode'] = [
            'enum' => [
                \gradingform_checklist_controller::OBSERVATION_MODE_DISABLED,
                \gradingform_checklist_controller::OBSERVATION_MODE_DATE,
                \gradingform_checklist_controller::OBSERVATION_MODE_DATETIME,
            ],
        ];
        $settings['observationdefault'] = [
            'enum' => [
                \gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
                \gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
            ],
        ];
        return [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            '$id' => 'https://moodle.org/plugins/gradingform_checklist/import.schema.json',
            'title' => 'Moodle gradingform_checklist import',
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['format', 'version', 'name', 'groups'],
            'properties' => [
                'format' => [
                    'const' => self::FORMAT,
                ],
                'version' => [
                    'const' => self::VERSION,
                ],
                'name' => [
                    'type' => 'string',
                    'minLength' => 1,
                    'maxLength' => 255,
                ],
                'description' => [
                    'type' => 'string',
                ],
                'settings' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => $settings,
                ],
                'benchmark' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'enabled' => ['type' => 'boolean'],
                        'buttonlabel' => ['type' => 'string', 'maxLength' => 255],
                        'buttonicon' => ['type' => 'string', 'maxLength' => 255],
                        'html' => ['type' => 'string'],
                        'files' => [
                            'type' => 'array',
                            'description' => 'Reserved for imported DOCX embedded files. JSON embedded files are out of scope for version 1.',
                            'maxItems' => 0,
                            'items' => [
                                'type' => 'object',
                                'additionalProperties' => false,
                                'required' => ['filename', 'content', 'encoding'],
                                'properties' => [
                                    'filename' => ['type' => 'string', 'minLength' => 1],
                                    'content' => ['type' => 'string'],
                                    'encoding' => ['const' => 'base64'],
                                ],
                            ],
                        ],
                    ],
                ],
                'groups' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'items' => [
                        'type' => 'object',
                        'additionalProperties' => false,
                        'required' => ['description', 'items'],
                        'properties' => [
                            'description' => [
                                'type' => 'string',
                                'minLength' => 1,
                                'maxLength' => \MoodleQuickForm_checklisteditor::GROUP_DESCRIPTION_MAX_LENGTH,
                            ],
                            'items' => [
                                'type' => 'array',
                                'minItems' => 1,
                                'items' => [
                                    'type' => 'object',
                                    'additionalProperties' => false,
                                    'required' => ['definition', 'score'],
                                    'properties' => [
                                        'definition' => [
                                            'type' => 'string',
                                            'minLength' => 1,
                                            'maxLength' => \MoodleQuickForm_checklisteditor::ITEM_DEFINITION_MAX_LENGTH,
                                        ],
                                        'score' => [
                                            'type' => 'number',
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
