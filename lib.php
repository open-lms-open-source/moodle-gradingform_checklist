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
 * Grading method controller for the Checklist plugin
 *
 * @package    gradingform_checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/checklisteditor.php');
require_once($CFG->dirroot.'/grade/grading/form/lib.php');

/** checklist: Used to compare our gradeitem_type against. */
const CHECKLIST = 'checklist';


/**
 * This controller encapsulates the checklist grading logic
 */
class gradingform_checklist_controller extends gradingform_controller {

    // Modes of displaying the checklist (used in gradingform_checklist_renderer)
    /** checklist display mode: For editing (moderator or teacher creates a checklist) */
    const DISPLAY_EDIT_FULL     = 1;
    /** checklist display mode: Preview the checklist design with hidden fields */
    const DISPLAY_EDIT_FROZEN   = 2;
    /** checklist display mode: Preview the checklist design (for person with manage permission) */
    const DISPLAY_PREVIEW       = 3;
    /** checklist display mode: Preview the checklist (for people being graded) */
    const DISPLAY_PREVIEW_GRADED= 8;
    /** checklist display mode: For evaluation, enabled (teacher grades a student) */
    const DISPLAY_EVAL          = 4;
    /** checklist display mode: For evaluation, with hidden fields */
    const DISPLAY_EVAL_FROZEN   = 5;
    /** checklist display mode: Teacher reviews filled checklist */
    const DISPLAY_REVIEW        = 6;
    /** checklist display mode: Display filled checklist (i.e. students see their grades) */
    const DISPLAY_VIEW          = 7;

    /** Observation date selector disabled. */
    const OBSERVATION_MODE_DISABLED = 'disabled';
    /** Observation date selector stores only the observation date. */
    const OBSERVATION_MODE_DATE = 'date';
    /** Observation date selector stores the observation date and time. */
    const OBSERVATION_MODE_DATETIME = 'datetime';
    /** Observation date is pre-filled with the current time for new grading instances. */
    const OBSERVATION_DEFAULT_NOW = 'now';
    /** Observation date starts blank for new grading instances. */
    const OBSERVATION_DEFAULT_BLANK = 'blank';

    /**
     * Returns the checklist plugin renderer
     *
     * @param moodle_page $page the target page
     * @return gradingform_checklist_renderer
     */
    public function get_renderer(moodle_page $page) {
        return $page->get_renderer('gradingform_'. $this->get_method_name());
    }

    /**
     * Returns the HTML code displaying the preview of the checklist grading form
     *
     * @throws \core\exception\coding_exception
     * @param moodle_page $page the target page
     * @return string
     */
    public function render_preview(moodle_page $page) {
        if (!$this->is_form_defined()) {
            throw new \core\exception\coding_exception('It is the caller\'s responsibility to make sure that the form is actually defined');
        }

        $output = $this->get_renderer($page);
        $groups = $this->definition->checklist_groups;
        $options = $this->get_options();
        $checklist = '';
        if (has_capability('moodle/grade:managegradingforms', $page->context)) {
            $checklist .= $output->display_checklist_mapping_explained($this->get_min_max_score());
            $checklist .= $output->display_checklist($groups, $options, self::DISPLAY_PREVIEW, 'checklist');
        } else if (!empty($options['alwaysshowdefinition'])) {
            $checklist .= $output->display_checklist($groups, $options, self::DISPLAY_PREVIEW_GRADED, 'checklist');
        }

        return $checklist;
    }

    /**
     * Returns a message and checklist import actions while the form is unavailable.
     *
     * @return string|null
     */
    public function form_unavailable_notification() {
        return $this->render_import_actions();
    }

