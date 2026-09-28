<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Checklist import validation.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

use gradingform_checklist\local\config;

/**
 * Normalises and validates canonical checklist imports.
 */
class import_validator {
    /** @var array Supported boolean setting labels mapped to internal option names. */
    protected const BOOLEAN_SETTINGS = [
        'Always show checklist to students' => 'alwaysshowdefinition',
        'Show remarks to students' => 'showremarksstudent',
        'Allow bulk check/uncheck' => 'enablebulkcheck',
        'Show group and overall points while grading' => 'showgrouppointseval',
        'Show group and overall points to students' => 'showgrouppointstudent',
        'Allow group remarks' => 'enablegroupremarks',
        'Require group comments when a group has checked items' => 'requiregroupcommentschecked',
        'Require at least one group comment' => 'requireatleastonegroupcomment',
        'Show item points while grading' => 'showitempointseval',
        'Show item points to students' => 'showitempointstudent',
        'Allow item remarks' => 'enableitemremarks',
        'Require comments for checked items' => 'requireitemcommentschecked',
        'Require at least one item comment' => 'requireatleastoneitemcomment',
    ];

    /** @var array Supported non-boolean setting labels mapped to internal option names. */
    protected const VALUE_SETTINGS = [
        'Group comment heading' => 'groupremarkheading',
        'Observation date selector' => 'observationmode',
        'Default observation date' => 'observationdefault',
    ];

    /**
     * Returns the display-label to internal setting map.
     *
     * @return array
     */
    public static function setting_label_map(): array {
        return self::BOOLEAN_SETTINGS + self::VALUE_SETTINGS;
    }

