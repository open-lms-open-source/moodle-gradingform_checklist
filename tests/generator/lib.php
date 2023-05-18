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
 * Generator for the gradingforum_checklist plugin.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/checklist.php');
require_once(__DIR__ . '/criterion.php');

use tests\gradingform_checklist\generator\checklist;
use tests\gradingform_checklist\generator\criterion;

/**
 * Generator for the gradingforum_checklist plugintype.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_checklist_generator extends component_generator_base {

    /**
     * Create an instance of a checklist.
     *
     * @param context $context
     * @param string $component
     * @param string $area
     * @param string $name
     * @param string $description
     * @param array $criteria The list of criteria to add to the generated checklist
     * @return gradingform_checklist_controller
     */
    public function create_instance(
        context $context,
        string $component,
        string $area,
        string $name,
        string $description,
        array $criteria
    ): gradingform_checklist_controller {
        global $USER;

        if ($USER->id === 0) {
            throw new \coding_exception('Creation of a checklist must currently be run as a user.');
        }

        // Fetch the controller for this context/component/area.
        $generator = \testing_util::get_data_generator();
        $gradinggenerator = $generator->get_plugin_generator('core_grading');
        $controller = $gradinggenerator->create_instance($context, $component, $area, 'checklist');

        // Generate a definition for the supplied checklist.
        $checklist = $this->get_checklist($name, $description);
        foreach ($criteria as $name => $criterion) {
            $checklist->add_criteria($this->get_criterion($name, $criterion));
        }

        // Update the controller wih the checklist definition.
        $controller->update_definition($checklist->get_definition());

        return $controller;
    }

    /**
     * Get a new checklist for use with the checklist controller.
     *
     * Note: This is just a helper class used to build a new definition. It does not persist the data.
     *
     * @param string $name
     * @param string $description
     * @return checklist
     */
    protected function get_checklist(string $name, string $description): checklist {
        return new checklist($name, $description);
    }

    /**
     * Get a new checklist for use with a gradingform_checklist_generator_checklist.
     *
     * Note: This is just a helper class used to build a new definition. It does not persist the data.
     *
     * @param string $description
     * @param array $items Set of items in the form definition => score
     * @return gradingform_checklist_generator_criterion
     */
    protected function get_criterion(string $description, array $items = []): criterion {
        return new criterion($description, $items);
    }

    /**
     * Given a controller instance, fetch the item and criterion information for the specified values.
     *
     * @param gradingform_controller $controller
     * @param string $description The description to match the criterion on
     * @param float $score The value to match the item on
     * @return array
     */
    public function get_item_and_criterion_for_values(
        gradingform_controller $controller,
        string $description,
        float $score
    ): array {
        $definition = $controller->get_definition();
        $criteria = $definition->checklist_groups;

        $criterion = $item = null;

        $criterion = array_reduce($criteria, function($carry, $criterion) use ($description) {
            if ($criterion['description'] === $description) {
                $carry = $criterion;
            }

            return $carry;
        }, null);

        if ($criterion) {
            $criterion = (object) $criterion;
            $item = array_reduce($criterion->items, function($carry, $item) use ($score) {
                if ($item['score'] == $score) {
                    $carry = $item;
                }
                return $carry;
            });
            $item = $item ? (object) $item : null;
        }

        return [
            'criterion' => $criterion,
            'item' => $item,
        ];
    }

    /**
     * Get submitted form data for the supplied controller, itemid, and values.
     * The returned data is in the format used by checklist when handling form submission.
     *
     * @param gradingform_checklist_controller $controller
     * @param int $itemid
     * @param array $values A set of array values where the array key is the name of the criterion, and the value is an
     * array with the desired score, and any remark.
     */
    public function get_submitted_form_data(gradingform_checklist_controller $controller, int $itemid, array $values): array {
        $result = [
            'itemid' => $itemid,
            'groups' => [],
        ];
        foreach ($values as $criterionname => ['score' => $score, 'remark' => $remark, 'checked' => $checked]) {
            [
                'criterion' => $criterion,
                'item' => $item,
            ] = $this->get_item_and_criterion_for_values($controller, $criterionname, $score);
            if ($checked) {
                $result['groups'][$criterion->id]['items'][$item->id] = [
                    'id' => $item->id,
                    'remark' => $remark,
                    'checked' => $checked,
                ];
            } else {
                $result['groups'][$criterion->id]['items'][$item->id] = [
                    'remark' => $remark,
                    'checked' => $checked,
                ];
            }

        }

        return $result;
    }

    /**
     * Generate a checklist controller with sample data required for testing of this class.
     *
     * @param context $context
     * @param string $component
     * @param string $area
     * @return gradingform_checklist_controller
     */
    public function get_test_checklist(context $context, string $component, string $area): gradingform_checklist_controller {
        $criteria = [
            'Group 1' => [
                'Has title' => 1
            ],
            'Group 2' => [
                'Has references' => 1
            ],
        ];

        return $this->create_instance($context, $component, $area, 'testchecklist', 'Description text', $criteria);
    }

    /**
     * Fetch a set of sample data.
     *
     * @param gradingform_checklist_controller $controller
     * @param int $itemid
     * @param float $spellingscore
     * @param string $spellingremark
     * @param float $picturescore
     * @param string $pictureremark
     * @return array
     */
    public function get_test_form_data(
        gradingform_checklist_controller $controller,
        int $itemid,
        float $group1itemcheck,
        string $group1itemremark,
        float $group2itemcheck,
        string $group2itemremark
    ): array {
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');
        return $checklistgenerator->get_submitted_form_data($controller, $itemid, [
            'Group 1' => [
                'score' => $group1itemcheck,
                'remark' => $group1itemremark,
                'checked' => false,
            ],
            'Group 2' => [
                'score' => $group2itemcheck,
                'remark' => $group2itemremark,
                'checked' => true,
            ],
        ]);
    }
}
