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
 * The main roshine configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod
 * @subpackage roshine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/roshine/locallib.php');
/**
 * Module instance settings form
 */
class mod_roshine_mod_form extends moodleform_mod {


    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('roshinename', 'roshine'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        //$layouts = ros_get_keyboard_layouts_db();
        //$mform->addElement('select', 'layout', get_string('layout', 'roshine'), $layouts);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'roshinename', 'roshine');

        // Adding the standard "intro" and "introformat" fields
        //$this->add_intro_editor();
        $this->standard_intro_elements();

        //-------------------------------------------------------------------------------
        // Adding the rest of roshine settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
        //$mform->addElement('static', 'label1', 'roshinesetting1', 'Your roshine fields go here. Replace me!');
        //$mform->addElement('header', 'fexercise', get_string('fexercise', 'roshine'));
        //$mform->addElement('static', 'label2', 'roshinesetting2', 'Your roshine fields go here. Replace me!');
        //!!!!$mform->addElement('select', 'type', get_string('forumtype', 'forum'), $FORUM_TYPES, $attributes);
        global $CFG, $COURSE;
        //$mform->addElement('checkbox', 'isexam', get_string('isexamtext', 'roshine'));
        //$mform->setHelpButton('enablegroupfunction', array('enablegroupfunction', get_string('enablegroupfunction', 'pluginimworkingon'), 'pluginimworkingon'));
        //$mform->setDefault('isexam', 0);
        //$mform->addElement('text', 'requiredgoal', get_string('requiredgoal', 'roshine'));
        //$lsns = ros_get_typerlessons();
        //$mform->addElement('select', 'lesson', get_string('flesson', 'roshine'), $lsns);
        //$mform->addElement('select', 'exercise', get_string('fexercise', 'roshine'), ros_get_typerexercises());
        /*
         $mform->addElement('static', 'description', get_string('description', 'exercise'),
    get_string('descriptionofexercise', 'exercise', $COURSE->students)); 
         */
        
        // Open and close dates.
        $mform->addElement('date_time_selector', 'timeopen', get_string('roshineopentime', 'roshine'),
                array('optional' => true, 'step' => 1));
        //$mform->addHelpButton('timeopen', 'quizopenclose', 'quiz');

        $mform->addElement('date_time_selector', 'timeclose', get_string('roshineclosetime', 'roshine'),
                array('optional' => true, 'step' => 1));
                
        //$mform->addElement('header', 'fsecurity' get_string('fsecurity', 'roshine'));
        // Require password to begin quiz attempt.
        $mform->addElement('passwordunmask', 'password', get_string('requirepassword', 'roshine'));
        
        $mform->addElement('header', 'roshinez', get_string('pluginadministration', 'roshine'));
        $jlnk3 = $webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$COURSE->id;
        $mform->addElement('html', '<a id="jlnk3" href="'.$jlnk3.'">'.get_string('emanage', 'roshine').'</a>');
        //$mform->setType('password', PARAM_TEXT);
        //$mform->addHelpButton('password', 'requirepassword', 'roshine');
        //$mform->setAdvanced('quizpassword', $quizconfig->password_adv);
        //$mform->setDefault('password', $quizconfig->password); 
        //$mform->addElement('header', 'zakaj', "to plus");
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }
}
