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

defined('MOODLE_INTERNAL') || die();

/**
 * Central access to Checklist administrator configuration.
 *
 * Missing values deliberately fall back to the historical behaviour so that
 * an upgrade cannot disable an existing workflow.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config {

    /** @var array Default administrator values. */
    private const DEFAULTS = [
        'groupdescriptionmaxchars' => 500,
        'itemdefinitionmaxchars' => 1500,
        'enablewordimport' => 1,
        'enablejsonimport' => 1,
        'enablejsonwebservice' => 0,
        'enablewordtemplate' => 1,
        'enablejsonexample' => 1,
        'enablejsonschema' => 1,
        'enablebenchmarks' => 1,
    ];

    /** @var array Default values for newly-created checklist definitions. */
    private const OPTION_DEFAULTS = [
        'alwaysshowdefinition' => 1,
        'showitempointseval' => 0,
        'showitempointstudent' => 0,
        'showgrouppointseval' => 0,
        'showgrouppointstudent' => 0,
        'enableitemremarks' => 0,
        'enablegroupremarks' => 1,
        'showremarksstudent' => 1,
        'enablebulkcheck' => 0,
        'requireitemcommentschecked' => 0,
        'requireatleastoneitemcomment' => 0,
        'requiregroupcommentschecked' => 0,
        'requireatleastonegroupcomment' => 0,
        'groupremarkheading' => '',
        'observationmode' => 'disabled',
        'observationdefault' => 'now',
    ];

    /**
     * Get an administrator setting with a safe historical fallback.
     *
     * @param string $name
     * @return mixed
     */
    public static function get(string $name) {
        $default = self::DEFAULTS[$name] ?? self::OPTION_DEFAULTS[$name] ?? null;
        $value = get_config('gradingform_checklist', $name);
        return $value === false ? $default : $value;
    }

    /**
     * Whether a feature is enabled.
     *
     * @param string $name
     * @return bool
     */
    public static function enabled(string $name): bool {
        return (bool)self::get($name);
    }

    /**
     * Get a configured text limit.
     *
     * @param string $name
     * @return int
     */
    public static function limit(string $name): int {
        $value = (int)self::get($name);
        return max(1, min(100000, $value));
    }

    /**
     * Get defaults for a newly-created checklist definition.
     *
     * @return array
     */
    public static function option_defaults(): array {
        $options = self::OPTION_DEFAULTS;
        foreach (array_keys($options) as $name) {
            $value = self::get($name);
            if (is_bool($value)) {
                $value = $value ? 1 : 0;
            } else if (is_int(self::OPTION_DEFAULTS[$name])) {
                $value = (int)$value;
            }
            $options[$name] = $value;
        }
        if (empty($options['enableitemremarks'])) {
            $options['requireitemcommentschecked'] = 0;
            $options['requireatleastoneitemcomment'] = 0;
        }
        if (empty($options['enablegroupremarks'])) {
            $options['requiregroupcommentschecked'] = 0;
            $options['requireatleastonegroupcomment'] = 0;
        }
        if (!in_array($options['observationmode'], ['disabled', 'date', 'datetime'], true)) {
            $options['observationmode'] = 'disabled';
        }
        if (!in_array($options['observationdefault'], ['now', 'blank'], true)) {
            $options['observationdefault'] = 'now';
        }
        return $options;
    }
}
