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
 * Copies benchmark files from a legacy group itemid to the definition itemid.
 *
 * @param int $contextid The grading area context id.
 * @param int $legacyitemid The legacy group id used as file itemid.
 * @param int $definitionid The new definition id used as file itemid.
 */
function xmldb_gradingform_checklist_copy_legacy_benchmark_files(int $contextid, int $legacyitemid,
        int $definitionid): void {
    $fs = get_file_storage();
    $files = $fs->get_area_files(
        $contextid,
        'gradingform_checklist',
        'benchmark',
        $legacyitemid,
        'filepath, filename',
        false
    );

    foreach ($files as $file) {
        $target = $fs->get_file(
            $contextid,
            'gradingform_checklist',
            'benchmark',
            $definitionid,
            $file->get_filepath(),
            $file->get_filename()
        );

        if ($target) {
            if ($target->get_contenthash() === $file->get_contenthash()) {
                $file->delete();
            }
            continue;
        }

        $fs->create_file_from_storedfile([
            'contextid' => $contextid,
            'component' => 'gradingform_checklist',
            'filearea' => 'benchmark',
            'itemid' => $definitionid,
            'filepath' => $file->get_filepath(),
            'filename' => $file->get_filename(),
            'userid' => $file->get_userid(),
            'author' => $file->get_author(),
            'license' => $file->get_license(),
        ], $file);
        $file->delete();
    }
}

/**
 * Preserves benchmark content temporarily stored against checklist groups.
 *
 * @param bool $hasbenchmarkfield Whether the legacy benchmark field exists.
 * @param bool $hasformatfield Whether the legacy benchmarkformat field exists.
 */
function xmldb_gradingform_checklist_migrate_legacy_benchmarks(bool $hasbenchmarkfield, bool $hasformatfield): void {
    global $DB;

    if (!$hasbenchmarkfield && !$hasformatfield) {
        return;
    }

    $benchmarksql = $hasbenchmarkfield ? 'clg.benchmark' : "''";
    $formatsql = $hasformatfield ? 'clg.benchmarkformat' : (string)FORMAT_HTML;
    $definitions = [];

    $sql = "SELECT clg.id AS groupid,
                   clg.sortorder,
                   gd.id AS definitionid,
                   ga.contextid,
                   {$benchmarksql} AS legacybenchmark,
                   {$formatsql} AS legacybenchmarkformat
              FROM {gradingform_checklist_groups} clg
              JOIN {grading_definitions} gd ON gd.id = clg.definitionid
              JOIN {grading_areas} ga ON ga.id = gd.areaid
             WHERE gd.method = ?
          ORDER BY gd.id, clg.sortorder, clg.id";
    $rs = $DB->get_recordset_sql($sql, ['checklist']);
    foreach ($rs as $record) {
        $definitionid = (int)$record->definitionid;
        if (!isset($definitions[$definitionid])) {
            $definitions[$definitionid] = [
                'benchmarks' => [],
                'seen' => [],
            ];
        }

        xmldb_gradingform_checklist_copy_legacy_benchmark_files(
            (int)$record->contextid,
            (int)$record->groupid,
            $definitionid
        );

        $benchmark = trim((string)$record->legacybenchmark);
        if ($benchmark === '' || isset($definitions[$definitionid]['seen'][$benchmark])) {
            continue;
        }

        $definitions[$definitionid]['seen'][$benchmark] = true;
        $definitions[$definitionid]['benchmarks'][] = [
            'benchmark' => $benchmark,
            'benchmarkformat' => (int)($record->legacybenchmarkformat ?? FORMAT_HTML),
        ];
    }
    $rs->close();

    foreach ($definitions as $definitionid => $definition) {
        if (empty($definition['benchmarks'])) {
            continue;
        }

        $existing = $DB->get_record('gradingform_checklist_bench', ['definitionid' => $definitionid]);
        if ($existing && trim((string)$existing->benchmark) !== '') {
            continue;
        }

        $benchmarks = $definition['benchmarks'];
        if (count($benchmarks) === 1) {
            $benchmark = reset($benchmarks);
            $text = $benchmark['benchmark'];
            $format = $benchmark['benchmarkformat'] ?: FORMAT_HTML;
        } else {
            $text = implode("\n<hr />\n", array_column($benchmarks, 'benchmark'));
            $format = FORMAT_HTML;
        }

        $record = (object)[
            'definitionid' => $definitionid,
            'benchmark' => $text,
            'benchmarkformat' => $format,
            'buttonlabel' => 'Open to view Benchmarks',
            'buttonicon' => 'fa-solid fa-file-circle-check',
        ];

        if ($existing) {
            $record->id = $existing->id;
            $DB->update_record('gradingform_checklist_bench', $record);
        } else {
            $DB->insert_record('gradingform_checklist_bench', $record);
        }
    }
}

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
        $benchmarkfield = new xmldb_field('benchmark');
        $formatfield = new xmldb_field('benchmarkformat');
        $hasbenchmarkfield = $dbman->field_exists($table, $benchmarkfield);
        $hasformatfield = $dbman->field_exists($table, $formatfield);

        xmldb_gradingform_checklist_migrate_legacy_benchmarks($hasbenchmarkfield, $hasformatfield);

        if ($hasbenchmarkfield) {
            $dbman->drop_field($table, $benchmarkfield);
        }
        if ($hasformatfield) {
            $dbman->drop_field($table, $formatfield);
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

    // Version 2026080700 contains administration-setting and documentation changes only.
    if ($oldversion < 2026080700) {
        upgrade_plugin_savepoint(true, 2026080700, 'gradingform', 'checklist');
    }

    // Version 2026081200 refreshes service metadata and hardens benchmark defaults.
    if ($oldversion < 2026081200) {
        upgrade_plugin_savepoint(true, 2026081200, 'gradingform', 'checklist');
    }

    return true;
}
