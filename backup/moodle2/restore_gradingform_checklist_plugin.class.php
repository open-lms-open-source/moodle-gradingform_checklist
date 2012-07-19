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
 * Support for restore API
 *
 * @package    gradingform
 * @subpackage checklist
 * @author     Sam Chaffee
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Defines checklist backup structures
 */
class restore_gradingform_checklist_plugin extends restore_gradingform_plugin {
    /**
     * Declares the checklist XML paths attached to the form definition element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_definition_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_checklist_group',
            $this->get_pathfor('/groups/group'));

        $paths[] = new restore_path_element('gradingform_checklist_item',
            $this->get_pathfor('/groups/group/items/item'));

        return $paths;
    }

    /**
     * Declares the checklist XML paths attached to the form instance element
     *
     * @return array of {@link restore_path_element}
     */
    protected function define_instance_plugin_structure() {

        $paths = array();

        $paths[] = new restore_path_element('gradingform_checklist_filling',
            $this->get_pathfor('/fillings/filling'));

        return $paths;
    }

    /**
     * Processes group element data
     *
     * Sets the mapping 'gradingform_checklist_group' to be used later by
     * {@link self::process_gradinform_checklist_filling()}
     *
     * @param stdClass $data
     */
    public function process_gradingform_checklist_group($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->definitionid = $this->get_new_parentid('grading_definition');

        $newid = $DB->insert_record('gradingform_checklist_groups', $data);
        $this->set_mapping('gradingform_checklist_group', $oldid, $newid);
    }

    /**
     * Processes item element data
     *
     * Sets the mapping 'gradingform_checklist_item' to be used later by
     * {@link self::process_gradinform_checklist_filling()}
     *
     * @param stdClass $data
     */
    public function process_gradingform_checklist_item($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->groupid = $this->get_new_parentid('gradingform_checklist_group');

        $newid = $DB->insert_record('gradingform_checklist_items', $data);
        $this->set_mapping('gradingform_checklist_item', $oldid, $newid);
    }

    /**
     * Processes filling element data
     *
     * @param stdClass $data
     */
    public function process_gradingform_checklist_filling($data) {
        global $DB;

        $data = (object)$data;
        $data->instanceid = $this->get_new_parentid('grading_instance');
        $data->groupid = $this->get_mappingid('gradingform_checklist_group', $data->groupid);
        $data->itemid = $this->get_mappingid('gradingform_checklist_item', $data->itemid);

        $DB->insert_record('gradingform_checklist_fills', $data);
    }
}