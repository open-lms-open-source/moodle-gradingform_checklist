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
 * Renderer for the Checklist plugin
 *
 * @package    gradingform
 * @subpackage checklist
 * @author     Sam Chaffee
 * @copyright  2011 Marina Glancy
 * @copyright  2012 Moodlerooms Inc. (http://www.moodlerooms.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checklist grading method plugin renderer
 *
 */
class gradingform_checklist_renderer extends plugin_renderer_base {
    /**
     * This function returns html code for displaying group. Depending on $mode it may be the
     * code to edit checklist, to preview the checklist, to evaluate somebody or to review the evaluation.
     *
     * This function may be called from display_checklist() to display the whole checklist, or it can be
     * called by itself to return a template used by JavaScript to add new empty groups to the
     * checklist being designed.
     * In this case it will use macros like {NAME}, {ITEMS}, {GROUP-id}, etc.
     *
     * When overriding this function it is very important to remember that all elements of html
     * form (in edit or evaluate mode) must have the name $elementname.
     *
     * Also JavaScript relies on the class names of elements and when developer changes them
     * script might stop working.
     *
     * @param int $mode checklist display mode @see gradingform_checklist_controller
     * @param array $options
     * @param string $elementname the name of the form element (in editor mode) or the prefix for div ids (in view mode)
     * @param array|null $group group data
     * @param string $itemsstr evaluated templates for this group items
     * @param array|null $gvalue (only in view mode) teacher's feedback on this group
     * @return string
     */
    public function group_template($mode, $options, $elementname = '{NAME}', $group = null, $itemsstr = '{ITEMS}', $gvalue = null) {
        // TODO description format, remark format
        if ($group === null || !is_array($group) || !array_key_exists('id', $group)) {
            $group = array('id' => '{GROUP-id}', 'description' => '{GROUP-description}', 'sortorder' => '{GROUP-sortorder}', 'class' => '{GROUP-class}');
        } else {
            foreach (array('sortorder', 'description', 'class') as $key) {
                // set missing array elements to empty strings to avoid warnings
                if (!array_key_exists($key, $group)) {
                    $group[$key] = '';
                }
            }
        }
        $grouptemplate = html_writer::start_tag('div', array('class' => 'group'. $group['class'], 'id' => '{NAME}-groups-{GROUP-id}'));
        $controls = '';
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $controls .= html_writer::start_tag('div', array('class' => 'controls'));
            foreach (array('moveup', 'delete', 'movedown') as $key) {
                $value = get_string('group'.$key, 'gradingform_checklist');
                $labelforctrl = html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-'.$key));
                $button = $labelforctrl . html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][{GROUP-id}]['.$key.']',
                    'id' => '{NAME}-groups-{GROUP-id}-'.$key, 'value' => $value, 'title' => $value, 'tabindex' => -1));
                $controls .= html_writer::tag('div', $button, array('class' => $key));
            }
            $controls .= html_writer::end_tag('div'); // .controls
            $grouptemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][sortorder]', 'value' => $group['sortorder']));
            $labelfordesc = html_writer::tag('label', get_string('groupdescription', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-description-input'));
            $description = $labelfordesc . html_writer::empty_tag('input', array('type' => 'text', 'id' => '{NAME}-groups-{GROUP-id}-description-input',
                    'name' => '{NAME}[groups][{GROUP-id}][description]', 'value' => $group['description']));
        } else {
            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN) {
                $grouptemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][sortorder]', 'value' => $group['sortorder']));
                $grouptemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][description]', 'value' => $group['description']));
            }
            $description = $group['description'];
        }
        $descriptionclass = 'description';
        if (isset($group['error_description'])) {
            $descriptionclass .= ' error';
        }
        $grouptemplate .= html_writer::tag('div', $description, array('class' => $descriptionclass, 'id' => '{NAME}-groups-{GROUP-id}-description'));
        $grouptemplate .= $controls;
        $itemsstrdiv = html_writer::tag('div', html_writer::tag('div', $itemsstr, array('id' => '{NAME}-groups-{GROUP-id}-items')));
        $itemsclass = 'items';
        if (isset($group['error_items'])) {
            $itemsclass .= ' error';
        }
        $grouptemplate .= html_writer::tag('div', $itemsstrdiv, array('class' => $itemsclass));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('groupadditem', 'gradingform_checklist');
            $labelforadditem = html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-additem'));
            $button = $labelforadditem . html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][{GROUP-id}][items][additem]',
                    'id' => '{NAME}-groups-{GROUP-id}-items-additem', 'value' => $value, 'title' => $value));
            $grouptemplate .= html_writer::tag('div', $button, array('class' => 'additem'));
        }
        $displayremark = ($options['enablegroupremarks'] && ($mode != gradingform_checklist_controller::DISPLAY_VIEW || $options['showremarksstudent']));
        if ($displayremark) {
            $currentremark = '';
            if (isset($gvalue['items'][0]['remark'])) {
                $currentremark = $gvalue['items'][0]['remark'];
            }
            if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
                $labelforremark = html_writer::tag('label', get_string('groupremark', 'gradingform_checklist', $group['description']),
                        array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-0-remark'));
                $input = $labelforremark . html_writer::tag('textarea', htmlspecialchars($currentremark),
                        array('id' => '{NAME}-groups-{GROUP-id}-items-0-remark', 'name' => '{NAME}[groups][{GROUP-id}][items][0][remark]', 'cols' => '10', 'rows' => '5'));
                $grouptemplate .= html_writer::tag('div', $input, array('class' => 'remark'));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN) {
                $grouptemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][0][remark]', 'value' => $currentremark));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
                $feedbackstr = empty($currentremark) ? '' : html_writer::tag('span', get_string('groupfeedback', 'gradingform_checklist', $group['description']) . ': ', array('class' => 'checklistfeedback'));
                $grouptemplate .= html_writer::tag('div', $feedbackstr . $currentremark, array('class' => 'remark')); // TODO maybe some prefix here like 'Teacher remark:'
            }
        }

        $displaypointseval = $options['showitempointseval'] && ($mode == gradingform_checklist_controller::DISPLAY_EVAL
                || $mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_REVIEW);
        $displaypointsrev  = $options['showitempointstudent'] && ($mode == gradingform_checklist_controller::DISPLAY_VIEW);

        if ($displaypointseval || $displaypointsrev) {
            // tally the checked pts and total pts
            $checkedpts = 0;
            $totalpts   = 0;
            foreach ($group['items'] as $itemid => $item) {
                $totalpts += $item['score'];
                if (!empty($gvalue['items'][$itemid]['checked'])) {
                    $checkedpts += $item['score'];
                }
            }
            $checkedpts = html_writer::tag('span', $checkedpts, array('class' => 'scoredpoints'));
            $totalpts   = html_writer::tag('span', $totalpts, array('class' => 'outofpoints'));

            // add to the template
            $grouptemplate .= html_writer::tag('div', get_string('grouppoints', 'gradingform_checklist') . ": $checkedpts/$totalpts", array('class' => 'pointstotals'));
        }
        $grouptemplate .= html_writer::end_tag('div'); // .group

        $grouptemplate = str_replace('{NAME}', $elementname, $grouptemplate);
        $grouptemplate = str_replace('{GROUP-id}', $group['id'], $grouptemplate);
        return $grouptemplate;
    }

    /**
     * This function returns html code for displaying one item of one group. Depending on $mode
     * it may be the code to edit checklist, to preview the checklist, to evaluate somebody or to review the evaluation.
     *
     * This function may be called from display_checklist() to display the whole checklist, or it can be
     * called by itself to return a template used by JavaScript to add new empty item to the
     * group during the design of checklist.
     * In this case it will use macros like {NAME}, {GROUP-id}, {ITEM-id}, etc.
     *
     * When overriding this function it is very important to remember that all elements of html
     * form (in edit or evaluate mode) must have the name $elementname.
     *
     * Also JavaScript relies on the class names of elements and when developer changes them
     * script might stop working.
     *
     * @param int $mode checklist display mode @see gradingform_checklist_controller
     * @param array $options
     * @param string $elementname the name of the form element (in editor mode) or the prefix for div ids (in view mode)
     * @param string|int $groupid either id of the nesting group or a macro for template
     * @param array|null $item item data, also in view mode it might also have property $item['checked'] whether this item is checked
     * @return string
     */
    public function item_template($mode, $options, $elementname = '{NAME}', $groupid = '{GROUP-id}', $item = null) {
        // TODO definition format
        if (!isset($item['id'])) {
            $item = array('id' => '{ITEM-id}', 'definition' => '{ITEM-definition}', 'score' => '{ITEM-score}', 'class' => '{ITEM-class}', 'sortorder' => '{ITEM-sortorder}', 'checked' => false);
        } else {
            foreach (array('score', 'definition', 'class', 'checked') as $key) {
                // set missing array elements to empty strings to avoid warnings
                if (!array_key_exists($key, $item)) {
                    $item[$key] = '';
                }
            }
        }

        // Template for one item within one group
        $divattributes = array('id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}', 'class' => 'item'. $item['class']);

        $itemtemplate = html_writer::start_tag('div', $divattributes);
        $itemtemplate .= html_writer::start_tag('div', array('class' => 'item-wrapper'));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $labelfordef = html_writer::tag('label', get_string('itemdefinition', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition-input'));
            $definition = $labelfordef . html_writer::empty_tag('input', array('type' => 'text',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition-input',
                    'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][definition]', 'value' => $item['definition']));

            $labelforscore = html_writer::tag('label', get_string('itemscore', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score-input'));
            $score = $labelforscore . html_writer::empty_tag('input', array('type' => 'text', 'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score-input',
                    'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][score]', 'size' => '3', 'value' => $item['score']));

            $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][sortorder]', 'value' => $item['sortorder']));
        } else {
            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN) {
                $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][definition]', 'value' => $item['definition']));
                $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][score]', 'value' => $item['score']));
                $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][sortorder]', 'value' => $item['sortorder']));
            }
            $definition = $item['definition'];
            $score = $item['score'];
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
            $labelforcheckitem = html_writer::tag('label', get_string('checkitem', 'gradingform_checklist', $item['definition']),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-id-input'));
            $input = $labelforcheckitem . html_writer::empty_tag('input', array('type' => 'checkbox',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-id-input', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][id]',
                    'value' => $item['id']) + ($item['checked'] ? array('checked' => 'checked') : array()));
            $itemtemplate .= html_writer::tag('div', $input, array('class' => 'checkbox'));
        } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
            if (empty($item['checked'])) {
                $iconname = 'i/grade_incorrect';
                $alttext  = get_string('unchecked', 'gradingform_checklist');
            } else {
                $iconname = 'i/grade_correct';
                $alttext  = get_string('checked', 'gradingform_checklist');
            }
            $itemtemplate .= html_writer::tag('div', $this->output->pix_icon($iconname, $alttext), array('class' => 'checkbox'));
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN && $item['checked']) {
            $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][id]', 'value' => $item['id']));
        }

        if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
            $realscore = !empty($item['checked']) ? $item['score'] : '0';
            $score = $realscore . '/' . $score;
        }
        $score = html_writer::tag('span', $score, array('id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score', 'class' => 'scorevalue'));
        $definitionclass = 'definition';
        if (isset($item['error_definition'])) {
            $definitionclass .= ' error';
        }
        $itemtemplate .= html_writer::tag('div', $definition, array('class' => $definitionclass, 'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition'));
        $displayscore = true;
        if (!$options['showitempointseval'] && in_array($mode, array(gradingform_checklist_controller::DISPLAY_EVAL, gradingform_checklist_controller::DISPLAY_EVAL_FROZEN, gradingform_checklist_controller::DISPLAY_REVIEW))) {
            $displayscore = false;
        }
        if (!$options['showitempointstudent'] && in_array($mode, array(gradingform_checklist_controller::DISPLAY_VIEW, gradingform_checklist_controller::DISPLAY_PREVIEW_GRADED))) {
            $displayscore = false;
        }
        if ($displayscore) {
            $scoreclass = 'score';
            if (isset($item['error_score'])) {
                $scoreclass .= ' error';
            }
            $itemtemplate .= html_writer::tag('div', get_string('scorepostfix', 'gradingform_checklist', $score), array('class' => $scoreclass));
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('itemdelete', 'gradingform_checklist');
            $labelfordelete = html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-delete'));
            $button = $labelfordelete . html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][delete]',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-delete', 'value' => $value, 'title' => $value, 'tabindex' => -1));
            $itemtemplate .= html_writer::tag('div', $button, array('class' => 'delete'));
        }
        $displayremark = ($options['enableitemremarks'] && ($mode != gradingform_checklist_controller::DISPLAY_VIEW || $options['showremarksstudent']));
        if ($displayremark) {
            $currentremark = '';
            if (isset($item['remark'])) {
                $currentremark = $item['remark'];
            }
            if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
                $labelforremark = html_writer::tag('label', get_string('itemremark', 'gradingform_checklist', $item['definition']),
                        array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-remark-input'));
                $input = $labelforremark . html_writer::tag('textarea', htmlspecialchars($currentremark), array('id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-remark-input',
                        'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][remark]', 'cols' => '20', 'rows' => '3'));
                $itemtemplate .= html_writer::tag('div', $input, array('class' => 'remark'));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN) {
                $itemtemplate .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][remark]', 'value' => $currentremark));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
                $feedbackstr = empty($currentremark) ? '' : html_writer::tag('span', get_string('itemfeedback', 'gradingform_checklist', $item['definition']) . ': ', array('class' => 'checklistfeedback'));
                $itemtemplate .= html_writer::tag('div', $feedbackstr . $currentremark, array('class' => 'remark')); // TODO maybe some prefix here like 'Teacher remark:'
            }
        }
        $itemtemplate .= html_writer::end_tag('div'); // .item-wrapper
        $itemtemplate .= html_writer::end_tag('div'); // .item

        $itemtemplate = str_replace('{NAME}', $elementname, $itemtemplate);
        $itemtemplate = str_replace('{GROUP-id}', $groupid, $itemtemplate);
        $itemtemplate = str_replace('{ITEM-id}', $item['id'], $itemtemplate);
        return $itemtemplate;
    }

    /**
     * This function returns html code for displaying checklist template (content before and after
     * groups list). Depending on $mode it may be the code to edit checklist, to preview the checklist,
     * to evaluate somebody or to review the evaluation.
     *
     * This function is called from display_checklist() to display the whole checklist.
     *
     * When overriding this function it is very important to remember that all elements of html
     * form (in edit or evaluate mode) must have the name $elementname.
     *
     * Also JavaScript relies on the class names of elements and when developer changes them
     * script might stop working.
     *
     * @param int $mode checklist display mode @see gradingform_checklist_controller
     * @param array $options
     * @param string $elementname the name of the form element (in editor mode) or the prefix for div ids (in view mode)
     * @param string $groupsstr evaluated templates for this checklist's groups
     * @param string $totalpointsstr the total points string
     * @return string
     */
    protected function checklist_template($mode, $options, $elementname, $groupsstr, $totalpointsstr) {
        $classsuffix = ''; // CSS suffix for class of the main div. Depends on the mode
        switch ($mode) {
            case gradingform_checklist_controller::DISPLAY_EDIT_FULL:
                $classsuffix = ' editor editable'; break;
            case gradingform_checklist_controller::DISPLAY_EDIT_FROZEN:
                $classsuffix = ' editor frozen';  break;
            case gradingform_checklist_controller::DISPLAY_PREVIEW:
            case gradingform_checklist_controller::DISPLAY_PREVIEW_GRADED:
                $classsuffix = ' editor preview';  break;
            case gradingform_checklist_controller::DISPLAY_EVAL:
                $classsuffix = ' evaluate editable'; break;
            case gradingform_checklist_controller::DISPLAY_EVAL_FROZEN:
                $classsuffix = ' evaluate frozen';  break;
            case gradingform_checklist_controller::DISPLAY_REVIEW:
                $classsuffix = ' review';  break;
            case gradingform_checklist_controller::DISPLAY_VIEW:
                $classsuffix = ' view';  break;
        }

        $checklisttemplate = html_writer::start_tag('div', array('id' => 'checklist-{NAME}', 'class' => 'clearfix gradingform_checklist'.$classsuffix));
        $checklisttemplate .= html_writer::tag('div', $groupsstr, array('class' => 'groups', 'id' => '{NAME}-groups'));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('addgroup', 'gradingform_checklist');
            $labelforaddgroup = html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-addgroup'));
            $input = $labelforaddgroup . html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][addgroup]',
                    'id' => '{NAME}-groups-addgroup', 'value' => $value, 'title' => $value));
            $checklisttemplate .= html_writer::tag('div', $input, array('class' => 'addgroup'));
        }
        $checklisttemplate .= $totalpointsstr;
        $checklisttemplate .= $this->checklist_edit_options($mode, $options);
        $checklisttemplate .= html_writer::end_tag('div');

        return str_replace('{NAME}', $elementname, $checklisttemplate);
    }

    /**
     * Generates html template to view/edit the checklist options. Expression {NAME} is used in
     * template for the form element name
     *
     * @param int $mode
     * @param array $options
     * @return string
     */
    protected function checklist_edit_options($mode, $options) {
        if ($mode != gradingform_checklist_controller::DISPLAY_EDIT_FULL
            && $mode != gradingform_checklist_controller::DISPLAY_EDIT_FROZEN
            && $mode != gradingform_checklist_controller::DISPLAY_PREVIEW) {
            // Options are displayed only for people who can manage
            return '';
        }
        $html = html_writer::start_tag('div', array('class' => 'options'));
        $html .= html_writer::tag('div', get_string('checklistoptions', 'gradingform_checklist'), array('class' => 'optionsheading'));
        $attrs = array('type' => 'hidden', 'name' => '{NAME}[options][optionsset]', 'value' => 1);
        foreach ($options as $option => $value) {
            $html .= html_writer::start_tag('div', array('class' => 'option '.$option));
            $attrs = array('name' => '{NAME}[options]['.$option.']', 'id' => '{NAME}-options-'.$option);

            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN && $value) {
                $html .= html_writer::empty_tag('input', $attrs + array('type' => 'hidden', 'value' => $value));
            }
            // Display option as checkbox
            $attrs['type'] = 'checkbox';
            $attrs['value'] = 1;
            if ($value) {
                $attrs['checked'] = 'checked';
            }
            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_PREVIEW) {
                $attrs['disabled'] = 'disabled';
                unset($attrs['name']);
            }
            $html .= html_writer::empty_tag('input', $attrs);
            $html .= html_writer::tag('label', get_string($option, 'gradingform_checklist'), array('for' => $attrs['id']));

            $html .= html_writer::end_tag('div'); // .option
        }
        $html .= html_writer::end_tag('div'); // .options
        return $html;
    }

    /**
     * This function returns html code for displaying checklist. Depending on $mode it may be the code
     * to edit checklist, to preview the checklist, to evaluate somebody or to review the evaluation.
     *
     * It is very unlikely that this function needs to be overriden by theme. It does not produce
     * any html code, it just prepares data about checklist design and evaluation, adds the CSS
     * class to elements and calls the functions item_template, group_template and
     * checklist_template
     *
     * @param array $groups data about the checklist design
     * @param array $options
     * @param int $mode checklist display mode @see gradingform_checklist_controller
     * @param string $elementname the name of the form element (in editor mode) or the prefix for div ids (in view mode)
     * @param array $values evaluation result
     * @return string
     */
    public function display_checklist($groups, $options, $mode, $elementname = null, $values = null) {
        $groupsstr = '';
        $totalpointsstr = '';
        $totalpoints = 0;
        $scoredpoints = 0;
        $cnt = 0;
        foreach ($groups as $id => $group) {
            $group['class'] = $this->get_css_class_suffix($cnt++, sizeof($groups) -1);
            $group['id'] = $id;
            $itemsstr = '';
            $itemcnt = 0;
            if (isset($values['groups'][$id])) {
                $groupvalue = $values['groups'][$id];
            } else {
                $groupvalue = null;
            }
            foreach ($group['items'] as $itemid => $item) {
                $item['id'] = $itemid;
                $item['class'] = $this->get_css_class_suffix($itemcnt++, sizeof($group['items']) -1);
                $item['checked'] = !empty($groupvalue['items'][$itemid]['checked']);
                if ($item['checked'] && ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW)) {
                    $item['class'] .= ' checked';
                    //in mode DISPLAY_EVAL the class 'checked' will be added by JS if it is enabled. If JS is not enabled, the 'checked' class will only confuse
                }
                if (!empty($groupvalue['items'][$itemid]['savedchecked'])) {
                    $item['class'] .= ' currentchecked';
                }
                if (!empty($groupvalue['items'][$itemid]['remark'])) {
                    $item['remark'] = $groupvalue['items'][$itemid]['remark'];
                }
                $itemsstr .= $this->item_template($mode, $options, $elementname, $id, $item);

                // tally for total and scored points
                $totalpoints += $item['score'];
                if (!empty($groupvalue['items'][$itemid]['checked'])) {
                    $scoredpoints += $item['score'];
                }
            }
            $groupsstr .= $this->group_template($mode, $options, $elementname, $group, $itemsstr, $groupvalue);
        }

        $displaypointseval = $options['showitempointseval'] && ($mode == gradingform_checklist_controller::DISPLAY_EVAL
            || $mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_REVIEW);
        $displaypointsrev  = $options['showitempointstudent'] && ($mode == gradingform_checklist_controller::DISPLAY_VIEW);

        if ($displaypointseval || $displaypointsrev) {
            $checkedpts = html_writer::tag('span', $scoredpoints, array('class' => 'scoredpoints'));
            $totalpts   = html_writer::tag('span', $totalpoints, array('class' => 'outofpoints'));

            // add to the template
            $totalpointsstr = html_writer::tag('div', get_string('overallpoints', 'gradingform_checklist') . ": $checkedpts/$totalpts", array('class' => 'pointstotals'));
        }
        return $this->checklist_template($mode, $options, $elementname, $groupsstr, $totalpointsstr);
    }

    /**
     * Help function to return CSS class names for element (first/last/even/odd) with leading space
     *
     * @param int $idx index of this element in the row/column
     * @param int $maxidx maximum index of the element in the row/column
     * @return string
     */
    protected function get_css_class_suffix($idx, $maxidx) {
        $class = '';
        if ($idx == 0) {
            $class .= ' first';
        }
        if ($idx == $maxidx) {
            $class .= ' last';
        }
        if ($idx%2) {
            $class .= ' odd';
        } else {
            $class .= ' even';
        }
        return $class;
    }

    /**
     * Displays for the student the list of instances or default content if no instances found
     *
     * @param array $instances array of objects of type gradingform_checklist_instance
     * @param string $defaultcontent default string that would be displayed without advanced grading
     * @param boolean $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function display_instances($instances, $defaultcontent, $cangrade) {
        $return = '';
        if (sizeof($instances)) {
            $return .= html_writer::start_tag('div', array('class' => 'advancedgrade'));
            $idx = 0;
            foreach ($instances as $instance) {
                $return .= $this->display_instance($instance, $idx++, $cangrade);
            }
            $return .= html_writer::end_tag('div');
        }
        return $return. $defaultcontent;
    }

    /**
     * Displays one grading instance
     *
     * @param gradingform_checklist_instance $instance
     * @param int $idx unique number of instance on page
     * @param boolean $cangrade whether current user has capability to grade in this context
     * @return string
     */
    public function display_instance(gradingform_checklist_instance $instance, $idx, $cangrade) {
        $groups = $instance->get_controller()->get_definition()->checklist_groups;
        $options = $instance->get_controller()->get_options();
        $values = $instance->get_checklist_filling();
        if ($cangrade) {
            $mode = gradingform_checklist_controller::DISPLAY_REVIEW;
        } else {
            $mode = gradingform_checklist_controller::DISPLAY_VIEW;
        }
        $output = '';
        $output .= $this->box($instance->get_controller()->get_formatted_description(), 'gradingform_checklist-description');
        $output .= $this->display_checklist($groups, $options, $mode, 'checklist'.$idx, $values);

        return $output;
    }

    public function display_regrade_confirmation($elementname, $changelevel, $value) {
        $html = html_writer::start_tag('div', array('class' => 'gradingform_checklist-regrade'));
        if ($changelevel<=2) {
            $html .= get_string('regrademessage1', 'gradingform_checklist');
            $selectoptions = array(
                0 => get_string('regradeoption0', 'gradingform_checklist'),
                1 => get_string('regradeoption1', 'gradingform_checklist')
            );
            $html .= html_writer::select($selectoptions, $elementname.'[regrade]', $value, false);
        } else {
            $html .= get_string('regrademessage5', 'gradingform_checklist');
            $html .= html_writer::empty_tag('input', array('name' => $elementname.'[regrade]', 'value' => 1, 'type' => 'hidden'));
        }
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Generates and returns HTML code to display information box about how checklist score is converted to the grade
     *
     * @param array $scores
     * @return string
     */
    public function display_checklist_mapping_explained($scores) {
        $html = '';
        if (!$scores) {
            return $html;
        }
        $html .= $this->box(
            html_writer::tag('h4', get_string('checklistmapping', 'gradingform_checklist')).
                html_writer::tag('div', get_string('checklistmappingexplained', 'gradingform_checklist', (object)$scores))
            , 'generalbox checklistmappingexplained');
        return $html;
    }
}