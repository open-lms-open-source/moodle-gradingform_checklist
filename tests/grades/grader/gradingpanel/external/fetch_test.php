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
use core_grades\component_gradeitems;
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
class fetch_test extends advanced_testcase {

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
        fetch::execute('mod_invalid', 1, 'foo', 2);
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
        fetch::execute('mod_forum', 1, 'foo', 2);
    }

    /**
     * Ensure that an execute against a different grading method is rejected.
     */
    public function test_execute_incorrect_type(): void {
        $this->resetAfterTest();

        $forum = $this->get_forum_instance([
            // Negative numbers mean a scale.
            'grade_forum' => 5,
        ]);
        $course = $forum->get_course_record();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $student = $this->getDataGenerator()->create_and_enrol($course, 'student');
        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage("not configured for advanced grading with a checklist");
        fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id);
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_fetch_empty(): void {
        $this->resetAfterTest();

        [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->setUser($teacher);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');

        $result = fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $student->id);
        $result = external_api::clean_returnvalue(fetch::execute_returns(), $result);

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
        $this->assertEquals('- / 100.00', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(100, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(null, $result['grade']['gradedby']);

        $this->assertArrayHasKey('criteria', $result['grade']);
        $criteria = $result['grade']['criteria'];
        $this->assertCount(count($definition->checklist_groups), $criteria);
        foreach ($criteria as $criterion) {
            $this->assertArrayHasKey('id', $criterion);
            $criterionid = $criterion['id'];
            $sourcecriterion = $definition->checklist_groups[$criterionid];

            $this->assertArrayHasKey('description', $criterion);
            $this->assertEquals($sourcecriterion['description'], $criterion['description']);

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
    }

    /**
     * Ensure that an execute against the correct grading method returns the current state of the user.
     */
    public function test_execute_fetch_graded(): void {
        $this->resetAfterTest();

        [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->execute_and_assert_fetch($forum, $controller, $definition, $teacher, $teacher, $student);
    }

    /**
     * Class mates should not get other's grades.
     */
    public function test_execute_fetch_does_not_return_data_to_other_students(): void {
        $this->resetAfterTest();

        [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
            'course' => $course,
        ] = $this->get_test_data();

        $evilstudent = $this->getDataGenerator()->create_and_enrol($course, 'student');

        $this->expectException(\core\exception\required_capability_exception::class);
        $this->execute_and_assert_fetch($forum, $controller, $definition, $evilstudent, $teacher, $student);
    }

    /**
     * Grades can be returned to graded user.
     */
    public function test_execute_fetch_return_data_to_graded_user(): void {
        $this->resetAfterTest();

        [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
        ] = $this->get_test_data();

        $this->execute_and_assert_fetch($forum, $controller, $definition, $student, $teacher, $student);
    }

    /**
     * Executes and performs all the assertions of the fetch method with the given parameters.
     */
    private function execute_and_assert_fetch ($forum, $controller, $definition, $fetcheruser, $grader, $gradeduser) {
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $this->setUser($grader);

        $gradeitem = component_gradeitem::instance('mod_forum', $forum->get_context(), 'forum');
        $grade = $gradeitem->get_grade_for_user($gradeduser, $grader);
        $instance = $gradeitem->get_advanced_grading_instance($grader, $grade);

        $submissiondata = $checklistgenerator->get_test_form_data($controller, (int) $gradeduser->id,
            1, 'This is the first comment',
            1, 'This is the second comment'
        );

        $gradeitem->store_grade_from_formdata($gradeduser, $grader, (object) [
            'instanceid' => $instance->get_id(),
            'advancedgrading' => $submissiondata,
        ]);

        $this->setUser($fetcheruser);

        $result = fetch::execute('mod_forum', (int) $forum->get_context()->id, 'forum', (int) $gradeduser->id);
        $result = external_api::clean_returnvalue(fetch::execute_returns(), $result);

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
        $this->assertEquals('50.00 / 100.00', $result['grade']['usergrade']);

        $this->assertArrayHasKey('maxgrade', $result['grade']);
        $this->assertIsInt($result['grade']['maxgrade']);
        $this->assertEquals(100, $result['grade']['maxgrade']);

        $this->assertArrayHasKey('gradedby', $result['grade']);
        $this->assertEquals(fullname($grader), $result['grade']['gradedby']);

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
        $forum = $datagenerator->create_module('forum', array_merge($config, ['course' => $course->id, 'grade_forum' => 100]));

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

        // In the situation of mod_forum this would be the id from forum_grades.
        $itemid = 1;
        $instance = $controller->create_instance($student->id, $itemid);

        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            1, 'This is the first comment',
            1, 'This is the second comment'
        );

        // Update this instance with data.
        $instance->update($data);

        return [
            'forum' => $forum,
            'controller' => $controller,
            'definition' => $definition,
            'student' => $student,
            'teacher' => $teacher,
            'course' => $course,
        ];
    }
    /**
     * Fetch a set of sample data.
     *
     * @param \gradingform_checklist_controller $controller
     * @param int $itemid
     * @param float $spellingscore
     * @param string $spellingremark
     * @param float $picturescore
     * @param string $pictureremark
     * @return array
     */
    protected function get_test_form_data(
        \gradingform_checklist_controller $controller,
        int $itemid,
        float $spellingscore,
        string $spellingremark,
        float $picturescore,
        string $pictureremark
    ): array {
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        return $checklistgenerator->get_test_form_data(
            $controller,
            $itemid,
            $spellingscore,
            $spellingremark,
            $picturescore,
            $pictureremark
        );
    }
}
