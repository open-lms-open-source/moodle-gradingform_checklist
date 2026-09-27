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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Checklist import upload form.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

use gradingform_checklist\local\config;

/**
 * Uploads a DOCX or JSON checklist import file.
 */
class gradingform_checklist_import_form extends moodleform {
    /**
     * Defines the form.
     */
    public function definition(): void {
        $form = $this->_form;

        $form->addElement('hidden', 'areaid');
        $form->setType('areaid', PARAM_INT);

        $form->addElement('hidden', 'returnurl');
        $form->setType('returnurl', PARAM_RAW);

        $acceptedtypes = [];
        if (config::enabled('enablewordimport')) {
            $acceptedtypes[] = '.docx';
        }
        if (config::enabled('enablejsonimport')) {
            $acceptedtypes[] = '.json';
        }
        $form->addElement('filepicker', 'importfile', get_string('importfile', 'gradingform_checklist'), null, [
            'accepted_types' => $acceptedtypes,
            'maxbytes' => get_max_upload_file_size(),
        ]);
        $form->addRule('importfile', get_string('required'), 'required');

        $this->add_action_buttons(true, get_string('importpreview', 'gradingform_checklist'));
    }

    /**
     * Validates the uploaded file extension.
     *
     * @param array $data submitted data
     * @param array $files submitted files
     * @return array
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);
        $filename = $this->get_new_filename('importfile');
        if ($filename === false || $filename === '') {
            if (!isset($errors['importfile'])) {
                $errors['importfile'] = get_string('required');
            }
            return $errors;
        }

        if ($filename !== '') {
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $allowed = ($extension === 'docx' && config::enabled('enablewordimport'))
                || ($extension === 'json' && config::enabled('enablejsonimport'));
            if (!$allowed) {
                $errors['importfile'] = get_string('importerrorfiletype', 'gradingform_checklist');
            }
        }
        return $errors;
    }
}
