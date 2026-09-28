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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace gradingform_checklist\local;

/**
 * Applies checklist option dependencies and validates grading comments.
 *
 * The legacy controller keeps its public methods as compatibility bridges while
 * this class owns the self-contained option policy.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class option_policy {
    /** @var string Observation selector disabled. */
    private const OBSERVATION_DISABLED = 'disabled';
    /** @var string Date-only observation selector. */
    private const OBSERVATION_DATE = 'date';
    /** @var string Date-and-time observation selector. */
    private const OBSERVATION_DATETIME = 'datetime';

    /**
     * Returns whether observation capture is enabled.
     *
     * @param array $options Checklist options.
     * @return bool
     */
    public static function observation_enabled(array $options): bool {
        return !empty($options['observationmode'])
            && $options['observationmode'] !== self::OBSERVATION_DISABLED;
    }

    /**
     * Cleans an observation mode.
     *
     * @param string|null $mode Submitted mode.
     * @return string
     */
    public static function clean_observation_mode(?string $mode): string {
        $valid = [self::OBSERVATION_DISABLED, self::OBSERVATION_DATE, self::OBSERVATION_DATETIME];
        return in_array($mode, $valid, true) ? $mode : self::OBSERVATION_DISABLED;
    }

    /**
     * Cleans an observation default.
     *
     * @param string|null $default Submitted default.
     * @return string
     */
    public static function clean_observation_default(?string $default): string {
        return in_array($default, ['now', 'blank'], true) ? $default : 'now';
    }

    /**
     * Formats an observation date for display.
     *
     * @param int $timestamp Observation timestamp.
     * @param string $mode Observation mode.
     * @return string
     */
    public static function format_observation_date(int $timestamp, string $mode): string {
        $format = $mode === self::OBSERVATION_DATE ? 'strftimedate' : 'strftimedatetime';
        return userdate($timestamp, get_string($format, 'langconfig'));
    }

    /**
     * Formats an observation date for an HTML input.
     *
     * @param int $timestamp Observation timestamp.
     * @return string
     */
    public static function format_observation_date_input(int $timestamp): string {
        $date = usergetdate($timestamp);
        return sprintf('%04d-%02d-%02d', $date['year'], $date['mon'], $date['mday']);
    }

    /**
     * Formats an observation time for an HTML input.
     *
     * @param int $timestamp Observation timestamp.
     * @return string
     */
    public static function format_observation_time_input(int $timestamp): string {
        $date = usergetdate($timestamp);
        return sprintf('%02d:%02d', $date['hours'], $date['minutes']);
    }

    /**
     * Disables required-comment options when their matching remark field is disabled.
     *
     * @param array $options Checklist options.
     * @return array
     */
    public static function normalise_comment_dependencies(array $options): array {
        if (empty($options['enableitemremarks'])) {
            $options['requireitemcommentschecked'] = 0;
            $options['requireatleastoneitemcomment'] = 0;
        }
        if (empty($options['enablegroupremarks'])) {
            $options['requiregroupcommentschecked'] = 0;
            $options['requireatleastonegroupcomment'] = 0;
        }
        return $options;
    }

    /**
     * Returns the configured group remark heading.
     *
     * @param array $options Checklist options.
     * @return string
     */
    public static function group_remark_heading(array $options): string {
        if (!empty($options['groupremarkheading'])) {
            return trim(clean_param($options['groupremarkheading'], PARAM_TEXT));
        }
        return get_string('groupremarkheadingdefault', 'gradingform_checklist');
    }

    /**
     * Finds required-comment validation errors.
     *
     * @param array $groups Checklist definition groups.
     * @param array $options Checklist options.
     * @param array $value Submitted grading value.
     * @return array
     */
    public static function required_comment_errors(array $groups, array $options, array $value): array {
        $options = self::normalise_comment_dependencies($options);
        $required = [
            'itemeach' => !empty($options['requireitemcommentschecked']),
            'itemone' => !empty($options['requireatleastoneitemcomment']),
            'groupeach' => !empty($options['requiregroupcommentschecked']),
            'groupone' => !empty($options['requireatleastonegroupcomment']),
        ];
        if (!array_filter($required)) {
            return [];
        }

        $haschecked = false;
        $hasitemremark = false;
        $hasgroupremark = false;
        $missingitems = [];
        $missinggroups = [];
        $firstitem = null;
        $firstgroup = null;

        foreach ($groups as $groupid => $group) {
            $submitteditems = $value['groups'][$groupid]['items'] ?? [];
            $groupchecked = false;
            foreach ($group['items'] as $itemid => $item) {
                $submitted = $submitteditems[$itemid] ?? [];
                if (empty($submitted['id']) && empty($submitted['checked'])) {
                    continue;
                }
                $haschecked = true;
                $groupchecked = true;
                $detail = self::error_detail($groupid, $group, $itemid, $item, 'item');
                $firstitem ??= $detail;
                $remark = trim(clean_param($submitted['remark'] ?? '', PARAM_TEXT));
                if ($remark === '') {
                    $detail['rule'] = 'err_requireitemcommentschecked';
                    $missingitems[] = $detail;
                } else {
                    $hasitemremark = true;
                }
            }
            if (!$groupchecked) {
                continue;
            }
            $detail = self::error_detail($groupid, $group, 0, [], 'group');
            $firstgroup ??= $detail;
            $remark = trim(clean_param($submitteditems[0]['remark'] ?? '', PARAM_TEXT));
            if ($remark === '') {
                $detail['rule'] = 'err_requiregroupcommentschecked';
                $missinggroups[] = $detail;
            } else {
                $hasgroupremark = true;
            }
        }

        $errors = $required['itemeach'] ? $missingitems : [];
        if ($required['itemone'] && $haschecked && !$hasitemremark && $firstitem !== null) {
            $firstitem['rule'] = 'err_requireatleastoneitemcomment';
            $errors[] = $firstitem;
        }
        if ($required['groupeach']) {
            $errors = array_merge($errors, $missinggroups);
        }
        if ($required['groupone'] && $haschecked && !$hasgroupremark && $firstgroup !== null) {
            $firstgroup['rule'] = 'err_requireatleastonegroupcomment';
            $errors[] = $firstgroup;
        }
        return $errors;
    }

    /**
     * Builds validation error details.
     *
     * @param int $groupid Group id.
     * @param array $group Group definition.
     * @param int $itemid Item id.
     * @param array $item Item definition.
     * @param string $type Field type.
     * @return array
     */
    private static function error_detail(int $groupid, array $group, int $itemid, array $item, string $type): array {
        return [
            'rule' => '',
            'groupid' => $groupid,
            'group' => $group['description'] ?? '',
            'itemid' => $itemid,
            'item' => $item['definition'] ?? '',
            'fieldtype' => $type,
        ];
    }

    /**
     * Returns the related feedback field id.
     *
     * @param array $error Structured validation error.
     * @param string $elementname Grading form element name.
     * @return string
     */
    public static function error_field_id(array $error, string $elementname): string {
        if (($error['fieldtype'] ?? '') === 'group') {
            return $elementname . '-groups-' . $error['groupid'] . '-items-0-remark';
        }
        return $elementname . '-groups-' . $error['groupid'] . '-items-' . $error['itemid'] . '-remark-input';
    }

    /**
     * Formats an error for display.
     *
     * @param array $error Structured validation error.
     * @return string
     */
    public static function format_error(array $error): string {
        $a = (object)['group' => $error['group'] ?? '', 'item' => $error['item'] ?? ''];
        return get_string($error['rule'], 'gradingform_checklist', $a);
    }
}