    /**
     * Renders checklist import and template download actions.
     *
     * @return string
     */
    public function render_import_actions(): string {
        global $PAGE;

        $areaid = $this->get_areaid();
        $importid = 'gradingform-checklist-import-action-' . $areaid;
        $importurl = new \moodle_url('/grade/grading/form/checklist/import.php', ['areaid' => $areaid]);
        $downloadlinks = [
            \html_writer::link(new \moodle_url('/grade/grading/form/checklist/template.php', [
                'areaid' => $areaid,
                'sesskey' => sesskey(),
            ]), get_string('downloadwordtemplate', 'gradingform_checklist'), ['class' => 'btn btn-secondary']),
            \html_writer::link(new \moodle_url('/grade/grading/form/checklist/jsonexample.php', [
                'areaid' => $areaid,
                'sesskey' => sesskey(),
            ]), get_string('downloadjsonexample', 'gradingform_checklist'), ['class' => 'btn btn-secondary']),
            \html_writer::link(new \moodle_url('/grade/grading/form/checklist/jsonschema.php', [
                'areaid' => $areaid,
                'sesskey' => sesskey(),
            ]), get_string('downloadjsonschema', 'gradingform_checklist'), ['class' => 'btn btn-secondary']),
        ];

        // fa-upload is available in both Moodle versions supported by the plugin.
        $importicon = \html_writer::tag('span', '', ['class' => 'fa fa-upload', 'aria-hidden' => 'true']);
        $importtext = \html_writer::tag('span', get_string('importchecklist', 'gradingform_checklist'), [
            'class' => 'action-text',
        ]);
        $importaction = \html_writer::link($importurl, $importicon . $importtext, [
            'class' => 'action btn btn-lg',
            'id' => $importid,
        ]);

        $movescript = "
(function() {
    var attempts = 0;
    var findActions = function(notification) {
        var actions = notification ? notification.previousElementSibling : null;
        if (actions && actions.classList.contains('actions') && actions.querySelector('a.action.btn.btn-lg')) {
            return actions;
        }
        var actionRows = document.querySelectorAll('#region-main .actions');
        for (var i = 0; i < actionRows.length; i++) {
            if (actionRows[i].querySelector('a.action.btn.btn-lg')) {
                return actionRows[i];
            }
        }
        return null;
    };
    var moveImportAction = function() {
        attempts++;
        var importAction = document.getElementById(" . json_encode($importid) . ");
        if (!importAction) {
            if (attempts < 40) {
                window.setTimeout(moveImportAction, 50);
            }
            return;
        }
        var importBlock = importAction.closest('.gradingform-checklist-import-actions');
        var notification = importBlock ? importBlock.closest('.alert') : null;
        var actions = findActions(notification);
        if (!actions) {
            if (attempts < 40) {
                window.setTimeout(moveImportAction, 50);
            }
            return;
        }
        actions.classList.add('gradingform-checklist-action-row');
        var firstAction = actions.querySelector('a.action.btn.btn-lg:not(#' + importAction.id + ')');
        if (firstAction && firstAction.nextElementSibling !== importAction) {
            actions.insertBefore(importAction, firstAction.nextElementSibling);
        } else if (!actions.contains(importAction)) {
            actions.appendChild(importAction);
        }
        var primary = importBlock ? importBlock.querySelector('.gradingform-checklist-import-primary') : null;
        if (primary && !primary.querySelector('a')) {
            primary.remove();
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', moveImportAction);
    } else {
        moveImportAction();
    }
})();
";

        $PAGE->requires->js_init_code($movescript);

        $actions = \html_writer::div($importaction, 'gradingform-checklist-import-primary');
        $actions .= \html_writer::div(implode(' ', $downloadlinks), 'gradingform-checklist-import-downloads');
        return \html_writer::div($actions, 'gradingform-checklist-import-actions');
    }

    /**
     * Deletes the checklist definition and all the associated information
     */
    protected function delete_plugin_definition() {
        global $DB;

        // get the list of instances
        $instances = array_keys($DB->get_records('grading_instances', array('definitionid' => $this->definition->id), '', 'id'));
        // delete all fillings
        $DB->delete_records_list('gradingform_checklist_fills', 'instanceid', $instances);
        $DB->delete_records_list('gradingform_checklist_obs', 'instanceid', $instances);
        // delete instances
        $DB->delete_records_list('grading_instances', 'id', $instances);
        $this->delete_definition_benchmark_files();
        $DB->delete_records('gradingform_checklist_bench', ['definitionid' => $this->definition->id]);

        // get the list of groups records
        $groups = array_keys($DB->get_records('gradingform_checklist_groups', array('definitionid' => $this->definition->id), '', 'id'));
        // delete checklist items items
        $DB->delete_records_list('gradingform_checklist_items', 'groupid', $groups);
        // delete groups
        $DB->delete_records_list('gradingform_checklist_groups', 'id', $groups);
    }

    /**
     * If instanceid is specified and grading instance exists and it is created by this rater for
     * this item, this instance is returned.
     * If there exists a draft for this raterid+itemid, take this draft (this is the change from parent)
     * Otherwise new instance is created for the specified rater and itemid
     *
     * @param int $instanceid
     * @param int $raterid
     * @param int $itemid
     * @return gradingform_instance
     */
    public function get_or_create_instance($instanceid, $raterid, $itemid) {
        global $DB;
        if ($instanceid &&
            $instance = $DB->get_record('grading_instances', array('id'  => $instanceid, 'raterid' => $raterid, 'itemid' => $itemid), '*', IGNORE_MISSING)) {
            return $this->get_instance($instance);
        }
        if ($itemid && $raterid) {
            if ($rs = $DB->get_records('grading_instances', array('definitionid' => $this->definition->id, 'raterid' => $raterid, 'itemid' => $itemid), 'timemodified DESC', '*', 0, 1)) {
                $record = reset($rs);
                $currentinstance = $this->get_current_instance($raterid, $itemid);
                if ($record->status == gradingform_checklist_instance::INSTANCE_STATUS_INCOMPLETE &&
                    (!$currentinstance || $record->timemodified > $currentinstance->get_data('timemodified'))) {
                    $record->isrestored = true;
                    return $this->get_instance($record);
                }
            }
        }
        return $this->create_instance($raterid, $itemid);
    }

    /**
     * Extends the module settings navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING, the user has the permission moodle/grade:managegradingforms
     * and there is an area with the active grading method set to the given plugin.
     *
     * @param settings_navigation $settingsnav {@link settings_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_settings_navigation(settings_navigation $settingsnav, navigation_node $node=null) {
        $node->add(get_string('definechecklist', 'gradingform_checklist'),
            $this->get_editor_url(), settings_navigation::TYPE_CUSTOM,
            null, null, new \core\output\pix_icon('icon', '', 'gradingform_checklist'));
    }

    /**
     * Extends the module navigation
     *
     * This function is called when the context for the page is an activity module with the
     * FEATURE_ADVANCED_GRADING and there is an area with the active grading method set to the given plugin.
     *
     * @param global_navigation $navigation {@link global_navigation}
     * @param navigation_node $node {@link navigation_node}
     */
    public function extend_navigation(global_navigation $navigation, navigation_node $node=null) {
        if (has_capability('moodle/grade:managegradingforms', $this->get_context())) {
            // no need for preview if user can manage forms, he will have link to manage.php in settings instead
            return;
        }
        if ($this->is_form_defined() && ($options = $this->get_options()) && !empty($options['alwaysshowdefinition'])) {
            $node->add(get_string('gradingof', 'gradingform_checklist', get_grading_manager($this->get_areaid())->get_area_title()),
                new \core\url('/grade/grading/form/'.$this->get_method_name().'/preview.php', array('areaid' => $this->get_areaid())),
                settings_navigation::TYPE_CUSTOM);
        }
    }

    /**
     * Saves the checklist definition into the database
     *
     * @see parent::update_definition()
     * @param stdClass $newdefinition checklist definition data as coming from gradingform_checklist_editchecklist::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     */
    public function update_definition(stdClass $newdefinition, $usermodified = null) {
        $this->update_or_check_checklist($newdefinition, $usermodified, true);
        if (isset($newdefinition->checklist['regrade']) && $newdefinition->checklist['regrade']) {
            $this->mark_for_regrade();
        }
    }

    /**
     * Imports a normalised checklist definition.
     *
     * @param array $data canonical import data
     * @param int $status target grading definition status
     * @param bool $markforregrade whether existing grading instances should be marked for review
     * @param int|null $usermodified optional userid of the author of the definition
     */
    public function import_definition_from_data(
        array $data,
        int $status = self::DEFINITION_STATUS_DRAFT,
        bool $markforregrade = false,
        ?int $usermodified = null
    ): void {
        $newdefinition = new stdClass();
        $newdefinition->areaid = $this->areaid;
        $newdefinition->name = $data['name'] ?? '';
        $newdefinition->description_editor = [
            'text' => $data['description'] ?? '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
        $newdefinition->status = $status;
        if ($status == self::DEFINITION_STATUS_READY) {
            $newdefinition->savechecklist = 1;
        } else {
            $newdefinition->savechecklistdraft = 1;
        }

        $newdefinition->checklist = [
            'groups' => [],
            'options' => $data['settings'] ?? self::get_default_options(),
        ];
        if ($markforregrade) {
            $newdefinition->checklist['regrade'] = 1;
        }

        $groupid = 1;
        $itemid = 1;
        foreach ($data['groups'] ?? [] as $group) {
            $newitems = [];
            foreach ($group['items'] ?? [] as $item) {
                $newitems['NEWID' . $itemid] = [
                    'definition' => $item['definition'] ?? '',
                    'score' => $item['score'] ?? 0,
                    'sortorder' => $itemid,
                ];
                $itemid++;
            }
            $newdefinition->checklist['groups']['NEWID' . $groupid] = [
                'description' => $group['description'] ?? '',
                'sortorder' => $groupid,
                'items' => $newitems,
            ];
            $groupid++;
        }

        $benchmark = $data['benchmark'] ?? [];
        $enabled = !empty($benchmark['enabled']);
        $benchmarkhtml = $benchmark['html'] ?? '';
        $newdefinition->usebenchmark = $enabled ? 1 : 0;
        $newdefinition->removebenchmark = $enabled ? 0 : 1;
        $newdefinition->benchmarkbuttonlabel = $benchmark['buttonlabel'] ?? get_string('benchmarkbuttondefault', 'gradingform_checklist');
        $newdefinition->benchmarkbuttonicon = $benchmark['buttonicon'] ?? 'fa-solid fa-file-circle-check';
        $newdefinition->benchmark_editor = [
            'text' => $enabled ? $benchmarkhtml : '',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];

        $this->update_definition($newdefinition, $usermodified);
        if ($enabled) {
            $this->replace_imported_benchmark_files($benchmark['files'] ?? []);
        }
    }

    /**
     * Replaces benchmark files imported from DOCX canonical data.
     *
     * @param array $files benchmark files
     */
    protected function replace_imported_benchmark_files(array $files): void {
        $this->delete_definition_benchmark_files();
        if (empty($files)) {
            return;
        }
        $fs = get_file_storage();
        foreach ($files as $file) {
            $filename = clean_param($file['filename'] ?? '', PARAM_FILE);
            if ($filename === '') {
                continue;
            }
            if ($fs->file_exists($this->get_context()->id, 'gradingform_checklist', 'benchmark',
                    $this->definition->id, '/', $filename)) {
                continue;
            }
            $content = $file['content'] ?? '';
            if (($file['encoding'] ?? 'base64') === 'base64') {
                $content = base64_decode((string)$content, true);
                if ($content === false) {
                    continue;
                }
            }
            $fs->create_file_from_string([
                'contextid' => $this->get_context()->id,
                'component' => 'gradingform_checklist',
                'filearea' => 'benchmark',
                'itemid' => $this->definition->id,
                'filepath' => '/',
                'filename' => $filename,
            ], $content);
        }
    }

    /**
     * Either saves the checklist definition into the database or check if it has been changed.
     * Returns the level of changes:
     * 0 - no changes
     * 1 - only texts or groups sortorders are changed, students probably do not require re-grading
     * 2 - added items but maximum score on checklist is the same, students still may not require re-grading
     * 3 - removed groups or added items or changed number of points, students require re-grading but may be re-graded automatically
     * 4 - removed items - students require re-grading and not all students may be re-graded automatically
     * 5 - added groups - all students require manual re-grading
     *
     * @param stdClass $newdefinition checklist definition data as coming from gradingform_checklist_editchecklist::get_data()
     * @param int|null $usermodified optional userid of the author of the definition, defaults to the current user
     * @param boolean $doupdate if true actually updates DB, otherwise performs a check
     *
     * @return int
     */
    public function update_or_check_checklist(stdClass $newdefinition, $usermodified = null, $doupdate = false) {
        global $DB;

        // firstly update the common definition data in the {grading_definition} table
        if ($this->definition === false) {
            if (!$doupdate) {
                // if we create the new definition there is no such thing as re-grading anyway
                return 5;
            }
            // if definition does not exist yet, create a blank one
            // (we need id to save files embedded in description)
            parent::update_definition(new stdClass(), $usermodified);
            parent::load_definition();
        }
        if (!isset($newdefinition->checklist['options'])) {
            $newdefinition->checklist['options'] = self::get_default_options();
        }
        $newdefinition->checklist['options'] = self::normalise_comment_option_dependencies($newdefinition->checklist['options']);
        $newdefinition->options = json_encode($newdefinition->checklist['options']);
        $editoroptions = self::description_form_field_options($this->get_context());
        $newdefinition = file_postupdate_standard_editor($newdefinition, 'description', $editoroptions, $this->get_context(),
            'grading', 'description', $this->definition->id);

        // reload the definition from the database
        $currentdefinition = $this->get_definition(true);
        $haschanges = array();
        $currentbenchmark = $currentdefinition->benchmark ?? self::get_default_benchmark();
        $newbenchmark = $this->get_submitted_benchmark($newdefinition);
        if ($doupdate) {
            $newbenchmark = $this->save_definition_benchmark_files($newbenchmark);
        }
        if ($this->benchmark_has_changes($currentbenchmark, $newbenchmark)) {
            $haschanges[1] = true;
        }

        // update checklist data
        if (empty($newdefinition->checklist['groups'])) {
            $newgroups = array();
        } else {
            $newgroups = $newdefinition->checklist['groups']; // new ones to be saved
        }
        $currentgroups = $currentdefinition->checklist_groups;
        $groupsfields = array('sortorder', 'description');
        $itemfields = array('score', 'sortorder', 'definition');
        foreach ($newgroups as $id => $group) {
            // get list of submitted items
            $itemsdata = array();
            if (array_key_exists('items', $group)) {
                $itemsdata = $group['items'];
            }
            $groupmaxscore = null;
            if (preg_match('/^NEWID\d+$/', $id)) {
                // insert group into DB
                $data = array('definitionid' => $this->definition->id);
                foreach ($groupsfields as $key) {
                    if (array_key_exists($key, $group)) {
                        if ($key == 'description') {
                            $group[$key] = MoodleQuickForm_checklisteditor::clean_multiline_text($group[$key]);
                        }
                        $data[$key] = $group[$key];
                    }
                }
                if ($doupdate) {
                    $id = $DB->insert_record('gradingform_checklist_groups', $data);
                }
                $haschanges[5] = true;
            } else {
                // update group in DB
                $data = array();
                foreach ($groupsfields as $key) {
                    if (array_key_exists($key, $group) && $key == 'description') {
                        $group[$key] = MoodleQuickForm_checklisteditor::clean_multiline_text($group[$key]);
                    }
                    if (array_key_exists($key, $group) && $group[$key] != ($currentgroups[$id][$key] ?? null)) {
                        $data[$key] = $group[$key];
                    }
                }
                if (!empty($data)) {
                    // update only if something is changed
                    $data['id'] = $id;
                    if ($doupdate) {
                        $DB->update_record('gradingform_checklist_groups', $data);
                    }
                    $haschanges[1] = true;
                }
                // remove deleted items from DB and calculate the maximum score for this groups
                foreach ($currentgroups[$id]['items'] as $itemid => $currentitem) {
                    // group max score is all sum of all items (all items checked)
                    $groupmaxscore += $currentitem['score'];

                    if (!array_key_exists($itemid, $itemsdata)) {
                        if ($doupdate) {
                            $DB->delete_records('gradingform_checklist_items', array('id' => $itemid));
                        }
                        $haschanges[4] = true;
                    }
                }
            }
            foreach ($itemsdata as $itemid => $item) {
                if (isset($item['score'])) {
                    $item['score'] = (float)$item['score'];
                    if ($item['score'] < 0) {
                        // TODO why we can't allow negative score for checklist?
                        $item['score'] = 0;
                    }
                }
                if (preg_match('/^NEWID\d+$/', $itemid)) {
                    // insert item into DB
                    $data = array('groupid' => $id);
                    foreach ($itemfields as $key) {
                        if (array_key_exists($key, $item)) {
                            if ($key == 'definition') {
                                $item[$key] = MoodleQuickForm_checklisteditor::clean_multiline_text($item[$key]);
                            }
                            $data[$key] = $item[$key];
                        }
                    }
                    if ($doupdate) {
                        $itemid = $DB->insert_record('gradingform_checklist_items', $data);
                    }

                    // additional item means that maximum group score will change
                    $haschanges[3] = true;

                } else {
                    // update item in DB
                    $data = array();
                    foreach ($itemfields as $key) {
                        if (array_key_exists($key, $item) && $key == 'definition') {
                            $item[$key] = MoodleQuickForm_checklisteditor::clean_multiline_text($item[$key]);
                        }
                        if (array_key_exists($key, $item) && $item[$key] != $currentgroups[$id]['items'][$itemid][$key]) {
                            $data[$key] = $item[$key];
                        }
                    }
                    if (!empty($data)) {
                        // update only if something is changed
                        $data['id'] = $itemid;
                        if ($doupdate) {
                            $DB->update_record('gradingform_checklist_items', $data);
                        }
                        if (isset($data['score'])) {
                            $haschanges[3] = true;
                        }
                        $haschanges[1] = true;
                    }
                }
            }
        }
        // remove deleted groups from DB
        foreach (array_keys($currentgroups) as $id) {
            if (!array_key_exists($id, $newgroups)) {
                if ($doupdate) {
                    $DB->delete_records('gradingform_checklist_groups', array('id' => $id));
                    $DB->delete_records('gradingform_checklist_items', array('groupid' => $id));
                }
                $haschanges[3] = true;
            }
        }
        foreach (array('status', 'description', 'descriptionformat', 'name', 'options') as $key) {
            if (isset($newdefinition->$key) && $newdefinition->$key != $this->definition->$key) {
                $haschanges[1] = true;
            }
        }
        if ($usermodified && $usermodified != $this->definition->usermodified) {
            $haschanges[1] = true;
        }
        if (!count($haschanges)) {
            return 0;
        }
        if ($doupdate) {
            parent::update_definition($newdefinition, $usermodified);
            $this->save_definition_benchmark($newbenchmark);
            $this->load_definition();
        }
        // return the maximum level of changes
        $changelevels = array_keys($haschanges);
        sort($changelevels);
        return array_pop($changelevels);
    }

    /**
     * Converts the current definition into an object suitable for the editor form's set_data()
     *
     * @param boolean $addemptygroup whether to add an empty group if the checklist is completely empty (just being created)
     * @return stdClass
     */
    public function get_definition_for_editing($addemptygroup = false) {

        $definition = $this->get_definition();
        $properties = new stdClass();
        $properties->areaid = $this->areaid;
        if ($definition) {
            foreach (array('id', 'name', 'description', 'descriptionformat', 'status') as $key) {
                $properties->$key = $definition->$key;
            }
            $options = self::description_form_field_options($this->get_context());
            $properties = file_prepare_standard_editor($properties, 'description', $options, $this->get_context(),
                'grading', 'description', $definition->id);
            $benchmark = (object) ($definition->benchmark ?? self::get_default_benchmark());
            $hasbenchmark = trim((string)($benchmark->benchmark ?? '')) !== '';
            $benchmark->benchmarkformat = $benchmark->benchmarkformat ?? FORMAT_HTML;
            $benchmark = file_prepare_standard_editor($benchmark, 'benchmark',
                self::benchmark_form_field_options($this->get_context()), $this->get_context(),
                'gradingform_checklist', 'benchmark', $definition->id);
            $properties->usebenchmark = $hasbenchmark ? 1 : 0;
            $properties->removebenchmark = 0;
            $properties->benchmark_editor = $benchmark->benchmark_editor;
            $properties->benchmarkbuttonlabel = $benchmark->buttonlabel ?? get_string('benchmarkbuttondefault', 'gradingform_checklist');
            $properties->benchmarkbuttonicon = $benchmark->buttonicon ?? 'fa-solid fa-file-circle-check';
        }
        $properties->checklist = array('groups' => array(), 'options' => $this->get_options());
        if (!empty($definition->checklist_groups)) {
            $properties->checklist['groups'] = $definition->checklist_groups;
        } else if (!$definition && $addemptygroup) {
            $properties->checklist['groups'] = array('addgroup' => 1);
        }

        return $properties;
    }

    /**
     * Returns the form definition suitable for cloning into another area
     *
     * @see parent::get_definition_copy()
     * @param gradingform_controller $target the controller of the new copy
     * @return stdClass definition structure to pass to the target's {@link update_definition()}
     */
    public function get_definition_copy(gradingform_controller $target) {

        $new = parent::get_definition_copy($target);
        $old = $this->get_definition_for_editing();
        $new->description_editor = $old->description_editor;
        $new->benchmark_editor = $old->benchmark_editor ?? ['text' => '', 'format' => FORMAT_HTML, 'itemid' => 0];
        $new->usebenchmark = $old->usebenchmark ?? 0;
        $new->removebenchmark = 0;
        $new->benchmarkbuttonlabel = $old->benchmarkbuttonlabel ?? get_string('benchmarkbuttondefault', 'gradingform_checklist');
        $new->benchmarkbuttonicon = $old->benchmarkbuttonicon ?? 'fa-solid fa-file-circle-check';
        $new->checklist = array('groups' => array(), 'options' => $old->checklist['options']);
        $newgroupid = 1;
        $newitemid = 1;
        foreach ($old->checklist['groups'] as  $oldgroup) {
            unset($oldgroup['id']);
            if (isset($oldgroup['items'])) {
                foreach ($oldgroup['items'] as $olditemid => $olditem) {
                    unset($olditem['id']);
                    $oldgroup['items']['NEWID'.$newitemid] = $olditem;
                    unset($oldgroup['items'][$olditemid]);
                    $newitemid++;
                }
            } else {
                $oldgroup['items'] = array();
            }
            $new->checklist['groups']['NEWID'.$newgroupid] = $oldgroup;
            $newgroupid++;
        }

        return $new;
    }


    /**
     * Returns the default single benchmark settings.
     *
     * @return array
     */
    public static function get_default_benchmark(): array {
        return [
            'benchmark' => '',
            'benchmarkformat' => FORMAT_HTML,
            'buttonlabel' => get_string('benchmarkbuttondefault', 'gradingform_checklist'),
            'buttonicon' => 'fa-solid fa-file-circle-check',
        ];
    }

    /**
     * Returns submitted single benchmark data without saving draft files.
     *
     * @param stdClass $newdefinition submitted definition
     * @return array
     */
    protected function get_submitted_benchmark(stdClass $newdefinition): array {
        $currentbenchmark = $this->get_definition()->benchmark ?? self::get_default_benchmark();
        $benchmark = self::get_default_benchmark();
        if (!empty($newdefinition->removebenchmark)) {
            $benchmark['delete'] = true;
            return $benchmark;
        }

        if (empty($newdefinition->usebenchmark)) {
            return $currentbenchmark;
        }

        if (!isset($newdefinition->benchmark_editor) || !is_array($newdefinition->benchmark_editor)) {
            return $currentbenchmark;
        }

        if (isset($newdefinition->benchmark_editor) && is_array($newdefinition->benchmark_editor)) {
            $benchmark['benchmark'] = clean_param($newdefinition->benchmark_editor['text'] ?? '', PARAM_RAW);
            $benchmark['benchmarkformat'] = (int) ($newdefinition->benchmark_editor['format'] ?? FORMAT_HTML);
            $benchmark['benchmarkitemid'] = (int) ($newdefinition->benchmark_editor['itemid'] ?? 0);
        }
        $benchmark['buttonlabel'] = clean_param($newdefinition->benchmarkbuttonlabel ?? $benchmark['buttonlabel'], PARAM_TEXT);
        $benchmark['buttonicon'] = clean_param($newdefinition->benchmarkbuttonicon ?? $benchmark['buttonicon'], PARAM_TEXT);
        if ($benchmark['buttonlabel'] === '') {
            $benchmark['buttonlabel'] = get_string('benchmarkbuttondefault', 'gradingform_checklist');
        }
        if ($benchmark['buttonicon'] === '') {
            $benchmark['buttonicon'] = 'fa-solid fa-file-circle-check';
        }
        return $benchmark;
    }

    /**
     * Saves files embedded in the single benchmark draft area and returns rewritten benchmark data.
     *
     * @param array $benchmark submitted benchmark data
     * @return array
     */
    protected function save_definition_benchmark_files(array $benchmark): array {
        if (empty($benchmark['benchmarkitemid'])) {
            return $benchmark;
        }
        $benchmark['benchmark'] = file_save_draft_area_files(
            $benchmark['benchmarkitemid'],
            $this->get_context()->id,
            'gradingform_checklist',
            'benchmark',
            $this->definition->id,
            self::benchmark_form_field_options($this->get_context()),
            $benchmark['benchmark'] ?? ''
        );
        unset($benchmark['benchmarkitemid']);
        return $benchmark;
    }

    /**
     * Saves the single benchmark row.
     *
     * @param array $benchmark benchmark data
     */
    protected function save_definition_benchmark(array $benchmark): void {
        global $DB;

        if (!empty($benchmark['delete'])) {
            $this->delete_definition_benchmark_files();
            $DB->delete_records('gradingform_checklist_bench', ['definitionid' => $this->definition->id]);
            return;
        }

        $record = (object) [
            'definitionid' => $this->definition->id,
            'benchmark' => $benchmark['benchmark'] ?? '',
            'benchmarkformat' => (int) ($benchmark['benchmarkformat'] ?? FORMAT_HTML),
            'buttonlabel' => $benchmark['buttonlabel'] ?? get_string('benchmarkbuttondefault', 'gradingform_checklist'),
            'buttonicon' => $benchmark['buttonicon'] ?? 'fa-solid fa-file-circle-check',
        ];
        if ($existing = $DB->get_record('gradingform_checklist_bench', ['definitionid' => $this->definition->id], 'id')) {
            $record->id = $existing->id;
            $DB->update_record('gradingform_checklist_bench', $record);
        } else {
            $DB->insert_record('gradingform_checklist_bench', $record);
        }
    }

    /**
     * Returns whether benchmark data changed.
     *
     * @param array $current current data
     * @param array $new new data
     * @return bool
     */
    protected function benchmark_has_changes(array $current, array $new): bool {
        foreach (['benchmark', 'benchmarkformat', 'buttonlabel', 'buttonicon'] as $key) {
            if (($current[$key] ?? null) != ($new[$key] ?? null)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deletes files embedded in the single benchmark.
     */
    protected function delete_definition_benchmark_files(): void {
        $fs = get_file_storage();
        $fs->delete_area_files($this->get_context()->id, 'gradingform_checklist', 'benchmark', $this->definition->id);
    }

    /**
     * Formats the single teacher-only benchmark for display.
     *
     * @return array
     */
    public function get_formatted_benchmark(): array {
        $benchmark = $this->definition->benchmark ?? self::get_default_benchmark();
        if (empty($benchmark['benchmark'])) {
            return [];
        }
        $context = $this->get_context();
        $text = file_rewrite_pluginfile_urls(
            $benchmark['benchmark'],
            'pluginfile.php',
            $context->id,
            'gradingform_checklist',
            'benchmark',
            $this->definition->id,
            self::benchmark_form_field_options($context)
        );
        return [
            'content' => format_text($text, $benchmark['benchmarkformat'] ?? FORMAT_HTML, [
                'noclean' => false,
                'trusted' => false,
                'filter' => true,
                'context' => $context,
            ]),
            'buttonlabel' => $benchmark['buttonlabel'] ?? get_string('benchmarkbuttondefault', 'gradingform_checklist'),
            'buttonicon' => $benchmark['buttonicon'] ?? 'fa-solid fa-file-circle-check',
            'title' => get_string('benchmark', 'gradingform_checklist'),
            'id' => $this->definition->id,
        ];
    }

    /**
     * Formats the definition description for display on page
     *
     * @return string
     */
    public function get_formatted_description() {
        if (!isset($this->definition->description)) {
            return '';
        }
        $context = $this->get_context();

        $options = self::description_form_field_options($this->get_context());
        $description = file_rewrite_pluginfile_urls($this->definition->description, 'pluginfile.php', $context->id,
            'grading', 'description', $this->definition->id, $options);

        $formatoptions = array(
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $context
        );
        return format_text($description, $this->definition->descriptionformat, $formatoptions);
    }

    /**
     * Marks all instances filled with this checklist with the status INSTANCE_STATUS_NEEDUPDATE
     */
    public function mark_for_regrade() {
        global $DB;
        if ($this->has_active_instances()) {
            $conditions = array('definitionid'  => $this->definition->id,
                'status'  => gradingform_instance::INSTANCE_STATUS_ACTIVE);
            $DB->set_field('grading_instances', 'status', gradingform_instance::INSTANCE_STATUS_NEEDUPDATE, $conditions);
        }
    }

    /**
     * Loads the checklist form definition if it exists
     *
     * There is a new array called 'checklist_groups' appended to the list of parent's definition properties.
     */
    protected function load_definition() {
        global $DB;
        $sql = "SELECT gd.*,
                       cb.benchmark AS cbbenchmark, cb.benchmarkformat AS cbbenchmarkformat,
                       cb.buttonlabel AS cbbuttonlabel, cb.buttonicon AS cbbuttonicon,
                       clg.id AS clgid, clg.sortorder AS clgsortorder, clg.description AS clgdescription,
                       cli.id AS cliid, cli.score AS cliscore, cli.sortorder AS clisortorder, cli.definition AS clidefinition
                  FROM {grading_definitions} gd
             LEFT JOIN {gradingform_checklist_bench} cb ON (cb.definitionid = gd.id)
             LEFT JOIN {gradingform_checklist_groups} clg ON (clg.definitionid = gd.id)
             LEFT JOIN {gradingform_checklist_items} cli ON (cli.groupid = clg.id)
                 WHERE gd.areaid = :areaid AND gd.method = :method
              ORDER BY clg.sortorder, cli.sortorder";
        $params = array('areaid' => $this->areaid, 'method' => $this->get_method_name());

        $rs = $DB->get_recordset_sql($sql, $params);
        $this->definition = false;
        foreach ($rs as $record) {
            // pick the common definition data
            if ($this->definition === false) {
                $this->definition = new stdClass();
                foreach (array('id', 'name', 'description', 'descriptionformat', 'status', 'copiedfromid',
                             'timecreated', 'usercreated', 'timemodified', 'usermodified', 'timecopied', 'options') as $fieldname) {
                    $this->definition->$fieldname = $record->$fieldname;
                }
                $this->definition->benchmark = self::get_default_benchmark();
                if ($record->cbbenchmark !== null || $record->cbbuttonlabel !== null || $record->cbbuttonicon !== null) {
                    $this->definition->benchmark = [
                        'benchmark' => $record->cbbenchmark ?? '',
                        'benchmarkformat' => $record->cbbenchmarkformat ?? FORMAT_HTML,
                        'buttonlabel' => $record->cbbuttonlabel ?: get_string('benchmarkbuttondefault', 'gradingform_checklist'),
                        'buttonicon' => $record->cbbuttonicon ?: 'fa-solid fa-file-circle-check',
                    ];
                }
                $this->definition->checklist_groups = array();
            }
            // pick the groups data
            if (!empty($record->clgid) && empty($this->definition->checklist_groups[$record->clgid])) {
                foreach (array('id', 'sortorder', 'description') as $fieldname) {
                    $this->definition->checklist_groups[$record->clgid][$fieldname] = $record->{'clg'.$fieldname};
                }
                $this->definition->checklist_groups[$record->clgid]['items'] = array();
            }
            // pick the items data
            if (!empty($record->cliid)) {
                foreach (array('id', 'score', 'sortorder', 'definition') as $fieldname) {
                    $value = $record->{'cli'.$fieldname};
                    if ($fieldname == 'score') {
                        $value = (float)$value; // To prevent display like 1.00000
                    }
                    $this->definition->checklist_groups[$record->clgid]['items'][$record->cliid][$fieldname] = $value;
                }
            }
        }
        $rs->close();
    }

    /**
     * Returns html code to be included in student's feedback.
     *
     * @param moodle_page $page
     * @param int $itemid
     * @param array $gradinginfo result of function grade_get_grades
     * @param string $defaultcontent default string to be returned if no active grading is found
     * @param boolean $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
        return $this->get_renderer($page)->display_instances($this->get_active_instances($itemid), $defaultcontent, $cangrade);
    }

    /**
     * Calculates and returns the possible minimum and maximum score (in points) for this checklist
     *
     * @return array
     */
    public function get_min_max_score() {
        if (!$this->is_form_available()) {
            return null;
        }
        $returnvalue = array('minscore' => 0, 'maxscore' => 0);
        foreach ($this->get_definition()->checklist_groups as $group) {
            foreach ($group['items'] as $item) {
                $returnvalue['maxscore'] += $item['score'];
            }
        }
        return $returnvalue;
    }

    //// full-text search support /////////////////////////////////////////////

    /**
     * Prepare the part of the search query to append to the FROM statement
     *
     * @param string $gdid the alias of grading_definitions.id column used by the caller
     * @return string
     */
    public static function sql_search_from_tables($gdid) {
        return " LEFT JOIN {gradingform_checklist_groups} clg ON (clg.definitionid = $gdid)
                 LEFT JOIN {gradingform_checklist_items} cli ON (cli.groupid = clg.id)";
    }

    /**
     * Prepare the parts of the SQL WHERE statement to search for the given token
     *
     * The returned array cosists of the list of SQL comparions and the list of
     * respective parameters for the comparisons. The returned chunks will be joined
     * with other conditions using the OR operator.
     *
     * @param string $token token to search for
     * @return array
     */
    public static function sql_search_where($token) {
        global $DB;

        $subsql = array();
        $params = array();

        // search in checklist group description
        $subsql[] = $DB->sql_like('clg.description', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';

        // search in checklist item definition
        $subsql[] = $DB->sql_like('cli.definition', '?', false, false);
        $params[] = '%'.$DB->sql_like_escape($token).'%';

        return array($subsql, $params);
    }

    /**
     * Options for displaying the checklist description field in the form
     *
     * @param object $context
     * @return array options for the form description field
     */
    public static function benchmark_form_field_options($context) {
        global $CFG;
        return array(
            'maxfiles' => -1,
            'maxbytes' => get_max_upload_file_size($CFG->maxbytes),
            'context' => $context,
            'trusttext' => false,
            'subdirs' => 0,
        );
    }

    /**
     * Options for displaying the checklist description field in the form
     *
     * @param object $context
     * @return array options for the form description field
     */
    public static function description_form_field_options($context) {
        global $CFG;
        return array(
            'maxfiles' => -1,
            'maxbytes' => get_max_upload_file_size($CFG->maxbytes),
            'context'  => $context,
        );
    }

    /**
     * Returns the default options for the checklist display
     *
     * @return array
     */
    public static function get_default_options() {
        $options = array(
            'alwaysshowdefinition' => 1,
            'showitempointseval' => 0,
            'showitempointstudent' => 0,
            'showgrouppointseval' => 0,
            'showgrouppointstudent' => 0,
            'enableitemremarks' => 0,
            'enablegroupremarks' => 1,
            'showremarksstudent' => 1,
            'enablebulkcheck' => 0,
            'requireitemcommentschecked' => 0,
            'requireatleastoneitemcomment' => 0,
            'requiregroupcommentschecked' => 0,
            'requireatleastonegroupcomment' => 0,
            'groupremarkheading' => '',
            'observationmode' => self::OBSERVATION_MODE_DISABLED,
            'observationdefault' => self::OBSERVATION_DEFAULT_NOW,
        );
        return $options;
    }

    /**
     * Returns whether the observation date selector is enabled for the checklist definition.
     *
     * @param array $options checklist definition options
     * @return bool
     */
    public static function observation_enabled(array $options): bool {
        return !empty($options['observationmode'])
            && $options['observationmode'] !== self::OBSERVATION_MODE_DISABLED;
    }

    /**
     * Sanitises an observation selector mode.
     *
     * @param string|null $mode submitted mode
     * @return string
     */
    public static function clean_observation_mode(?string $mode): string {
        $valid = array(
            self::OBSERVATION_MODE_DISABLED,
            self::OBSERVATION_MODE_DATE,
            self::OBSERVATION_MODE_DATETIME,
        );
        return in_array($mode, $valid, true) ? $mode : self::OBSERVATION_MODE_DISABLED;
    }

    /**
     * Sanitises an observation date default setting.
     *
     * @param string|null $default submitted default
     * @return string
     */
    public static function clean_observation_default(?string $default): string {
        $valid = array(self::OBSERVATION_DEFAULT_NOW, self::OBSERVATION_DEFAULT_BLANK);
        return in_array($default, $valid, true) ? $default : self::OBSERVATION_DEFAULT_NOW;
    }

    /**
     * Formats an observation timestamp using Moodle language/user date formats.
     *
     * @param int $timestamp observation timestamp
     * @param string $mode observation mode
     * @return string
     */
    public static function format_observation_date(int $timestamp, string $mode): string {
        if ($mode === self::OBSERVATION_MODE_DATE) {
            return userdate($timestamp, get_string('strftimedate', 'langconfig'));
        }
        return userdate($timestamp, get_string('strftimedatetime', 'langconfig'));
    }

    /**
     * Formats a timestamp for an HTML date input.
     *
     * Browser date inputs require YYYY-MM-DD, regardless of Moodle display locale.
     *
     * @param int $timestamp observation timestamp
     * @return string
     */
    public static function format_observation_date_input(int $timestamp): string {
        $date = usergetdate($timestamp);
        return sprintf('%04d-%02d-%02d', $date['year'], $date['mon'], $date['mday']);
    }

    /**
     * Formats a timestamp for an HTML time input.
     *
     * Browser time inputs require HH:MM in 24-hour format, regardless of Moodle display locale.
     *
     * @param int $timestamp observation timestamp
     * @return string
     */
    public static function format_observation_time_input(int $timestamp): string {
        $date = usergetdate($timestamp);
        return sprintf('%02d:%02d', $date['hours'], $date['minutes']);
    }

    /**
     * Normalises required-comment options against their matching remark options.
     *
     * @param array $options checklist definition options
     * @return array
     */
    public static function normalise_comment_option_dependencies(array $options): array {
        if (empty($options['enableitemremarks'])) {
            $options['requireitemcommentschecked'] = 0;
            $options['requireatleastoneitemcomment'] = 0;
        }
        if (empty($options['enablegroupremarks'])) {
            $options['requiregroupcommentschecked'] = 0;
            $options['requireatleastonegroupcomment'] = 0;
        }
        return $options;
    }

    /**
     * Returns whether item remark fields are enabled.
     *
     * @param array $options checklist definition options
     * @return bool
     */
    public static function item_remarks_enabled(array $options): bool {
        return !empty($options['enableitemremarks']);
    }

    /**
     * Returns whether group remark fields are enabled.
     *
     * @param array $options checklist definition options
     * @return bool
     */
    public static function group_remarks_enabled(array $options): bool {
        return !empty($options['enablegroupremarks']);
    }

    /**
     * Gets the visible heading to display above group remark fields.
     *
     * @param array $options checklist definition options
     * @return string
     */
    public static function get_group_remark_heading(array $options): string {
        if (!empty($options['groupremarkheading'])) {
            return trim(clean_param($options['groupremarkheading'], PARAM_TEXT));
        }
        return get_string('groupremarkheadingdefault', 'gradingform_checklist');
    }

    /**
     * Finds required-comment validation errors in submitted checklist grading data.
     *
     * @param array $groups checklist definition groups
     * @param array $options checklist definition options
     * @param array $value submitted grading value
     * @return array structured error data
     */
    public static function get_required_comment_errors(array $groups, array $options, array $value): array {
        $options = self::normalise_comment_option_dependencies($options);
        $requireitemcommentschecked = !empty($options['requireitemcommentschecked']);
        $requireatleastoneitemcomment = !empty($options['requireatleastoneitemcomment']);
        $requiregroupcommentschecked = !empty($options['requiregroupcommentschecked']);
        $requireatleastonegroupcomment = !empty($options['requireatleastonegroupcomment']);

        if (!$requireitemcommentschecked && !$requireatleastoneitemcomment
                && !$requiregroupcommentschecked && !$requireatleastonegroupcomment) {
            return array();
        }

        $hascheckeditem = false;
        $hascheckeditemremark = false;
        $hascheckedgroupremark = false;
        $missingcheckeditemremarks = array();
        $missingcheckedgroupremarks = array();
        $firstcheckeditem = null;
        $firstcheckedgroup = null;

        foreach ($groups as $groupid => $group) {
            $submittedgroup = $value['groups'][$groupid] ?? array('items' => array());
            $submitteditems = $submittedgroup['items'] ?? array();
            $groupcontainscheckeditem = false;

            foreach ($group['items'] as $itemid => $item) {
                $submitteditem = $submitteditems[$itemid] ?? array();
                $ischecked = !empty($submitteditem['id']) || !empty($submitteditem['checked']);

                if (!$ischecked) {
                    continue;
                }

                $hascheckeditem = true;
                $groupcontainscheckeditem = true;
                if ($firstcheckeditem === null) {
                    $firstcheckeditem = array(
                        'rule' => '',
                        'groupid' => $groupid,
                        'group' => $group['description'] ?? '',
                        'itemid' => $itemid,
                        'item' => $item['definition'] ?? '',
                        'fieldtype' => 'item',
                    );
                }

                $remark = '';
                if (isset($submitteditem['remark'])) {
                    $remark = trim(clean_param($submitteditem['remark'], PARAM_TEXT));
                }

                if ($remark === '') {
                    $missingcheckeditemremarks[] = array(
                        'rule' => 'err_requireitemcommentschecked',
                        'groupid' => $groupid,
                        'group' => $group['description'] ?? '',
                        'itemid' => $itemid,
                        'item' => $item['definition'] ?? '',
                        'fieldtype' => 'item',
                    );
                } else {
                    $hascheckeditemremark = true;
                }
            }

            if ($groupcontainscheckeditem) {
                if ($firstcheckedgroup === null) {
                    $firstcheckedgroup = array(
                        'rule' => '',
                        'groupid' => $groupid,
                        'group' => $group['description'] ?? '',
                        'itemid' => 0,
                        'item' => '',
                        'fieldtype' => 'group',
                    );
                }
                $groupremark = '';
                if (isset($submitteditems[0]['remark'])) {
                    $groupremark = trim(clean_param($submitteditems[0]['remark'], PARAM_TEXT));
                }

                if ($groupremark === '') {
                    $missingcheckedgroupremarks[] = array(
                        'rule' => 'err_requiregroupcommentschecked',
                        'groupid' => $groupid,
                        'group' => $group['description'] ?? '',
                        'itemid' => 0,
                        'item' => '',
                        'fieldtype' => 'group',
                    );
                } else {
                    $hascheckedgroupremark = true;
                }
            }
        }

        $errors = array();
        if ($requireitemcommentschecked) {
            $errors = array_merge($errors, $missingcheckeditemremarks);
        }
        if ($requireatleastoneitemcomment && $hascheckeditem && !$hascheckeditemremark) {
            $firstcheckeditem['rule'] = 'err_requireatleastoneitemcomment';
            $errors[] = $firstcheckeditem;
        }
        if ($requiregroupcommentschecked) {
            $errors = array_merge($errors, $missingcheckedgroupremarks);
        }
        if ($requireatleastonegroupcomment && $hascheckeditem && !$hascheckedgroupremark) {
            $firstcheckedgroup['rule'] = 'err_requireatleastonegroupcomment';
            $errors[] = $firstcheckedgroup;
        }

        return $errors;
    }

    /**
     * Returns the id of the feedback field for a required-comment validation error.
     *
     * @param array $error structured validation error
     * @param string $elementname grading form element name
     * @return string
     */
    public static function get_required_comment_error_field_id(array $error, string $elementname): string {
        if (($error['fieldtype'] ?? '') === 'group') {
            return $elementname.'-groups-'.$error['groupid'].'-items-0-remark';
        }
        return $elementname.'-groups-'.$error['groupid'].'-items-'.$error['itemid'].'-remark-input';
    }

    /**
     * Formats a required-comment validation error for display.
     *
     * @param array $error structured validation error
     * @return string
     */
    public static function format_required_comment_error(array $error): string {
        $a = (object) array(
            'group' => $error['group'] ?? '',
            'item' => $error['item'] ?? '',
        );
        return get_string($error['rule'], 'gradingform_checklist', $a);
    }

    /**
     * Gets the options of this checklist definition, fills the missing options with default values
     *
     * @return array
     */
    public function get_options() {
        $options = self::get_default_options();
        if (!empty($this->definition->options)) {
            $thisoptions = json_decode($this->definition->options);
            foreach ($thisoptions as $option => $value) {
                $options[$option] = $value;
            }
        }
        return $options;
    }

    /**
     * Returns whether the points of the groups and items should be displayed taking into account the method configuration
     * and whether the user is grading or not.
     *
     * @param bool $isgrading The user is reviewing their grades or is grading.
     * @return bool
     */
    public function can_display_item_points(bool $isgrading): bool {
        $options = $this->get_options();
        return (!empty($options['showitempointstudent']) && !$isgrading)
            || (!empty($options['showitempointseval']) && $isgrading);
    }

    /**
     * Returns whether group and overall point totals should be displayed.
     *
     * @param bool $isgrading The user is reviewing their grades or is grading.
     * @return bool
     */
    public function can_display_group_points(bool $isgrading): bool {
        $options = $this->get_options();
        return (!empty($options['showgrouppointstudent']) && !$isgrading)
            || (!empty($options['showgrouppointseval']) && $isgrading);
    }

    /**
     * Returns whether points should be displayed in legacy callers.
     *
     * @param bool $isgrading The user is reviewing their grades or is grading.
     * @return bool
     */
    public function can_display_points(bool $isgrading): bool {
        return $this->can_display_item_points($isgrading) || $this->can_display_group_points($isgrading);
    }

    /**
     * Returns whether group feedback should be displayed taking into account the method configuration and whether
     * the user is grading or not.
     *
     * @param bool $isgrading The user is reviewing their grades or is grading.
     * @return bool
     */
    public function can_display_group_feedback(bool $isgrading): bool {
        $options = $this->get_options();
        return self::group_remarks_enabled($options) && ($isgrading || !empty($options['showremarksstudent']));
    }

    /**
     * Returns whether item feedback should be displayed taking into account the method configuration and whether
     * the user is grading or not.
     *
     * @param bool $isgrading The user is reviewing their grades or is grading.
     * @return bool
     */
    public function can_display_item_feedback(bool $isgrading): bool {
        $options = $this->get_options();
        return self::item_remarks_enabled($options) && ($isgrading || !empty($options['showremarksstudent']));
    }
}


/**
 * Serves files embedded in checklist benchmarks.
 *
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param context $context context object
 * @param string $filearea file area
 * @param array $args file arguments
 * @param bool $forcedownload force download
 * @param array $options file serving options
 * @return bool
 */
function gradingform_checklist_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($filearea !== 'benchmark') {
        return false;
    }
    require_login($course, false, $cm);
    if (!has_capability('moodle/grade:managegradingforms', $context) && !has_capability('moodle/grade:grade', $context)) {
        return false;
    }
    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'gradingform_checklist', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Class to manage one checklist grading instance. Stores information and performs actions like
 * update, copy, validate, submit, etc.
 *
 * @copyright  2011 Marina Glancy
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 */
class gradingform_checklist_instance extends gradingform_instance {

    protected $checklist;
    /** @var array Required-comment validation errors from the most recent validation. */
    protected $requiredcommenterrors = array();
    /** @var bool Whether the most recent validation failed because the observation date was missing. */
    protected $observationdateerror = false;

    /**
     * Deletes this (INCOMPLETE) instance from database.
     */
    public function cancel() {
        global $DB;

        parent::cancel();
        $DB->delete_records('gradingform_checklist_fills', array('instanceid' => $this->get_id()));
        $DB->delete_records('gradingform_checklist_obs', array('instanceid' => $this->get_id()));
    }

    /**
     * Duplicates the instance before editing (optionally substitutes raterid and/or itemid with
     * the specified values)
     *
     * @param int $raterid value for raterid in the duplicate
     * @param int $itemid value for itemid in the duplicate
     * @return int id of the new instance
     */
    public function copy($raterid, $itemid) {
        global $DB;
        $instanceid = parent::copy($raterid, $itemid);
        $currentgrade = $this->get_checklist_filling();
        foreach ($currentgrade['groups'] as $groupid => $group) {
            foreach ($group['items'] as $record) {
                $params = array('instanceid' => $instanceid, 'groupid' => $groupid,
                        'itemid' => $record['itemid'], 'checked' => $record['checked'], 'remark' => $record['remark'],
                        'remarkformat' => $record['remarkformat']);
                $DB->insert_record('gradingform_checklist_fills', $params);
            }
        }
        if (!empty($currentgrade['observation']['observationdate'])) {
            $DB->insert_record('gradingform_checklist_obs', array(
                'instanceid' => $instanceid,
                'observationdate' => $currentgrade['observation']['observationdate'],
                'observationmode' => $currentgrade['observation']['observationmode'],
            ));
        }
        return $instanceid;
    }

    /**
     * Retrieves from DB and returns the data how this checklist was filled
     *
     * @param boolean $force whether to force DB query even if the data is cached
     * @return array
     */
    public function get_checklist_filling($force = false) {
        global $DB;

        if ($this->checklist === null || $force) {
            $records = $DB->get_records('gradingform_checklist_fills', array('instanceid' => $this->get_id()));
            $this->checklist = array('groups' => array());
            foreach ($records as $record) {
                if (empty($this->checklist['groups'][$record->groupid])) {
                    $this->checklist['groups'][$record->groupid] = array('items' => array());
                }
                $this->checklist['groups'][$record->groupid]['items'][$record->itemid] = (array)$record;
            }
            $observation = $DB->get_record('gradingform_checklist_obs', array('instanceid' => $this->get_id()));
            if ($observation) {
                $this->checklist['observation'] = (array)$observation;
            }
        }
        return $this->checklist;
    }

    /**
     * Converts submitted observation date fields to a timestamp.
     *
     * @param array $observation submitted observation data
     * @param string $mode observation mode
     * @return int|null
     */
    protected function get_submitted_observation_timestamp(array $observation, string $mode): ?int {
        if (empty($observation['date'])) {
            return null;
        }

        $dateparts = explode('-', clean_param($observation['date'], PARAM_TEXT));
        if (count($dateparts) !== 3) {
            return null;
        }
        [$year, $month, $day] = array_map('intval', $dateparts);

        $hour = 0;
        $minute = 0;
        if ($mode === gradingform_checklist_controller::OBSERVATION_MODE_DATETIME) {
            if (empty($observation['time'])) {
                return null;
            }
            $timeparts = explode(':', clean_param($observation['time'], PARAM_TEXT));
            if (count($timeparts) < 2) {
                return null;
            }
            $hour = (int)$timeparts[0];
            $minute = (int)$timeparts[1];
        }

        if (!checkdate($month, $day, $year) || $hour < 0 || $hour > 23 || $minute < 0 || $minute > 59) {
            return null;
        }

        return make_timestamp($year, $month, $day, $hour, $minute);
    }

    /**
     * Updates observation metadata for this grading instance.
     *
     * @param array $data submitted grading data
     */
    protected function update_observation_date(array $data): void {
        global $DB;

        $options = $this->get_controller()->get_options();
        if (!gradingform_checklist_controller::observation_enabled($options)) {
            $DB->delete_records('gradingform_checklist_obs', array('instanceid' => $this->get_id()));
            return;
        }

        $mode = gradingform_checklist_controller::clean_observation_mode($options['observationmode']);
        $timestamp = null;
        if (!empty($data['observation']) && is_array($data['observation'])) {
            $timestamp = $this->get_submitted_observation_timestamp($data['observation'], $mode);
        }
        if ($timestamp === null) {
            return;
        }

        $record = $DB->get_record('gradingform_checklist_obs', array('instanceid' => $this->get_id()));
        $newrecord = array(
            'instanceid' => $this->get_id(),
            'observationdate' => $timestamp,
            'observationmode' => $mode,
        );
        if ($record) {
            $newrecord['id'] = $record->id;
            $DB->update_record('gradingform_checklist_obs', $newrecord);
        } else {
            $DB->insert_record('gradingform_checklist_obs', $newrecord);
        }
    }

    /**
     * Updates the instance with the data received from grading form. This function may be
     * called via AJAX when grading is not yet completed, so it does not change the
     * status of the instance.
     *
     * @param array $data
     */
    public function update($data) {
        global $DB;

        $currentgrade = $this->get_checklist_filling();
        parent::update($data);
        $this->update_observation_date($data);

        foreach ($data['groups'] as $groupid => $group) {
            foreach($group['items'] as $itemid => $record) {
                $record['remarkformat'] = FORMAT_HTML;
                //handle deletions later
                if (empty($record['remark']) && empty($record['id'])) {
                    continue;
                }
                if (!array_key_exists($groupid, $currentgrade['groups']) || !array_key_exists($itemid, $currentgrade['groups'][$groupid]['items'])) {
                    $newrecord = array('instanceid' => $this->get_id(), 'groupid' => $groupid,
                        'itemid' => $itemid, 'checked' => !empty($record['id']), 'remarkformat' => $record['remarkformat']);
                    if (isset($record['remark'])) {
                        $newrecord['remark'] = clean_param($record['remark'], PARAM_TEXT);
                    }
                    $DB->insert_record('gradingform_checklist_fills', $newrecord);
                } else {
                    $newrecord = array('id' => $currentgrade['groups'][$groupid]['items'][$itemid]['id']);
                    foreach (array('remark', 'remarkformat') as $key) {
                        if (isset($record[$key]) && $key == 'remark') {
                            $record[$key] = clean_param($record[$key], PARAM_TEXT);
                        }
                        if (isset($record[$key]) && $currentgrade['groups'][$groupid]['items'][$itemid][$key] != $record[$key]) {
                            $newrecord[$key] = $record[$key];
                        }
                    }

                    if (!empty($record['id']) && empty($currentgrade['groups'][$groupid]['items'][$itemid]['checked'])) {
                        $newrecord['checked'] = 1;
                    } else if (empty($record['id']) && !empty($currentgrade['groups'][$groupid]['items'][$itemid]['checked'])) {
                        $newrecord['checked'] = 0;
                    }
                    if (count($newrecord) > 1) {
                        $DB->update_record('gradingform_checklist_fills', $newrecord);
                    }
                }
            }
        }

        // take care of unchecked items / deleted comments
        foreach ($currentgrade['groups'] as $groupid => $group) {
            foreach($group['items'] as $itemid => $record) {
                // if the 'id' and 'remark' elements are empty then it is not checked and there is no comment
                if (empty($data['groups'][$groupid]['items'][$itemid]['id']) && empty($data['groups'][$groupid]['items'][$itemid]['remark'])) {
                    $DB->delete_records('gradingform_checklist_fills', array('id' => $record['id']));
                }
            }
        }

        $this->get_checklist_filling(true);
    }

    /**
     * Validates submitted checklist grading data.
     *
     * @param array $elementvalue value of element as came in form submit
     * @return bool
     */
    public function validate_grading_element($elementvalue) {
        $this->requiredcommenterrors = array();
        $this->observationdateerror = false;

        if (!isset($elementvalue['groups']) || !is_array($elementvalue['groups'])) {
            return false;
        }

        $this->requiredcommenterrors = gradingform_checklist_controller::get_required_comment_errors(
            $this->get_controller()->get_definition()->checklist_groups,
            $this->get_controller()->get_options(),
            $elementvalue
        );

        $options = $this->get_controller()->get_options();
        if (gradingform_checklist_controller::observation_enabled($options)) {
            $mode = gradingform_checklist_controller::clean_observation_mode($options['observationmode']);
            $observation = [];
            if (!empty($elementvalue['observation']) && is_array($elementvalue['observation'])) {
                $observation = $elementvalue['observation'];
            }
            $this->observationdateerror = $this->get_submitted_observation_timestamp($observation, $mode) === null;
        }

        return empty($this->requiredcommenterrors) && !$this->observationdateerror;
    }

    /**
     * Returns required-comment validation errors from the most recent validation.
     *
     * @return array
     */
    public function get_required_comment_validation_errors(): array {
        return $this->requiredcommenterrors;
    }

    /**
     * Returns required-comment validation error messages from the most recent validation.
     *
     * @param string|null $elementname optional grading form element name for summary links
     * @return array
     */
    public function get_required_comment_validation_error_messages(?string $elementname = null): array {
        $messages = array();
        foreach ($this->requiredcommenterrors as $error) {
            $message = gradingform_checklist_controller::format_required_comment_error($error);
            if ($elementname !== null) {
                $fieldid = gradingform_checklist_controller::get_required_comment_error_field_id($error, $elementname);
                $message = \core\output\html_writer::link('#'.$fieldid, $message);
            }
            $messages[] = $message;
        }
        return $messages;
    }

    /**
     * Returns validation error messages from the most recent grading validation.
     *
     * @param string|null $elementname optional grading form element name for summary links
     * @return array
     */
    public function get_grading_validation_error_messages(?string $elementname = null): array {
        $messages = $this->get_required_comment_validation_error_messages($elementname);
        if ($this->observationdateerror) {
            $message = get_string('err_observationdate', 'gradingform_checklist');
            if ($elementname !== null) {
                $message = \core\output\html_writer::link('#'.$elementname.'-observation-date', $message);
            }
            $messages[] = $message;
        }
        return $messages;
    }

    /**
     * Returns whether the most recent validation failed on the observation date.
     *
     * @return bool
     */
    public function has_observation_date_validation_error(): bool {
        return $this->observationdateerror;
    }

    /**
     * Adds display-friendly checked flags to submitted grading data.
     *
     * Checkbox submissions use an item id field, while the renderer expects a checked flag.
     *
     * @param array $value submitted grading value
     * @return array
     */
    protected function normalise_grading_value_for_display(array $value): array {
        if (empty($value['groups']) || !is_array($value['groups'])) {
            return $value;
        }

        foreach ($value['groups'] as $groupid => $group) {
            if (empty($group['items']) || !is_array($group['items'])) {
                continue;
            }
            foreach ($group['items'] as $itemid => $item) {
                if (!empty($item['id'])) {
                    $value['groups'][$groupid]['items'][$itemid]['checked'] = 1;
                } else if (!isset($item['checked'])) {
                    $value['groups'][$groupid]['items'][$itemid]['checked'] = 0;
                }
            }
        }

        return $value;
    }

    /**
     * Submits grading data and returns the grade.
     *
     * @param array $elementvalue value of element as came in form submit
     * @param int $itemid the item being graded
     * @return float|int
     */
    public function submit_and_get_grade($elementvalue, $itemid) {
        $elementvalue['itemid'] = $itemid;
        if (!$this->validate_grading_element($elementvalue)) {
            $this->update($elementvalue);
            return -1;
        }
        return parent::submit_and_get_grade($elementvalue, $itemid);
    }

    /**
     * Calculates the grade to be pushed to the gradebook
     *
     * @return int the valid grade from $this->get_controller()->get_grade_range()
     */
    public function get_grade() {
        $grade = $this->get_checklist_filling(true);

        if (!($scores = $this->get_controller()->get_min_max_score()) || $scores['maxscore'] <= $scores['minscore']) {
            return -1;
        }

        $graderange = array_keys($this->get_controller()->get_grade_range());
        if (empty($graderange)) {
            return -1;
        }
        sort($graderange);
        $mingrade = $graderange[0];
        $maxgrade = $graderange[count($graderange) - 1];

        $curscore = 0;
        foreach ($grade['groups'] as $groupid => $group) {
            foreach ($group['items'] as $itemid => $record) {
                // itemid of 0 means a group remark, not used for scoring; also make sure it is checked
                if (!empty($itemid) && !empty($record['checked'])) {
                    $curscore += $this->get_controller()->get_definition()->checklist_groups[$groupid]['items'][$record['itemid']]['score'];
                }
            }
        }

        $gradeoffset = ($curscore-$scores['minscore'])/($scores['maxscore']-$scores['minscore'])*($maxgrade-$mingrade);
        if ($this->get_controller()->get_allow_grade_decimals()) {
            return $gradeoffset + $mingrade;
        }
        return round($gradeoffset, 0) + $mingrade;
    }

    /**
     * Returns html for form element of type 'grading'.
     *
     * @param moodle_page $page
     * @param MoodleQuickForm_grading $gradingformelement
     * @return string
     */
    public function render_grading_element($page, $gradingformelement) {
        if (!$gradingformelement->_flagFrozen) {
            $module = array('name'=>'gradingform_checklist', 'fullpath'=>'/grade/grading/form/checklist/js/checklist.js',
                'strings' => array(
                    array('tickall', 'gradingform_checklist'),
                    array('untickall', 'gradingform_checklist'),
                    array('benchmark', 'gradingform_checklist'),
                    array('closebenchmark', 'gradingform_checklist'),
                ));
            $page->requires->js_init_call('M.gradingform_checklist.init', array(array('name' => $gradingformelement->getName())), true, $module);
            $mode = gradingform_checklist_controller::DISPLAY_EVAL;
        } else {
            if ($gradingformelement->_persistantFreeze) {
                $mode = gradingform_checklist_controller::DISPLAY_EVAL_FROZEN;
            } else {
                $mode = gradingform_checklist_controller::DISPLAY_REVIEW;
            }
        }
        $groups = $this->get_controller()->get_definition()->checklist_groups;
        $options = $this->get_controller()->get_options();
        $value = $gradingformelement->getValue();
        $submitted = $value !== null;
        $html = '';
        if ($value === null) {
            $value = $this->get_checklist_filling();
        } else {
            $value = $this->normalise_grading_value_for_display($value);
        }
        if ($submitted && !$this->validate_grading_element($value)) {
            $errors = $this->get_grading_validation_error_messages($gradingformelement->getName());
            $message = empty($errors) ? get_string('checklistnotcompleted', 'gradingform_checklist') : implode('<br />', $errors);
            $html .= \core\output\html_writer::tag('div', $message, array(
                'class' => 'gradingform_checklist-error',
                'role' => 'alert',
            ));
            if ($this->has_observation_date_validation_error()) {
                $fieldid = $gradingformelement->getName().'-observation-date';
                $html .= \core\output\html_writer::tag('script',
                    "require(['jquery'], function($) { $('#'+".json_encode($fieldid).").focus(); });"
                );
            } else if (!empty($this->requiredcommenterrors)) {
                $requiredcommenterrors = $this->requiredcommenterrors;
                $fieldid = gradingform_checklist_controller::get_required_comment_error_field_id(
                    reset($requiredcommenterrors),
                    $gradingformelement->getName()
                );
                $html .= \core\output\html_writer::tag('script',
                    "require(['jquery'], function($) { $('#'+".json_encode($fieldid).").focus(); });"
                );
            }
        }
        $currentinstance = $this->get_current_instance();
        if ($currentinstance && $currentinstance->get_status() == gradingform_instance::INSTANCE_STATUS_NEEDUPDATE) {
            $html .= \core\output\html_writer::tag('div', get_string('needregrademessage', 'gradingform_checklist'), array('class' => 'gradingform_checklist-regrade'));
        }
        $haschanges = false;
        if ($currentinstance) {
            $curfilling = $currentinstance->get_checklist_filling();
            foreach ($curfilling['groups'] as $groupid => $group) {
                foreach ($group['items'] as $itemid => $item) {
                    // the saved checked status
                    $value['groups'][$groupid]['items'][$itemid]['savedchecked'] = !empty($item['checked']);
                    $newremark = null;
                    $newchecked = null;
                    if (isset($value['groups'][$groupid]['items'][$itemid]['remark'])) {
                        $newremark = $value['groups'][$groupid]['items'][$itemid]['remark'];
                    }
                    if (isset($value['groups'][$groupid]['items'][$itemid]['id'])) {
                        $newchecked = !empty($value['groups'][$groupid]['items'][$itemid]['id']);
                    }
                    if ($newchecked != !empty($item['checked']) || $newremark != $item['remark']) {
                        $haschanges = true;
                    }
                }
            }
        }
        if ($this->get_data('isrestored') && $haschanges) {
            $html .= \core\output\html_writer::tag('div', get_string('restoredfromdraft', 'gradingform_checklist'), array('class' => 'gradingform_checklist-restored'));
        }

        $html .= \core\output\html_writer::tag('div', $this->get_controller()->get_formatted_description(), array('class' => 'gradingform_checklist-description clearfix'));
        if ($mode != gradingform_checklist_controller::DISPLAY_VIEW) {
            $html .= $this->get_controller()->get_renderer($page)->display_benchmark_button(
                $this->get_controller()->get_formatted_benchmark()
            );
        }

        $html .= $this->get_controller()->get_renderer($page)->display_checklist($groups, $options, $mode, $gradingformelement->getName(), $value);
        return $html;
    }

}
