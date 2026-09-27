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
 * Checklist import preview rendering.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

/**
 * Renders canonical checklist import data for confirmation before import.
 */
class import_preview {
    /**
     * Renders a preview of canonical import data.
     *
     * @param array $data canonical import data
     * @param \moodle_page $page target page
     * @return string
     */
    public static function render(array $data, \moodle_page $page): string {
        $groups = self::groups_for_renderer($data['groups'] ?? []);
        $options = $data['settings'] ?? \gradingform_checklist_controller::get_default_options();

        $html = \html_writer::start_tag('section', [
            'class' => 'gradingform-checklist-import-preview',
            'aria-labelledby' => 'gradingform-checklist-import-preview-heading',
        ]);
        $html .= \html_writer::tag('h3', get_string('importpreviewheading', 'gradingform_checklist'), [
            'id' => 'gradingform-checklist-import-preview-heading',
        ]);
        $html .= self::summary($data, $page);
        $html .= self::benchmark($data['benchmark'] ?? [], $page);

        $renderer = $page->get_renderer('gradingform_checklist');
        $html .= \html_writer::tag(
            'div',
            $renderer->display_checklist(
                $groups,
                $options,
                \gradingform_checklist_controller::DISPLAY_PREVIEW,
                'importpreviewchecklist'
            ),
            ['class' => 'gradingform-checklist-import-preview-checklist']
        );
        $html .= \html_writer::end_tag('section');

        return $html;
    }

    /**
     * Renders import metadata.
     *
     * @param array $data canonical import data
     * @param \moodle_page $page target page
     * @return string
     */
    protected static function summary(array $data, \moodle_page $page): string {
        $groups = $data['groups'] ?? [];
        $itemcount = 0;
        foreach ($groups as $group) {
            $itemcount += count($group['items'] ?? []);
        }

        $html = \html_writer::start_tag('dl', ['class' => 'gradingform-checklist-import-preview-summary']);
        $html .= \html_writer::tag('dt', get_string('name', 'gradingform_checklist'));
        $html .= \html_writer::tag('dd', s($data['name'] ?? ''));
        $html .= \html_writer::tag('dt', get_string('description', 'gradingform_checklist'));
        $html .= \html_writer::tag('dd', format_text($data['description'] ?? '', FORMAT_HTML, [
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $page->context,
        ]));
        $html .= \html_writer::tag('dt', get_string('importgroups', 'gradingform_checklist'));
        $html .= \html_writer::tag('dd', count($groups));
        $html .= \html_writer::tag('dt', get_string('importitems', 'gradingform_checklist'));
        $html .= \html_writer::tag('dd', $itemcount);
        $html .= \html_writer::tag('dt', get_string('benchmark', 'gradingform_checklist'));
        $html .= \html_writer::tag('dd', !empty($data['benchmark']['enabled']) ? get_string('yes') : get_string('no'));
        $html .= \html_writer::end_tag('dl');

        return $html;
    }

    /**
     * Renders benchmark guidance when the import enables it.
     *
     * @param array $benchmark canonical benchmark data
     * @param \moodle_page $page target page
     * @return string
     */
    protected static function benchmark(array $benchmark, \moodle_page $page): string {
        if (empty($benchmark['enabled']) || empty($benchmark['html'])) {
            return '';
        }

        $content = format_text($benchmark['html'], FORMAT_HTML, [
            'noclean' => false,
            'trusted' => false,
            'filter' => true,
            'context' => $page->context,
        ]);

        $html = \html_writer::start_tag('section', [
            'class' => 'gradingform-checklist-import-preview-benchmark',
            'aria-labelledby' => 'gradingform-checklist-import-preview-benchmark-heading',
        ]);
        $html .= \html_writer::tag('h4', get_string('benchmark', 'gradingform_checklist'), [
            'id' => 'gradingform-checklist-import-preview-benchmark-heading',
        ]);
        $html .= \html_writer::tag('div', $content, [
            'class' => 'gradingform-checklist-import-preview-benchmark-content',
        ]);
        $html .= \html_writer::end_tag('section');

        return $html;
    }

    /**
     * Converts canonical import groups into the checklist renderer structure.
     *
     * @param array $groups canonical groups
     * @return array
     */
    protected static function groups_for_renderer(array $groups): array {
        $rendergroups = [];
        $groupid = 1;
        $itemid = 1;
        foreach ($groups as $group) {
            $renderitems = [];
            $itemsortorder = 1;
            foreach ($group['items'] ?? [] as $item) {
                $renderitems['NEWID' . $itemid] = [
                    'definition' => s($item['definition'] ?? ''),
                    'score' => s((string)($item['score'] ?? 0)),
                    'sortorder' => $itemsortorder,
                ];
                $itemid++;
                $itemsortorder++;
            }
            $rendergroups['NEWID' . $groupid] = [
                'description' => s($group['description'] ?? ''),
                'sortorder' => $groupid,
                'items' => $renderitems,
            ];
            $groupid++;
        }

        return $rendergroups;
    }
}
