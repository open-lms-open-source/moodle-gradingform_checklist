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
 * Generator testcase for the gradingform_checklist generator.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist;

use advanced_testcase;
use context_module;
use gradingform_checklist_controller;
use gradingform_controller;

global $CFG;
require_once($CFG->dirroot . '/grade/grading/form/checklist/lib.php');

/**
 * Generator testcase for the gradingform_checklist generator.
 *
 * @package    gradingform_checklist
 * @category   test
 * @copyright  Copyright (c) 2023 Open LMS (https://www.openlms.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_test extends advanced_testcase {

    /**
     * Test checklist editor validation accepts the configured long-text limits.
     */
    public function test_checklist_editor_validates_long_text_limits(): void {
        $this->resetAfterTest(true);

        $groupdescription = str_repeat('G', \MoodleQuickForm_checklisteditor::get_group_description_max_length());
        $itemdefinition = str_repeat('I', \MoodleQuickForm_checklisteditor::get_item_definition_max_length());

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $validvalue = [
            'groups' => [
                'NEWID1' => [
                    'description' => $groupdescription,
                    'items' => [
                        'NEWID1' => [
                            'definition' => $itemdefinition,
                            'score' => 1,
                        ],
                    ],
                ],
            ],
        ];

        $this->assertFalse($editor->validate($validvalue));

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $invalidgroupvalue = $validvalue;
        $invalidgroupvalue['groups']['NEWID1']['description'] .= 'G';
        $groupvalidation = $editor->validate($invalidgroupvalue);

        $this->assertNotFalse($groupvalidation);
        $this->assertStringContainsString(get_string('err_descriptionmax', 'gradingform_checklist',
            \MoodleQuickForm_checklisteditor::get_group_description_max_length()), $groupvalidation);

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $invaliditemvalue = $validvalue;
        $invaliditemvalue['groups']['NEWID1']['items']['NEWID1']['definition'] .= 'I';
        $itemvalidation = $editor->validate($invaliditemvalue);

        $this->assertNotFalse($itemvalidation);
        $this->assertStringContainsString(get_string('err_definitionmax', 'gradingform_checklist',
            \MoodleQuickForm_checklisteditor::get_item_definition_max_length()), $itemvalidation);
    }

    /**
     * Test checklist editor can reorder items without JavaScript.
     */
    public function test_checklist_editor_reorders_items_without_javascript(): void {
        $this->resetAfterTest(true);

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $submittedvalues = [
            'checklist' => [
                'groups' => [
                    'NEWID1' => [
                        'description' => 'Group',
                        'items' => [
                            'NEWID1' => [
                                'definition' => 'First item',
                                'score' => 1,
                                'movedown' => 1,
                            ],
                            'NEWID2' => [
                                'definition' => 'Second item',
                                'score' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $value = $editor->exportValue($submittedvalues, true);
        $items = array_values($value['checklist']['groups']['NEWID1']['items']);

        $this->assertSame('Second item', $items[0]['definition']);
        $this->assertSame(1, $items[0]['sortorder']);
        $this->assertSame('First item', $items[1]['definition']);
        $this->assertSame(2, $items[1]['sortorder']);
    }

    /**
     * Test checklist editor can add a group after an existing group without JavaScript.
     */
    public function test_checklist_editor_adds_group_after_existing_group_without_javascript(): void {
        $this->resetAfterTest(true);

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $submittedvalues = [
            'checklist' => [
                'groups' => [
                    'NEWID1' => [
                        'description' => 'Group 1',
                        'addgroupafter' => 1,
                        'items' => [
                            'NEWID1' => [
                                'definition' => 'First item',
                                'score' => 1,
                            ],
                        ],
                    ],
                    'NEWID2' => [
                        'description' => 'Group 2',
                        'items' => [
                            'NEWID2' => [
                                'definition' => 'Second item',
                                'score' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $value = $editor->exportValue($submittedvalues, true);
        $groups = array_values($value['checklist']['groups']);

        $this->assertCount(3, $groups);
        $this->assertSame('Group 1', $groups[0]['description']);
        $this->assertSame('', $groups[1]['description']);
        $this->assertSame('Group 2', $groups[2]['description']);
    }

    /**
     * Test checklist editor can add a group after the final group without JavaScript.
     */
    public function test_checklist_editor_adds_group_after_final_group_without_javascript(): void {
        $this->resetAfterTest(true);

        $editor = new \MoodleQuickForm_checklisteditor('checklist', 'Checklist');
        $submittedvalues = [
            'checklist' => [
                'groups' => [
                    'NEWID1' => [
                        'description' => 'Group 1',
                        'items' => [
                            'NEWID1' => [
                                'definition' => 'First item',
                                'score' => 1,
                            ],
                        ],
                    ],
                    'NEWID2' => [
                        'description' => 'Group 2',
                        'addgroupafter' => 1,
                        'items' => [
                            'NEWID2' => [
                                'definition' => 'Second item',
                                'score' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $value = $editor->exportValue($submittedvalues, true);
        $groups = array_values($value['checklist']['groups']);

        $this->assertCount(3, $groups);
        $this->assertSame('Group 1', $groups[0]['description']);
        $this->assertSame('Group 2', $groups[1]['description']);
        $this->assertSame('', $groups[2]['description']);
    }

    /**
     * Test groups render an add-group-after control in the full editor.
     */
    public function test_checklist_editor_renders_add_group_after_controls_between_groups(): void {
        $this->resetAfterTest(true);

        $renderer = $GLOBALS['PAGE']->get_renderer('gradingform_checklist');
        $options = gradingform_checklist_controller::get_default_options();
        $groups = [
            'NEWID1' => [
                'description' => 'Group 1',
                'sortorder' => 1,
                'items' => [
                    'NEWID1' => ['definition' => 'First item', 'score' => 1, 'sortorder' => 1],
                ],
            ],
            'NEWID2' => [
                'description' => 'Group 2',
                'sortorder' => 2,
                'items' => [
                    'NEWID2' => ['definition' => 'Second item', 'score' => 1, 'sortorder' => 1],
                ],
            ],
        ];

        $html = $renderer->display_checklist($groups, $options,
                gradingform_checklist_controller::DISPLAY_EDIT_FULL, 'checklist');

        $this->assertStringContainsString('id="checklist-groups-NEWID1-addgroupafter"', $html);
        $this->assertStringContainsString('name="checklist[groups][NEWID1][addgroupafter]"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID2-addgroupafter"', $html);
        $this->assertStringContainsString('name="checklist[groups][NEWID2][addgroupafter]"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID1-items-additem"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID2-items-additem"', $html);
        $this->assertStringNotContainsString('id="checklist-groups-addgroup"', $html);
        $this->assertStringNotContainsString('name="checklist[groups][addgroup]"', $html);
        $this->assertSame(6, substr_count($html, 'addgroupafter'));
    }

    /**
     * Test checklist editor renders only down movement controls for items.
     */
    public function test_checklist_editor_renders_only_item_move_down_controls(): void {
        $this->resetAfterTest(true);

        $renderer = $GLOBALS['PAGE']->get_renderer('gradingform_checklist');
        $options = gradingform_checklist_controller::get_default_options();
        $groups = [
            'NEWID1' => [
                'description' => 'Group 1',
                'sortorder' => 1,
                'items' => [
                    'NEWID1' => ['definition' => 'First item', 'score' => 1, 'sortorder' => 1],
                    'NEWID2' => ['definition' => 'Second item', 'score' => 1, 'sortorder' => 2],
                ],
            ],
        ];

        $html = $renderer->display_checklist($groups, $options,
                gradingform_checklist_controller::DISPLAY_EDIT_FULL, 'checklist');

        $this->assertStringContainsString('id="checklist-groups-NEWID1-moveup"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID1-movedown"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID1-items-NEWID1-movedown"', $html);
        $this->assertStringContainsString('id="checklist-groups-NEWID1-items-NEWID1-delete"', $html);
        $this->assertStringNotContainsString('id="checklist-groups-NEWID1-items-NEWID1-moveup"', $html);
        $this->assertStringNotContainsString('name="checklist[groups][NEWID1][items][NEWID1][moveup]"', $html);
    }

    /**
     * Test benchmark controls expose an initially hidden, accessible content region.
     */
    public function test_benchmark_button_has_accessible_initial_state(): void {
        $this->resetAfterTest(true);

        $renderer = $GLOBALS['PAGE']->get_renderer('gradingform_checklist');
        $html = $renderer->display_benchmark_button([
            'id' => 42,
            'content' => '<p>Teacher guidance</p>',
            'title' => 'Benchmark guidance',
            'buttonlabel' => 'Open benchmarks',
            'buttonicon' => 'fa-solid fa-file-circle-check',
        ]);

        $this->assertStringContainsString('class="benchmark-toggle btn btn-primary"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('data-benchmark-content="42"', $html);
        $this->assertStringContainsString('hidden="hidden"', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
        $this->assertStringContainsString('gradingform_checklist/benchmark', $GLOBALS['PAGE']->requires->get_end_code());
    }

    /**
     * Test benchmark button icons are limited to safe Font Awesome class tokens.
     */
    public function test_benchmark_button_icon_cleaner(): void {
        $this->assertSame(
            'fa-solid fa-file-circle-check',
            gradingform_checklist_controller::clean_benchmark_button_icon('fa-solid fa-file-circle-check')
        );
        $this->assertSame(
            'fa fa-check',
            gradingform_checklist_controller::clean_benchmark_button_icon(' fa   fa-check ')
        );
        $this->assertSame(
            gradingform_checklist_controller::DEFAULT_BENCHMARK_BUTTON_ICON,
            gradingform_checklist_controller::clean_benchmark_button_icon('fa-solid" onclick="alert(1)')
        );
        $this->assertSame(
            gradingform_checklist_controller::DEFAULT_BENCHMARK_BUTTON_ICON,
            gradingform_checklist_controller::clean_benchmark_button_icon('not-an-icon')
        );
    }

    /**
     * Test checklist creation with max-length group descriptions and item definitions.
     */
    public function test_checklist_creation_with_long_group_and_item_text(): void {
        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $groupdescription = str_repeat('G', \MoodleQuickForm_checklisteditor::get_group_description_max_length());
        $itemdefinition = str_repeat('I', \MoodleQuickForm_checklisteditor::get_item_definition_max_length());

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'longtextchecklist', 'Description', [
            $groupdescription => [
                $itemdefinition => 1,
            ],
        ]);

        $definition = $controller->get_definition();
        $groupids = array_keys($definition->checklist_groups);
        $group = $definition->checklist_groups[$groupids[0]];
        $itemids = array_keys($group['items']);
        $item = $group['items'][$itemids[0]];

        $this->assertSame($groupdescription, $group['description']);
        $this->assertSame($itemdefinition, $item['definition']);
    }

    /**
     * Test checklist creation preserves line breaks in group descriptions and item definitions.
     */
    public function test_checklist_creation_preserves_multiline_group_and_item_text(): void {
        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $groupdescription = "Portfolio criteria\n- Planning\n- Layout";
        $itemdefinition = "Use typography techniques\n- hierarchy\n- spacing\n- contrast";

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'multilinechecklist',
            'Description', [
                $groupdescription => [
                    $itemdefinition => 1,
                ],
            ]);

        $definition = $controller->get_definition();
        $groupids = array_keys($definition->checklist_groups);
        $group = $definition->checklist_groups[$groupids[0]];
        $itemids = array_keys($group['items']);
        $item = $group['items'][$itemids[0]];

        $this->assertSame($groupdescription, $group['description']);
        $this->assertSame($itemdefinition, $item['definition']);
    }

    /**
     * Test checklist defaults keep the existing preview and remark options enabled.
     */
    public function test_default_options_enable_preview_and_group_remarks_only(): void {
        $options = gradingform_checklist_controller::get_default_options();

        $this->assertSame(1, $options['alwaysshowdefinition']);
        $this->assertSame(1, $options['showremarksstudent']);
        $this->assertSame(1, $options['enablegroupremarks']);
        $this->assertSame(0, $options['enableitemremarks']);
        $this->assertSame(0, $options['showitempointseval']);
        $this->assertSame(0, $options['showitempointstudent']);
        $this->assertSame(0, $options['showgrouppointseval']);
        $this->assertSame(0, $options['showgrouppointstudent']);
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_MODE_DISABLED,
            $options['observationmode']
        );
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
            $options['observationdefault']
        );
    }

    /**
     * Test observation option cleaners only accept supported values.
     */
    public function test_observation_option_cleaners(): void {
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_MODE_DATE,
            gradingform_checklist_controller::clean_observation_mode(gradingform_checklist_controller::OBSERVATION_MODE_DATE)
        );
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_MODE_DISABLED,
            gradingform_checklist_controller::clean_observation_mode('invalid')
        );
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
            gradingform_checklist_controller::clean_observation_default(gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK)
        );
        $this->assertSame(
            gradingform_checklist_controller::OBSERVATION_DEFAULT_NOW,
            gradingform_checklist_controller::clean_observation_default('invalid')
        );
    }

    /**
     * Test observation date/time input values use browser-safe formats.
     */
    public function test_observation_date_time_input_formatters(): void {
        $timestamp = make_timestamp(2026, 8, 2, 14, 30);

        $this->assertSame('2026-08-02', gradingform_checklist_controller::format_observation_date_input($timestamp));
        $this->assertSame('14:30', gradingform_checklist_controller::format_observation_time_input($timestamp));
    }

    /**
     * Test observation date validation and storage for a grading instance.
     */
    public function test_observation_datetime_is_required_and_stored(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'checklist',
            'Description', [
                'Group 1' => [
                    'Observed skill' => 1,
                ],
            ], [
                'observationmode' => gradingform_checklist_controller::OBSERVATION_MODE_DATETIME,
                'observationdefault' => gradingform_checklist_controller::OBSERVATION_DEFAULT_BLANK,
            ]);
        $instance = $controller->create_instance($user->id, 1);

        $data = $checklistgenerator->get_submitted_form_data($controller, 1, [
            'Group 1' => [
                'score' => 1,
                'remark' => '',
                'checked' => true,
            ],
        ]);

        $this->assertFalse($instance->validate_grading_element($data));
        $this->assertTrue($instance->has_observation_date_validation_error());
        $this->assertContains(get_string('err_observationdate', 'gradingform_checklist'),
            $instance->get_grading_validation_error_messages());

        $data['observation'] = [
            'date' => '2026-08-02',
            'time' => '14:30',
        ];
        $this->assertTrue($instance->validate_grading_element($data));
        $instance->update($data);

        $record = $DB->get_record('gradingform_checklist_obs', ['instanceid' => $instance->get_id()], '*', MUST_EXIST);
        $this->assertSame(make_timestamp(2026, 8, 2, 14, 30), (int)$record->observationdate);
        $this->assertSame(gradingform_checklist_controller::OBSERVATION_MODE_DATETIME, $record->observationmode);
    }

    /**
     * Test observation date-only mode stores a date without requiring a time.
     */
    public function test_observation_date_only_does_not_require_time(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'checklist',
            'Description', [
                'Group 1' => [
                    'Observed skill' => 1,
                ],
            ], [
                'observationmode' => gradingform_checklist_controller::OBSERVATION_MODE_DATE,
            ]);
        $instance = $controller->create_instance($user->id, 1);

        $data = $checklistgenerator->get_submitted_form_data($controller, 1, [
            'Group 1' => [
                'score' => 1,
                'remark' => '',
                'checked' => true,
            ],
        ]);
        $data['observation'] = [
            'date' => '2026-08-02',
        ];

        $this->assertTrue($instance->validate_grading_element($data));
        $instance->update($data);

        $record = $DB->get_record('gradingform_checklist_obs', ['instanceid' => $instance->get_id()], '*', MUST_EXIST);
        $this->assertSame(make_timestamp(2026, 8, 2, 0, 0), (int)$record->observationdate);
        $this->assertSame(gradingform_checklist_controller::OBSERVATION_MODE_DATE, $record->observationmode);
    }

    /**
     * Test required-comment validation for checked checklist items.
     */
    public function test_required_item_comment_validation(): void {
        $groups = $this->get_required_comment_test_groups();
        $options = gradingform_checklist_controller::get_default_options();
        $options['enableitemremarks'] = 1;
        $options['requireitemcommentschecked'] = 1;

        $value = $this->get_required_comment_test_value('', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertContains('err_requireitemcommentschecked', $this->get_required_comment_error_rules($errors));
        $this->assertSame('Group 1', $errors[0]['group']);
        $this->assertSame('Item 1', $errors[0]['item']);

        $value = $this->get_required_comment_test_value('Item comment', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requireitemcommentschecked', $this->get_required_comment_error_rules($errors));
    }

    /**
     * Test at-least-one item comment validation only applies when items are checked.
     */
    public function test_required_at_least_one_item_comment_validation(): void {
        $groups = $this->get_required_comment_test_groups();
        $options = gradingform_checklist_controller::get_default_options();
        $options['enableitemremarks'] = 1;
        $options['requireatleastoneitemcomment'] = 1;

        $value = $this->get_required_comment_test_value('', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertContains('err_requireatleastoneitemcomment', $this->get_required_comment_error_rules($errors));

        $value = $this->get_required_comment_test_value('', '', false);
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requireatleastoneitemcomment', $this->get_required_comment_error_rules($errors));

        $value = $this->get_required_comment_test_value('Item comment', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requireatleastoneitemcomment', $this->get_required_comment_error_rules($errors));
    }

    /**
     * Test required-comment validation for groups with checked checklist items.
     */
    public function test_required_group_comment_validation(): void {
        $groups = $this->get_required_comment_test_groups();
        $options = gradingform_checklist_controller::get_default_options();
        $options['requiregroupcommentschecked'] = 1;

        $value = $this->get_required_comment_test_value('', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertContains('err_requiregroupcommentschecked', $this->get_required_comment_error_rules($errors));
        $this->assertSame('Group 1', $errors[0]['group']);
        $this->assertSame('group', $errors[0]['fieldtype']);

        $value = $this->get_required_comment_test_value('', 'Group comment');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requiregroupcommentschecked', $this->get_required_comment_error_rules($errors));
    }

    /**
     * Test at-least-one group comment validation only applies when items are checked.
     */
    public function test_required_at_least_one_group_comment_validation(): void {
        $groups = $this->get_required_comment_test_groups();
        $options = gradingform_checklist_controller::get_default_options();
        $options['requireatleastonegroupcomment'] = 1;

        $value = $this->get_required_comment_test_value('', '');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertContains('err_requireatleastonegroupcomment', $this->get_required_comment_error_rules($errors));

        $value = $this->get_required_comment_test_value('', '', false);
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requireatleastonegroupcomment', $this->get_required_comment_error_rules($errors));

        $value = $this->get_required_comment_test_value('', 'Group comment');
        $errors = gradingform_checklist_controller::get_required_comment_errors($groups, $options, $value);
        $this->assertNotContains('err_requireatleastonegroupcomment', $this->get_required_comment_error_rules($errors));
    }

    /**
     * Test required-comment options do not enable the matching remark fields.
     */
    public function test_required_comment_options_do_not_enable_remark_fields(): void {
        $options = gradingform_checklist_controller::get_default_options();
        $options['enableitemremarks'] = 0;
        $options['enablegroupremarks'] = 0;
        $this->assertFalse(gradingform_checklist_controller::item_remarks_enabled($options));
        $this->assertFalse(gradingform_checklist_controller::group_remarks_enabled($options));

        $options['requireitemcommentschecked'] = 1;
        $this->assertFalse(gradingform_checklist_controller::item_remarks_enabled($options));

        $options['requireitemcommentschecked'] = 0;
        $options['requireatleastoneitemcomment'] = 1;
        $this->assertFalse(gradingform_checklist_controller::item_remarks_enabled($options));

        $options['requiregroupcommentschecked'] = 1;
        $this->assertFalse(gradingform_checklist_controller::group_remarks_enabled($options));

        $options['requiregroupcommentschecked'] = 0;
        $options['requireatleastonegroupcomment'] = 1;
        $this->assertFalse(gradingform_checklist_controller::group_remarks_enabled($options));

        $options['enableitemremarks'] = 1;
        $options['enablegroupremarks'] = 1;
        $this->assertTrue(gradingform_checklist_controller::item_remarks_enabled($options));
        $this->assertTrue(gradingform_checklist_controller::group_remarks_enabled($options));
    }

    /**
     * Test required-comment validation ignores stale required options when remarks are disabled.
     */
    public function test_required_comment_validation_ignores_disabled_remark_options(): void {
        $groups = $this->get_required_comment_test_groups();
        $options = gradingform_checklist_controller::get_default_options();
        $options['enableitemremarks'] = 0;
        $options['enablegroupremarks'] = 0;
        $options['requireitemcommentschecked'] = 1;
        $options['requireatleastoneitemcomment'] = 1;
        $options['requiregroupcommentschecked'] = 1;
        $options['requireatleastonegroupcomment'] = 1;

        $errors = gradingform_checklist_controller::get_required_comment_errors(
            $groups,
            $options,
            $this->get_required_comment_test_value('', '')
        );

        $this->assertEmpty($errors);
        $options = gradingform_checklist_controller::normalise_comment_option_dependencies($options);
        $this->assertSame(0, $options['requireitemcommentschecked']);
        $this->assertSame(0, $options['requireatleastoneitemcomment']);
        $this->assertSame(0, $options['requiregroupcommentschecked']);
        $this->assertSame(0, $options['requireatleastonegroupcomment']);
    }

    /**
     * Test checklist creation.
     */
    public function test_checklist_creation(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $name = 'myfirstchecklist';
        $description = 'My first checklist';
        $criteria = [
            'Group 1' => [
                'Has title' => 1
            ],
            'Group 2' => [
                'Has references' => 1
            ],
        ];

        // Unit under test.
        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', $name, $description, $criteria);

        $this->assertInstanceOf(gradingform_checklist_controller::class, $controller);

        $definition = $controller->get_definition();
        $this->assertNotEmpty($definition->id);
        $this->assertEquals($name, $definition->name);
        $this->assertEquals($description, $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);
        $this->assertNotEmpty($definition->timecreated);
        $this->assertNotEmpty($definition->timemodified);
        $this->assertEquals($user->id, $definition->usercreated);

        $this->assertNotEmpty($definition->checklist_groups);
        $this->assertCount(2, $definition->checklist_groups);

        // Check the criteria1 criteria.
        $criteriaids = array_keys($definition->checklist_groups);

        $criteria1 = $definition->checklist_groups[$criteriaids[0]];
        $this->assertNotEmpty($criteria1['id']);
        $this->assertEquals(1, $criteria1['sortorder']);
        $this->assertEquals('Group 1', $criteria1['description']);

        $this->assertNotEmpty($criteria1['items']);
        $items = $criteria1['items'];
        $itemids = array_keys($items);

        $item = $items[$itemids[0]];
        $this->assertEquals(1, $item['score']);
        $this->assertEquals('Has title', $item['definition']);

        // Check the times criteria2 criteria.
        $criteria2 = $definition->checklist_groups[$criteriaids[1]];
        $this->assertNotEmpty($criteria2['id']);
        $this->assertEquals('Group 2', $criteria2['description']);

        $this->assertNotEmpty($criteria2['items']);
        $items = $criteria2['items'];
        $itemids = array_keys($items);

        $item = $items[$itemids[0]];
        $this->assertEquals(1, $item['score']);
        $this->assertEquals('Has references', $item['definition']);

    }

    /**
     * Test checklist creation with decimal item scores.
     */
    public function test_checklist_creation_with_decimal_score(): void {
        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'decimalchecklist',
            'Description', [
                'Group 1' => [
                    'Has decimal score' => 1.5,
                ],
            ]);

        $definition = $controller->get_definition();
        $group = reset($definition->checklist_groups);
        $item = reset($group['items']);

        $this->assertEquals(1.5, $item['score']);
    }

    /**
     * Test removing benchmark content deletes the benchmark record.
     */
    public function test_update_definition_removes_benchmark(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'benchmarkchecklist',
            'Description', [
                'Group 1' => [
                    'Has title' => 1,
                ],
            ], [
                'benchmark' => '<p>Teacher benchmark for checklist</p>',
            ]);

        $definition = $controller->get_definition();
        $this->assertTrue($DB->record_exists('gradingform_checklist_bench', ['definitionid' => $definition->id]));

        $updateddefinition = $controller->get_definition_for_editing();
        $updateddefinition->usebenchmark = 0;
        $updateddefinition->removebenchmark = 1;
        $controller->update_definition($updateddefinition);

        $definition = $controller->get_definition(true);
        $this->assertFalse($DB->record_exists('gradingform_checklist_bench', ['definitionid' => $definition->id]));
        $this->assertEmpty($definition->benchmark['benchmark']);
    }

    /**
     * Test existing benchmark content keeps the benchmark editor active when reopening the definition.
     */
    public function test_existing_benchmark_is_active_when_editing_definition(): void {
        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'benchmarkchecklist',
            'Description', [
                'Group 1' => [
                    'Has title' => 1,
                ],
            ], [
                'benchmark' => '<p>Teacher benchmark for checklist</p>',
            ]);

        $definition = $controller->get_definition_for_editing();

        $this->assertSame(1, $definition->usebenchmark);
        $this->assertSame(0, $definition->removebenchmark);
        $this->assertStringContainsString('Teacher benchmark for checklist', $definition->benchmark_editor['text']);
    }

    /**
     * Test saving an existing benchmark without changing benchmark controls preserves it.
     */
    public function test_update_definition_preserves_existing_benchmark(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'benchmarkchecklist',
            'Description', [
                'Group 1' => [
                    'Has title' => 1,
                ],
            ], [
                'benchmark' => '<p>Teacher benchmark for checklist</p>',
            ]);

        $updateddefinition = $controller->get_definition_for_editing();
        $controller->update_definition($updateddefinition);

        $definition = $controller->get_definition(true);
        $this->assertTrue($DB->record_exists('gradingform_checklist_bench', ['definitionid' => $definition->id]));
        $this->assertStringContainsString('Teacher benchmark for checklist', $definition->benchmark['benchmark']);
    }

    /**
     * Test hidden benchmark form state does not remove an existing benchmark without explicit remove intent.
     */
    public function test_update_definition_without_benchmark_editor_preserves_existing_benchmark(): void {
        global $DB;

        $this->resetAfterTest(true);

        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'benchmarkchecklist',
            'Description', [
                'Group 1' => [
                    'Has title' => 1,
                ],
            ], [
                'benchmark' => '<p>Teacher benchmark for checklist</p>',
            ]);

        $updateddefinition = $controller->get_definition_for_editing();
        $updateddefinition->usebenchmark = 0;
        $updateddefinition->removebenchmark = 0;
        unset($updateddefinition->benchmark_editor);
        $controller->update_definition($updateddefinition);

        $definition = $controller->get_definition(true);
        $this->assertTrue($DB->record_exists('gradingform_checklist_bench', ['definitionid' => $definition->id]));
        $this->assertStringContainsString('Teacher benchmark for checklist', $definition->benchmark['benchmark']);
    }

    /**
     * Test the get_item_and_criterion_for_values function.
     * This is used for finding criterion and item information within a checklist.
     */
    public function test_get_item_and_criterion_for_values(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        // Data for testing.
        $description = 'My first checklist';
        $criteria = [
            'Group 1' => [
                'Has title' => 1
            ],
            'Group 2' => [
                'Has references' => 1
            ],
        ];

        $this->setUser($user);
        $controller = $checklistgenerator->create_instance($context, 'mod_assign', 'submission', 'checklist', $description, $criteria);

        // Valid criterion and item.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertEquals('Group 1', $result['criterion']->description);
        $this->assertEquals('1', $result['item']->score);
        $this->assertEquals('Has title', $result['item']->definition);

        // Valid criterion. Invalid item.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 3);
        $this->assertEquals('Group 1', $result['criterion']->description);
        $this->assertNull($result['item']);

        // Invalid criterion.
        $result = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Foo', 0);
        $this->assertNull($result['criterion']);
    }

    /**
     * Tests for the get_test_checklist function.
     */
    public function test_get_test_checklist(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $checklist = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');
        $definition = $checklist->get_definition();

        $this->assertEquals('testchecklist', $definition->name);
        $this->assertEquals('Description text', $definition->description);
        $this->assertEquals(gradingform_controller::DEFINITION_STATUS_READY, $definition->status);

        // Should create a checklist with 2 criterion.
        $this->assertCount(2, $definition->checklist_groups);
    }

    /**
     * Test the get_submitted_form_data function.
     */
    public function test_get_submitted_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');

        $result = $checklistgenerator->get_submitted_form_data($controller, 93, [
            'Group 1' => [
                'score' => 1,
                'remark' => 'This is the first comment',
                'checked' => false,
            ],
            'Group 2' => [
                'score' => 1,
                'remark' => 'This is the second comment',
                'checked' => true,
            ],
        ]);

        $this->assertIsArray($result);
        $this->assertEquals(93, $result['itemid']);
        $this->assertIsArray($result['groups']);
        $this->assertCount(2, $result['groups']);

        $group1 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertIsArray($result['groups'][$group1['criterion']->id]);
        $this->assertArrayHasKey($group1['item']->id, $result['groups'][$group1['criterion']->id]['items']);
        $this->assertEquals('This is the first comment', $result['groups'][$group1['criterion']->id]['items'][$group1['item']->id]['remark']);

        $group2 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 2', 1);
        $this->assertIsArray($result['groups'][$group2['criterion']->id]);
        $this->assertArrayHasKey($group2['item']->id, $result['groups'][$group2['criterion']->id]['items']);
        $this->assertEquals('This is the second comment', $result['groups'][$group2['criterion']->id]['items'][$group2['item']->id]['remark']);
    }

    /**
     * Test the get_test_form_data function.
     */
    public function test_get_test_form_data(): void {
        $this->resetAfterTest(true);

        // Fetch generators.
        $generator = \testing_util::get_data_generator();
        $checklistgenerator = $generator->get_plugin_generator('gradingform_checklist');

        // Create items required for testing.
        $course = $generator->create_course();
        $module = $generator->create_module('assign', ['course' => $course]);
        $user = $generator->create_user();
        $context = context_module::instance($module->cmid);

        $this->setUser($user);
        $controller = $checklistgenerator->get_test_checklist($context, 'assign', 'submissions');

        // Unit under test.
        $result = $checklistgenerator->get_test_form_data(
            $controller,
            9999,
            1, 'This is the first comment',
            1, 'This is the second comment'
        );

        $this->assertIsArray($result);
        $this->assertEquals(9999, $result['itemid']);
        $this->assertIsArray($result['groups']);
        $this->assertCount(2, $result['groups']);

        $group1 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 1', 1);
        $this->assertIsArray($result['groups'][$group1['criterion']->id]);
        $this->assertArrayHasKey($group1['item']->id, $result['groups'][$group1['criterion']->id]['items']);
        $this->assertEquals('This is the first comment', $result['groups'][$group1['criterion']->id]['items'][$group1['item']->id]['remark']);

        $group2 = $checklistgenerator->get_item_and_criterion_for_values($controller, 'Group 2', 1);
        $this->assertIsArray($result['groups'][$group2['criterion']->id]);
        $this->assertArrayHasKey($group2['item']->id, $result['groups'][$group2['criterion']->id]['items']);
        $this->assertEquals('This is the second comment', $result['groups'][$group2['criterion']->id]['items'][$group2['item']->id]['remark']);
    }

    /**
     * Gets a minimal checklist definition for required-comment tests.
     *
     * @return array
     */
    protected function get_required_comment_test_groups(): array {
        return [
            1 => [
                'id' => 1,
                'description' => 'Group 1',
                'items' => [
                    11 => [
                        'id' => 11,
                        'definition' => 'Item 1',
                        'score' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Gets submitted checklist data for required-comment tests.
     *
     * @param string $itemremark submitted item remark
     * @param string $groupremark submitted group remark
     * @param bool $checked whether the item is checked
     * @return array
     */
    protected function get_required_comment_test_value(string $itemremark, string $groupremark, bool $checked = true): array {
        $item = [
            'remark' => $itemremark,
        ];
        if ($checked) {
            $item['id'] = 11;
        }

        return [
            'groups' => [
                1 => [
                    'items' => [
                        0 => [
                            'remark' => $groupremark,
                        ],
                        11 => $item,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns validation rule ids from structured required-comment errors.
     *
     * @param array $errors structured errors
     * @return array
     */
    protected function get_required_comment_error_rules(array $errors): array {
        return array_map(static function(array $error): string {
            return $error['rule'];
        }, $errors);
    }
}
