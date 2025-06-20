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

declare(strict_types = 1);

namespace gradingform_checklist\grades\grader\gradingpanel\external;

use advanced_testcase;
use \core\exception\coding_exception;
use core_grades\component_gradeitem;
use core_external\external_api;
use mod_forum\local\entities\forum as forum_entity;
use \core\exception\moodle_exception;

/**
 * Unit tests for core_grades\component_gradeitems;
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
class store_test extends advanced_testcase {

    protected function setUp(): void {
        global $CFG;
    }

    /**
     * Ensure that an execute with an invalid component is rejected.
     */
    public function test_execute_invalid_component(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("The 'foo' item is not valid for the 'mod_invalid' component");
        store::execute('mod_invalid', 1, 'foo', 2, false, 'formdata');
    }

    /**
     * Ensure that an execute with an invalid itemname on a valid component is rejected.
     */
    public function test_execute_invalid_itemname(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(coding_exception::class);
        $this->expectExceptionMessage("The 'foo' item is not valid for the 'mod_forum' component");
        store::execute('mod_forum', 1, 'foo', 2, false, 'formdata');
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_incorrect_type(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance([
            'grade_forum' => 5,
        ]);
         $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        store::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id, false, 'formdata');
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_disabled(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance();
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("Grading is not enabled");
        store::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id, false, 'formdata');
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_store_graded(): void {
        $this->resetAfterTest();
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');
        $grade = $gradeitem->get_grade_for_user($student, $teacher);
        $instance = $gradeitem->get_advanced_grading_instance($teacher, $grade);

        $submissiondata = $checklistgenerator->get_test_form_data($controller, (int) $student->id,
            1, 'This is the first comment',
            1, 'This is the second comment'
        );

        $formdata = http_build_query((object) [
            'instanceid' => $instance->get_id(),
            'advancedgrading' => $submissiondata,
        ], '', '&');

        $result = store::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id, false, $formdata);
        $result = external_api::clean_returnvalue(store::execute_returns(), $result);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('templatename', $result);

        $this->assertEquals('gradingform_checklist/grades/grader/gradingpanel', $result['templatename']);

        $this->assertArrayHasKey('warnings', $result);
        $this->assertIsArray($result['warnings']);
        $this->assertEmpty($result['warnings']);

        // Test the grade array items.
        $this->assertArrayHasKey('grade', $result);
        $this->assertIsArray($result['grade']);
        $this->assertIsInt($result['grade']['timecreated']);

        $this->assertArrayHasKey('timemodified', $result['grade']);
        $this->assertIsInt($result['grade']['timemodified']);

        $this->assertArrayHasKey('usergrade', $result['grade']);
        $this->assertEquals('1.00 / 2.00', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(2, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(fullname($teacher), $result['grade']['gradedby']);

        $this->assertArrayHasKey('criteria', $result['grade']);
        $criteria = $result['grade']['criteria'];
        $this->assertCount(count($definition->checklist_groups), $criteria);
        foreach ($criteria as $criterion) {
            $this->assertArrayHasKey('id', $criterion);
            $criterionid = $criterion['id'];
            $sourcecriterion = $definition->checklist_groups[$criterionid];

            $this->assertArrayHasKey('description', $criterion);
            $this->assertEquals($sourcecriterion['description'], $criterion['description']);

            $this->assertArrayHasKey('remark', $criterion['items'][0]);

            $this->assertArrayHasKey('items', $criterion);

            $items = $criterion['items'];
            foreach ($items as $item) {
                $itemid = $item['id'];
                if (!isset($itemid)) {
                    continue;
                }
                $sourceitem = $sourcecriterion['items'][$itemid];

                $this->assertArrayHasKey('criterionid', $item);
                $this->assertEquals($criterionid, $item['criterionid']);

                $this->assertArrayHasKey('checked', $item);

                $this->assertArrayHasKey('definition', $item);
                $this->assertEquals($sourceitem['definition'], $item['definition']);

                $this->assertArrayHasKey('score', $item);
                $this->assertEquals($sourceitem['score'], $item['score']);
            }

        }

        $this->assertEquals(false, $criteria[0]['items'][0]['checked']);
        $this->assertEquals('This is the first comment', $criteria[0]['items'][0]['remark']);
        $this->assertEquals(true, $criteria[1]['items'][0]['checked']);
        $this->assertEquals('This is the second comment', $criteria[1]['items'][0]['remark']);
    }

    /**
     * Get a forum instance.
     *
     * @param array $config
     * @return forum_entity
     */
    protected function get_forum_instance(array $config = []): forum_entity {
        $this->resetAfterTest();

        $datagenerator = $this->getDataGenerator();
        $course = $datagenerator->create_course();
        $forum = $datagenerator->create_module('forum', array_merge($config, ['course' => $course->id]));

        $vaultfactory = \mod_forum\local\container::get_vault_factory();
        $vault = $vaultfactory->get_forum_vault();

        return $vault->get_from_id((int) $forum->id);
    }

    /**
     * Get test data for forums graded using a checklist.
     *
     * @return array
     */
    protected function get_test_data(): array {
        global $DB;

        $this->resetAfterTest();

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $forum = $this->get_forum_instance();
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->setUser($teacher);
        $controller = $checklistgenerator->get_test_checklist($forum->get_context(), 'forum', 'forum');
        $definition = $controller->get_definition();

        $DB->set_field('forum', 'grade_forum', count($definition->checklist_groups), ['id' => $forum->get_id()]);
        return [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
        ];
    }
}
