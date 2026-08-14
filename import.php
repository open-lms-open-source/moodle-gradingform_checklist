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
require_once($CFG->dirroot . '/grade/grading/form/checklist/classes/local/importer/import_preview.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->libdir . '/filelib.php');

use gradingform_checklist\local\importer\canonical_import_data;
use gradingform_checklist\local\importer\docx_importer;
use gradingform_checklist\local\importer\import_preview;
use gradingform_checklist\local\importer\json_importer;
use gradingform_checklist\local\config;

$areaid = required_param('areaid', PARAM_INT);
$returnurl = trim(optional_param('returnurl', '', PARAM_RAW));
$confirmimport = optional_param('confirmimport', 0, PARAM_BOOL);

$manager = get_grading_manager($areaid);
list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);

if (!config::enabled('enablewordimport') && !config::enabled('enablejsonimport')) {
    throw new moodle_exception('importdisabled', 'gradingform_checklist');
}

$controller = $manager->get_controller('checklist');
if ($returnurl !== '' && strpos($returnurl, $CFG->wwwroot . '/') === 0) {
    $returnurl = substr($returnurl, strlen($CFG->wwwroot));
}
$returnurl = clean_param($returnurl, PARAM_LOCALURL);
if ($returnurl === '') {
    $returnurl = $manager->get_management_url()->out_as_local_url(false);
}

$url = new \core\url('/grade/grading/form/checklist/import.php', ['areaid' => $areaid, 'returnurl' => $returnurl]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('importchecklist', 'gradingform_checklist'));
$PAGE->set_heading(get_string('importchecklist', 'gradingform_checklist'));
$PAGE->add_body_class('gradingform-checklist-import-page');

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
 * Converts an unexpected import failure into a user-facing import result.
 *
 * @param \Throwable $exception unexpected failure
 * @return canonical_import_data
 */
function gradingform_checklist_import_unexpected_error(\Throwable $exception): canonical_import_data {
    debugging('Checklist import failed: ' . $exception->getMessage(), DEBUG_DEVELOPER);
    return new canonical_import_data([], [], [get_string('importerrorunexpected', 'gradingform_checklist')]);
}

/**
 * Parses the file selected by Moodle's filepicker from the user's draft file area.
 *
 * This keeps the standard Moodle File Picker, including repositories and private files,
 * but avoids moodleform::get_data() for this page because that validation path can fail
 * before the importer receives the selected draft file.
 *
 * @param int $draftitemid submitted draft item id
 * @param string|null $extension detected source extension
 * @return canonical_import_data
 */
function gradingform_checklist_import_parse_draft_file(int $draftitemid, ?string &$extension = null): canonical_import_data {
    global $USER;

    if ($draftitemid <= 0) {
        return new canonical_import_data([], [], [get_string('required')]);
    }

    $fs = get_file_storage();
    $usercontext = \context_user::instance($USER->id);
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id DESC', false);
    if (empty($files)) {
        return new canonical_import_data([], [], [get_string('required')]);
    }

    /** @var \stored_file $file */
    $file = reset($files);
    $filename = clean_param($file->get_filename(), PARAM_FILE);
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($extension === 'json') {
        if (!config::enabled('enablejsonimport')) {
            throw new \moodle_exception('importdisabled', 'gradingform_checklist');
        }
        return (new json_importer())->parse($file->get_content());
    }

    if ($extension === 'docx') {
        if (!config::enabled('enablewordimport')) {
            throw new \moodle_exception('importdisabled', 'gradingform_checklist');
        }
        $tempdir = make_request_directory();
        $safefilename = $filename !== '' ? $filename : 'checklist-import.docx';
        $temppath = $tempdir . '/' . $safefilename;
        if (!$file->copy_content_to($temppath)) {
            throw new \moodle_exception('importerrorunexpected', 'gradingform_checklist');
        }
        return (new docx_importer())->parse($temppath);
    }

    return new canonical_import_data([], [], [get_string('importerrorfiletype', 'gradingform_checklist')]);
}

