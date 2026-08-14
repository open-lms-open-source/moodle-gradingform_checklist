<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Downloads the DOCX checklist import template.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

if (!\gradingform_checklist\local\config::enabled('enablewordtemplate')) {
    throw new moodle_exception('featuredisabled', 'gradingform_checklist');
}

$areaid = required_param('areaid', PARAM_INT);
$manager = get_grading_manager($areaid);
list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);
require_sesskey();

$template = __DIR__ . '/docs/checklist-import-template.docx';
if (!is_readable($template)) {
    send_file_not_found();
}

send_file(
    $template,
    'checklist-import-template.docx',
    0,
    0,
    false,
    true,
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
);
