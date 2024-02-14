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
 * Generator for the gradingforum_checklist plugin.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\gradingform_checklist\generator;

use gradingform_controller;
use stdClass;

/**
 * Test checklist.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class checklist {

    /** @var array $criteria The criteria for this checklist. */
    protected $criteria = [];

    /** @var string The name of this checklist. */
    protected $name;

    /** @var string A description for this checklist. */
    protected $description;

    /** @var array The checklist options. */
    protected $options = [];

    /**
     * Create a new gradingform_checklist_generator_checklist.
     *
     * @param string $name
     * @param string $description
     */
    public function __construct(string $name, string $description) {
        $this->name = $name;
        $this->description = $description;

        $this->set_option('alwaysshowdefinition', 1);
        $this->set_option('showitempointseval', 1);
        $this->set_option('showitempointstudent', 1);
        $this->set_option('enableitemremarks', 1);
        $this->set_option('enablegroupremarks', 1);
        $this->set_option('showremarksstudent', 1);

    }

    /**
     * Creates the checklist using the appropriate APIs.
     */
    public function get_definition(): stdClass {
        return (object) [
            'name' => $this->name,
            'description_editor' => [
                'text' => $this->description,
                'format' => FORMAT_HTML,
                'itemid' => 1,
            ],
            'checklist' => [
                'groups' => $this->get_all_criterion_values(),
                'options' => $this->options,
            ],
            'savechecklist' => 'Save checklist and make it ready',
            'status' => gradingform_controller::DEFINITION_STATUS_READY,
        ];
    }

    /**
     * Set an option for the checklist.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function set_option(string $key, $value): self {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * Adds a criterion to the checklist.
     *
     * @param criterion $criterion The criterion object (class below).
     * @return self
     */
    public function add_criteria(criterion $criterion): self {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * Get all criterion values.
     *
     * @return array
     */
    protected function get_all_criterion_values(): array {
        $result = [];

        foreach ($this->criteria as $index => $criterion) {
            $id = $index + 1;
            $result["NEWID{$id}"] = $criterion->get_all_values($id);
        }

        return $result;

    }
}
