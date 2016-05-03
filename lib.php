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
 * @package    gradingform
 * @subpackage checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/grade/grading/form/lib.php');

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
     * @throws coding_exception
     * @param moodle_page $page the target page
     * @return string
     */
    public function render_preview(moodle_page $page) {
        if (!$this->is_form_defined()) {
            throw new coding_exception('It is the caller\'s responsibility to make sure that the form is actually defined');
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
     * Deletes the checklist definition and all the associated information
     */
    protected function delete_plugin_definition() {
        global $DB;

        // get the list of instances
        $instances = array_keys($DB->get_records('grading_instances', array('definitionid' => $this->definition->id), '', 'id'));
        // delete all fillings
        $DB->delete_records_list('gradingform_checklist_fills', 'instanceid', $instances);
        // delete instances
        $DB->delete_records_list('grading_instances', 'id', $instances);
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
            null, null, new pix_icon('icon', '', 'gradingform_checklist'));
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
                new moodle_url('/grade/grading/form/'.$this->get_method_name().'/preview.php', array('areaid' => $this->get_areaid())),
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
        $newdefinition->options = json_encode($newdefinition->checklist['options']);
        $editoroptions = self::description_form_field_options($this->get_context());
        $newdefinition = file_postupdate_standard_editor($newdefinition, 'description', $editoroptions, $this->get_context(),
            'grading', 'description', $this->definition->id);

        // reload the definition from the database
        $currentdefinition = $this->get_definition(true);

        // update checklist data
        $haschanges = array();
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
                            $group[$key] = trim(clean_param($group[$key], PARAM_TEXT));
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
                        $group[$key] = trim(clean_param($group[$key], PARAM_TEXT));
                    }
                    if (array_key_exists($key, $group) && $group[$key] != $currentgroups[$id][$key]) {
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
                                $item[$key] = trim(clean_param($item[$key], PARAM_TEXT));
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
                            $item[$key] = trim(clean_param($item[$key], PARAM_TEXT));
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
                       clg.id AS clgid, clg.sortorder AS clgsortorder, clg.description AS clgdescription,
                       cli.id AS cliid, cli.score AS cliscore, cli.sortorder AS clisortorder, cli.definition AS clidefinition
                  FROM {grading_definitions} gd
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
            'showitempointseval' => 1,
            'showitempointstudent' => 1,
            'enableitemremarks' => 1,
            'enablegroupremarks' => 1,
            'showremarksstudent' => 1
        );
        return $options;
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
}

/**
 * Class to manage one checklist grading instance. Stores information and performs actions like
 * update, copy, validate, submit, etc.
 *
 * @copyright  2011 Marina Glancy
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 */
class gradingform_checklist_instance extends gradingform_instance {

    protected $checklist;

    /**
     * Deletes this (INCOMPLETE) instance from database.
     */
    public function cancel() {
        global $DB;

        parent::cancel();
        $DB->delete_records('gradingform_checklist_fills', array('instanceid' => $this->get_id()));
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
        }
        return $this->checklist;
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

        foreach ($data['groups'] as $groupid => $group) {
            foreach($group['items'] as $itemid => $record) {
                //handle deletions later
                if (empty($record['remark']) && empty($record['id'])) {
                    continue;
                }
                if (!array_key_exists($groupid, $currentgrade['groups']) || !array_key_exists($itemid, $currentgrade['groups'][$groupid]['items'])) {
                    $newrecord = array('instanceid' => $this->get_id(), 'groupid' => $groupid,
                        'itemid' => $itemid, 'checked' => !empty($record['id']), 'remarkformat' => FORMAT_MOODLE);
                    if (isset($record['remark'])) {
                        $newrecord['remark'] = clean_param($record['remark'], PARAM_TEXT);
                    }
                    $DB->insert_record('gradingform_checklist_fills', $newrecord);
                } else {
                    $newrecord = array('id' => $currentgrade['groups'][$groupid]['items'][$itemid]['id']);
                    foreach (array('remark'/*, 'remarkformat' TODO */) as $key) {
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
     * Calculates the grade to be pushed to the gradebook
     *
     * @return int the valid grade from $this->get_controller()->get_grade_range()
     */
    public function get_grade() {
        $grade = $this->get_checklist_filling();

        if (!($scores = $this->get_controller()->get_min_max_score()) || $scores['maxscore'] <= $scores['minscore']) {
            return -1;
        }

        $graderange = array_keys($this->get_controller()->get_grade_range());
        if (empty($graderange)) {
            return -1;
        }
        sort($graderange);
        $mingrade = $graderange[0];
        $maxgrade = $graderange[sizeof($graderange) - 1];

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
            $module = array('name'=>'gradingform_checklist', 'fullpath'=>'/grade/grading/form/checklist/js/checklist.js');
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
        $html = '';
        if ($value === null) {
            $value = $this->get_checklist_filling();
        } else if (!$this->validate_grading_element($value)) {
            $html .= html_writer::tag('div', get_string('checklistnotcompleted', 'gradingform_checklist'), array('class' => 'gradingform_checklist-error'));
        }
        $currentinstance = $this->get_current_instance();
        if ($currentinstance && $currentinstance->get_status() == gradingform_instance::INSTANCE_STATUS_NEEDUPDATE) {
            $html .= html_writer::tag('div', get_string('needregrademessage', 'gradingform_checklist'), array('class' => 'gradingform_checklist-regrade'));
        }
        $haschanges = false;
        if ($currentinstance) {
            $curfilling = $currentinstance->get_checklist_filling();
            foreach ($curfilling['groups'] as $groupid => $group) {
                foreach ($group['items'] as $itemid => $item)
                    // the saved checked status
                    $value['groups'][$groupid]['items'][$itemid]['savedchecked'] = !empty($item['checked']);
                    $newremark = null;
                    $newchecked = null;
                    if (isset($value['groups'][$groupid]['items'][$itemid]['remark'])) $newremark = $value['groups'][$groupid]['items'][$itemid]['remark'];
                    if (isset($value['groups'][$groupid]['items'][$itemid]['id'])) $newchecked = !empty($value['groups'][$groupid]['items'][$itemid]['id']);
                    if ($newchecked != !empty($item['checked']) || $newremark != $item['remark']) {
                        $haschanges = true;
                }
            }
        }
        if ($this->get_data('isrestored') && $haschanges) {
            $html .= html_writer::tag('div', get_string('restoredfromdraft', 'gradingform_checklist'), array('class' => 'gradingform_checklist-restored'));
        }

        $html .= html_writer::tag('div', $this->get_controller()->get_formatted_description(), array('class' => 'gradingform_checklist-description clearfix'));

        $html .= $this->get_controller()->get_renderer($page)->display_checklist($groups, $options, $mode, $gradingformelement->getName(), $value);
        return $html;
    }

}