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
 * Privacy Subsystem implementation.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy Subsystem implementation.
 */
class provider implements
    \core_grading\privacy\gradingform_provider_v2,
    \core_privacy\local\metadata\provider {
    /**
     * Returns metadata about this plugin.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table('gradingform_checklist_bench', [
            'definitionid' => 'privacy:metadata:definitionid',
            'benchmark' => 'privacy:metadata:benchmark',
            'benchmarkformat' => 'privacy:metadata:benchmarkformat',
            'buttonlabel' => 'privacy:metadata:buttonlabel',
            'buttonicon' => 'privacy:metadata:buttonicon',
        ], 'privacy:metadata:benchmarksummary');

        $collection->add_database_table('gradingform_checklist_fills', [
            'instanceid' => 'privacy:metadata:instanceid',
            'groupid' => 'privacy:metadata:groupid',
            'itemid' => 'privacy:metadata:itemid',
            'checked' => 'privacy:metadata:checked',
            'remark' => 'privacy:metadata:remark',
            'remarkformat' => 'privacy:metadata:remarkformat',
        ], 'privacy:metadata:fillingssummary');

        $collection->add_database_table('gradingform_checklist_obs', [
            'instanceid' => 'privacy:metadata:instanceid',
            'observationdate' => 'privacy:metadata:observationdate',
            'observationmode' => 'privacy:metadata:observationmode',
        ], 'privacy:metadata:observationsummary');

        return $collection;
    }

    /**
     * Export checklist data relating to an advanced grading instance.
     *
     * @param \context $context Context to use with the export writer.
     * @param int $instanceid The grading instance ID.
     * @param array $subcontext The directory to export this data to.
     */
    public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext): void {
        global $DB;

        $params = ['instanceid' => $instanceid];
        $sql = "SELECT cf.id,
                       cg.description AS groupdescription,
                       ci.definition AS itemdefinition,
                       ci.score,
                       cf.checked,
                       cf.remark,
                       cf.remarkformat,
                       cf.groupid,
                       cf.itemid
                  FROM {gradingform_checklist_fills} cf
                  JOIN {gradingform_checklist_groups} cg ON cg.id = cf.groupid
             LEFT JOIN {gradingform_checklist_items} ci ON ci.id = cf.itemid
                 WHERE cf.instanceid = :instanceid
              ORDER BY cg.sortorder, ci.sortorder, cf.itemid";
        $records = $DB->get_records_sql($sql, $params);

        $checklistsubcontext = array_merge($subcontext, [get_string('checklist', 'gradingform_checklist'), $instanceid]);
        if ($records) {
            writer::with_context($context)->export_data($checklistsubcontext, (object) $records);
        }

        $observation = $DB->get_record('gradingform_checklist_obs', ['instanceid' => $instanceid],
            'observationdate, observationmode');
        if ($observation) {
            writer::with_context($context)->export_data(array_merge($checklistsubcontext,
                [get_string('observationdate', 'gradingform_checklist')]), $observation);
        }
    }

    /**
     * Deletes checklist data related to the provided grading instance IDs.
     *
     * @param array $instanceids The grading instance IDs to delete information from.
     */
    public static function delete_gradingform_for_instances(array $instanceids): void {
        global $DB;

        $DB->delete_records_list('gradingform_checklist_fills', 'instanceid', $instanceids);
        $DB->delete_records_list('gradingform_checklist_obs', 'instanceid', $instanceids);
    }
}