$result = null;
$importid = null;

if ($confirmimport) {
    try {
        require_sesskey();
        $importid = required_param('importid', PARAM_ALPHANUM);
        $stored = $SESSION->gradingform_checklist_import[$areaid][$importid] ?? '';
        $sourceextension = is_array($stored) ? ($stored['extension'] ?? '') : '';
        $stored = is_array($stored) ? ($stored['data'] ?? '') : $stored;
        if (($sourceextension === 'json' && !config::enabled('enablejsonimport'))
                || ($sourceextension === 'docx' && !config::enabled('enablewordimport'))) {
            throw new \moodle_exception('importdisabled', 'gradingform_checklist');
        }
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
    } catch (\Throwable $exception) {
        $result = gradingform_checklist_import_unexpected_error($exception);
    }
}

$mform = new gradingform_checklist_import_form($url);
$mform->set_data(['areaid' => $areaid, 'returnurl' => $returnurl]);

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if (!$confirmimport && optional_param('submitbutton', '', PARAM_TEXT) !== '') {
    try {
        require_sesskey();
        $draftitemid = file_get_submitted_draft_itemid('importfile');
        $sourceextension = null;
        $result = gradingform_checklist_import_parse_draft_file((int)$draftitemid, $sourceextension);
    } catch (\Throwable $exception) {
        $result = gradingform_checklist_import_unexpected_error($exception);
    }

    if ($result && !$result->has_errors()) {
        $encoded = $result->encode();
        if ($encoded === '') {
            $result = new canonical_import_data([], [], [get_string('importerrorunexpected', 'gradingform_checklist')]);
        } else {
            $importid = random_string(32);
            if (!isset($SESSION->gradingform_checklist_import)) {
                $SESSION->gradingform_checklist_import = [];
            }
            if (!isset($SESSION->gradingform_checklist_import[$areaid])) {
                $SESSION->gradingform_checklist_import[$areaid] = [];
            }
            $SESSION->gradingform_checklist_import[$areaid][$importid] = [
                'data' => $encoded,
                'extension' => $sourceextension,
            ];
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('importchecklist', 'gradingform_checklist'));

$downloads = [];
if (config::enabled('enablewordtemplate')) {
    $downloads[] = \html_writer::link(new \core\url('/grade/grading/form/checklist/template.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadwordtemplate', 'gradingform_checklist'),
        ['class' => 'btn btn-secondary']);
}
if (config::enabled('enablejsonexample')) {
    $downloads[] = \html_writer::link(new \core\url('/grade/grading/form/checklist/jsonexample.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadjsonexample', 'gradingform_checklist'),
        ['class' => 'btn btn-secondary']);
}
if (config::enabled('enablejsonschema')) {
    $downloads[] = \html_writer::link(new \core\url('/grade/grading/form/checklist/jsonschema.php',
        ['areaid' => $areaid, 'sesskey' => sesskey()]), get_string('downloadjsonschema', 'gradingform_checklist'),
        ['class' => 'btn btn-secondary']);
}
if ($downloads) {
    echo \html_writer::div(implode(' ', $downloads), 'gradingform-checklist-import-downloads');
}

if ($result) {
    echo gradingform_checklist_import_messages($result->get_errors(), \core\output\notification::NOTIFY_ERROR);
    echo gradingform_checklist_import_messages($result->get_warnings(), \core\output\notification::NOTIFY_WARNING);
    if (!$result->has_errors()) {
        if ($controller->has_active_instances()) {
            echo $OUTPUT->notification(get_string('importactiveinstances', 'gradingform_checklist'),
                \core\output\notification::NOTIFY_WARNING);
        }
        echo import_preview::render($result->get_data(), $PAGE);
        echo \html_writer::start_tag('form', [
            'method' => 'post',
            'action' => $url->out(false),
            'class' => 'gradingform-checklist-import-confirm',
        ]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'areaid', 'value' => $areaid]);
        echo \html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'returnurl', 'value' => $returnurl]);
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
