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
 * Checklist editor form
 *
 * @package    gradingform_checklist
 * @author     Sam Chaffee
 * @copyright  2011 Marina Glancy <marina@moodle.com>
 * @copyright  Copyright (c) 2012 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/checklisteditor.php');

use gradingform_checklist\local\config;
MoodleQuickForm::registerElementType('checklisteditor', $CFG->dirroot.'/grade/grading/form/checklist/checklisteditor.php', 'MoodleQuickForm_checklisteditor');

/**
 * Defines the checklist edit form
 */
class gradingform_checklist_editchecklist extends moodleform {

    /**
     * Form element definition
     */
    public function definition() {
        $form = $this->_form;

        $form->addElement('hidden', 'areaid');
        $form->setType('areaid', PARAM_INT);

        $form->addElement('hidden', 'returnurl');
        $form->setType('returnurl', PARAM_LOCALURL);

        // name
        $form->addElement('text', 'name', get_string('name', 'gradingform_checklist'), array('size'=>52));
        $form->addRule('name', get_string('required'), 'required');
        $form->setType('name', PARAM_TEXT);

        // description
        $options = gradingform_checklist_controller::description_form_field_options($this->_customdata['context']);
        $form->addElement('editor', 'description_editor', get_string('description', 'gradingform_checklist'), null, $options);
        $form->setType('description_editor', PARAM_RAW);

        // benchmark
        $form->addElement('hidden', 'usebenchmark', 0);
        $form->setType('usebenchmark', PARAM_BOOL);
        $form->setDefault('usebenchmark', 0);
        $form->addElement('hidden', 'removebenchmark', 0);
        $form->setType('removebenchmark', PARAM_BOOL);
        $form->setDefault('removebenchmark', 0);

        $benchmarkbuttons = \core\output\html_writer::start_div('form-group row fitem');
        $benchmarkbuttons .= \core\output\html_writer::div('', 'col-md-3 col-form-label d-flex pb-0 pr-md-0');
        $benchmarkbuttons .= \core\output\html_writer::start_div('col-md-9 form-inline align-items-start felement pt-1 pb-1');
        $benchmarkbuttons .= \core\output\html_writer::start_div('gradingform-checklist-benchmark-actions');
        $benchmarkbuttons .= \core\output\html_writer::tag('button', get_string('addbenchmark', 'gradingform_checklist'), [
            'type' => 'button',
            'class' => 'btn btn-primary',
            'id' => 'gradingform-checklist-add-benchmark',
        ]);
        $benchmarkbuttons .= \core\output\html_writer::tag('button', get_string('removebenchmark', 'gradingform_checklist'), [
            'type' => 'button',
            'class' => 'btn btn-primary ml-2',
            'id' => 'gradingform-checklist-remove-benchmark',
        ]);
        $benchmarkbuttons .= \core\output\html_writer::end_div();
        $benchmarkbuttons .= \core\output\html_writer::end_div();
        $benchmarkbuttons .= \core\output\html_writer::end_div();
        $form->addElement('html', $benchmarkbuttons);

        $benchmarkoptions = gradingform_checklist_controller::benchmark_form_field_options($this->_customdata['context']);
        $form->addElement('editor', 'benchmark_editor', get_string('benchmark', 'gradingform_checklist'), null, $benchmarkoptions);
        $form->setType('benchmark_editor', PARAM_RAW);

        $form->addElement('text', 'benchmarkbuttonlabel', get_string('benchmarkbuttonlabel', 'gradingform_checklist'),
            ['size' => 52]);
        $form->setType('benchmarkbuttonlabel', PARAM_TEXT);
        $form->setDefault('benchmarkbuttonlabel', get_string('benchmarkbuttondefault', 'gradingform_checklist'));

        $form->addElement('text', 'benchmarkbuttonicon', get_string('benchmarkbuttonicon', 'gradingform_checklist'),
            ['size' => 52]);
        $form->setType('benchmarkbuttonicon', PARAM_TEXT);
        $form->setDefault('benchmarkbuttonicon', 'fa-solid fa-file-circle-check');

        $this->add_benchmark_toggle_script();

        // checklist editor
        $form->addElement('html',
            \core\output\html_writer::div(
                \core\output\html_writer::tag('h3', get_string('checklist', 'gradingform_checklist'),
                    ['class' => 'gradingform-checklist-section-heading']),
                'form-group row fitem gradingform-checklist-heading-row'
            )
        );
        $element = $form->addElement('checklisteditor', 'checklist', '');
        $form->setType('checklist', PARAM_RAW);

        $buttonarray = array();
        $buttonarray[] = &$form->createElement('submit', 'savechecklist', get_string('savechecklist', 'gradingform_checklist'));
        if ($this->_customdata['allowdraft']) {
            $buttonarray[] = &$form->createElement('submit', 'savechecklistdraft', get_string('savechecklistdraft', 'gradingform_checklist'));
        }
        $editbutton = &$form->createElement('submit', 'editchecklist', ' ');
        $editbutton->freeze();
        $buttonarray[] = &$editbutton;
        $buttonarray[] = &$form->createElement('cancel');
        $form->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $form->closeHeaderBefore('buttonar');
    }

    /**
     * Setup the form depending on current values. This method is called after definition(),
     * data submission and set_data().
     * All form setup that is dependent on form values should go in here.
     *
     * Update button text for ready checklists.
     */
    public function definition_after_data() {
        if (($this->_defaultValues['status'] ?? null) == gradingform_controller::DEFINITION_STATUS_READY) {
            $this->findButton('savechecklist')->setValue(get_string('save', 'gradingform_checklist'));
        }
        if (!config::enabled('enablebenchmarks') && empty($this->_defaultValues['usebenchmark'])) {
            $this->_form->addElement('html', \html_writer::div(
                get_string('benchmarkdisabled', 'gradingform_checklist'), 'alert alert-info'));
        }
    }

