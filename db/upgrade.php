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
 * @package    gradingform_checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
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


    if ($oldversion < 2026073000) {
        $table = new xmldb_table('gradingform_checklist_groups');

        $field = new xmldb_field('benchmark', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('benchmarkformat', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'benchmark');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2026073000, 'gradingform', 'checklist');
    }

    if ($oldversion < 2026073100) {
        $table = new xmldb_table('gradingform_checklist_bench');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('definitionid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('benchmark', XMLDB_TYPE_TEXT, null, null, null, null, null);
            $table->add_field('benchmarkformat', XMLDB_TYPE_INTEGER, '2', null, null, null, null);
            $table->add_field('buttonlabel', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'Open to view Benchmarks');
            $table->add_field('buttonicon', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, 'fa-solid fa-file-circle-check');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('definitionid', XMLDB_KEY_FOREIGN_UNIQUE, ['definitionid'], 'grading_definitions', ['id']);
            $dbman->create_table($table);
        }

        $table = new xmldb_table('gradingform_checklist_groups');
        $field = new xmldb_field('benchmark');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }
        $field = new xmldb_field('benchmarkformat');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $fs = get_file_storage();
        $contexts = $DB->get_fieldset_sql("
            SELECT DISTINCT ga.contextid
              FROM {grading_definitions} gd
              JOIN {grading_areas} ga ON ga.id = gd.areaid
             WHERE gd.method = ?", ['checklist']);
        foreach ($contexts as $contextid) {
            $fs->delete_area_files((int) $contextid, 'gradingform_checklist', 'benchmark');
        }

        upgrade_plugin_savepoint(true, 2026073100, 'gradingform', 'checklist');
    }

    if ($oldversion < 2026080200) {
        $table = new xmldb_table('gradingform_checklist_obs');
        if (!$dbman->table_exists($table)) {
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('observationdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
            $table->add_field('observationmode', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, 'date');
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
            $table->add_key('instanceid', XMLDB_KEY_FOREIGN_UNIQUE, ['instanceid'], 'grading_instances', ['id']);
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2026080200, 'gradingform', 'checklist');
    }

    return true;
}
