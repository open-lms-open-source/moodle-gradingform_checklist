<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Downloads the checklist import JSON Schema.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');

if (!\gradingform_checklist\local\config::enabled('enablejsonschema')) {
    throw new moodle_exception('featuredisabled', 'gradingform_checklist');
}

use gradingform_checklist\local\importer\canonical_import_data;

$areaid = required_param('areaid', PARAM_INT);
$manager = get_grading_manager($areaid);
list($context, $course, $cm) = get_context_info_array($manager->get_context()->id);

require_login($course, true, $cm);
require_capability('moodle/grade:managegradingforms', $context);
require_sesskey();

$json = json_encode(canonical_import_data::json_schema(),
    JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

send_file(
    $json,
    'checklist-import.schema.json',
    0,
    0,
    true,
    true,
    'application/schema+json; charset=utf-8'
);