    /**
     * Adds client-side benchmark controls.
     */
    protected function add_benchmark_toggle_script(): void {
        global $PAGE;

        $benchmarkenabled = config::enabled('enablebenchmarks') ? 'true' : 'false';

        $PAGE->requires->js_init_code(<<<JS
require(['jquery'], function($) {
    const benchmarkFeatureEnabled = {$benchmarkenabled};
    const useBenchmark = $('#id_usebenchmark, input[name="usebenchmark"]').first();
    const removeBenchmark = $('#id_removebenchmark, input[name="removebenchmark"]').first();
    const addButton = $('#gradingform-checklist-add-benchmark');
    const removeButton = $('#gradingform-checklist-remove-benchmark');
    const benchmarkFields = $('#fitem_id_benchmark_editor, #fitem_id_benchmarkbuttonlabel, #fitem_id_benchmarkbuttonicon');

    function setBenchmarkEnabled(enabled, remove) {
        if (!benchmarkFeatureEnabled && enabled) {
            return;
        }
        useBenchmark.val(enabled ? 1 : 0);
        removeBenchmark.val(remove ? 1 : 0);
        benchmarkFields.toggleClass('d-none', !enabled);
        addButton.toggleClass('d-none', enabled);
        removeButton.toggleClass('d-none', !enabled);
    }

    addButton.on('click', function() {
        setBenchmarkEnabled(true, false);
    });
    removeButton.on('click', function() {
        setBenchmarkEnabled(false, true);
    });
    if (!useBenchmark.length || !removeBenchmark.length) {
        return;
    }
    if (!benchmarkFeatureEnabled && useBenchmark.val() !== '1') {
        addButton.addClass('d-none');
    }
    setBenchmarkEnabled(useBenchmark.val() === '1', removeBenchmark.val() === '1');
});
JS);
    }

    /**
     * Form validation.
     * If there are errors return array of errors ("fieldname"=>"error message"),
     * otherwise true if ok.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *               or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files) {
        $err = parent::validation($data, $files);
        $err = array();

        if (!config::enabled('enablebenchmarks')
                && !empty($data['usebenchmark'])
                && empty($this->_defaultValues['usebenchmark'])) {
            $err['benchmark_editor'] = get_string('benchmarkdisabled', 'gradingform_checklist');
        }
        $form = $this->_form;
        $checklistel = $form->getElement('checklist');
        if ($checklistel->non_js_button_pressed($data['checklist'])) {
            // if JS is disabled and button such as 'Add group' is pressed - prevent from submit
            $err['checklistdummy'] = 1;
        } else if (isset($data['editchecklist'])) {
            // continue editing
            $err['checklistdummy'] = 1;
        } else if (isset($data['savechecklist']) && $data['savechecklist']) {
            // If user attempts to make checklist active - it needs to be validated
            if ($checklistel->validate($data['checklist']) !== false) {
                $err['checklistdummy'] = 1;
            }
        }
        return $err;
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();
        if (!empty($data->savechecklist)) {
            $data->status = gradingform_controller::DEFINITION_STATUS_READY;
        } else if (!empty($data->savechecklistdraft)) {
            $data->status = gradingform_controller::DEFINITION_STATUS_DRAFT;
        }
        return $data;
    }

    /**
     * Check if there are changes in the checklist and it is needed to ask user whether to
     * mark the current grades for re-grading. User may confirm re-grading and continue,
     * return to editing or cancel the changes
     *
     * @param gradingform_checklist_controller $controller
     * @return boolean
     */
    public function need_confirm_regrading($controller) {
        $data = $this->get_data();
        if (isset($data->checklist['regrade'])) {
            // we have already displayed the confirmation on the previous step
            return false;
        }
        if (!isset($data->savechecklist) || !$data->savechecklist) {
            // we only need confirmation when button 'Save checklist' is pressed
            return false;
        }
        if (!$controller->has_active_instances()) {
            // nothing to re-grade, confirmation not needed
            return false;
        }
        $changelevel = $controller->update_or_check_checklist($data);
        if ($changelevel == 0) {
            // no changes in the checklist, no confirmation needed
            return false;
        }

        // Freeze form elements and pass the values in hidden fields.
        $form = $this->_form;
        foreach (array('checklist', 'name'/*, 'description_editor'*/) as $fieldname) {
            $el =& $form->getElement($fieldname);
            $el->freeze();
            $el->setPersistantFreeze(true);
            if ($fieldname == 'checklist') {
                $el->add_regrade_confirmation($changelevel);
            }
        }

        // replace button text 'savechecklist' and unfreeze 'Back to edit' button
        $this->findButton('savechecklist')->setValue(get_string('continue'));
        $el =& $this->findButton('editchecklist');
        $el->setValue(get_string('backtoediting', 'gradingform_checklist'));
        $el->unfreeze();

        return true;
    }

    /**
     * Returns a form element (submit button) with the name $elementname
     *
     * @param string $elementname
     * @return HTML_QuickForm_element
     */
    protected function &findButton($elementname) {
        $form = $this->_form;
        $buttonar =& $form->getElement('buttonar');
        $elements =& $buttonar->getElements();
        foreach ($elements as $el) {
            if ($el->getName() == $elementname) {
                return $el;
            }
        }
        return null;
    }
}
