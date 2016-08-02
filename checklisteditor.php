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
 * @copyright  2011 Marina Glancy
 * @copyright  Copyright (c) 2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("HTML/QuickForm/input.php");

class MoodleQuickForm_checklisteditor extends HTML_QuickForm_input {
    /** help message */
    public $_helpbutton = '';
    /** stores the result of the last validation: null - undefined, false - no errors, string - error(s) text */
    protected $validationerrors = null;
    /** if element has already been validated **/
    protected $wasvalidated = false;
    /** If non-submit (JS) button was pressed: null - unknown, true/false - button was/wasn't pressed */
    protected $nonjsbuttonpressed = false;
    /** Message to display in front of the editor (that there exist grades on this checklist being edited) */
    protected $regradeconfirmation = false;

    function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $attributes);
    }

    /**
     * set html for help button
     *
     * @access public
     * @param array $helpbuttonargs array of arguments to make a help button
     * @param string $function function name to call to get html
     */
    public function setHelpButton($helpbuttonargs, $function='helpbutton'){
        debugging('component setHelpButton() is not used any more, please use $mform->setHelpButton() instead');
    }

    /**
     * get html for help button
     *
     * @access   public
     * @return  string html for help button
     */
    public function getHelpButton() {
        return $this->_helpbutton;
    }

    /**
     * The renderer will take care itself about different display in normal and frozen states
     *
     * @return string
     */
    public function getElementTemplateType() {
        return 'default';
    }

    /**
     * Specifies that confirmation about re-grading needs to be added to this checklist editor.
     * $changelevel is saved in $this->regradeconfirmation and retrieved in toHtml()
     *
     * @see gradingform_checklist_controller::update_or_check_checklist()
     * @param int $changelevel
     */
    public function add_regrade_confirmation($changelevel) {
        $this->regradeconfirmation = $changelevel;
    }

    /**
     * Returns html string to display this element
     *
     * @return string
     */
    public function toHtml() {
        global $PAGE;
        $html = $this->_getTabs();
        $renderer = $PAGE->get_renderer('gradingform_checklist');
        $data = $this->prepare_data(null, $this->wasvalidated);
        if (!$this->_flagFrozen) {
            $mode = gradingform_checklist_controller::DISPLAY_EDIT_FULL;
            $module = array('name'=>'gradingform_checklisteditor', 'fullpath'=>'/grade/grading/form/checklist/js/checklisteditor.js',
                'strings' => array(array('confirmdeletegroup', 'gradingform_checklist'), array('confirmdeleteitem', 'gradingform_checklist'),
                    array('groupempty', 'gradingform_checklist'), array('itemempty', 'gradingform_checklist')
                ));
            $PAGE->requires->js_init_call('M.gradingform_checklisteditor.init', array(
                    array('name' => $this->getName(),
                        'grouptemplate' => $renderer->group_template($mode, $data['options'], $this->getName()),
                        'itemtemplate' => $renderer->item_template($mode, $data['options'], $this->getName())
                    )),
                true, $module);
        } else {
            // Checklist is frozen, no javascript needed
            if ($this->_persistantFreeze) {
                $mode = gradingform_checklist_controller::DISPLAY_EDIT_FROZEN;
            } else {
                $mode = gradingform_checklist_controller::DISPLAY_PREVIEW;
            }
        }
        if ($this->regradeconfirmation) {
            if (!isset($data['regrade'])) {
                $data['regrade'] = 1;
            }
            $html .= $renderer->display_regrade_confirmation($this->getName(), $this->regradeconfirmation, $data['regrade']);
        }
        if ($this->validationerrors) {
            $html .= $renderer->notification($this->validationerrors, 'error');
        }
        $html .= $renderer->display_checklist($data['groups'], $data['options'], $mode, $this->getName());
        return $html;
    }

    /**
     * Prepares the data passed in $_POST:
     * - processes the pressed buttons 'additem', 'addgroup', 'moveup', 'movedown', 'delete' (when JavaScript is disabled)
     *   sets $this->nonjsbuttonpressed to true/false if such button was pressed
     * - if options not passed (i.e. we create a new checklist) fills the options array with the default values
     * - if options are passed completes the options array with unchecked checkboxes
     * - if $withvalidation is set, adds 'error_xxx' attributes to elements that contain errors and creates an error string
     *   and stores it in $this->validationerrors
     *
     * @param array $value
     * @param boolean $withvalidation whether to enable data validation
     * @return array
     */
    protected function prepare_data($value = null, $withvalidation = false) {
        if (null === $value) {
            $value = $this->getValue();
        }
        if ($this->nonjsbuttonpressed === null) {
            $this->nonjsbuttonpressed = false;
        }
        $totalscore = 0;
        $errors = array();
        $return = array('groups' => array(), 'options' => gradingform_checklist_controller::get_default_options());
        if (!isset($value['groups'])) {
            $value['groups'] = array();
            $errors['err_nogroups'] = 1;
        }
        // If options are present in $value, replace default values with submitted values
        if (!empty($value['options'])) {
            foreach (array_keys($return['options']) as $option) {
                // special treatment for checkboxes
                if (!empty($value['options'][$option])) {
                    $return['options'][$option] = $value['options'][$option];
                } else {
                    $return['options'][$option] = null;
                }
            }
        }
        if (is_array($value)) {
            // for other array keys of $value no special treatmeant neeeded, copy them to return value as is
            foreach (array_keys($value) as $key) {
                if ($key != 'options' && $key != 'groups') {
                    $return[$key] = $value[$key];
                }
            }
        }

        // iterate through groups
        $lastaction = null;
        $lastid = null;
        foreach ($value['groups'] as $id => $group) {
            if ($id == 'addgroup') {
                $id = $this->get_next_id(array_keys($value['groups']));
                $group = array('description' => '', 'items' => array());
                $i = 0;

                // score is 1 by default
                $group['items']['NEWID'.($i++)]['score'] = 1;

                // add more items so there are at least 3 in the new group. Score is 1 by default
                for ($i= $i; $i < 3; $i++) {
                    $group['items']['NEWID'.$i]['score'] = 1;
                }
                // set other necessary fields (definition) for the items in the new group
                foreach (array_keys($group['items']) as $i) {
                    $group['items'][$i]['definition'] = '';
                }
                $this->nonjsbuttonpressed = true;
            }
            $items = array();
            $maxscore = null;
            if (array_key_exists('items', $group)) {
                foreach ($group['items'] as $itemid => $item) {
                    if ($itemid == 'additem') {
                        $itemid = $this->get_next_id(array_keys($group['items']));
                        $item = array(
                            'definition' => '',
                            'score' => 1,
                        );
                        $this->nonjsbuttonpressed = true;
                    }
                    if (!array_key_exists('delete', $item)) {
                        if ($withvalidation) {
                            $deflength = strlen(trim(clean_param($item['definition'], PARAM_TEXT)));
                            if (!$deflength) {
                                $errors['err_nodefinition'] = 1;
                                $item['error_definition'] = true;
                            } else if ($deflength > 255) {
                                $errors['err_definitionmax'] = 1;
                                $item['error_definition'] = true;
                            }
                            if (!preg_match('#^[\+]?\d*$#', trim($item['score'])) && !preg_match('#^[\+]?\d*[\.,]\d+$#', trim($item['score']))) {
                                // TODO why we can't allow negative score for checklist?
                                $errors['err_scoreformat'] = 1;
                                $item['error_score'] = true;
                            } else if ($item['score'] > 1000) {
                                $errors['err_scoremax'] = 1;
                                $item['error_score'] = true;
                            }
                        }
                        $items[$itemid] = $item;
                        if ($maxscore === null || (float)$item['score'] > $maxscore) {
                            $maxscore = (float)$item['score'];
                        }
                    } else {
                        $this->nonjsbuttonpressed = true;
                    }
                }

                //sortorder for items
                $itemsortorder = 1;
                foreach (array_keys($items) as $itemid) {
                    $items[$itemid]['sortorder'] = $itemsortorder++;
                }
            }
            $totalscore += (float)$maxscore;
            $group['items'] = $items;
            if ($withvalidation && !array_key_exists('delete', $group)) {
                if (count($items) < 1) {
                    $errors['err_minoneitems'] = 1;
                    $group['error_items'] = true;
                }
                $descriptionlen = strlen(trim(clean_param($group['description'], PARAM_TEXT)));
                if (!$descriptionlen) {
                    $errors['err_nodescription'] = 1;
                    $group['error_description'] = true;
                } else if ($descriptionlen > 255) {
                    $errors['err_descriptionmax'] = 1;
                    $group['error_description'] = true;
                }
            }
            if (array_key_exists('moveup', $group) || $lastaction == 'movedown') {
                unset($group['moveup']);
                if ($lastid !== null) {
                    $lastgroup = $return['groups'][$lastid];
                    unset($return['groups'][$lastid]);
                    $return['groups'][$id] = $group;
                    $return['groups'][$lastid] = $lastgroup;
                } else {
                    $return['groups'][$id] = $group;
                }
                $lastaction = null;
                $lastid = $id;
                $this->nonjsbuttonpressed = true;
            } else if (array_key_exists('delete', $group)) {
                $this->nonjsbuttonpressed = true;
            } else {
                if (array_key_exists('movedown', $group)) {
                    unset($group['movedown']);
                    $lastaction = 'movedown';
                    $this->nonjsbuttonpressed = true;
                }
                $return['groups'][$id] = $group;
                $lastid = $id;
            }
        }

        if ($totalscore <= 0) {
            $errors['err_totalscore'] = 1;
        }

        // add sort order field to groups
        $csortorder = 1;
        foreach (array_keys($return['groups']) as $id) {
            $return['groups'][$id]['sortorder'] = $csortorder++;
        }

        // create validation error string (if needed)
        if ($withvalidation) {
            if (count($errors)) {
                $rv = array();
                foreach ($errors as $error => $v) {
                    $rv[] = get_string($error, 'gradingform_checklist');
                }
                $this->validationerrors = implode('<br/ >', $rv);
            } else {
                $this->validationerrors = false;
            }
            $this->wasvalidated = true;
        }
        return $return;
    }

    /**
     * Scans array $ids to find the biggest element ! NEWID*, increments it by 1 and returns
     *
     * @param array $ids
     * @return string
     */
    protected function get_next_id($ids) {
        $maxid = 0;
        foreach ($ids as $id) {
            if (preg_match('/^NEWID(\d+)$/', $id, $matches) && ((int)$matches[1]) > $maxid) {
                $maxid = (int)$matches[1];
            }
        }
        return 'NEWID'.($maxid+1);
    }


    /**
     * Checks if a submit button was pressed which is supposed to be processed on client side by JS
     * but user seem to have disabled JS in the browser.
     * (buttons 'add groups', 'add item', 'move up', 'move down', etc.)
     * In this case the form containing this element is prevented from being submitted
     *
     * @param array $value
     * @return boolean true if non-submit button was pressed and not processed by JS
     */
    public function non_js_button_pressed($value) {
        if ($this->nonjsbuttonpressed === null) {
            $this->prepare_data($value);
        }
        return $this->nonjsbuttonpressed;
    }

    /**
     * Validates that checklist has at least one group, at least one item within one group,
     * each item has a valid score, all items have filled definitions and all groups
     * have filled descriptions
     *
     * @param array $value
     * @return string|false error text or false if no errors found
     */
    public function validate($value) {
        if (!$this->wasvalidated) {
            $this->prepare_data($value, true);
        }
        return $this->validationerrors;
    }

    /**
     * Prepares the data for saving
     * @see prepare_data()
     *
     * @param array $submitValues
     * @param boolean $assoc
     * @return array
     */
    public function exportValue(&$submitValues, $assoc = false) {
        $value =  $this->prepare_data($this->_findValue($submitValues));
        return $this->_prepareValue($value, $assoc);
    }
}