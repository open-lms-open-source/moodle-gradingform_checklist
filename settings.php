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
 * Administration settings for the Checklist advanced grading form.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Moodle core supplies the settings page when grading-form settings are loaded.
    // Keep a compatible fallback for direct loading by older Moodle versions.
    if (!isset($settings) || !($settings instanceof admin_settingpage)) {
        $settings = new admin_settingpage(
            'gradingform_checklist_settings',
            new lang_string('pluginname', 'gradingform_checklist'),
            'moodle/site:config'
        );
    }

    $settings->add(new admin_setting_heading(
        'gradingform_checklist/limitsheading',
        new lang_string('adminlimitsheading', 'gradingform_checklist'),
        new lang_string('adminlimitsdescription', 'gradingform_checklist')
    ));
    $settings->add(new admin_setting_configtext(
        'gradingform_checklist/groupdescriptionmaxchars',
        new lang_string('admingroupdescriptionmaxchars', 'gradingform_checklist'),
        new lang_string('admingroupdescriptionmaxchars_desc', 'gradingform_checklist'),
        500,
        PARAM_INT,
        10
    ));
    $settings->add(new admin_setting_configtext(
        'gradingform_checklist/itemdefinitionmaxchars',
        new lang_string('adminitemdefinitionmaxchars', 'gradingform_checklist'),
        new lang_string('adminitemdefinitionmaxchars_desc', 'gradingform_checklist'),
        1500,
        PARAM_INT,
        10
    ));

    $settings->add(new admin_setting_heading(
        'gradingform_checklist/featuresheading',
        new lang_string('adminfeaturesheading', 'gradingform_checklist'),
        new lang_string('adminfeaturesdescription', 'gradingform_checklist')
    ));
    $featuredefaults = [
        'enablewordimport' => 'adminenablewordimport',
        'enablejsonimport' => 'adminenablejsonimport',
        'enablejsonwebservice' => 'adminenablejsonwebservice',
        'enablewordtemplate' => 'adminenablewordtemplate',
        'enablejsonexample' => 'adminenablejsonexample',
        'enablejsonschema' => 'adminenablejsonschema',
        'enablebenchmarks' => 'adminenablebenchmarks',
    ];
    foreach ($featuredefaults as $name => $label) {
        $settings->add(new admin_setting_configcheckbox(
            'gradingform_checklist/' . $name,
            new lang_string($label, 'gradingform_checklist'),
            new lang_string($label . '_desc', 'gradingform_checklist'),
            (int)\gradingform_checklist\local\config::get($name)
        ));
    }

    $settings->add(new admin_setting_heading(
        'gradingform_checklist/defaultsheading',
        new lang_string('admindefaultsheading', 'gradingform_checklist'),
        new lang_string('admindefaultsdescription', 'gradingform_checklist')
    ));
    foreach ([
        'alwaysshowdefinition' => 'adminalwaysshowdefinition',
        'showitempointseval' => 'adminshowitempointseval',
        'showitempointstudent' => 'adminshowitempointstudent',
        'showgrouppointseval' => 'adminshowgrouppointseval',
        'showgrouppointstudent' => 'adminshowgrouppointstudent',
        'enableitemremarks' => 'adminenableitemremarks',
        'enablegroupremarks' => 'adminenablegroupremarks',
        'showremarksstudent' => 'adminshowremarksstudent',
        'enablebulkcheck' => 'adminenablebulkcheck',
        'requireitemcommentschecked' => 'adminrequireitemcommentschecked',
        'requireatleastoneitemcomment' => 'adminrequireatleastoneitemcomment',
        'requiregroupcommentschecked' => 'adminrequiregroupcommentschecked',
        'requireatleastonegroupcomment' => 'adminrequireatleastonegroupcomment',
    ] as $name => $label) {
        $settings->add(new admin_setting_configcheckbox(
            'gradingform_checklist/' . $name,
            new lang_string($label, 'gradingform_checklist'),
            new lang_string('admindefault_desc', 'gradingform_checklist'),
            (int)(\gradingform_checklist\local\config::option_defaults()[$name] ?? 0)
        ));
    }
    $settings->add(new admin_setting_configtext(
        'gradingform_checklist/groupremarkheading',
        new lang_string('groupremarkheading', 'gradingform_checklist'),
        new lang_string('admindefault_desc', 'gradingform_checklist'),
        '',
        PARAM_TEXT,
        50
    ));
    $settings->add(new admin_setting_configselect(
        'gradingform_checklist/observationmode',
        new lang_string('observationmode', 'gradingform_checklist'),
        new lang_string('admindefault_desc', 'gradingform_checklist'),
        'disabled',
        [
            'disabled' => new lang_string('observationmodedisabled', 'gradingform_checklist'),
            'date' => new lang_string('observationmodedate', 'gradingform_checklist'),
            'datetime' => new lang_string('observationmodedatetime', 'gradingform_checklist'),
        ]
    ));
    $settings->add(new admin_setting_configselect(
        'gradingform_checklist/observationdefault',
        new lang_string('observationdefault', 'gradingform_checklist'),
        new lang_string('admindefault_desc', 'gradingform_checklist'),
        'now',
        [
            'now' => new lang_string('observationdefaultnow', 'gradingform_checklist'),
            'blank' => new lang_string('observationdefaultblank', 'gradingform_checklist'),
        ]
    ));
}
