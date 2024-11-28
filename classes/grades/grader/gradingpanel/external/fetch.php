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
 * Web services relating to fetching of a checklist for the grading panel.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace gradingform_checklist\grades\grader\gradingpanel\external;

global $CFG;

use coding_exception;
use context;
use core_grades\component_gradeitem as gradeitem;
use core_grades\component_gradeitems;
// Moodle patch INT-19461: Add missing core_external imports
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\util;
use stdClass;
use moodle_exception;
require_once($CFG->dirroot.'/grade/grading/form/checklist/lib.php');

/**
 * Web services relating to fetching of a checklist for the grading panel.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class fetch extends external_api {

    /**
     * Describes the parameters for fetching the grading panel for a simple grade.
     *
     * @return external_function_parameters
     * @since Moodle 3.8
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters ([
            'component' => new external_value(
                PARAM_ALPHANUMEXT,
                'The name of the component',
                VALUE_REQUIRED
            ),
            'contextid' => new external_value(
                PARAM_INT,
                'The ID of the context being graded',
                VALUE_REQUIRED
            ),
            'itemname' => new external_value(
                PARAM_ALPHANUM,
                'The grade item itemname being graded',
                VALUE_REQUIRED
            ),
            'gradeduserid' => new external_value(
                PARAM_INT,
                'The ID of the user show',
                VALUE_REQUIRED
            ),
        ]);
    }

    /**
     * Fetch the data required to build a grading panel for a simple grade.
     *
     * @param string $component
     * @param int $contextid
     * @param string $itemname
     * @param int $gradeduserid
     * @return array
     * @since Moodle 3.8
     */
    public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
        global $CFG, $USER;
        require_once("{$CFG->libdir}/gradelib.php");
        [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ] = self::validate_parameters(self::execute_parameters(), [
            'component' => $component,
            'contextid' => $contextid,
            'itemname' => $itemname,
            'gradeduserid' => $gradeduserid,
        ]);

        // Validate the context.
        $context = context::instance_by_id($contextid);
        self::validate_context($context);

        // Validate that the supplied itemname is a gradable item.
        if (!component_gradeitems::is_valid_itemname($component, $itemname)) {
            throw new coding_exception("The '{$itemname}' item is not valid for the '{$component}' component");
        }

        // Fetch the gradeitem instance.
        $gradeitem = gradeitem::instance($component, $context, $itemname);

        if (CHECKLIST !== $gradeitem->get_advanced_grading_method()) {
            throw new moodle_exception(
                "The {$itemname} item in {$component}/{$contextid} is not configured for advanced grading with a checklist"
            );
        }

        // Fetch the actual data.
        $gradeduser = \core_user::get_user($gradeduserid, '*', MUST_EXIST);

        // One can access its own grades. Others just if they're graders.
        $isgrading = false;
        if ($gradeduserid != $USER->id) {
            $gradeitem->require_user_can_grade($gradeduser, $USER);
            $isgrading = true;
        }

        return self::get_fetch_data($gradeitem, $gradeduser, $isgrading);
    }

    /**
     * Get the data to be fetched and create the structure ready for Mustache.
     *
     * @param gradeitem $gradeitem
     * @param stdClass $gradeduser
     * @return array
     */
    public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser, bool $isgrading = true): array {
        global $USER;
        // Set up all the controllers etc that we'll be needing.
        $hasgrade = $gradeitem->user_has_grade($gradeduser);
        $grade = $gradeitem->get_formatted_grade_for_user($gradeduser, $USER);
        $instance = $gradeitem->get_advanced_grading_instance($USER, $grade);

        if (!$instance) {
            throw new moodle_exception('error:gradingunavailable', 'grading');
        }

        $controller = $instance->get_controller();
        $definition = $controller->get_definition();
        $fillings = $instance->get_checklist_filling();
        $context = $controller->get_context();
        $definitionid = (int) $definition->id;

        $options = $controller->get_options();

        // Calculate when to show the elements depending on whether the user is grading or viewing their grades.
        $templateoptions = new stdClass();
        $templateoptions->showpoints = $controller->can_display_points($isgrading);
        $templateoptions->showgroupfeedback = $controller->can_display_group_feedback($isgrading);
        $templateoptions->showitemfeedback = $controller->can_display_item_feedback($isgrading);

        // Set up some items we need to return on other interfaces.
        $gradegrade = \grade_grade::fetch(['itemid' => $gradeitem->get_grade_item()->id, 'userid' => $gradeduser->id]);
        $gradername = $gradegrade ? fullname(\core_user::get_user($gradegrade->usermodified)) : null;
        $maxgrade = max(array_keys($controller->get_grade_range()));

        $criterion = [];
        $maxpoints = 0;
        $points = 0;
        if ($definition->checklist_groups) {
            // Iterate over the defined criterion in the checklist and map out what we need to render each item.
            $criterion = array_map(function ($criterion) use ($definitionid, $fillings, $context, $hasgrade, $templateoptions,
                &$maxpoints, &$points) {
                // The general structure we'll be returning, we still need to get the remark (if any) and the levels associated.
                $maxgrouppoints = 0;
                $grouppoints = 0;
                $result = [
                    'id' => $criterion['id'],
                    'description' => self::get_formatted_text(
                        $context,
                        $definitionid,
                        'description',
                        $criterion['description'],
                        0
                    ),
                    'maxgrouppoints' => 0,
                    'grouppoints' => 0,
                ];

                $result['items'] = array_map(function ($items) use ($criterion, $fillings, $context, $definitionid,
                    $templateoptions, &$maxgrouppoints, &$grouppoints) {
                    $result = [
                        'id' => $items['id'],
                        'criterionid' => $criterion['id'],
                        'score' => $items['score'],
                        'definition' => self::get_formatted_text(
                            $context,
                            $definitionid,
                            'definition',
                            $items['definition'],
                            0
                        ),
                        'checked' => null,
                    ];

                    $filling = [];
                    if (!empty($fillings['groups'][$criterion['id']]['items'][$items['id']])) {
                        $filling = $fillings['groups'][$criterion['id']]['items'][$items['id']];
                        if ($templateoptions->showitemfeedback) {
                            $result['remark'] = self::get_formatted_text($context,
                                $definitionid,
                                'remark',
                                $filling['remark'],
                                (int) $filling['remarkformat']
                            );
                        }
                    }

                    // Consult the grade filling to see if an item has been selected and if it is the current item.
                    if (array_key_exists('itemid', $filling) && $filling['itemid'] == $items['id']) {
                        $result['checked'] = $filling['checked'];
                    }

                    // Check if the item score is equal to 1 and increment the corresponding count.
                    if ($result['checked']) {
                        $grouppoints += $items['score'];
                    }
                    $maxgrouppoints += $items['score'];
                    return $result;
                }, $criterion['items']);
                if ($templateoptions->showgroupfeedback && !empty($fillings['groups'][$criterion['id']]['items'][0])){
                    $groupfeedbackfill = $fillings['groups'][$criterion['id']]['items'][0];
                    $result['groupfeedback'] = self::get_formatted_text($context, $definitionid, 'remark',
                        $groupfeedbackfill['remark'], (int) $groupfeedbackfill['remarkformat']);
                }
                // Add the item counts to the criterion structure.
                if ($templateoptions->showpoints) {
                    $result['maxgrouppoints'] = $maxgrouppoints;
                    $result['grouppoints'] = $grouppoints;
                }

                if ($templateoptions->showpoints) {
                    $maxpoints += $maxgrouppoints;
                    $points += $grouppoints;
                }

                return $result;
            }, $definition->checklist_groups);
        }

        return [
            'templatename' => 'gradingform_checklist/grades/grader/gradingpanel',
            'hasgrade' => $hasgrade,
            'grade' => [
                'instanceid' => $instance->get_id(),
                'options' => $templateoptions,
                'criteria' => $criterion,
                'maxpoints' => $maxpoints,
                'points' => $points,
                'usergrade' => $grade->usergrade,
                'maxgrade' => $maxgrade,
                'gradedby' => $gradername,
                'timecreated' => $grade->timecreated,
                'timemodified' => $grade->timemodified,
            ],
            'warnings' => [],
        ];
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_single_structure
     * @since Moodle 3.8
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'templatename' => new external_value(PARAM_SAFEPATH, 'The template to use when rendering this data'),
            'hasgrade' => new external_value(PARAM_BOOL, 'Does the user have a grade?'),
            'grade' => new external_single_structure([
                'instanceid' => new external_value(PARAM_INT, 'The id of the current grading instance'),
                'options' => new external_single_structure([
                    'showpoints' => new external_value(PARAM_BOOL, 'The points should be displayed'),
                    'showgroupfeedback' => new external_value(PARAM_BOOL, 'The group feedback should be displayed'),
                    'showitemfeedback' => new external_value(PARAM_BOOL, 'The item feedback should be displayed'),
                ]),
                'criteria' => new external_multiple_structure(
                    new external_single_structure([
                        'id' => new external_value(PARAM_INT, 'ID of the Criteria'),
                        'description' => new external_value(PARAM_RAW, 'Description of the Criteria'),
                        'groupfeedback' => new external_value(PARAM_RAW, 'Group feedback', VALUE_OPTIONAL),
                        'maxgrouppoints' => new external_value(PARAM_INT, 'Maximum number of criterion points', VALUE_OPTIONAL),
                        'grouppoints' => new external_value(PARAM_INT, 'Criterion points', VALUE_OPTIONAL),
                        'items' => new external_multiple_structure(new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'ID of item'),
                            'criterionid' => new external_value(PARAM_INT, 'ID of the criterion this matches to'),
                            'score' => new external_value(PARAM_RAW, 'What this item is worth'),
                            'definition' => new external_value(PARAM_RAW, 'Definition of the item'),
                            'checked' => new external_value(PARAM_BOOL, 'Selected flag'),
                            'remark' => new external_value(PARAM_RAW, 'Any remarks for this criterion for the user being assessed', VALUE_OPTIONAL),
                        ])),
                    ])
                ),
                'maxpoints' => new external_value(PARAM_INT, 'Maximum number of points for all criteria', VALUE_OPTIONAL),
                'points' => new external_value(PARAM_INT, 'Points obtained for all criteria', VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT, 'The time that the grade was created'),
                'usergrade' => new external_value(PARAM_RAW, 'Current user grade'),
                'maxgrade' => new external_value(PARAM_RAW, 'Max possible grade'),
                'gradedby' => new external_value(PARAM_RAW, 'The assumed grader of this grading instance'),
                'timemodified' => new external_value(PARAM_INT, 'The time that the grade was last updated'),
            ]),
            'warnings' => new external_warnings(),
        ]);
    }

    /**
     * Get a formatted version of the remark/description/etc.
     *
     * @param context $context
     * @param int $definitionid
     * @param string $filearea The file area of the field
     * @param string $text The text to be formatted
     * @param int $format The input format of the string
     * @return string
     */
    protected static function get_formatted_text(context $context, int $definitionid, string $filearea, string $text, int $format): string {
        $formatoptions = [
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
        ];
        [$newtext, ] = util::format_text($text, $format, $context, 'grading', $filearea, $definitionid, $formatoptions);
        return $newtext;
    }
}
