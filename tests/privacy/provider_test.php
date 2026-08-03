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
 * Privacy tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\privacy;

use context_module;
use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

/**
 * Privacy tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {
    /**
     * Test that checklist fill metadata is declared.
     */
    public function test_get_metadata(): void {
        $collection = new collection('gradingform_checklist');
        $collection = provider::get_metadata($collection);
        $metadata = $collection->get_collection();

        $this->assertCount(3, $metadata);

        $tables = [];
        foreach ($metadata as $table) {
            $tables[$table->get_name()] = $table;
        }

        $this->assertArrayHasKey('gradingform_checklist_bench', $tables);
        $benchmarktable = $tables['gradingform_checklist_bench'];
        $this->assertEquals('privacy:metadata:benchmarksummary', $benchmarktable->get_summary());

        $benchmarkfields = $benchmarktable->get_privacy_fields();
        $this->assertArrayHasKey('definitionid', $benchmarkfields);
        $this->assertArrayHasKey('benchmark', $benchmarkfields);
        $this->assertArrayHasKey('benchmarkformat', $benchmarkfields);
        $this->assertArrayHasKey('buttonlabel', $benchmarkfields);
        $this->assertArrayHasKey('buttonicon', $benchmarkfields);

        $this->assertArrayHasKey('gradingform_checklist_fills', $tables);
        $filltable = $tables['gradingform_checklist_fills'];
        $this->assertEquals('privacy:metadata:fillingssummary', $filltable->get_summary());

        $privacyfields = $filltable->get_privacy_fields();
        $this->assertArrayHasKey('instanceid', $privacyfields);
        $this->assertArrayHasKey('groupid', $privacyfields);
        $this->assertArrayHasKey('itemid', $privacyfields);
        $this->assertArrayHasKey('checked', $privacyfields);
        $this->assertArrayHasKey('remark', $privacyfields);
        $this->assertArrayHasKey('remarkformat', $privacyfields);

        $this->assertArrayHasKey('gradingform_checklist_obs', $tables);
        $observationtable = $tables['gradingform_checklist_obs'];
        $this->assertEquals('privacy:metadata:observationsummary', $observationtable->get_summary());

        $observationfields = $observationtable->get_privacy_fields();
        $this->assertArrayHasKey('instanceid', $observationfields);
        $this->assertArrayHasKey('observationdate', $observationfields);
        $this->assertArrayHasKey('observationmode', $observationfields);
    }

    /**
     * Test the export of checklist data.
     */
    public function test_export_gradingform_instance_data(): void {
        $this->resetAfterTest();

        [
            'context' => $context,
            'instance' => $instance,
        ] = $this->create_graded_checklist_instance(1);

        provider::export_gradingform_instance_data($context, $instance->get_id(), ['Test']);
        $data = (array) writer::with_context($context)->get_data(['Test', 'Checklist', $instance->get_id()]);

        $this->assertCount(2, $data);

        $records = array_values($data);
        $this->assertEquals('Group 1', $records[0]->groupdescription);
        $this->assertEquals('Has title', $records[0]->itemdefinition);
        $this->assertEquals(0, $records[0]->checked);
        $this->assertEquals('This is the first comment', $records[0]->remark);

        $this->assertEquals('Group 2', $records[1]->groupdescription);
        $this->assertEquals('Has references', $records[1]->itemdefinition);
        $this->assertEquals(1, $records[1]->checked);
        $this->assertEquals('This is the second comment', $records[1]->remark);
    }

    /**
     * Test the deletion of checklist user information via grading instance ID.
     */
    public function test_delete_gradingform_for_instances(): void {
        global $DB;

        $this->resetAfterTest();

        $first = $this->create_graded_checklist_instance(1);
        $second = $this->create_graded_checklist_instance(2);

        $this->assertCount(4, $DB->get_records('gradingform_checklist_fills'));

        provider::delete_gradingform_for_instances([$second['instance']->get_id()]);

        $records = $DB->get_records('gradingform_checklist_fills');
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $this->assertEquals($first['instance']->get_id(), $record->instanceid);
        }
    }

    /**
     * Create and grade a sample checklist instance.
     *
     * @param int $itemid The graded item ID to use when creating the grading instance.
     * @return array Test context and grading instance.
     */
    protected function create_graded_checklist_instance(int $itemid): array {
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);

        $controller = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $checklistgenerator->get_test_form_data(
            $controller,
            $itemid,
            1,
            'This is the first comment',
            1,
            'This is the second comment'
        );

        $instance->update($data);

        return [
            'context' => $context,
            'instance' => $instance,
        ];
    }
}
