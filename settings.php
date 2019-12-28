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
 * Administration settings definitions for the Roshine module.
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/roshine/lib.php');
    require_once($CFG->dirroot.'/mod/roshine/locallib.php');

    // Availability settings.
    $settings->add(new admin_setting_heading('mod_roshine/availibility', get_string('availability'), ''));

    // Recent activity setting.
    $name = new lang_string('showrecentactivity', 'roshine');
    $description = new lang_string('showrecentactivityconfig', 'roshine');
    $settings->add(new admin_setting_configcheckbox('mod_roshine/showrecentactivity',
                                                    $name,
                                                    $description,
                                                    0));
                                                    
    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_roshine/password',
        get_string('password', 'roshine'), get_string('configpassword_desc', 'roshine'),
        array('value' => 0, 'adv' => true)));

    // Options settings.
    $settings->add(new admin_setting_heading('mod_roshine/options', get_string('options', 'roshine'), ''));

    // Default typing precision.
    $precs = array();
    for ($i=0; $i<=100; $i++) {
        $precs[] = $i;
    }
    $settings->add(new admin_setting_configselect('mod_roshine/defaultprecision',
        get_string('defaultprecision', 'roshine'), '', 97, $precs)); 

    // Default text alignment while typing an exercise.
    $settings->add(new admin_setting_configselect('mod_roshine/defaulttextalign',
        get_string('defaulttextalign', 'mod_roshine'),
        get_string('defaulttextalign_help', 'mod_roshine'), 0,
        array(get_string('defaulttextalign_left', 'mod_roshine'),
              get_string('defaulttextalign_center', 'mod_roshine'),
              get_string('defaulttextalign_right', 'mod_roshine'))));

    // Default text alignment while editing or creating an exercise.
    $settings->add(new admin_setting_configselect('mod_roshine/defaulteditalign',
        get_string('defaulteditalign', 'mod_roshine'),
        get_string('defaulteditalign_help', 'mod_roshine'), 0,
        array(get_string('defaulttextalign_left', 'mod_roshine'),
              get_string('defaulttextalign_center', 'mod_roshine'),
              get_string('defaulttextalign_right', 'mod_roshine'))));

    // Default continuous typing setting.
    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_roshine/continuoustype',
        get_string('continuoustype', 'roshine'), get_string('continuoustype_help', 'roshine'),
        array('value' => 0, 'adv' => false)));

    // Default count space as a mistake typing setting.
    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_roshine/countmistypedspaces',
        get_string('countmistypedspaces', 'roshine'), get_string('countmistypedspaces_help', 'roshine'),
        array('value' => 0, 'adv' => false)));

    // Default count each wrong keystroke as a mistake setting.
    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_roshine/countmistakes',
        get_string('countmistakes', 'roshine'), get_string('countmistakes_help', 'roshine'),
        array('value' => 0, 'adv' => false)));

    // Default show keyboard setting.
    $settings->add(new admin_setting_configcheckbox_with_advanced('mod_roshine/showkeyboard',
        get_string('showkeyboard', 'roshine'), get_string('showkeyboard_help', 'roshine'),
        array('value' => 1, 'adv' => false)));

    // Default keyboard layout.
    $layouts = ros_get_keyboard_layouts_db();
    $settings->add(new admin_setting_configselect('mod_roshine/defaultlayout',
        get_string('defaultlayout', 'roshine'), '', 2, $layouts));

    // Lesson export settings.
    $settings->add(new admin_setting_heading('mod_roshine/lesson_export', get_string('lesson_export', 'roshine'), ''));

    // Lesson export filename setting.
    $name = new lang_string('lesson_export_filename', 'roshine');
    $description = new lang_string('lesson_export_filenameconfig', 'roshine');
    $settings->add(new admin_setting_configcheckbox('mod_roshine/lesson_export_filename',
                                                    $name,
                                                    $description,
                                                    0));

    // Appearance settings.
    $settings->add(new admin_setting_heading('mod_roshine/appearance', get_string('appearance'), ''));

    // Date format setting.
    $settings->add(new admin_setting_configtext(
        'mod_roshine/dateformat',
        get_string('dateformat', 'roshine'),
        get_string('configdateformat', 'roshine'),
        'M d, Y G:i', PARAM_TEXT, 15)
    );

    // Passing grade background colour setting.
    $settings->add(new admin_setting_configcolourpicker(
    'mod_roshine/passbgc',
        get_string('passbgc_title', 'roshine'),
        get_string('passbgc_descr', 'roshine'),
        get_string('passbgc_colour', 'roshine'),
        null)
    );

    // Failing grade background colour setting.
    $settings->add(new admin_setting_configcolourpicker(
    'mod_roshine/failbgc',
        get_string('failbgc_title', 'roshine'),
        get_string('failbgc_descr', 'roshine'),
        get_string('failbgc_colour', 'roshine'),
        null)
    );

    // Suspicion marks colour setting.
    $settings->add(new admin_setting_configcolourpicker(
    'mod_roshine/suspicion',
        get_string('suspicion_title', 'roshine'),
        get_string('suspicion_descr', 'roshine'),
        get_string('suspicion_colour', 'roshine'),
        null)
    );

    // Statistics bar colour setting.
    $name = 'mod_roshine/statscolor';
    $title = get_string('statscolor_title', 'roshine');
    $description = get_string('statscolor_descr', 'roshine');
    $default = get_string('statscolor_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Key top colour setting.
    $name = 'mod_roshine/normalkeytops';
    $title = get_string('normalkeytops_title', 'roshine');
    $description = get_string('normalkeytops_descr', 'roshine');
    $default = get_string('normalkeytops_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Keyboard background colour setting.
    $name = 'mod_roshine/keyboardbgc';
    $title = get_string('keyboardbgc_title', 'roshine');
    $description = get_string('keyboardbgc_descr', 'roshine');
    $default = get_string('keyboardbgc_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Cursor colour setting.
    $name = 'mod_roshine/cursorcolor';
    $title = get_string('cursorcolor_title', 'roshine');
    $description = get_string('cursorcolor_descr', 'roshine');
    $default = get_string('cursorcolor_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Text background colour setting.
    $name = 'mod_roshine/textbgc';
    $title = get_string('textbgc_title', 'roshine');
    $description = get_string('textbgc_descr', 'roshine');
    $default = get_string('textbgc_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

    // Text error background colour setting.
    $name = 'mod_roshine/texterrorcolor';
    $title = get_string('texterrorcolor_title', 'roshine');
    $description = get_string('texterrorcolor_descr', 'roshine');
    $default = get_string('texterrorcolor_colour', 'roshine');
    $setting = new admin_setting_configcolourpicker($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $settings->add($setting);

}
