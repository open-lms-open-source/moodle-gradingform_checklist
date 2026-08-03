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
 * @package    gradingform_checklist
 * @author     Sam Chaffee
 * @copyright  2011 Marina Glancy
 * @copyright  2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/checklisteditor.php');
require_once($CFG->libdir . '/editorlib.php');

/**
 * Checklist grading method plugin renderer
 *
 */
class gradingform_checklist_renderer extends \core\output\plugin_renderer_base {

    /**
     * Returns the single benchmark button and hidden content for teacher-only benchmarks.
     *
     * @param array $benchmark formatted benchmark data
     * @return string
     */
    public function display_benchmark_button(array $benchmark): string {
        if (empty($benchmark['content'])) {
            return '';
        }
        $this->page->requires->js_init_code($this->benchmark_display_js());
        $button = \core\output\html_writer::tag('button',
            \core\output\html_writer::tag('i', '', ['class' => $benchmark['buttonicon'] . ' mr-2', 'aria-hidden' => 'true']).
            \core\output\html_writer::tag('span', s($benchmark['buttonlabel']), ['class' => 'benchmark-button-label']),
            [
                'type' => 'button',
                    'class' => 'benchmark-toggle btn btn-primary',
                'aria-expanded' => 'false',
                'data-benchmark-id' => $benchmark['id'],
                'title' => s($benchmark['buttonlabel']),
            ]
        );
        $content = \core\output\html_writer::tag('div',
            \core\output\html_writer::tag('h5', $benchmark['title'], ['class' => 'benchmark-content-title']).
            \core\output\html_writer::tag('div', $benchmark['content'], ['class' => 'benchmark-content-body']),
            [
                'class' => 'benchmark-content hiddenelement',
                'data-benchmark-content' => $benchmark['id'],
                'hidden' => 'hidden',
            ]
        );
        return \core\output\html_writer::tag('div', $button.$content,
            ['class' => 'benchmark-control benchmark-control-single d-flex justify-content-center w-100 py-3 my-2']);
    }

