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
 * Support for backup API
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
class backup_gradingform_checklist_plugin extends backup_gradingform_plugin {

    /**
     * Declares checklist structures to append to the grading form definition
     * @return backup_plugin_element
     */
    protected function define_definition_plugin_structure() {

        // Append data only if the grand-parent element has 'method' set to 'checklist'
        $plugin = $this->get_plugin_element(null, '../../method', 'checklist');

        // Create a visible container for our data
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect our visible container to the parent
        $plugin->add_child($pluginwrapper);

        // Define our elements
        $groups = new backup_nested_element('groups');

        $group = new backup_nested_element('group', array('id'), array(
                'sortorder', 'description', 'descriptionformat'));

        $items = new backup_nested_element('items');

        $item = new backup_nested_element('item', array('id'), array('sortorder',
                'score', 'definition', 'definitionformat'));

        // Build elements hierarchy
        $pluginwrapper->add_child($groups);
        $groups->add_child($group);
        $group->add_child($items);
        $items->add_child($item);

        // Set sources to populate the data

        $group->set_source_table('gradingform_checklist_groups',
            array('definitionid' => backup::VAR_PARENTID));

        $item->set_source_table('gradingform_checklist_items',
            array('groupid' => backup::VAR_PARENTID));

        return $plugin;
    }

    /**
     * Declares checklist structures to append to the grading form instances
     * @return backup_plugin_element
     */
    protected function define_instance_plugin_structure() {

        // Append data only if the ancestor 'definition' element has 'method' set to 'checklist'
        $plugin = $this->get_plugin_element(null, '../../../../method', 'checklist');

        // Create a visible container for our data
        $pluginwrapper = new backup_nested_element($this->get_recommended_name());

        // Connect our visible container to the parent
        $plugin->add_child($pluginwrapper);

        // Define our elements

        $fillings = new backup_nested_element('fillings');

        $filling = new backup_nested_element('filling', array('id'), array(
            'groupid', 'itemid', 'checked', 'remark', 'remarkformat'));

        // Build elements hierarchy

        $pluginwrapper->add_child($fillings);
        $fillings->add_child($filling);

        // Set sources to populate the data

        $filling->set_source_table('gradingform_checklist_fills',
            array('instanceid' => backup::VAR_PARENTID));

        // no need to annotate ids or files yet (one day when remark field supports
        // embedded fileds, they must be annotated here)

        return $plugin;
    }
}