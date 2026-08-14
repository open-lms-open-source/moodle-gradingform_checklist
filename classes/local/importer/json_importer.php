<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * JSON checklist importer.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

defined('MOODLE_INTERNAL') || die();

/**
 * Parses canonical JSON imports.
 */
class json_importer {
    /**
     * Parses JSON content.
     *
     * @param string $content JSON content
     * @return canonical_import_data
     */
    public function parse(string $content): canonical_import_data {
        $decoded = json_decode($content, true);
        if (!is_array($decoded)) {
            return new canonical_import_data([], [], [get_string('importerrorinvalidjson', 'gradingform_checklist')]);
        }
        return import_validator::validate($decoded);
    }
}
