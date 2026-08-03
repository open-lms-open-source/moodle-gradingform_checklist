<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Imports checklist definitions from DOCX or JSON.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/import_form.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');

use gradingform_checklist\local\importer\canonical_import_data;
use gradingform_checklist\local\importer\docx_importer;
use gradingform_checklist\local\importer\json_importer;

$areaid = required_param('areaid', PARAM_INT);
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$confirmimport = optional_param('confirmimport', 0, PARAM_BOOL);

$manager = get_grading_manager($areaid);
list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);

$controller = $manager->get_controller('checklist');
if ($returnurl === '') {
    $returnurl = $manager->get_management_url();
}

$url = new \core\url('/grade/grading/form/checklist/import.php', ['areaid' => $areaid, 'returnurl' => $returnurl]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('importchecklist', 'gradingform_checklist'));
$PAGE->set_heading(get_string('importchecklist', 'gradingform_checklist'));

/**
 * Renders import warning or error messages.
 *
 * @param array $messages messages
 * @param string $type notification type
 * @return string
 */
function gradingform_checklist_import_messages(array $messages, string $type): string {
    global $OUTPUT;

    $html = '';
    foreach ($messages as $message) {
        $html .= $OUTPUT->notification($message, $type);
    }
    return $html;
}

/**
 * Renders a compact preview of canonical import data.
 *
 * @param array $data canonical import data
 * @return string
 */
function gradingform_checklist_import_preview(array $data): string {
    $html = \html_writer::tag('h3', get_string('importpreviewheading', 'gradingform_checklist'));
    $html .= \html_writer::start_tag('dl', ['class' => 'gradingform-checklist-import-preview']);
    $html .= \html_writer::tag('dt', get_string('name', 'gradingform_checklist'));
    $html .= \html_writer::tag('dd', s($data['name'] ?? ''));
    $html .= \html_writer::tag('dt', get_string('description', 'gradingform_checklist'));
    $html .= \html_writer::tag('dd', format_text($data['description'] ?? '', FORMAT_HTML));
    $html .= \html_writer::tag('dt', get_string('importgroups', 'gradingform_checklist'));
    $html .= \html_writer::tag('dd', count($data['groups'] ?? []));
    $html .= \html_writer::tag('dt', get_string('benchmark', 'gradingform_checklist'));
    $html .= \html_writer::tag('dd', !empty($data['benchmark']['enabled']) ? get_string('yes') : get_string('no'));
    $html .= \html_writer::end_tag('dl');

    foreach ($data['groups'] ?? [] as $group) {
        $table = new \html_table();
        $table->head = [get_string('itemdefinition', 'gradingform_checklist'), get_string('itemscore', 'gradingform_checklist')];
        foreach ($group['items'] ?? [] as $item) {
            $table->data[] = [s($item['definition'] ?? ''), s((string)($item['score'] ?? ''))];
        }
        $html .= \html_writer::tag('h4', s($group['description'] ?? ''));
        $html .= \html_writer::table($table);
    }

    return $html;
}

if ($confirmimport) {
    require_sesskey();
    $importid = required_param('importid', PARAM_ALPHANUM);
    $stored = $SESSION->gradingform_checklist_import[$areaid][$importid] ?? '';
    $result = canonical_import_data::decode($stored, true);
    if (!$result->has_errors()) {
        $status = $controller->has_active_instances()
            ? gradingform_controller::DEFINITION_STATUS_READY
            : gradingform_controller::DEFINITION_STATUS_DRAFT;
        $controller->import_definition_from_data($result->get_data(), $status, $controller->has_active_instances());
        unset($SESSION->gradingform_checklist_import[$areaid][$importid]);
        $editurl = new \core\url('/grade/grading/form/checklist/edit.php', ['areaid' => $areaid, 'returnurl' => $returnurl]);
        redirect($editurl, get_string('importsuccess', 'gradingform_checklist'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }
}

$mform = new gradingform_checklist_import_form($url);
$mform->set_data(['areaid' => $areaid, 'returnurl' => $returnurl]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

$result = null;
$importid = null;
if ($formdata = $mform->get_data()) {
    $filename = $mform->get_new_filename('importfile');
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($extension === 'json') {
        $result = (new json_importer())->parse($mform->get_file_content('importfile'));
    } else if ($extension === 'docx') {
        $tempdir = make_request_directory();
        $temppath = $tempdir . '/' . clean_param($filename, PARAM_FILE);
        file_put_contents($temppath, $mform->get_file_content('importfile'));
        $result = (new docx_importer())->parse($temppath);
    }
    if ($result && !$result->has_errors()) {
        $importid = random_string(32);
        if (!isset($SESSION->gradingform_checklist_import)) {
            $SESSION->gradingform_checklist_import = [];
        }
        if (!isset($SESSION->gradingform_checklist_import[$areaid])) {
            $SESSION->gradingform_checklist_import[$areaid] = [];
        }
        $SESSION->gradingform_checklist_import[$areaid][$importid] = $result->encode();
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importchecklist', 'gradingform_checklist'));

$downloads = [
    \html_writer::link(new \core\url('/grade/grading/form/checklist/template.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadwordtemplate', 'gradingform_checklist')),
    \html_writer::link(new \core\url('/grade/grading/form/checklist/jsonexample.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadjsonexample', 'gradingform_checklist')),
    \html_writer::link(new \core\url('/grade/grading/form/checklist/jsonschema.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadjsonschema', 'gradingform_checklist')),
];
echo \html_writer::div(implode(' ', $downloads), 'gradingform-checklist-import-downloads');

if ($result) {
    echo gradingform_checklist_import_messages($result->get_errors(), \core\output\notification::NOTIFY_ERROR);
    echo gradingform_checklist_import_messages($result->get_warnings(), \core\output\notification::NOTIFY_WARNING);
    if (!$result->has_errors()) {
        if ($controller->has_active_instances()) {
            echo $OUTPUT->notification(get_string('importactiveinstances', 'gradingform_checklist'),
                \core\output\notification::NOTIFY_WARNING);
        }
        echo gradingform_checklist_import_preview($result->get_data());
        echo \html_writer::start_tag('form', ['method' => 'post', 'action' => $url->out(false)]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'areaid', 'value' => $areaid]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'returnurl', 'value' => s($returnurl)]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirmimport', 'value' => 1]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'importid', 'value' => $importid]);
        echo \html_writer::tag('button', get_string('importconfirmreplace', 'gradingform_checklist'), [
            'type' => 'submit',
            'class' => 'btn btn-primary',
        ]);
        echo \html_writer::end_tag('form');
    }
}

$mform->display();
echo $OUTPUT->footer();