    /**
     * Validates and normalises a canonical import array.
     *
     * @param array $raw raw import array
     * @param bool $allowfiles whether benchmark file payloads are allowed
     * @return canonical_import_data
     */
    public static function validate(array $raw, bool $allowfiles = false): canonical_import_data {
        $warnings = [];
        $errors = [];
        $data = [
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => '',
            'description' => '',
            'settings' => \gradingform_checklist_controller::get_default_options(),
            'benchmark' => [
                'enabled' => false,
                'buttonlabel' => get_string('benchmarkbuttondefault', 'gradingform_checklist'),
                'buttonicon' => \gradingform_checklist_controller::DEFAULT_BENCHMARK_BUTTON_ICON,
                'html' => '',
                'files' => [],
            ],
            'groups' => [],
        ];

        if (($raw['format'] ?? canonical_import_data::FORMAT) !== canonical_import_data::FORMAT) {
            $errors[] = get_string('importerrorformat', 'gradingform_checklist');
        }
        if ((int)($raw['version'] ?? canonical_import_data::VERSION) !== canonical_import_data::VERSION) {
            $errors[] = get_string('importerrorversion', 'gradingform_checklist');
        }

        $data['name'] = self::clean_plain($raw['name'] ?? '');
        $data['description'] = clean_param((string)($raw['description'] ?? ''), PARAM_RAW);
        if ($data['name'] === '') {
            $errors[] = get_string('importerrorname', 'gradingform_checklist');
        }

        if (!empty($raw['settings']) && is_array($raw['settings'])) {
            foreach ($raw['settings'] as $key => $value) {
                $internal = self::normalise_setting_key((string)$key);
                if ($internal === null) {
                    $warnings[] = get_string('importwarningunknownsetting', 'gradingform_checklist', $key);
                    continue;
                }
                if (in_array($internal, self::BOOLEAN_SETTINGS, true)) {
                    $bool = self::normalise_bool($value);
                    if ($bool === null) {
                        $errors[] = get_string('importerrorinvalidsettingvalue', 'gradingform_checklist', $key);
                        continue;
                    }
                    $data['settings'][$internal] = $bool ? 1 : 0;
                    continue;
                }
                if ($internal === 'groupremarkheading') {
                    $data['settings'][$internal] = self::clean_plain($value);
                } else if ($internal === 'observationmode') {
                    $mode = self::normalise_observation_mode($value);
                    if ($mode === null) {
                        $errors[] = get_string('importerrorobservationmode', 'gradingform_checklist');
                        continue;
                    }
                    $data['settings'][$internal] = $mode;
                } else if ($internal === 'observationdefault') {
                    $default = self::normalise_observation_default($value);
                    if ($default === null) {
                        $errors[] = get_string('importerrorobservationdefault', 'gradingform_checklist');
                        continue;
                    }
                    $data['settings'][$internal] = $default;
                }
            }
        }
        $data['settings'] = \gradingform_checklist_controller::normalise_comment_option_dependencies($data['settings']);

        if (!empty($raw['benchmark']) && is_array($raw['benchmark'])) {
            $enabled = self::normalise_bool($raw['benchmark']['enabled'] ?? false);
            if ($enabled === null) {
                $errors[] = get_string('importerrorbenchmarkenabled', 'gradingform_checklist');
                $enabled = false;
            }
            $data['benchmark']['enabled'] = (bool)$enabled;
            if ($data['benchmark']['enabled'] && !config::enabled('enablebenchmarks')) {
                $errors[] = get_string('benchmarkdisabled', 'gradingform_checklist');
                $data['benchmark']['enabled'] = false;
            }
            $data['benchmark']['buttonlabel'] = self::clean_plain(
                $raw['benchmark']['buttonlabel'] ?? get_string('benchmarkbuttondefault', 'gradingform_checklist')
            );
            $data['benchmark']['buttonicon'] = \gradingform_checklist_controller::clean_benchmark_button_icon(
                $raw['benchmark']['buttonicon'] ?? ''
            );
            $data['benchmark']['html'] = clean_param((string)($raw['benchmark']['html'] ?? ''), PARAM_RAW);
            if (!empty($raw['benchmark']['files']) && !$allowfiles) {
                $warnings[] = get_string('importwarningjsonbenchmarkfiles', 'gradingform_checklist');
            } else if (!empty($raw['benchmark']['files']) && is_array($raw['benchmark']['files'])) {
                foreach ($raw['benchmark']['files'] as $file) {
                    if (!is_array($file) || empty($file['filename']) || !array_key_exists('content', $file)) {
                        $warnings[] = get_string('importwarninginvalidbenchmarkfile', 'gradingform_checklist');
                        continue;
                    }
                    $data['benchmark']['files'][] = [
                        'filename' => clean_param($file['filename'], PARAM_FILE),
                        'content' => (string)$file['content'],
                        'encoding' => clean_param($file['encoding'] ?? 'base64', PARAM_ALPHANUMEXT),
                    ];
                }
            }
            if ($data['benchmark']['buttonlabel'] === '') {
                $data['benchmark']['buttonlabel'] = get_string('benchmarkbuttondefault', 'gradingform_checklist');
            }
            if (
                $data['benchmark']['enabled'] && trim(strip_tags($data['benchmark']['html'])) === ''
                    && empty($data['benchmark']['files'])
            ) {
                $warnings[] = get_string('importwarningemptybenchmark', 'gradingform_checklist');
                $data['benchmark']['enabled'] = false;
            }
        }

        if (!empty($raw['groups']) && is_array($raw['groups'])) {
            foreach ($raw['groups'] as $groupindex => $group) {
                if (!is_array($group)) {
                    $warnings[] = get_string('importwarninginvalidgroup', 'gradingform_checklist', $groupindex + 1);
                    continue;
                }
                $description = self::clean_plain($group['description'] ?? '');
                $items = [];
                if (!empty($group['items']) && is_array($group['items'])) {
                    foreach ($group['items'] as $itemindex => $item) {
                        if (!is_array($item)) {
                            $warnings[] = get_string(
                                'importwarninginvaliditem',
                                'gradingform_checklist',
                                ($groupindex + 1) . '.' . ($itemindex + 1)
                            );
                            continue;
                        }
                        $definition = self::clean_plain($item['definition'] ?? '');
                        $score = trim((string)($item['score'] ?? ''));
                        if ($definition === '' && $score === '') {
                            continue;
                        }
                        if ($definition === '') {
                            $errors[] = get_string(
                                'importerroritemdefinition',
                                'gradingform_checklist',
                                ($groupindex + 1) . '.' . ($itemindex + 1)
                            );
                            continue;
                        }
                        if (!is_numeric($score) || (float)$score < 0 || (float)$score > 1000) {
                            $errors[] = get_string(
                                'importerroritemscore',
                                'gradingform_checklist',
                                ($groupindex + 1) . '.' . ($itemindex + 1)
                            );
                            continue;
                        }
                        if (\core_text::strlen($definition) > \MoodleQuickForm_checklisteditor::get_item_definition_max_length()) {
                            $errors[] = get_string(
                                'err_definitionmax',
                                'gradingform_checklist',
                                \MoodleQuickForm_checklisteditor::get_item_definition_max_length()
                            );
                            continue;
                        }
                        $items[] = [
                            'definition' => $definition,
                            'score' => (float)$score,
                        ];
                    }
                }
                if ($description === '' && empty($items)) {
                    continue;
                }
                if ($description === '') {
                    $errors[] = get_string('importerrorgroupdescription', 'gradingform_checklist', $groupindex + 1);
                    continue;
                }
                if (\core_text::strlen($description) > \MoodleQuickForm_checklisteditor::get_group_description_max_length()) {
                    $errors[] = get_string(
                        'err_descriptionmax',
                        'gradingform_checklist',
                        \MoodleQuickForm_checklisteditor::get_group_description_max_length()
                    );
                    continue;
                }
                if (empty($items)) {
                    $errors[] = get_string('importerrorgroupitems', 'gradingform_checklist', $groupindex + 1);
                    continue;
                }
                $data['groups'][] = [
                    'description' => $description,
                    'items' => $items,
                ];
            }
        }

        if (empty($data['groups'])) {
            $errors[] = get_string('err_nogroups', 'gradingform_checklist');
        }

        return new canonical_import_data($data, array_values(array_unique($warnings)), array_values(array_unique($errors)));
    }

