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
 * The main roshine configuration form. It uses the standard core 
 * Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/roshine/locallib.php');
/**
 * Module instance settings form.
 */
class mod_roshine_mod_form extends moodleform_mod {

    /**
     * @var $course Protected modifier.
     */
    protected $course = null;
    /**
     * Constructor for the base roshine class.
     *
     * @param mixed $current
     * @param mixed $section
     * @param int $cm
     * @param mixed $course The current course  if it was already loaded,
     *                      otherwise this class will load one from the context as required.
     */
    public function __construct($current, $section, $cm, $course) {
        $this->course = $course;
        parent::__construct($current, $section, $cm, $course);
    }







    /**
     * Defines the Roshine mod_form.
     */
    public function definition() {
        global $CFG, $COURSE, $DB;
        $mform = $this->_form;

        $roshineconfig = get_config('mod_roshine');

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('roshinename', 'roshine'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'roshinename', 'roshine');
        if ($CFG->branch > 28) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Roshine activity setup, Availability settings. Open and close dates.
        $mform->addElement('header', 'availabilityhdr', get_string('availability'));

        $mform->addElement('date_time_selector', 'timeopen',
                           get_string('roshineopentime', 'roshine'),
                           array('optional' => true, 'step' => 1));
        $mform->addHelpButton('timeopen', 'quizopenclose', 'quiz');

        $mform->addElement('date_time_selector', 'timeclose',
                           get_string('roshineclosetime', 'roshine'),
                           array('optional' => true, 'step' => 1));

        // Roshine activity password setup.
        $mform->addElement('header', 'fsecurity', get_string('fsecurity', 'roshine'));
        $mform->addElement('selectyesno', 'usepassword', get_string('usepassword', 'roshine'));
        $mform->addHelpButton('usepassword', 'usepassword', 'roshine');
        $mform->setDefault('usepassword', $roshineconfig->password);
        //$mform->setAdvanced('usepassword', $roshineconfig->password_adv);
        $mform->addElement('passwordunmask', 'password', get_string('password', 'roshine'));
        $mform->setDefault('password', '');
        //$mform->setAdvanced('password', $roshineconfig->password_adv);
        $mform->setType('password', PARAM_RAW);
        $mform->disabledIf('password', 'usepassword', 'eq', 0);
        $mform->disabledIf('passwordunmask', 'usepassword', 'eq', 0);

        // Roshine activity setup, Options settings.
        $mform->addElement('header', 'optionhdr', get_string('options', 'roshine'));

        // Add a dropdown slector for Required precision. 11/25/17.
        $precs = array();
        for ($i = 0; $i <= 100; $i++) {
            $precs[] = $i;
        }
        $mform->addElement('select', 'requiredgoal', get_string('requiredgoal', 'roshine'), $precs);
        $mform->addHelpButton('requiredgoal', 'requiredgoal', 'roshine');
        $mform->setDefault('requiredgoal', $roshineconfig->defaultprecision);

        // Add a dropdown slector for text alignment.
        $aligns = array(get_string('defaulttextalign_left', 'mod_roshine'),
                      get_string('defaulttextalign_center', 'mod_roshine'),
                      get_string('defaulttextalign_right', 'mod_roshine'));
        $mform->addElement('select', 'textalign', get_string('defaulttextalign', 'roshine'), $aligns);
        $mform->addHelpButton('textalign', 'defaulttextalign', 'roshine');
        $mform->setDefault('textalign', $roshineconfig->defaulttextalign);

        // Continuous typing setup.
        $mform->addElement('selectyesno', 'continuoustype', get_string('continuoustype', 'roshine'));
        $mform->addHelpButton('continuoustype', 'continuoustype', 'roshine');
        $mform->setDefault('continuoustype', $roshineconfig->continuoustype);
        $mform->setAdvanced('continuoustype', $roshineconfig->continuoustype_adv);

        // Count mistyped spaces setup.
        $mform->addElement('selectyesno', 'countmistypedspaces', get_string('countmistypedspaces', 'roshine'));
        $mform->addHelpButton('countmistypedspaces', 'countmistypedspaces', 'roshine');
        $mform->setDefault('countmistypedspaces', $roshineconfig->countmistypedspaces);
        $mform->setAdvanced('countmistypedspaces', $roshineconfig->countmistypedspaces_adv);

        // Count mistakes setup.
        $mform->addElement('selectyesno', 'countmistakes', get_string('countmistakes', 'roshine'));
        $mform->addHelpButton('countmistakes', 'countmistakes', 'roshine');
        $mform->setDefault('countmistakes', $roshineconfig->countmistakes);
        $mform->setAdvanced('countmistakes', $roshineconfig->countmistakes_adv);

        // Show keyboard setup.
        $mform->addElement('selectyesno', 'showkeyboard', get_string('showkeyboard', 'roshine'));
        $mform->addHelpButton('showkeyboard', 'showkeyboard', 'roshine');
        $mform->setDefault('showkeyboard', $roshineconfig->showkeyboard);
        $mform->setAdvanced('showkeyboard', $roshineconfig->showkeyboard_adv);

        // Add a dropdown slector for keyboard layouts. 11/22/17.
        // Use function in localib.php to get layouts.
        $layouts = ros_get_keyboard_layouts_db();
        $mform->addElement('select', 'layout', get_string('layout', 'roshine'), $layouts);
        $mform->addHelpButton('layout', 'layout', 'roshine');
        $mform->setDefault('layout', $roshineconfig->defaultlayout);

        // Add setting for statistics bar background color.
        $attributes = 'size = "20"';
        $mform->setType('statsbgc', PARAM_NOTAGS);
        $mform->addElement('text', 'statsbgc', get_string('statsbgc', 'roshine'), $attributes);
        $mform->addHelpButton('statsbgc', 'statsbgc', 'roshine');
        $mform->setDefault('statsbgc', $roshineconfig->statscolor);

        // Add setting for keytop color.
        $mform->setType('keytopbgc', PARAM_NOTAGS);
        $mform->addElement('text', 'keytopbgc', get_string('keytopbgc', 'roshine'), $attributes);
        $mform->addHelpButton('keytopbgc', 'keytopbgc', 'roshine');
        $mform->setDefault('keytopbgc', $roshineconfig->normalkeytops);

        // Add setting for keyboard background color.
        $mform->setType('keybdbgc', PARAM_NOTAGS);
        $mform->addElement('text', 'keybdbgc', get_string('keybdbgc', 'roshine'), $attributes);
        $mform->addHelpButton('keybdbgc', 'keybdbgc', 'roshine');
        $mform->setDefault('keybdbgc', $roshineconfig->keyboardbgc);

        // Add setting for cursor color.
        $mform->setType('cursorcolor', PARAM_NOTAGS);
        $mform->addElement('text', 'cursorcolor', get_string('cursorcolor', 'roshine'), $attributes);
        $mform->addHelpButton('cursorcolor', 'cursorcolor', 'roshine');
        $mform->setDefault('cursorcolor', $roshineconfig->cursorcolor);

        // Add setting for texttotype background color.
        $mform->setType('textbgc', PARAM_NOTAGS);
        $mform->addElement('text', 'textbgc', get_string('textbgc', 'roshine'), $attributes);
        $mform->addHelpButton('textbgc', 'textbgc', 'roshine');
        $mform->setDefault('textbgc', $roshineconfig->textbgc);

        // Add setting for mistyped text background color.
        $mform->setType('texterrorcolor', PARAM_NOTAGS);
        $mform->addElement('text', 'texterrorcolor', get_string('texterrorcolor', 'roshine'), $attributes);
        $mform->addHelpButton('texterrorcolor', 'texterrorcolor', 'roshine');
        $mform->setDefault('texterrorcolor', $roshineconfig->texterrorcolor);

        // Roshine activity, link to Lesson/Categories and exercises.
        $mform->addElement('header', 'roshinez', get_string('pluginadministration', 'roshine'));
        $jlnk3 = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$COURSE->id;
        $mform->addElement('html', '<a id="jlnk3" href="'.$jlnk3.'">'.get_string('emanage', 'roshine').'</a>');

        // The rest of the common activity settings.
        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();
        $this->apply_admin_defaults();
        $this->add_action_buttons();
    }
    /**
     * Enforce validation rules here.
     *
     * @param array $data Post data to validate
     * @param array $files
     * @return array
     **/
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        // Check open and close times are consistent.
        if ($data['timeopen'] != 0 && $data['timeclose'] != 0 &&
                $data['timeclose'] < $data['timeopen']) {
            $errors['timeclose'] = get_string('closebeforeopen', 'roshine');
        }
        if (!empty($data['usepassword']) && empty($data['password'])) {
            $errors['password'] = get_string('emptypassword', 'roshine');
        }
        return $errors;
    }
}
