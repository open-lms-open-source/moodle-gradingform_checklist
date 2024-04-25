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
 * Generator testcase for the gradingforum_checklist generator.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist;

use advanced_testcase;
use context_module;
use gradingform_checklist_controller;
use gradingform_controller;

/**
 * Generator testcase for the gradingforum_checklist generator.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends advanced_testcase {

    /**
     * Test checklist creation.
     */
    public function test_checklist_creation(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $name = 'myfirstchecklist';
        $description = 'My first checklist';
        $criteria = [
            'Group 1' => [
                'Has title' => 1,
            ],
            'Group 2' => [
                'Has references' => 1,
            ],
        ];

        // Unit under test.
        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', $name, $description, $criteria);

        $this->assertInstanceOf(gradingform_checklist_controller::class, $controller);

        $definition = $controller->get_definition();
        $this->assertNotEmpty($definition->id);
        $this->assertEquals($name, $definition->name);
        $this->assertEquals($description, $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);
        $this->assertNotEmpty($definition->timecreated);
        $this->assertNotEmpty($definition->timemodified);
        $this->assertEquals($user->id, $definition->usercreated);

        $this->assertNotEmpty($definition->checklist_groups);
        $this->assertCount(2, $definition->checklist_groups);

        // Check the criteria1 criteria.
        $criteriaids = array_keys($definition->checklist_groups);

        $criteria1 = $definition->checklist_groups[$criteriaids[0]];
        $this->assertNotEmpty($criteria1['id']);
        $this->assertEquals(1, $criteria1['sortorder']);
        $this->assertEquals('Group 1', $criteria1['description']);

        $this->assertNotEmpty($criteria1['items']);
        $items = $criteria1['items'];
        $itemids = array_keys($items);

        $item = $items[$itemids[0]];
        $this->assertEquals(1, $item['score']);
        $this->assertEquals('Has title', $item['definition']);

        // Check the times criteria2 criteria.
        $criteria2 = $definition->checklist_groups[$criteriaids[1]];
        $this->assertNotEmpty($criteria2['id']);
        $this->assertEquals('Group 2', $criteria2['description']);

        $this->assertNotEmpty($criteria2['items']);
        $items = $criteria2['items'];
        $itemids = array_keys($items);

        $item = $items[$itemids[0]];
        $this->assertEquals(1, $item['score']);
        $this->assertEquals('Has references', $item['definition']);

    }

    /**
     * Test the get_item_and_criterion_for_values function.
     * This is used for finding criterion and item information within a checklist.
     */
    public function test_get_item_and_criterion_for_values(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $description = 'My first checklist';
        $criteria = [
            'Group 1' => [
                'Has title' => 1,
            ],
            'Group 2' => [
                'Has references' => 1,
            ],
        ];

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'checklist', $description, $criteria);

        // Valid criterion and item.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertEquals('Group 1', $result['criterion']->description);
        $this->assertEquals('1', $result['item']->score);
        $this->assertEquals('Has title', $result['item']->definition);

        // Valid criterion. Invalid item.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 3);
        $this->assertEquals('Group 1', $result['criterion']->description);
        $this->assertNull($result['item']);

        // Invalid criterion.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Foo', 0);
        $this->assertNull($result['criterion']);
    }

    /**
     * Tests for the get_test_checklist function.
     */
    public function test_get_test_checklist(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $checklist = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');
        $definition = $checklist->get_definition();

        $this->assertEquals('testchecklist', $definition->name);
        $this->assertEquals('Description text', $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);

        // Should create a checklist with 2 criterion.
        $this->assertCount(2, $definition->checklist_groups);
    }

    /**
     * Test the get_submitted_form_data function.
     */
    public function test_get_submitted_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');

        $result = $checklistgenerator->get_submitted_form_data($controller, 93, [
            'Group 1' => [
                'score' => 1,
                'remark' => 'This is the first comment',
                'checked' => false,
            ],
            'Group 2' => [
                'score' => 1,
                'remark' => 'This is the second comment',
                'checked' => true,
            ],
        ]);

        $this->assertIsArray($result);
        $this->assertEquals(93, $result['itemid']);
        $this->assertIsArray($result['groups']);
        $this->assertCount(2, $result['groups']);

        $group1 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertIsArray($result['groups'][$group1['criterion']->id]);
        $this->assertArrayHasKey($group1['item']->id, $result['groups'][$group1['criterion']->id]['items']);
        $this->assertEquals('This is the first comment', $result['groups'][$group1['criterion']->id]['items'][$group1['item']->id]['remark']);

        $group2 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 2', 1);
        $this->assertIsArray($result['groups'][$group2['criterion']->id]);
        $this->assertArrayHasKey($group2['item']->id, $result['groups'][$group2['criterion']->id]['items']);
        $this->assertEquals('This is the second comment', $result['groups'][$group2['criterion']->id]['items'][$group2['item']->id]['remark']);
    }

    /**
     * Test the get_test_form_data function.
     */
    public function test_get_test_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');

        // Unit under test.
        $result = $checklistgenerator->get_test_form_data(
            $controller,
            9999,
            1, 'This is the first comment',
            1, 'This is the second comment'
        );

        $this->assertIsArray($result);
        $this->assertEquals(9999, $result['itemid']);
        $this->assertIsArray($result['groups']);
        $this->assertCount(2, $result['groups']);

        $group1 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertIsArray($result['groups'][$group1['criterion']->id]);
        $this->assertArrayHasKey($group1['item']->id, $result['groups'][$group1['criterion']->id]['items']);
        $this->assertEquals('This is the first comment', $result['groups'][$group1['criterion']->id]['items'][$group1['item']->id]['remark']);

        $group2 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 2', 1);
        $this->assertIsArray($result['groups'][$group2['criterion']->id]);
        $this->assertArrayHasKey($group2['item']->id, $result['groups'][$group2['criterion']->id]['items']);
        $this->assertEquals('This is the second comment', $result['groups'][$group2['criterion']->id]['items'][$group2['item']->id]['remark']);
    }
}
