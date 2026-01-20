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

/**
 * Convenience class to create checklist criterion.
 *
 * @package    gradingform_checklist
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class criterion {
    /** @var string $description A description of the criterion. */
    public $description;

    /** @var array $items The items for this criterion. */
    public $items = [];

    /** @var integer $sortorder sort order of the criterion. */
    public $sortorder = 0;

    /**
     * Constructor for this test_criterion object
     *
     * @param string $description A description of this criterion.
     * @param array $items
     */
    public function __construct(string $description, array $items = []) {
        $this->description = $description;
        foreach ($items as $definition => $score) {
            $this->add_item($definition, $score, 1);
        }
    }

    /**
     * Adds items to the criterion.
     *
     * @param string $definition The definition for this items.
     * @param int $score The score received if this item is selected.
     * @param int $sortorder
     * @return self
     */
    public function add_item(string $definition, int $score, int $sortorder): self {
        $this->items[] = [
            'definition' => $definition,
            'score' => $score,
            'sortorder' => $sortorder,

        ];

        return $this;
    }

    /**
     * Get the description for this criterion.
     *
     * @return string
     */
    public function get_description(): string {
        return $this->description;
    }

    /**
     * Get the items for this criterion.
     *
     * @return array
     */
    public function get_items(): array {
        return $this->items;
    }

    /**
     * Get all values in an array for use when creating a new guide.
     * @return array
     */
    public function get_all_values(): array {
        return [
            'description' => $this->get_description(),
            'items' => $this->get_all_item_values(),
            'sortorder' => 1,
        ];
    }

    /**
     * Get all item values.
     *
     * @return array
     */
    public function get_all_item_values(): array {
        $result = [];

        foreach ($this->get_items() as $index => $item) {
            $id = $index + 1;
            $result["NEWID{$id}"] = $item;
        }

        return $result;
    }
}
