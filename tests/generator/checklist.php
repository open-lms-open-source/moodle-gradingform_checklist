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
 * Generator for the gradingform_checklist plugin.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tests\gradingform_checklist\generator;

use gradingform_checklist_controller;
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

    /** @var string Teacher-only benchmark content for this checklist. */
    protected $benchmark = '';

    /** @var int Text format for benchmark content. */
    protected $benchmarkformat = FORMAT_HTML;

    /** @var string Benchmark button label. */
    protected $benchmarkbuttonlabel = 'Open to view Benchmarks';

    /** @var string Benchmark button icon. */
    protected $benchmarkbuttonicon = 'fa-solid fa-file-circle-check';

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
        $this->set_option('showitempointseval', 0);
        $this->set_option('showitempointstudent', 0);
        $this->set_option('showgrouppointseval', 0);
        $this->set_option('showgrouppointstudent', 0);
        $this->set_option('enableitemremarks', 0);
        $this->set_option('enablegroupremarks', 1);
        $this->set_option('showremarksstudent', 1);
        $this->set_option('enablebulkcheck', 1);
        $this->set_option('groupremarkheading', '');
        $this->set_option('observationmode', gradingform_checklist_controller::OBSERVATION_MODE_DISABLED);
        $this->set_option('observationdefault', gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW);
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
            'benchmark_editor' => [
                'text' => $this->benchmark,
                'format' => $this->benchmarkformat,
                'itemid' => 0,
            ],
            'usebenchmark' => trim($this->benchmark) === '' ? 0 : 1,
            'removebenchmark' => 0,
            'benchmarkbuttonlabel' => $this->benchmarkbuttonlabel,
            'benchmarkbuttonicon' => $this->benchmarkbuttonicon,
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
     * Sets checklist benchmark content.
     *
     * @param string $benchmark
     * @param int $format
     * @param string $buttonlabel
     * @param string $buttonicon
     * @return self
     */
    public function set_benchmark(
        string $benchmark,
        int $format = FORMAT_HTML,
        string $buttonlabel = 'Open to view Benchmarks',
        string $buttonicon = 'fa-solid fa-file-circle-check'
    ): self {
        $this->benchmark = $benchmark;
        $this->benchmarkformat = $format;
        $this->benchmarkbuttonlabel = $buttonlabel;
        $this->benchmarkbuttonicon = $buttonicon;
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
