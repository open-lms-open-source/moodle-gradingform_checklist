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
 * Restore tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist;

use advanced_testcase;

global $CFG;
require_once($CFG->dirroot . '/backup/moodle2/restore_plugin.class.php');
require_once($CFG->dirroot . '/backup/moodle2/restore_gradingform_plugin.class.php');
require_once($CFG->dirroot . '/grade/grading/form/checklist/backup/moodle2/restore_gradingform_checklist_plugin.class.php');

/**
 * Restore tests for gradingform_checklist.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  2026 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class restore_test extends advanced_testcase {

    /**
     * Test group feedback sentinel item ids are not remapped as checklist items.
     */
    public function test_group_feedback_itemid_zero_is_preserved(): void {
        $reflector = new \ReflectionClass(\restore_gradingform_checklist_plugin::class);
        $restoreplugin = $reflector->newInstanceWithoutConstructor();
        $method = $reflector->getMethod('get_restored_filling_itemid');
        $method->setAccessible(true);

        $this->assertSame(0, $method->invoke($restoreplugin, 0));
    }
}
