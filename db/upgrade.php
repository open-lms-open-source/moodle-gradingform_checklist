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

/**
 * @package    gradingform
 * @subpackage checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Keeps track or checklist plugin upgrade path
 *
 * @param int $oldversion the DB version of currently installed plugin
 * @return bool true
 */
function xmldb_gradingform_checklist_upgrade($oldversion) {
    global $CFG, $DB, $OUTPUT;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012051001) {

        // Changing type of field description on table gradingform_checklist_groups to text
        $table = new xmldb_table('gradingform_checklist_groups');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'sortorder');

        // Launch change of type for field description
        $dbman->change_field_type($table, $field);

        // Changing type of field definition on table gradingform_checklist_items to text
        $table = new xmldb_table('gradingform_checklist_items');
        $field = new xmldb_field('definition', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'score');

        // Launch change of type for field definition
        $dbman->change_field_type($table, $field);

        // checklist savepoint reached
        upgrade_plugin_savepoint(true, 2012051001, 'gradingform', 'checklist');
    }

    return true;
}