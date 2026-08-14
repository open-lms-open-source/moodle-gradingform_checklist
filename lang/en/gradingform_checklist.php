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
 * @package    gradingform_checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['addgroup'] = 'Add group';
$string['addbenchmark'] = '+ Add benchmarks';
$string['benchmark'] = 'Benchmark';
$string['benchmarkbuttondefault'] = 'Open to view Benchmarks';
$string['benchmarkbuttonicon'] = 'Benchmark button icon';
$string['benchmarkbuttonlabel'] = 'Benchmark button label';
$string['closebenchmark'] = 'Close benchmark';
$string['editbenchmark'] = 'Edit benchmark';
$string['removebenchmark'] = 'Remove benchmarks';
$string['showbenchmark'] = 'Show benchmark';
$string['usebenchmark'] = 'Use benchmarks';
$string['privacy:metadata:benchmark'] = 'Teacher-only benchmark guidance attached to a checklist definition.';
$string['privacy:metadata:benchmarkformat'] = 'The text format of the checklist benchmark.';
$string['privacy:metadata:buttonicon'] = 'The Font Awesome icon class configured for the benchmark button.';
$string['privacy:metadata:buttonlabel'] = 'The label configured for the benchmark button.';
$string['privacy:metadata:observationdate'] = 'The date or date and time when the checklist observation was performed.';
$string['privacy:metadata:observationmode'] = 'Whether the observation was saved as a date-only value or a date-time value.';
$string['privacy:metadata:observationsummary'] = 'Stores observation dates attached to checklist grading instances.';
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
$string['downloadjsonexample'] = 'Download JSON example';
$string['downloadjsonschema'] = 'Download JSON schema';
$string['downloadwordtemplate'] = 'Download Word template';
$string['enablebulkcheck'] = 'Allow grader to select or unselect all checklist items';
$string['err_definitionmax'] = 'Item definition cannot be more than {$a} characters';
$string['err_descriptionmax'] = 'Group description cannot be more than {$a} characters';
$string['err_nodefinition'] = 'Item definition cannot be empty';
$string['err_nodescription'] = 'Group description cannot be empty';
$string['err_nogroups'] = 'Checklist must contain at least one group';
$string['err_minoneitems'] = 'Each group must contain at least one checklist item.';
$string['err_observationdate'] = 'Enter the observation date.';
$string['err_requireatleastonegroupcomment'] = 'Add at least one group comment. First checked group: "{$a->group}".';
$string['err_requireatleastoneitemcomment'] = 'Add at least one item comment. First checked item: "{$a->item}" in "{$a->group}".';
$string['err_requiregroupcommentschecked'] = 'Add a group comment for "{$a->group}".';
$string['err_requireitemcommentschecked'] = 'Add a comment for "{$a->item}" in "{$a->group}".';
$string['err_scoreformat'] = 'Enter a valid non-negative number for each item. Use a period (.) for decimals.';
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
$string['groupremarkheading'] = 'Criteria comment title';
$string['groupremarkheadingdefault'] = 'Criteria Comment';
$string['importactiveinstances'] = 'This checklist has already been used for grading. Importing will save the replacement as ready and mark existing grades for review.';
$string['importchecklist'] = 'Import checklist file';
$string['importconfirmreplace'] = 'Replace current checklist';
$string['importerrorbenchmarkenabled'] = 'Benchmark enabled must be yes, no, true, or false.';
$string['importerrorfiletype'] = 'Upload a .docx or .json file.';
$string['importerrordocxdocument'] = 'The Word document could not be read as a checklist import template.';
$string['importerrordocxopen'] = 'The Word document could not be opened.';
$string['importerrorformat'] = 'The import format is not supported.';
$string['importerrorgroupdescription'] = 'Group {$a} has items but no group description.';
$string['importerrorgroupitems'] = 'Group {$a} has a description but no valid items.';
$string['importerrorinvalidjson'] = 'The JSON file could not be decoded.';
$string['importerrorinvalidsettingvalue'] = 'The value for setting "{$a}" is not valid.';
$string['importerroritemdefinition'] = 'Item {$a} has points but no item text.';
$string['importerroritemscore'] = 'Item {$a} has an invalid points value.';
$string['importerrorname'] = 'Checklist name is required.';
$string['importerrorobservationdefault'] = 'Default observation date must be now or blank.';
$string['importerrorobservationmode'] = 'Observation date selector must be disabled, date, or datetime.';
$string['importerrorunexpected'] = 'The checklist import could not be completed. Check the file and try again.';
$string['importerrorversion'] = 'The import version is not supported.';
$string['importfile'] = 'Import file';
$string['importgroups'] = 'Groups';
$string['importitems'] = 'Items';
$string['importpreview'] = 'Preview import';
$string['importpreviewheading'] = 'Import preview';
$string['importsuccess'] = 'Checklist imported.';
$string['importwarningemptybenchmark'] = 'Benchmark guidance was enabled but empty, so benchmarks were not imported.';
$string['importwarninginvalidbenchmarkfile'] = 'A benchmark file entry could not be imported.';
$string['importwarninginvalidgroup'] = 'Group {$a} is not valid and was ignored.';
$string['importwarninginvaliditem'] = 'Item {$a} is not valid and was ignored.';
$string['importwarningjsonbenchmarkfiles'] = 'Benchmark files in JSON are not supported in import version 1 and were ignored.';
$string['importwarningorphanitems'] = 'An items table appeared before a group table and was ignored.';
$string['importwarningrichgroup'] = 'Images, nested tables, or rich content in group cells were stripped.';
$string['importwarningrichitems'] = 'Images, nested tables, or rich content in item cells were stripped.';
$string['importwarningunknownsetting'] = 'Setting "{$a}" is not supported and was ignored.';
$string['itemdefinition'] = 'Item definition';
$string['itemdelete'] = 'Delete item';
$string['itemempty'] = 'Click to edit item';
$string['itemfeedback'] = 'Feedback for "{$a}"';
$string['itemmovedown'] = 'Move item down';
$string['itemmoveup'] = 'Move item up';
$string['itemremark'] = 'Item remark for "{$a}"';
$string['itemscore'] = 'Item score';
$string['name'] = 'Name';
$string['needregrademessage'] = 'The checklist definition was changed after this student had been graded. The student cannot see this checklist until you review the checklist and update the grade.';
$string['optionsectiongroups'] = 'Groups';
$string['optionsectionitems'] = 'Items';
$string['optionsectionobservation'] = 'Checklist date selector';
$string['observationdate'] = 'Observation date';
$string['observationdefault'] = 'Default observation date';
$string['observationdefaultblank'] = 'Leave blank';
$string['observationdefaultnow'] = 'Auto-populate with current date/time';
$string['observationdatenotrecorded'] = 'No observation date recorded';
$string['observationmode'] = 'Observation date selector';
$string['observationmodedate'] = 'Date only';
$string['observationmodedatetime'] = 'Date and time';
$string['observationmodedisabled'] = 'Disabled';
$string['observationtime'] = 'Observation time';
$string['pluginname'] = 'Checklist';
$string['previewchecklist'] = 'Preview checklist';
$string['overallpoints'] = 'Overall points';
$string['regrademessage1'] = 'You are about to save changes to a checklist that has already been used for grading. Please
indicate if existing grades need to be reviewed. If you set this then the checklist will be hidden from students until their items are regraded.';
$string['regrademessage5'] = 'You are about to save significant changes to a checklist that has already been used for grading. The gradebook value will be unchanged, but the checklist will be hidden from students until their items are regraded.';
$string['regradeoption0'] = 'Do not mark for regrade';
$string['regradeoption1'] = 'Mark for regrade';
$string['requireatleastonegroupcomment'] = 'Require at least one group comment';
$string['requireatleastoneitemcomment'] = 'Require at least one item comment';
$string['requiregroupcommentschecked'] = 'Require group comments for groups with any checked item';
$string['requireitemcommentschecked'] = 'Require item comments for checked items';
$string['requiredcommentserror'] = '{$a}';
$string['restoredfromdraft'] = 'NOTE: The last attempt to grade this person was not saved properly so draft grades have been restored. If you want to cancel these changes, use the \'Cancel\' button below.';
$string['save'] = 'Save';
$string['savechecklist'] = 'Save checklist and make it ready';
$string['savechecklistdraft'] = 'Save as draft';
$string['scorepostfix'] = '{$a} points';
$string['showitempointseval'] = 'Display points for each item during evaluation';
$string['showitempointstudent'] = 'Display points for each item to those being graded';
$string['showgrouppointseval'] = 'Display group and overall points during evaluation';
$string['showgrouppointstudent'] = 'Display group and overall points to those being graded';
$string['enableitemremarks'] = 'Allow grader to add text remarks for each checklist item';
$string['enablegroupremarks'] = 'Allow grader to add text remarks for each checklist group';
$string['showremarksstudent'] = 'Show all remarks to those being graded';
$string['tickall'] = 'Select All';
$string['unchecked'] = 'Unchecked';
$string['untickall'] = 'Unselect All';
$string['maxlengthalert'] = 'This input field has a maximum length of {$a} characters';
$string['privacy:metadata:checked'] = 'Whether the checklist item was selected during grading.';
$string['privacy:metadata:benchmarksummary'] = 'Stores teacher-only benchmark guidance attached to checklist definitions.';
$string['privacy:metadata:definitionid'] = 'The grading definition identifier for the checklist benchmark.';
$string['privacy:metadata:fillingssummary'] = 'Stores checklist grading selections and remarks for an advanced grading instance.';
$string['privacy:metadata:groupid'] = 'An identifier for a checklist group being graded.';
$string['privacy:metadata:instanceid'] = 'An identifier relating to a grade in an activity.';
$string['privacy:metadata:itemid'] = 'An identifier for a checklist item being graded.';
$string['privacy:metadata:remark'] = 'Remarks related to the checklist item or group being assessed.';
$string['privacy:metadata:remarkformat'] = 'The text format of the checklist remark.';
$string['benchmarkdisabled'] = 'Benchmarks are disabled by the site administrator.';
$string['importdisabled'] = 'Checklist importing is disabled by the site administrator.';
$string['featuredisabled'] = 'This Checklist feature is disabled by the site administrator.';

