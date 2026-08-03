<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * External checklist definition import.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types = 1);

namespace gradingform_checklist\external;

global $CFG;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use gradingform_checklist\local\importer\json_importer;

require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');
require_once($CFG->dirroot . '/grade/grading/lib.php');

/**
 * Imports a canonical JSON checklist definition.
 */
class import_definition extends external_api {

    /**
     * Returns parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'areaid' => new external_value(PARAM_INT, 'Advanced grading area id', VALUE_REQUIRED),
            'importjson' => new external_value(PARAM_RAW, 'Canonical checklist import JSON', VALUE_REQUIRED),
            'status' => new external_value(PARAM_ALPHA, 'draft or ready', VALUE_DEFAULT, 'draft'),
        ]);
    }

    /**
     * Imports a checklist definition.
     *
     * @param int $areaid grading area id
     * @param string $importjson canonical JSON
     * @param string $status draft or ready
     * @return array
     */
    public static function execute(int $areaid, string $importjson, string $status = 'draft'): array {
        [
            'areaid' => $areaid,
            'importjson' => $importjson,
            'status' => $status,
        ] = self::validate_parameters(self::execute_parameters(), [
            'areaid' => $areaid,
            'importjson' => $importjson,
            'status' => $status,
        ]);

        $manager = get_grading_manager($areaid);
        $context = $manager->get_context();
        self::validate_context($context);
        require_capability('moodle/grade:managegradingforms', $context);

        $controller = $manager->get_controller('checklist');
        $result = (new json_importer())->parse($importjson);
        if ($result->has_errors()) {
            throw new \invalid_parameter_exception(implode(' ', $result->get_errors()));
        }

        $warnings = [];
        foreach ($result->get_warnings() as $warning) {
            $warnings[] = [
                'item' => 'import',
                'itemid' => $areaid,
                'warningcode' => 'importwarning',
                'message' => $warning,
            ];
        }

        $targetstatus = $status === 'ready'
            ? \gradingform_controller::DEFINITION_STATUS_READY
            : \gradingform_controller::DEFINITION_STATUS_DRAFT;
        $markforregrade = false;
        if ($controller->has_active_instances()) {
            $targetstatus = \gradingform_controller::DEFINITION_STATUS_READY;
            $markforregrade = true;
            $warnings[] = [
                'item' => 'import',
                'itemid' => $areaid,
                'warningcode' => 'importactiveinstances',
                'message' => get_string('importactiveinstances', 'gradingform_checklist'),
            ];
        }

        $controller->import_definition_from_data($result->get_data(), $targetstatus, $markforregrade);
        $definition = $controller->get_definition(true);

        return [
            'definitionid' => (int)$definition->id,
            'status' => $targetstatus === \gradingform_controller::DEFINITION_STATUS_READY ? 'ready' : 'draft',
            'warnings' => $warnings,
        ];
    }

    /**
     * Returns result structure.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'definitionid' => new external_value(PARAM_INT, 'Imported grading definition id'),
            'status' => new external_value(PARAM_ALPHA, 'Saved definition status'),
            'warnings' => new external_warnings(),
        ]);
    }
}