    /**
     * Returns JavaScript used by review pages to open benchmark content in the panel or modal.
     *
     * @return string
     */
    protected function benchmark_display_js(): string {
        $closelabel = json_encode(get_string('closebenchmark', 'gradingform_checklist'));
        return <<<JS
require(['jquery'], function($) {
    $(document).off('click.gradingformChecklistBenchmark', '.benchmark-toggle');
    $(document).on('click.gradingformChecklistBenchmark', '.benchmark-toggle', function() {
        const button = $(this);
        const benchmarkId = button.data('benchmark-id');
        const source = $('[data-benchmark-content="' + benchmarkId + '"]').first();
        const title = source.find('.benchmark-content-title').text();
        const body = source.find('.benchmark-content-body').html();
        const constrained = window.innerWidth < 1100 || $('[data-region="pdf"], .assignfeedback_editpdf_widget, .drawingregion, [data-region="review-panel"]').length > 0;
        let target = $('.gradingform_checklist-benchmark-' + (constrained ? 'modal' : 'panel'));
        const currentBenchmark = String(benchmarkId);

        if (!target.length) {
            if (constrained) {
                $('body').append('<div class="gradingform_checklist-benchmark-modal hiddenelement" role="dialog" aria-modal="true"><div class="benchmark-modal-dialog"><button type="button" class="benchmark-modal-close" aria-label="' + $closelabel + '">&times;</button><h5 class="benchmark-display-title"></h5><div class="benchmark-modal-body"></div></div></div>');
                target = $('.gradingform_checklist-benchmark-modal');
                target.find('.benchmark-modal-close').on('click', function() {
                    target.addClass('hiddenelement');
                    $('.benchmark-toggle').attr('aria-expanded', 'false');
                    $('body').removeClass('gradingform_checklist-benchmark-panel-open');
                });
            } else {
                $('body').append('<aside class="gradingform_checklist-benchmark-panel" role="complementary"><button type="button" class="benchmark-panel-close" aria-label="' + $closelabel + '">&times;</button><h5 class="benchmark-display-title"></h5><div class="benchmark-panel-body"></div></aside>');
                target = $('.gradingform_checklist-benchmark-panel');
                target.find('.benchmark-panel-close').on('click', function() {
                    target.removeClass('open');
                    $('.benchmark-toggle').attr('aria-expanded', 'false');
                    $('body').removeClass('gradingform_checklist-benchmark-panel-open');
                });
            }
        }

        const isOpen = constrained ? !target.hasClass('hiddenelement') : target.hasClass('open');
        if (isOpen && target.attr('data-current-benchmark') === currentBenchmark) {
            target.toggleClass('hiddenelement', constrained);
            target.removeClass('open');
            $('body').removeClass('gradingform_checklist-benchmark-panel-open');
            button.attr('aria-expanded', 'false');
            return;
        }

        $('.benchmark-toggle').attr('aria-expanded', 'false');
        button.attr('aria-expanded', 'true');
        target.attr('data-current-benchmark', currentBenchmark);
        if (constrained) {
            $('body').removeClass('gradingform_checklist-benchmark-panel-open');
            target.find('.benchmark-display-title').text(title);
            target.find('.benchmark-modal-body').html(body);
            target.removeClass('hiddenelement');
        } else {
            target.find('.benchmark-display-title').text(title);
            target.find('.benchmark-panel-body').html(body);
            target.addClass('open');
            $('body').addClass('gradingform_checklist-benchmark-panel-open');
        }
    });
});
JS;
    }

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
        $grouptemplate = \core\output\html_writer::start_tag('div', array('class' => 'group'. $group['class'], 'id' => '{NAME}-groups-{GROUP-id}'));
        $controls = '';
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $controls .= \core\output\html_writer::start_tag('div', array('class' => 'controls'));
            foreach (array('moveup', 'delete', 'movedown') as $key) {
                $value = get_string('group'.$key, 'gradingform_checklist');
                $labelforctrl = \core\output\html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-'.$key));
                $button = $labelforctrl . \core\output\html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][{GROUP-id}]['.$key.']',
                    'id' => '{NAME}-groups-{GROUP-id}-'.$key, 'value' => $value, 'title' => $value, 'tabindex' => -1));
                $controls .= \core\output\html_writer::tag('div', $button, array('class' => $key));
            }
            $controls .= \core\output\html_writer::end_tag('div'); // .controls
            $grouptemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][sortorder]', 'value' => $group['sortorder']));
            $labelfordesc = \core\output\html_writer::tag('label', get_string('groupdescription', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-description-input'));
            $description = $labelfordesc . \core\output\html_writer::tag('textarea', s($group['description']), array(
                    'id' => '{NAME}-groups-{GROUP-id}-description-input',
                    'name' => '{NAME}[groups][{GROUP-id}][description]',
                    'maxlength' => MoodleQuickForm_checklisteditor::GROUP_DESCRIPTION_MAX_LENGTH,
                    'rows' => '3', 'class' => 'checklisteditor-text'));
        } else {
            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN) {
                $grouptemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][sortorder]', 'value' => $group['sortorder']));
                $grouptemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][description]', 'value' => $group['description']));
            }
            $description = $group['description'];
        }
        $descriptionclass = 'description';
        if (isset($group['error_description'])) {
            $descriptionclass .= ' error';
        }
        $groupheader = \core\output\html_writer::tag('div', $description,
            array('class' => $descriptionclass, 'id' => '{NAME}-groups-{GROUP-id}-description'));
        $grouptemplate .= \core\output\html_writer::tag('div', $groupheader, array('class' => 'group-header'));
        $grouptemplate .= $controls;
        $itemsstrdiv = \core\output\html_writer::tag('div', \core\output\html_writer::tag('div', $itemsstr, array('id' => '{NAME}-groups-{GROUP-id}-items')));
        $itemsclass = 'items';
        if (isset($group['error_items'])) {
            $itemsclass .= ' error';
        }
        $grouptemplate .= \core\output\html_writer::tag('div', $itemsstrdiv, array('class' => $itemsclass));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('groupadditem', 'gradingform_checklist');
            $labelforadditem = \core\output\html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-additem'));
            $button = $labelforadditem . \core\output\html_writer::empty_tag('input', array('type' => 'submit',
                    'name' => '{NAME}[groups][{GROUP-id}][items][additem]',
                    'id' => '{NAME}-groups-{GROUP-id}-items-additem', 'value' => $value, 'title' => $value,
                    'class' => 'btn btn-primary'));
            $grouptemplate .= \core\output\html_writer::tag('div', $button, array('class' => 'additem'));
        }
        $displayremark = (gradingform_checklist_controller::group_remarks_enabled($options)
                && ($mode != gradingform_checklist_controller::DISPLAY_VIEW || $options['showremarksstudent']));
        if ($displayremark) {
            $currentremark = '';
            if (isset($gvalue['items'][0]['remark'])) {
                $currentremark = $gvalue['items'][0]['remark'];
            }
            if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
                $labelforremark = \core\output\html_writer::tag('label',
                        gradingform_checklist_controller::get_group_remark_heading($options),
                        array('class' => 'checklistremarkheading', 'for' => '{NAME}-groups-{GROUP-id}-items-0-remark'));
                $input = $labelforremark . \core\output\html_writer::tag('textarea',
                        htmlspecialchars($currentremark, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401),
                        array('id' => '{NAME}-groups-{GROUP-id}-items-0-remark', 'name' => '{NAME}[groups][{GROUP-id}][items][0][remark]', 'cols' => '10', 'rows' => '5'));
                $grouptemplate .= \core\output\html_writer::tag('div', $input, array('class' => 'remark'));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN) {
                $grouptemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][0][remark]', 'value' => $currentremark));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
                $feedbackstr = empty($currentremark) ? '' : \core\output\html_writer::tag('span', get_string('groupfeedback', 'gradingform_checklist', $group['description']) . ': ', array('class' => 'checklistfeedback'));
                $grouptemplate .= \core\output\html_writer::tag('div', $feedbackstr . $currentremark, array('class' => 'remark')); // TODO maybe some prefix here like 'Teacher remark:'
            }
        }

        $displaypointseval = $options['showgrouppointseval'] && ($mode == gradingform_checklist_controller::DISPLAY_EVAL
                || $mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_REVIEW);
        $displaypointsrev  = $options['showgrouppointstudent'] && ($mode == gradingform_checklist_controller::DISPLAY_VIEW);

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
            $checkedpts = \core\output\html_writer::tag('span', $checkedpts, array('class' => 'scoredpoints'));
            $totalpts   = \core\output\html_writer::tag('span', $totalpts, array('class' => 'outofpoints'));

            // add to the template
            $grouptemplate .= \core\output\html_writer::tag('div', get_string('grouppoints', 'gradingform_checklist') . ": $checkedpts/$totalpts", array('class' => 'pointstotals'));
        }
        $grouptemplate .= \core\output\html_writer::end_tag('div'); // .group

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

        $itemtemplate = \core\output\html_writer::start_tag('div', $divattributes);
        $itemtemplate .= \core\output\html_writer::start_tag('div', array('class' => 'item-wrapper'));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $labelfordef = \core\output\html_writer::tag('label', get_string('itemdefinition', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition-input'));
            $definition = $labelfordef . \core\output\html_writer::tag('textarea', s($item['definition']), array(
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition-input',
                    'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][definition]',
                    'maxlength' => MoodleQuickForm_checklisteditor::ITEM_DEFINITION_MAX_LENGTH,
                    'rows' => '4', 'class' => 'checklisteditor-text'));

            $labelforscore = \core\output\html_writer::tag('label', get_string('itemscore', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score-input'));
            $score = $labelforscore . \core\output\html_writer::empty_tag('input', array('type' => 'text', 'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score-input',
                    'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][score]', 'size' => '3', 'value' => $item['score']));

            $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][sortorder]', 'value' => $item['sortorder']));
        } else {
            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN) {
                $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][definition]', 'value' => $item['definition']));
                $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][score]', 'value' => $item['score']));
                $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][sortorder]', 'value' => $item['sortorder']));
            }
            $definition = $item['definition'];
            $score = $item['score'];
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
            $labelforcheckitem = \core\output\html_writer::tag('label', get_string('checkitem', 'gradingform_checklist', $item['definition']),
                    array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-id-input'));
            $input = $labelforcheckitem . \core\output\html_writer::empty_tag('input', array('type' => 'checkbox',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-id-input', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][id]',
                    'value' => $item['id']) + ($item['checked'] ? array('checked' => 'checked') : array()));
            $itemtemplate .= \core\output\html_writer::tag('div', $input, array('class' => 'checkbox'));
        } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
            if (empty($item['checked'])) {
                $iconname = 'i/grade_incorrect';
                $alttext  = get_string('unchecked', 'gradingform_checklist');
            } else {
                $iconname = 'i/grade_correct';
                $alttext  = get_string('checked', 'gradingform_checklist');
            }
            $itemtemplate .= \core\output\html_writer::tag('div', $this->output->pix_icon($iconname, $alttext), array('class' => 'checkbox'));
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN && $item['checked']) {
            $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][id]', 'value' => $item['id']));
        }

        if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
            $realscore = !empty($item['checked']) ? $item['score'] : '0';
            $score = $realscore . '/' . $score;
        }
        $score = \core\output\html_writer::tag('span', $score, array('id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-score', 'class' => 'scorevalue'));
        $definitionclass = 'definition';
        if (isset($item['error_definition'])) {
            $definitionclass .= ' error';
        }
        $itemtemplate .= \core\output\html_writer::tag('div', $definition, array('class' => $definitionclass, 'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-definition'));
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
            $itemtemplate .= \core\output\html_writer::tag('div', get_string('scorepostfix', 'gradingform_checklist', $score), array('class' => $scoreclass));
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $movecontrols = '';
            foreach (array('moveup', 'movedown') as $key) {
                $value = get_string('item'.$key, 'gradingform_checklist');
                $labelforctrl = \core\output\html_writer::tag('label', $value, array(
                    'class' => 'hiddenelement',
                    'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-'.$key,
                ));
                $button = $labelforctrl . \core\output\html_writer::empty_tag('input', array(
                    'type' => 'submit',
                    'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}]['.$key.']',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-'.$key,
                    'value' => $value,
                    'title' => $value,
                    'tabindex' => -1,
                ));
                $movecontrols .= \core\output\html_writer::tag('div', $button, array('class' => $key));
            }
            $itemtemplate .= \core\output\html_writer::tag('div', $movecontrols, array('class' => 'controls'));

            $value = get_string('itemdelete', 'gradingform_checklist');
            $labelfordelete = \core\output\html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-delete'));
            $button = $labelfordelete . \core\output\html_writer::empty_tag('input', array('type' => 'submit', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][delete]',
                    'id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-delete', 'value' => $value, 'title' => $value, 'tabindex' => -1));
            $itemtemplate .= \core\output\html_writer::tag('div', $button, array('class' => 'delete'));
        }
        $displayremark = (gradingform_checklist_controller::item_remarks_enabled($options)
                && ($mode != gradingform_checklist_controller::DISPLAY_VIEW || $options['showremarksstudent']));
        if ($displayremark) {
            $currentremark = '';
            if (isset($item['remark'])) {
                $currentremark = $item['remark'];
            }
            if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
                $labelforremark = \core\output\html_writer::tag('label', get_string('itemremark', 'gradingform_checklist', $item['definition']),
                        array('class' => 'hiddenelement', 'for' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-remark-input'));
                $input = $labelforremark . \core\output\html_writer::tag('textarea',
                        htmlspecialchars($currentremark, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401), array('id' => '{NAME}-groups-{GROUP-id}-items-{ITEM-id}-remark-input',
                        'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][remark]', 'cols' => '20', 'rows' => '3'));
                $itemtemplate .= \core\output\html_writer::tag('div', $input, array('class' => 'remark'));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN) {
                $itemtemplate .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[groups][{GROUP-id}][items][{ITEM-id}][remark]', 'value' => $currentremark));
            } else if ($mode == gradingform_checklist_controller::DISPLAY_REVIEW || $mode == gradingform_checklist_controller::DISPLAY_VIEW) {
                $feedbackstr = empty($currentremark) ? '' : \core\output\html_writer::tag('span', get_string('itemfeedback', 'gradingform_checklist', $item['definition']) . ': ', array('class' => 'checklistfeedback'));
                $itemtemplate .= \core\output\html_writer::tag('div', $feedbackstr . $currentremark, array('class' => 'remark')); // TODO maybe some prefix here like 'Teacher remark:'
            }
        }
        $itemtemplate .= \core\output\html_writer::end_tag('div'); // .item-wrapper
        $itemtemplate .= \core\output\html_writer::end_tag('div'); // .item

        $itemtemplate = str_replace('{NAME}', $elementname, $itemtemplate);
        $itemtemplate = str_replace('{GROUP-id}', $groupid, $itemtemplate);
        $itemtemplate = str_replace('{ITEM-id}', $item['id'], $itemtemplate);
        return $itemtemplate;
    }

    /**
     * Returns the bulk select control used above and below long grading forms.
     *
     * @return string
     */
    protected function bulk_check_controls(): string {
        $buttons = \core\output\html_writer::tag('button', get_string('tickall', 'gradingform_checklist'), array(
            'type' => 'button',
            'class' => 'btn btn-primary bulkchecktoggle',
            'data-action' => 'tickall',
        ));
        return \core\output\html_writer::tag('div', $buttons, array('class' => 'bulkcheckcontrols'));
    }

    /**
     * Returns the observation date selector or read-only observation date display.
     *
     * @param int $mode checklist display mode
     * @param array $options checklist definition options
     * @param string $elementname grading element name
     * @param array|null $values submitted or saved grading values
     * @return string
     */
    protected function observation_date_control($mode, array $options, string $elementname, ?array $values): string {
        if (!gradingform_checklist_controller::observation_enabled($options)) {
            return '';
        }

        $observation = $values['observation'] ?? array();
        $timestamp = !empty($observation['observationdate']) ? (int)$observation['observationdate'] : 0;
        $observationmode = gradingform_checklist_controller::clean_observation_mode($options['observationmode']);
        $submitteddate = !empty($observation['date']) ? clean_param($observation['date'], PARAM_TEXT) : '';
        $submittedtime = !empty($observation['time']) ? clean_param($observation['time'], PARAM_TEXT) : '';
        if ($timestamp <= 0 && $submitteddate === '' && $submittedtime === ''
                && $mode == gradingform_checklist_controller::DISPLAY_EVAL
                && $options['observationdefault'] === gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW) {
            $timestamp = time();
        }
        if ($timestamp <= 0 && $mode != gradingform_checklist_controller::DISPLAY_EVAL) {
            return '';
        }

        $html = \core\output\html_writer::start_tag('div', array('class' => 'observationdate'));
        $html .= \core\output\html_writer::tag('div', get_string('observationdate', 'gradingform_checklist'),
            array('class' => 'observationdate-title'));

        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL) {
            $datevalue = $submitteddate !== '' ? $submitteddate
                : ($timestamp > 0 ? gradingform_checklist_controller::format_observation_date_input($timestamp) : '');
            $html .= \core\output\html_writer::tag('label', get_string('observationdate', 'gradingform_checklist'),
                array('class' => 'hiddenelement', 'for' => $elementname.'-observation-date'));
            $html .= \core\output\html_writer::empty_tag('input', array(
                'type' => 'date',
                'id' => $elementname.'-observation-date',
                'name' => $elementname.'[observation][date]',
                'value' => $datevalue,
                'class' => 'form-control observationdate-date',
                'required' => 'required',
            ));
            if ($observationmode === gradingform_checklist_controller::OBSERVATION_MODE_DATETIME) {
                $timevalue = $submittedtime !== '' ? $submittedtime
                    : ($timestamp > 0 ? gradingform_checklist_controller::format_observation_time_input($timestamp) : '');
                $html .= \core\output\html_writer::tag('label', get_string('observationtime', 'gradingform_checklist'),
                    array('class' => 'hiddenelement', 'for' => $elementname.'-observation-time'));
                $html .= \core\output\html_writer::empty_tag('input', array(
                    'type' => 'time',
                    'id' => $elementname.'-observation-time',
                    'name' => $elementname.'[observation][time]',
                    'value' => $timevalue,
                    'class' => 'form-control observationdate-time',
                    'required' => 'required',
                ));
            }
        } else if ($timestamp > 0) {
            $html .= \core\output\html_writer::tag('div',
                gradingform_checklist_controller::format_observation_date($timestamp, $observation['observationmode'] ?? $observationmode),
                array('class' => 'observationdate-value'));
        }

        $html .= \core\output\html_writer::end_tag('div');
        return $html;
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
    protected function checklist_template($mode, $options, $elementname, $groupsstr, $totalpointsstr, $observationdatestr) {
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

        $checklisttemplate = \core\output\html_writer::start_tag('div', array('id' => 'checklist-{NAME}', 'class' => 'clearfix gradingform_checklist'.$classsuffix));
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL && !empty($options['enablebulkcheck'])) {
            $checklisttemplate .= $this->bulk_check_controls();
        }
        $checklisttemplate .= \core\output\html_writer::tag('div', $groupsstr, array('class' => 'groups', 'id' => '{NAME}-groups'));
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $value = get_string('addgroup', 'gradingform_checklist');
            $labelforaddgroup = \core\output\html_writer::tag('label', $value, array('class' => 'hiddenelement', 'for' => '{NAME}-groups-addgroup'));
            $input = $labelforaddgroup . \core\output\html_writer::empty_tag('input', array('type' => 'submit',
                    'name' => '{NAME}[groups][addgroup]',
                    'id' => '{NAME}-groups-addgroup', 'value' => $value, 'title' => $value,
                    'class' => 'btn btn-primary'));
            $checklisttemplate .= \core\output\html_writer::tag('div', $input, array('class' => 'addgroup'));
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EVAL && !empty($options['enablebulkcheck'])) {
            $checklisttemplate .= $this->bulk_check_controls();
        }
        $checklisttemplate .= $totalpointsstr;
        $checklisttemplate .= $observationdatestr;
        $checklisttemplate .= $this->checklist_edit_options($mode, $options);
        $checklisttemplate .= \core\output\html_writer::end_tag('div');

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
        $html = \core\output\html_writer::start_tag('div', array('class' => 'options'));
        $html .= \core\output\html_writer::tag('div', get_string('checklistoptions', 'gradingform_checklist'), array('class' => 'optionsheading'));
        $html .= \core\output\html_writer::empty_tag('input', array('type' => 'hidden', 'name' => '{NAME}[options][optionsset]', 'value' => 1));

        $optionorder = array(
            'alwaysshowdefinition',
            'showremarksstudent',
            'enablebulkcheck',
            array('heading' => 'optionsectiongroups'),
            'showgrouppointseval',
            'showgrouppointstudent',
            'enablegroupremarks',
            array('option' => 'requiregroupcommentschecked', 'parent' => 'enablegroupremarks'),
            array('option' => 'requireatleastonegroupcomment', 'class' => 'childoption', 'parent' => 'enablegroupremarks'),
            array('heading' => 'optionsectionitems'),
            'showitempointseval',
            'showitempointstudent',
            'enableitemremarks',
            array('option' => 'requireitemcommentschecked', 'parent' => 'enableitemremarks'),
            array('option' => 'requireatleastoneitemcomment', 'class' => 'childoption', 'parent' => 'enableitemremarks'),
            'groupremarkheading',
            array('heading' => 'optionsectionobservation'),
            'observationmode',
            'observationdefault',
        );

        $observationoptionsopen = false;
        foreach ($optionorder as $optioninfo) {
            if (is_array($optioninfo) && isset($optioninfo['heading'])) {
                $html .= \core\output\html_writer::tag('div', get_string($optioninfo['heading'], 'gradingform_checklist'),
                    array('class' => 'optionssectionheading'));
                if ($optioninfo['heading'] == 'optionsectionobservation') {
                    $html .= \core\output\html_writer::start_tag('div', array('class' => 'observationoptions'));
                    $observationoptionsopen = true;
                }
                continue;
            }

            $option = is_array($optioninfo) ? $optioninfo['option'] : $optioninfo;
            if (!array_key_exists($option, $options)) {
                continue;
            }

            $optionclass = 'option '.$option;
            if (is_array($optioninfo) && !empty($optioninfo['class'])) {
                $optionclass .= ' '.$optioninfo['class'];
            }
            $parentoption = is_array($optioninfo) && !empty($optioninfo['parent']) ? $optioninfo['parent'] : null;
            $parentenabled = $parentoption === null || !empty($options[$parentoption]);

            $value = $parentenabled ? $options[$option] : 0;
            $html .= \core\output\html_writer::start_tag('div', array('class' => $optionclass));
            $attrs = array('name' => '{NAME}[options]['.$option.']', 'id' => '{NAME}-options-'.$option);

            if ($option == 'groupremarkheading') {
                if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN && $value !== '') {
                    $html .= \core\output\html_writer::empty_tag('input', $attrs + array('type' => 'hidden', 'value' => $value));
                }
                if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_PREVIEW) {
                    unset($attrs['name']);
                    $attrs['disabled'] = 'disabled';
                }
                $html .= \core\output\html_writer::tag('label', get_string($option, 'gradingform_checklist'), array('for' => $attrs['id']));
                $html .= \core\output\html_writer::empty_tag('input', $attrs + array(
                    'type' => 'text',
                    'value' => $value,
                    'placeholder' => get_string('groupremarkheadingdefault', 'gradingform_checklist'),
                    'size' => '32',
                ));
                $html .= \core\output\html_writer::end_tag('div'); // .option
                continue;
            }

            if ($option == 'observationmode' || $option == 'observationdefault') {
                $observationdisabled = gradingform_checklist_controller::clean_observation_mode($options['observationmode'])
                    === gradingform_checklist_controller::OBSERVATION_MODE_DISABLED;
                if ($option == 'observationmode') {
                    $choices = array(
                        gradingform_checklist_controller::OBSERVATION_MODE_DISABLED => get_string('observationmodedisabled', 'gradingform_checklist'),
                        gradingform_checklist_controller::OBSERVATION_MODE_DATE => get_string('observationmodedate', 'gradingform_checklist'),
                        gradingform_checklist_controller::OBSERVATION_MODE_DATETIME => get_string('observationmodedatetime', 'gradingform_checklist'),
                    );
                    $value = gradingform_checklist_controller::clean_observation_mode($value);
                    $attrs['data-observation-mode'] = 1;
                } else {
                    $choices = array(
                        gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW => get_string('observationdefaultnow', 'gradingform_checklist'),
                        gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK => get_string('observationdefaultblank', 'gradingform_checklist'),
                    );
                    $value = gradingform_checklist_controller::clean_observation_default($value);
                    $attrs['data-observation-default'] = 1;
                    if ($observationdisabled) {
                        $attrs['disabled'] = 'disabled';
                    }
                }
                if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN && $value !== '') {
                    $html .= \core\output\html_writer::empty_tag('input', $attrs + array('type' => 'hidden', 'value' => $value));
                }
                if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_PREVIEW) {
                    unset($attrs['name']);
                    $attrs['disabled'] = 'disabled';
                }
                $html .= \core\output\html_writer::tag('label', get_string($option, 'gradingform_checklist'), array('for' => $attrs['id']));
                $select = \core\output\html_writer::start_tag('select', $attrs + array('class' => 'custom-select'));
                foreach ($choices as $choicevalue => $choicelabel) {
                    $choiceattrs = array('value' => $choicevalue);
                    if ($choicevalue === $value) {
                        $choiceattrs['selected'] = 'selected';
                    }
                    $select .= \core\output\html_writer::tag('option', $choicelabel, $choiceattrs);
                }
                $select .= \core\output\html_writer::end_tag('select');
                $html .= $select;
                $html .= \core\output\html_writer::end_tag('div'); // .option
                if ($option == 'observationdefault' && $observationoptionsopen) {
                    $html .= \core\output\html_writer::end_tag('div'); // .observationoptions
                    $observationoptionsopen = false;
                }
                continue;
            }

            if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN && $value) {
                $html .= \core\output\html_writer::empty_tag('input', $attrs + array('type' => 'hidden', 'value' => $value));
            }
            // Display option as checkbox
            $attrs['type'] = 'checkbox';
            $attrs['value'] = 1;
            if ($parentoption !== null) {
                $attrs['data-required-parent'] = '{NAME}-options-'.$parentoption;
            }
            if ($value) {
                $attrs['checked'] = 'checked';
            }
            if (!$parentenabled || $mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN
                    || $mode == gradingform_checklist_controller::DISPLAY_PREVIEW) {
                $attrs['disabled'] = 'disabled';
                if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FROZEN
                        || $mode == gradingform_checklist_controller::DISPLAY_PREVIEW) {
                    unset($attrs['name']);
                }
            }
            $html .= \core\output\html_writer::empty_tag('input', $attrs);
            $html .= \core\output\html_writer::tag('label', get_string($option, 'gradingform_checklist'), array('for' => $attrs['id']));

            $html .= \core\output\html_writer::end_tag('div'); // .option
        }
        if ($observationoptionsopen) {
            $html .= \core\output\html_writer::end_tag('div'); // .observationoptions
        }
        if ($mode == gradingform_checklist_controller::DISPLAY_EDIT_FULL) {
            $html .= \core\output\html_writer::tag('script', <<<JS
(function() {
    const checklist = document.getElementById('checklist-{NAME}');
    if (!checklist) {
        return;
    }
    const updateDependentOptions = function(parent) {
        const enabled = parent.checked;
        checklist.querySelectorAll('[data-required-parent]').forEach(function(option) {
            if (option.getAttribute('data-required-parent') !== parent.id) {
                return;
            }
            option.disabled = !enabled;
            if (!enabled) {
                option.checked = false;
            }
        });
    };
    [
        document.getElementById('{NAME}-options-enablegroupremarks'),
        document.getElementById('{NAME}-options-enableitemremarks')
    ].forEach(function(parent) {
        if (!parent) {
            return;
        }
        updateDependentOptions(parent);
        parent.addEventListener('change', function() {
            updateDependentOptions(parent);
        });
    });
    const observationMode = checklist.querySelector('[data-observation-mode]');
    const observationDefault = checklist.querySelector('[data-observation-default]');
    const updateObservationDefault = function() {
        if (!observationMode || !observationDefault) {
            return;
        }
        observationDefault.disabled = observationMode.value === 'disabled';
    };
    updateObservationDefault();
    if (observationMode) {
        observationMode.addEventListener('change', updateObservationDefault);
    }
}());
JS);
        }
        $html .= \core\output\html_writer::end_tag('div'); // .options
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
            $group['class'] = $this->get_css_class_suffix($cnt++, count($groups) - 1);
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
                $item['class'] = $this->get_css_class_suffix($itemcnt++, count($group['items']) - 1);
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

                // Tally for total and scored points.
                if (empty($item['error_score'])) {
                    $totalpoints += $item['score'];
                }
                if (!empty($groupvalue['items'][$itemid]['checked'])) {
                    $scoredpoints += $item['score'];
                }
            }
            $groupsstr .= $this->group_template($mode, $options, $elementname, $group, $itemsstr, $groupvalue);
        }

        $displaypointseval = $options['showgrouppointseval'] && ($mode == gradingform_checklist_controller::DISPLAY_EVAL
            || $mode == gradingform_checklist_controller::DISPLAY_EVAL_FROZEN || $mode == gradingform_checklist_controller::DISPLAY_REVIEW);
        $displaypointsrev  = $options['showgrouppointstudent'] && ($mode == gradingform_checklist_controller::DISPLAY_VIEW);

        if ($displaypointseval || $displaypointsrev) {
            $checkedpts = \core\output\html_writer::tag('span', $scoredpoints, array('class' => 'scoredpoints'));
            $totalpts   = \core\output\html_writer::tag('span', $totalpoints, array('class' => 'outofpoints'));

            // add to the template
            $totalpointsstr = \core\output\html_writer::tag('div', get_string('overallpoints', 'gradingform_checklist') . ": $checkedpts/$totalpts", array('class' => 'pointstotals'));
        }
        $observationdatestr = $this->observation_date_control($mode, $options, $elementname, $values);
        return $this->checklist_template($mode, $options, $elementname, $groupsstr, $totalpointsstr, $observationdatestr);
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
        if (count($instances)) {
            $return .= \core\output\html_writer::start_tag('div', array('class' => 'advancedgrade'));
            $idx = 0;
            foreach ($instances as $instance) {
                $return .= $this->display_instance($instance, $idx++, $cangrade);
            }
            $return .= \core\output\html_writer::end_tag('div');
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
        $description = $instance->get_controller()->get_formatted_description();
        if ($cangrade) {
            $description .= $this->display_benchmark_button($instance->get_controller()->get_formatted_benchmark());
        }
        $output = '';
        $output .= $this->box($description, 'gradingform_checklist-description gradingform_checklist');
        $output .= $this->display_checklist($groups, $options, $mode, 'checklist'.$idx, $values);

        return $output;
    }

    public function display_regrade_confirmation($elementname, $changelevel, $value) {
        $html = \core\output\html_writer::start_tag('div', array('class' => 'gradingform_checklist-regrade'));
        if ($changelevel<=2) {
            $html .= get_string('regrademessage1', 'gradingform_checklist');
            $selectoptions = array(
                0 => get_string('regradeoption0', 'gradingform_checklist'),
                1 => get_string('regradeoption1', 'gradingform_checklist')
            );
            $html .= \core\output\html_writer::select($selectoptions, $elementname.'[regrade]', $value, false);
        } else {
            $html .= get_string('regrademessage5', 'gradingform_checklist');
            $html .= \core\output\html_writer::empty_tag('input', array('name' => $elementname.'[regrade]', 'value' => 1, 'type' => 'hidden'));
        }
        $html .= \core\output\html_writer::end_tag('div');
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
            \core\output\html_writer::tag('h4', get_string('checklistmapping', 'gradingform_checklist')).
                \core\output\html_writer::tag('div', get_string('checklistmappingexplained', 'gradingform_checklist', (object)$scores))
            , 'generalbox checklistmappingexplained');
        return $html;
    }
}
