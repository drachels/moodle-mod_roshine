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
 * Define all the restore steps that will be used by the restore_roshine_activity_task.
 *
 * @package mod_roshine
 * @copyright 2016 onwards AL Rachels (drachels@drachels.com).
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Define the complete assignment structure for restore, with file and id annotations.
 *
 * Structure step to restore one roshine activity.
 *
 * @package mod_roshine
 * @copyright 2016 onwards AL Rachels (drachels@drachels.com).
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class restore_roshine_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the structure of the restore workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        $paths = array();
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $paths[] = new restore_path_element('roshine', '/activity/roshine');
        if ($userinfo) {
            $paths[] = new restore_path_element('roshine_grade', '/activity/roshine/grades/grade');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process an assign restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the roshine record.
        $newitemid = $DB->insert_record('roshine', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process a submission restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_attempt($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->roshine = $this->get_new_parentid('roshine');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->time = $this->apply_date_offset($data->time);

        $newitemid = $DB->insert_record('roshine_attempts', $data);
        $this->set_mapping('roshine_attempt', $oldid, $newitemid);
    }
    /**
     * Process a check restore - TODO: Check and remove as I am pretty sure this function is not needed.
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_check($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->roshine = $this->get_new_parentid('roshine');
        $data->starttime = $this->apply_date_offset($data->starttime);
        $data->endtime = $this->apply_date_offset($data->endtime);

        $newitemid = $DB->insert_record('roshine_checks', $data);
    }

    /**
     * Process a grade restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->roshine = $this->get_new_parentid('roshine');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timetaken = $this->apply_date_offset($data->timetaken);

        $newitemid = $DB->insert_record('roshine_grades', $data);
        $this->set_mapping('roshine_grade', $oldid, $newitemid);
    }

    /**
     * Process a layout restore - TODO: Check on remving this as I am pretty sure it is not needed.
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_layout($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->roshine = $this->get_new_parentid('roshine');

        $newitemid = $DB->insert_record('roshine_layouts', $data);
        $this->set_mapping('roshine_layout', $oldid, $newitemid);
    }
    /**
     * Process a lesson restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_lesson($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->roshine = $this->get_new_parentid('roshine');

        $newitemid = $DB->insert_record('roshine_lessons', $data);
        $this->set_mapping('roshine_lesson', $oldid, $newitemid);
    }

    /**
     * Process an exercise restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_roshine_exercise($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $oldlesson = $data->lesson;

        $data->roshine = $this->get_new_parentid('lesson');

        $newitemid = $DB->insert_record('roshine_exercises', $data);
        $this->set_mapping('roshine_exercise', $oldid, $newitemid);
        $this->set_mapping('roshine_exercise', $oldlesson, $newitemid);
    }

    /**
     * Once the database tables have been fully restored, restore the files
     * @return void
     */
    protected function after_execute() {
        // Add roshine related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_roshine', 'intro', null);
    }
}