// Site administration settings.
$string['adminlimitsheading'] = 'Checklist limits';
$string['adminlimitsdescription'] = 'These limits apply when a checklist is created or edited. Existing definitions are not changed, truncated, or rewritten.';
$string['admingroupdescriptionmaxchars'] = 'Maximum group description length';
$string['admingroupdescriptionmaxchars_desc'] = 'Maximum number of characters allowed in a group description. This affects new checklists and future edits only; existing definitions are not rewritten.';
$string['adminitemdefinitionmaxchars'] = 'Maximum item definition length';
$string['adminitemdefinitionmaxchars_desc'] = 'Maximum number of characters allowed in a checklist item definition. This affects new checklists and future edits only; existing definitions are not rewritten.';
$string['adminfeaturesheading'] = 'Feature availability';
$string['adminfeaturesdescription'] = 'All features are enabled by default. Leave a box checked to allow that feature. Clear a box to hide and block that feature. Existing checklist definitions and imported content remain available.';
$string['adminenablewordimport'] = 'Allow Word import';
$string['adminenablewordimport_desc'] = 'If enabled, teachers can import checklist definitions from Word documents. If disabled, Word import is hidden and blocked.';
$string['adminenablejsonimport'] = 'Allow JSON file import';
$string['adminenablejsonimport_desc'] = 'If enabled, teachers can import checklist definitions from JSON files. If disabled, JSON file import is hidden and blocked.';
$string['adminenablejsonwebservice'] = 'Allow JSON web service import';
$string['adminenablejsonwebservice_desc'] = 'If enabled, the checklist JSON import web service function can be used. If disabled, requests are rejected.';
$string['adminenablewordtemplate'] = 'Allow Word template download';
$string['adminenablewordtemplate_desc'] = 'If enabled, users can access the downloadable Word authoring template. If disabled, the template is hidden and blocked.';
$string['adminenablejsonexample'] = 'Allow JSON example download';
$string['adminenablejsonexample_desc'] = 'If enabled, users can access the downloadable JSON example. If disabled, the example is hidden and blocked.';
$string['adminenablejsonschema'] = 'Allow JSON schema download';
$string['adminenablejsonschema_desc'] = 'If enabled, users can access the downloadable JSON schema. If disabled, the schema is hidden and blocked.';
$string['adminenablebenchmarks'] = 'Allow benchmark guidance';
$string['adminenablebenchmarks_desc'] = 'If enabled, teachers can add benchmark guidance to new or edited checklist definitions. If disabled, new benchmark guidance cannot be added. Existing benchmarks remain available.';
$string['admindefaultsheading'] = 'Defaults for new checklists';
$string['admindefaultsdescription'] = 'These values are copied into new checklist definitions only. Teachers can change them for individual checklists. Existing definitions keep their stored options.';
$string['admindefault_desc'] = 'Default value for newly created checklist definitions. Teachers can change this setting for an individual checklist.';
$string['adminalwaysshowdefinition'] = 'Allow checklist preview before grading';
$string['adminshowitempointseval'] = 'Show item points during grading';
$string['adminshowitempointstudent'] = 'Show item points to students';
$string['adminshowgrouppointseval'] = 'Show group and total points during grading';
$string['adminshowgrouppointstudent'] = 'Show group and total points to students';
$string['adminenableitemremarks'] = 'Allow comments for checklist items';
$string['adminenablegroupremarks'] = 'Allow comments for checklist groups';
$string['adminshowremarksstudent'] = 'Show comments to students';
$string['adminenablebulkcheck'] = 'Allow bulk selection of checklist items';
$string['adminrequireitemcommentschecked'] = 'Require comments for checked items';
$string['adminrequireatleastoneitemcomment'] = 'Require at least one item comment';
$string['adminrequiregroupcommentschecked'] = 'Require comments for groups with checked items';
$string['adminrequireatleastonegroupcomment'] = 'Require at least one group comment';