    /**
     * Normalises a setting key from display or internal names.
     *
     * @param string $key setting key
     * @return string|null
     */
    protected static function normalise_setting_key(string $key): ?string {
        $key = trim($key);
        $map = self::setting_label_map();
        if (array_key_exists($key, $map)) {
            return $map[$key];
        }
        $valid = array_values($map);
        return in_array($key, $valid, true) ? $key : null;
    }

    /**
     * Cleans plain-text checklist data.
     *
     * @param mixed $value value
     * @return string
     */
    protected static function clean_plain($value): string {
        return \MoodleQuickForm_checklisteditor::clean_multiline_text((string)$value);
    }

    /**
     * Normalises booleans from JSON or template text.
     *
     * @param mixed $value value
     * @return bool|null
     */
    public static function normalise_bool($value): ?bool {
        if (is_bool($value)) {
            return $value;
        }
        if (is_int($value)) {
            return $value === 1 ? true : ($value === 0 ? false : null);
        }
        $value = strtolower(trim((string)$value));
        if (in_array($value, ['1', 'yes', 'true', 'on'], true)) {
            return true;
        }
        if (in_array($value, ['0', 'no', 'false', 'off', ''], true)) {
            return false;
        }
        return null;
    }

    /**
     * Normalises observation mode labels.
     *
     * @param mixed $value value
     * @return string|null
     */
    protected static function normalise_observation_mode($value): ?string {
        $value = strtolower(trim((string)$value));
        $map = [
            'disabled' => \gradingform_checklist_controller::OBSERVATION_MODE_DISABLED,
            'date only' => \gradingform_checklist_controller::OBSERVATION_MODE_DATE,
            'date' => \gradingform_checklist_controller::OBSERVATION_MODE_DATE,
            'date and time' => \gradingform_checklist_controller::OBSERVATION_MODE_DATETIME,
            'datetime' => \gradingform_checklist_controller::OBSERVATION_MODE_DATETIME,
        ];
        $mode = $map[$value] ?? $value;
        return $mode === \gradingform_checklist_controller::clean_observation_mode($mode) ? $mode : null;
    }

    /**
     * Normalises observation default labels.
     *
     * @param mixed $value value
     * @return string|null
     */
    protected static function normalise_observation_default($value): ?string {
        $value = strtolower(trim((string)$value));
        $map = [
            'auto-populate with current date/time' => \gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
            'now' => \gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
            'leave blank' => \gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
            'blank' => \gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
        ];
        $default = $map[$value] ?? $value;
        return $default === \gradingform_checklist_controller::clean_observation_default($default) ? $default : null;
    }
}
