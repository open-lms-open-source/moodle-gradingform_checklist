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
 * Upgrade tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist;

use advanced_testcase;
use context_module;

global $CFG;
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');
require_once($CFG->libdir . '/upgradelib.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/db/upgrade.php');

/**
 * Upgrade tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class upgrade_test extends advanced_testcase {

    /**
     * Test legacy group benchmark content and files are preserved during upgrade.
     */
    public function test_upgrade_preserves_legacy_group_benchmarks(): void {
        global $DB;

        $this->resetAfterTest(true);

        [
            'context' => $context,
            'definitionid' => $definitionid,
            'groupids' => $groupids,
        ] = $this->create_test_definition();
        $this->add_legacy_benchmark_fields();

        $DB->delete_records('gradingform_checklist_bench', ['definitionid' => $definitionid]);
        $DB->set_field('gradingform_checklist_groups', 'benchmark', '<p>First benchmark</p>',
            ['id' => $groupids[0]]);
        $DB->set_field('gradingform_checklist_groups', 'benchmarkformat', FORMAT_HTML, ['id' => $groupids[0]]);
        $DB->set_field('gradingform_checklist_groups', 'benchmark', '<p>Second benchmark</p>',
            ['id' => $groupids[1]]);
        $DB->set_field('gradingform_checklist_groups', 'benchmarkformat', FORMAT_HTML, ['id' => $groupids[1]]);

        $fs = get_file_storage();
        $fs->create_file_from_string([
            'contextid' => $context->id,
            'component' => 'gradingform_checklist',
            'filearea' => 'benchmark',
            'itemid' => $groupids[0],
            'filepath' => '/',
            'filename' => 'legacy-benchmark.txt',
        ], 'legacy benchmark file');

        set_config('version', 2026073000, 'gradingform_checklist');
        $this->assertTrue(\xmldb_gradingform_checklist_upgrade(2026073000));

        $record = $DB->get_record('gradingform_checklist_bench', ['definitionid' => $definitionid], '*', MUST_EXIST);
        $this->assertSame("<p>First benchmark</p>\n<hr />\n<p>Second benchmark</p>", $record->benchmark);
        $this->assertSame((int)FORMAT_HTML, (int)$record->benchmarkformat);
        $this->assertSame(get_string('benchmarkbuttondefault', 'gradingform_checklist'), $record->buttonlabel);

        $this->assertFalse($fs->file_exists($context->id, 'gradingform_checklist', 'benchmark', $groupids[0], '/',
            'legacy-benchmark.txt'));
        $storedfile = $fs->get_file($context->id, 'gradingform_checklist', 'benchmark', $definitionid, '/',
            'legacy-benchmark.txt');
        $this->assertNotFalse($storedfile);
        $this->assertSame('legacy benchmark file', $storedfile->get_content());

        $this->assert_legacy_benchmark_fields_removed();
    }

    /**
     * Test upgrade removes legacy fields when no legacy benchmark data exists.
     */
    public function test_upgrade_handles_empty_legacy_benchmark_fields(): void {
        global $DB;

        $this->resetAfterTest(true);

        ['definitionid' => $definitionid] = $this->create_test_definition();
        $this->add_legacy_benchmark_fields();

        $DB->delete_records('gradingform_checklist_bench', ['definitionid' => $definitionid]);

        set_config('version', 2026073000, 'gradingform_checklist');
        $this->assertTrue(\xmldb_gradingform_checklist_upgrade(2026073000));

        $this->assertFalse($DB->record_exists('gradingform_checklist_bench', ['definitionid' => $definitionid]));
        $this->assert_legacy_benchmark_fields_removed();
    }

    /**
     * Test upgrade can create the observation table from the upgrade path.
     */
    public function test_upgrade_creates_observation_table(): void {
        global $DB;

        $this->resetAfterTest(true);

        $dbman = $DB->get_manager();
        $table = new \xmldb_table('gradingform_checklist_obs');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        set_config('version', 2026073100, 'gradingform_checklist');
        $this->assertTrue(\xmldb_gradingform_checklist_upgrade(2026073100));
        $this->assertTrue($dbman->table_exists($table));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('observationdate')));
        $this->assertTrue($dbman->field_exists($table, new \xmldb_field('observationmode')));
    }

    /**
     * Creates a checklist definition with two groups.
     *
     * @return array Test context, definition id, and group ids.
     */
    private function create_test_definition(): array {
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'upgradechecklist',
            'Description', [
                'Group 1' => [
                    'Has title' => 1,
                ],
                'Group 2' => [
                    'Has references' => 1,
                ],
            ]);
        $definition = $controller->get_definition();

        return [
            'context' => $context,
            'definitionid' => (int)$definition->id,
            'groupids' => array_keys($definition->checklist_groups),
        ];
    }

    /**
     * Adds legacy benchmark fields to the current test schema.
     */
    private function add_legacy_benchmark_fields(): void {
        global $DB;

        $dbman = $DB->get_manager();
        $table = new \xmldb_table('gradingform_checklist_groups');

        $field = new \xmldb_field('benchmark', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new \xmldb_field('benchmarkformat', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'benchmark');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
    }

    /**
     * Asserts legacy benchmark fields were removed by upgrade.
     */
    private function assert_legacy_benchmark_fields_removed(): void {
        global $DB;

        $dbman = $DB->get_manager();
        $table = new \xmldb_table('gradingform_checklist_groups');

        $this->assertFalse($dbman->field_exists($table, new \xmldb_field('benchmark')));
        $this->assertFalse($dbman->field_exists($table, new \xmldb_field('benchmarkformat')));
    }
}
