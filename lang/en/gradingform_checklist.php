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
 * Language file for the Checklist plugin
 *
 * @package    gradingform
 * @subpackage checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Add group';
$string['alwaysshowdefinition'] = 'Allow users to preview checklist used in the module (otherwise checklist will only become visible after grading)';
$string['backtoediting'] = 'Back to editing';
$string['checked'] = 'Checked';
$string['checkitem'] = 'Mark full credit for "{$a}"';
$string['checklist'] = 'Checklist';
$string['checklistmapping'] = 'Score to grade mapping rules';
$string['checklistmappingexplained'] = 'The minimum possible score for this checklist is <b>{$a->minscore} points</b> and it will be converted to the minimum grade available in this module (which is zero unless the scale is used).
    The maximum score <b>{$a->maxscore} points</b> will be converted to the maximum grade.<br />
    Intermediate scores will be converted respectively and rounded to the nearest available grade.<br />
    If a scale is used instead of a grade, the score will be converted to the scale elements as if they were consecutive integers.';
$string['checklistoptions'] = 'Checklist options';
$string['checkliststatus'] = 'Current checklist status';
$string['confirmdeletegroup'] = 'Are you sure you want to delete this group?';
$string['confirmdeleteitem'] = 'Are you sure you want to delete this item?';
$string['definechecklist'] = 'Define checklist';
$string['description'] = 'Description';
$string['err_definitionmax'] = 'Item definition can not be more than 255 characters';
$string['err_descriptionmax'] = 'Group description can not be more than 255 characters';
$string['err_nodefinition'] = 'Item definition can not be empty';
$string['err_nodescription'] = 'Group description can not be empty';
$string['err_nogroups'] = 'Checklist must contain at least one group';
$string['err_scoreformat'] = 'Number of points for each item must be a valid non-negative number';
$string['err_scoremax'] = 'Number of points for each item must not be greater than 1000';
$string['err_totalscore'] = 'Maximum number of points possible when graded by the checklist must be more than zero';
$string['groupfeedback'] = 'Group feedback for "{$a}"';
$string['gradingof'] = '{$a} grading';
$string['groupadditem'] = 'Add item';
$string['groupdelete'] = 'Delete group';
$string['groupdescription'] = 'Group description';
$string['groupempty'] = 'Click to edit group';
$string['groupmovedown'] = 'Move down';
$string['groupmoveup'] = 'Move up';
$string['grouppoints'] = 'Group points';
$string['groupremark'] = 'Group remark for "{$a}"';
$string['itemdefinition'] = 'Item definition';
$string['itemdelete'] = 'Delete item';
$string['itemempty'] = 'Click to edit item';
$string['itemfeedback'] = 'Feedback for "{$a}"';
$string['itemremark'] = 'Item remark for "{$a}"';
$string['itemscore'] = 'Item score';
$string['name'] = 'Name';
$string['needregrademessage'] = 'The checklist definition was changed after this student had been graded. The student can not see this checklist until you review the checklist and update the grade.';
$string['pluginname'] = 'Checklist';
$string['previewchecklist'] = 'Preview checklist';
$string['overallpoints'] = 'Overall points';
$string['regrademessage1'] = 'You are about to save changes to a checklist that has already been used for grading. Please
indicate if existing grades need to be reviewed. If you set this then the checklist will be hidden from students until their items are regraded.';
$string['regrademessage5'] = 'You are about to save significant changes to a checklist that has already been used for grading. The gradebook value will be unchanged, but the checklist will be hidden from students until their items are regraded.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';
$string['restoredfromdraft'] = 'NOTE: The last attempt to grade this person was not saved properly so draft grades have been restored. If you want to cancel these changes, use the \'Cancel\' button below.';
$string['save'] = 'Save';
$string['savechecklist'] = 'Save checklist and make it ready';
$string['savechecklistdraft'] = 'Save as draft';
$string['scorepostfix'] = '{$a} points';
$string['showitempointseval'] = 'Display points for each item during evaluation';
$string['showitempointstudent'] = 'Display points for each item to those being graded';
$string['enableitemremarks'] = 'Allow grader to add text remarks for each checklist item';
$string['enablegroupremarks'] = 'Allow grader to add text remarks for each checklist group';
$string['showremarksstudent'] = 'Show all remarks to those being graded';
$string['unchecked'] = 'Unchecked';
$string['maxlengthalert'] = 'This input field has a maximum length of {$a} characters';
