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
 * Library of interface functions and constants for module roshine
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the roshine specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage roshine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function roshine_supports($feature) {
    switch($feature) {
        case FEATURE_GROUPS;
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_GROUPMEMBERSONLY:
            return false;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;

        default:
            return null;
    }
}

/**
 * Get users for this Roshine.
 *
 * @param int $roshinerid
 * @return array, false if none.
 */
function ros_get_users_of_one_instance($roshineid)
{
    global $DB, $CFG;
    $params = array();
    $toreturn = array();
    $gradestblname = $CFG->prefix."roshine_grades";
    $userstblname = $CFG->prefix."user";
    $sql = "SELECT DISTINCT ".$userstblname.".firstname, ".$userstblname.".lastname, ".$userstblname.".id".
    " FROM ".$gradestblname.
    " LEFT JOIN ".$userstblname." ON ".$gradestblname.".userid = ".$userstblname.".id".
    " WHERE (roshine=".$roshineid.")";
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
    }
    return false;
}

/**
 * Get grades for users for this Roshine.
 *
 * @param int $roshinerid
 * @param int $exerciseid
 * @param int $userid
 * @param int $orderby
 * @param int $desc
 * @return array, false if none.
 */
function ros_get_typer_grades_adv($roshineid, $exerciseid, $userid=0, $orderby=-1, $desc=false) {
    global $DB, $CFG;
    $params = array();
    $toreturn = array();
    $gradestblname = $CFG->prefix."roshine_grades";
    $userstblname = $CFG->prefix."user";
    $exertblname = $CFG->prefix."roshine_exercises";
    $atttblname = $CFG->prefix."roshine_attempts";
    $sql = "SELECT ".$gradestblname.".id, "
                    .$userstblname.".firstname, "
                    .$userstblname.".lastname, "
                    .$userstblname.".id as u_id, "
                    .$gradestblname.".pass, "
                    .$gradestblname.".mistakes, "
                    .$gradestblname.".timeinseconds, "
                    .$gradestblname.".hitsperminute, "
                    .$atttblname.".ros_suspicion, "
                    .$gradestblname.".fullhits, "
                    .$gradestblname.".precisionfield, "
                    .$gradestblname.".timetaken, "
                    .$exertblname.".exercisename, "
                    .$gradestblname.".wpm".
    " FROM ".$gradestblname.
    " LEFT JOIN ".$userstblname." ON ".$gradestblname.".userid = ".$userstblname.".id".
    " LEFT JOIN ".$exertblname." ON ".$gradestblname.".exercise = ".$exertblname.".id".
    " LEFT JOIN ".$atttblname." ON ".$atttblname.".id = ".$gradestblname.".attemptid".
    " WHERE (roshine=".$roshineid.") AND (exercise=".$exerciseid." OR ".$exerciseid."=0) AND".
    " (".$gradestblname.".userid=".$userid." OR ".$userid."=0)";
    if ($orderby == 0 || $orderby == -1) {
        $oby = " ORDER BY ".$gradestblname.".id";
    } else if ($orderby == 1) {
        $oby = " ORDER BY ".$userstblname.".firstname";
    } else if ($orderby == 2) {
        $oby = " ORDER BY ".$userstblname.".lastname";
    } else if ($orderby == 3) {
        $oby = " ORDER BY ".$atttblname.".ros_suspicion";
    } else if ($orderby == 4) {
        $oby = " ORDER BY ".$gradestblname.".mistakes";
    } else if ($orderby == 5) {
        $oby = " ORDER BY ".$gradestblname.".timeinseconds";
    } else if ($orderby == 6) {
        $oby = " ORDER BY ".$gradestblname.".hitsperminute";
    } else if ($orderby == 7) {
        $oby = " ORDER BY ".$gradestblname.".fullhits";
    } else if ($orderby == 8) {
        $oby = " ORDER BY ".$gradestblname.".precisionfield";
    } else if ($orderby == 9) {
        $oby = " ORDER BY ".$gradestblname.".timetaken";
    } else if ($orderby == 10) {
        $oby = " ORDER BY ".$exertblname.".exercisename";
    } else if ($orderby == 11) {
        $oby = " ORDER BY ".$gradestblname.".pass";
    } else if ($orderby == 12) {
        $oby = " ORDER BY ".$gradestblname.".wpm";
    } else {
        $oby = "";
    }
    $sql .= $oby;
    if ($desc) {
        $sql .= " DESC";
    }
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
    }
    return false;
}

/**
 * Get averages for users for this Roshine.
 *
 * @param int $grads
 * @return array.
 */
function ros_get_grades_average($grads)
{
    $povprecje = array();
    $cnt = count($grads);
    $povprecje['mistakes'] = 0;
    $povprecje['timeinseconds'] = 0;
    $povprecje['hitsperminute'] = 0;
    $povprecje['precision'] = 0;
    foreach($grads as $grade)
    {
        $povprecje['mistakes'] = $povprecje['mistakes'] + $grade->mistakes; 
        $povprecje['timeinseconds']  = $povprecje['timeinseconds'] + $grade->timeinseconds;
    }
    if($cnt != 0){
        $povprecje['mistakes'] = $povprecje['mistakes'] / $cnt;
        $povprecje['timeinseconds'] = $povprecje['timeinseconds'] / $cnt;
    }
    return $povprecje;
}

/**
 * Get grades for all users.
 *
 * @param int $sid
 * @param int $orderby
 * @param int $desc
 * @return array, false if null.
 */
function ros_get_typergradesfull($sid, $orderby=-1, $desc=false) {
    global $DB, $CFG;
    $params = array();
    $toreturn = array();
    $gradestblname = $CFG->prefix."roshine_grades";
    $userstblname = $CFG->prefix."user";
    $exertblname = $CFG->prefix."roshine_exercises";
    $atttblname = $CFG->prefix."roshine_attempts";
    $sql = "SELECT ".$gradestblname.".id, "
                    .$userstblname.".firstname, "
                    .$userstblname.".lastname, "
                    .$userstblname.".id as u_id, "
                    .$gradestblname.".pass, "
                    .$atttblname.".ros_suspicion, "
                    .$gradestblname.".mistakes, "
                    .$gradestblname.".timeinseconds, "
                    .$gradestblname.".hitsperminute, "
                    .$gradestblname.".fullhits, "
                    .$gradestblname.".precisionfield, "
                    .$gradestblname.".timetaken, "
                    .$exertblname.".exercisename, "
                    .$gradestblname.".wpm".
    " FROM ".$gradestblname.
    " LEFT JOIN ".$userstblname." ON ".$gradestblname.".userid = ".$userstblname.".id".
    " LEFT JOIN ".$exertblname." ON ".$gradestblname.".exercise = ".$exertblname.".id".
    " LEFT JOIN ".$atttblname." ON ".$atttblname.".id = ".$gradestblname.".attemptid".
    " WHERE roshine=".$sid;
    if ($orderby == 0 || $orderby == -1) {
        $oby = " ORDER BY ".$gradestblname.".id";
    } else if ($orderby == 1) {
        $oby = " ORDER BY ".$userstblname.".firstname";
    } else if ($orderby == 2) {
        $oby = " ORDER BY ".$userstblname.".lastname";
    } else if ($orderby == 3) {
        $oby = " ORDER BY ".$atttblname.".ros_suspicion";
    } else if ($orderby == 4) {
        $oby = " ORDER BY ".$gradestblname.".mistakes";
    } else if ($orderby == 5) {
        $oby = " ORDER BY ".$gradestblname.".timeinseconds";
    } else if ($orderby == 6) {
        $oby = " ORDER BY ".$gradestblname.".hitsperminute";
    } else if ($orderby == 7) {
        $oby = " ORDER BY ".$gradestblname.".fullhits";
    } else if ($orderby == 8) {
        $oby = " ORDER BY ".$gradestblname.".precisionfield";
    } else if ($orderby == 9) {
        $oby = " ORDER BY ".$gradestblname.".timetaken";
    } else if ($orderby == 10) {
        $oby = " ORDER BY ".$exertblname.".exercisename";
    } else if ($orderby == 12) {
        $oby = " ORDER BY ".$gradestblname.".wpm";
    } else {
        $oby = "";
    }
    $sql .= $oby;
    if ($desc) {
        $sql .= " DESC";
    }
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
    }
    return false;
}

/**
 * Get grades for one user.
 *
 * @param int $sid
 * @param int $uid
 * @param int $orderby
 * @param int $desc
 * @return array, false if null.
 */
function ros_get_typergradesuser($sid, $uid, $orderby=-1, $desc=false) {
    global $DB, $CFG;
    $params = array();
    $toreturn = array();
    $gradestblname = $CFG->prefix."roshine_grades";
    $userstblname = $CFG->prefix."user";
    $exertblname = $CFG->prefix."roshine_exercises";
    $atttblname = $CFG->prefix."roshine_attempts";
    $sql = "SELECT ".$gradestblname.".id, "
                    .$userstblname.".firstname, "
                    .$userstblname.".lastname, "
                    .$atttblname.".ros_suspicion, "
                    .$gradestblname.".mistakes, "
                    .$gradestblname.".timeinseconds, "
                    .$gradestblname.".hitsperminute, "
                    .$gradestblname.".fullhits, "
                    .$gradestblname.".precisionfield, "
                    .$gradestblname.".pass, "
                    .$gradestblname.".timetaken, "
                    .$exertblname.".exercisename, "
                    .$gradestblname.".wpm".
    " FROM ".$gradestblname.
    " LEFT JOIN ".$userstblname." ON ".$gradestblname.".userid = ".$userstblname.".id".
    " LEFT JOIN ".$exertblname." ON ".$gradestblname.".exercise = ".$exertblname.".id".
    " LEFT JOIN ".$atttblname." ON ".$atttblname.".id = ".$gradestblname.".attemptid".
    " WHERE roshine=".$sid." AND ".$gradestblname.".userid=".$uid;
    if ($orderby == 0 || $orderby == -1) {
        $oby = " ORDER BY ".$gradestblname.".id";
    } else if ($orderby == 1) {
        $oby = " ORDER BY ".$userstblname.".firstname";
    } else if ($orderby == 2) {
        $oby = " ORDER BY ".$userstblname.".lastname";
    } else if ($orderby == 3) {
        $oby = " ORDER BY ".$atttblname.".ros_suspicion";
    } else if ($orderby == 4) {
        $oby = " ORDER BY ".$gradestblname.".mistakes";
    } else if ($orderby == 5) {
        $oby = " ORDER BY ".$gradestblname.".timeinseconds";
    } else if ($orderby == 6) {
        $oby = " ORDER BY ".$gradestblname.".hitsperminute";
    } else if ($orderby == 7) {
        $oby = " ORDER BY ".$gradestblname.".fullhits";
    } else if ($orderby == 8) {
        $oby = " ORDER BY ".$gradestblname.".precisionfield";
    } else if ($orderby == 9) {
        $oby = " ORDER BY ".$gradestblname.".timetaken";
    } else if ($orderby == 10) {
        $oby = " ORDER BY ".$exertblname.".exercisename";
    } else if ($orderby == 12) {
        $oby = " ORDER BY ".$gradestblname.".wpm";
    } else {
        $oby = "";
    }
    $sql .= $oby;
    if ($desc) {
        $sql .= " DESC";
    }
    if ($grades = $DB->get_records_sql($sql, $params)) {
        return $grades;
    }
    return false;
}
/**
 * Saves a new instance of the roshine into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $roshine An object from the form in mod_form.php
 * @param mod_roshine_mod_form $mform
 * @return int The id of the newly inserted roshine record
 */
function roshine_add_instance(stdClass $roshine, mod_roshine_mod_form $mform = null) {
    global $DB;
    $roshine->timecreated = time();
    return $DB->insert_record('roshine', $roshine);
}


function ros_get_exercise_record($eid)
{
    global $DB;
    return $DB->get_record('roshine_exercises', array('id' => $eid));
}

function ros_exam_already_done($roshine, $user_id)
{
    global $DB;
    $table = 'roshine_grades';
    $select = 'userid='.$user_id.' AND roshine='.$roshine->id; //is put into the where clause
    $result = $DB->get_records_select($table,$select);
    if(!is_null($result) && count($result) > 0){
        return true;
    }
    return false;
}

function get_exercise_from_roshine($roshine_id, $lessonid, $user_id)
{
    global $DB;
    $table = 'roshine_grades';
    $select = 'userid='.$user_id.' AND roshine='.$roshine_id.' AND pass=1'; //is put into the where clause
    $result = $DB->get_records_select($table,$select);
    if(!is_null($result) && count($result) > 0){
        $max = 0;
        //$maxID = 0;
        foreach($result as $grd)
        {
            $exRec = $DB->get_record('roshine_exercises', array('id' => $grd->exercise));
            $zap_st = $exRec->snumber;
            if($zap_st > $max){
                $max = $zap_st;
                //$maxID = $exRec->id;
            }
        }
        return $DB->get_record('roshine_exercises', array('snumber' => ($max+1), 'lesson' => $lessonid));
    }
    else
        return $DB->get_record('roshine_exercises', array('snumber' => 1, 'lesson' => $lessonid));
}

function jget_roshine_record($sid)
{
    //get_record('roshine', array('id' => $n));
    global $DB;
    return $DB->get_record('roshine', array('id' => $sid));
}
/**
 * Updates an instance of the roshine in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $roshine An object from the form in mod_form.php
 * @param mod_roshine_mod_form $mform
 * @return boolean Success/Fail
 */
function roshine_update_instance(stdClass $roshine, mod_roshine_mod_form $mform = null) {
    global $DB;
    $roshine->timemodified = time();
    //$old = $DB->get_record('roshine', array('id' => $roshine->instance));
    $roshine->id = $roshine->instance;
    return $DB->update_record('roshine', $roshine);
}

/**
 * Removes an instance of the roshine from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function roshine_delete_instance($id) {
    global $DB;
    $roshine = $DB->get_record('roshine', array('id' => $id), '*', MUST_EXIST);
    roshine_delete_all_grades($roshine);
    if (! $roshine = $DB->get_record('roshine', array('id' => $id))) {
        return false;
    }
    roshine_delete_all_checks($id);
    $DB->delete_records('roshine_attempts', array('roshineid' => $id));
    $DB->delete_records('roshine', array('id' => $roshine->id));
    return true;
}

function roshine_delete_all_checks($m_id)
{
    global $DB;
    $rcs = $DB->get_records('roshine_attempts', array('roshineid' => $m_id));
    foreach($rcs as $at)
        $DB->delete_records('roshine_checks', array('attemptid' => $at->id));
}

/**
 * Removes all grades from the database.
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $roshine Id of the module instance
 */
function roshine_delete_all_grades($roshine) {
    //global $CFG, $DB;
    global $DB;
    //require_once($CFG->dirroot . '/mod/roshine/locallib.php');
    //question_engine::delete_questions_usage_by_activities(new qubaids_for_yyyyyy($yyyyyyyyyyyy->id));
    $DB->delete_records('roshine_grades', array('roshine' => $roshine->id));
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function roshine_user_outline($course, $user, $mod, $roshine) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = 'This is an outline.';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $roshine the module instance record
 * @return void, is supposed to echp directly
 */
function roshine_user_complete($course, $user, $mod, $roshine) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in roshine activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function roshine_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link roshine_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function roshine_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see roshine_get_recent_mod_activity()}
 * @param int $activity
 * @param int $courseid
 * @param int $detail
 * @param int $modnames
 * @param int $viewfullnames
 * @return void
 */
function roshine_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function roshine_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this roshine
 *
 * Must return an array of users who are participants for a given instance
 * of roshine. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $roshineid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function roshine_get_participants($roshineid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function roshine_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of roshine?
 *
 * This function returns if a scale is being used by one roshine
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $roshineid ID of an instance of this module
 * @return bool true if the scale is used by the given roshine instance
 */
function roshine_scale_used($roshineid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('roshine', array('id' => $roshineid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of roshine.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any roshine instance
 */
function roshine_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    //if ($scaleid and $DB->record_exists('roshine', array('grade' => -$scaleid))) {
   //     return true;
   // } else {
        return false;
    //}
}

/**
 * Creates or updates grade item for the give roshine instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $roshine instance object with extra cmidnumber and modname property
 * @return void
 */
function roshine_grade_item_update(stdClass $roshine) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($roshine->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $roshine->grade;
    $item['grademin']  = 0;

    grade_update('mod/roshine', $roshine->course, 'mod', 'roshine', $roshine->id, 0, null, $item);
}

/**
 * Update roshine grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $roshine instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function roshine_update_grades(stdClass $roshine, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/roshine', $roshine->course, 'mod', 'roshine', $roshine->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function roshine_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the roshine file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function roshine_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding roshine nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the roshine module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function roshine_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the roshine settings
 *
 * This function is called when the context for the page is a roshine module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $roshinenode {@link navigation_node}
 */
function roshine_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $navref) {
    global $PAGE, $DB;

    $cm = $PAGE->cm;
    if (!$cm) {
        return;
    }

    $context = $cm->context;
    $course = $PAGE->course;

    if (!$course) {
        return;
    }

    // Link to the Add new exercise / category page.
    if (has_capability('mod/roshine:aftersetup', $cm->context)) {
        $link = new moodle_url('eins.php', array('id' => $course->id));
        $linkname = get_string('eaddnew', 'roshine');
        $icon = new pix_icon('icon', '', 'roshine', array('class' => 'icon'));
        $node = $navref->add($linkname, $link, navigation_node::TYPE_SETTING, null, null, $icon);
    }

    // Link to Import new exercise / category.
    if (has_capability('mod/roshine:aftersetup', $cm->context)) {
        $link = new moodle_url('lsnimport.php', array('id' => $course->id));
        $linkname = get_string('lsnimport', 'roshine');
        $icon = new pix_icon('icon', '', 'roshine', array('class' => 'icon'));
        $node = $navref->add($linkname, $link, navigation_node::TYPE_SETTING, null, null, $icon);
    }

    // Link to exercise management page.
    if (has_capability('mod/roshine:aftersetup', $cm->context)) {
        $link = new moodle_url('exercises.php', array('id' => $course->id));
        $linkname = get_string('editexercises', 'roshine');
        $icon = new pix_icon('icon', '', 'roshine', array('class' => 'icon'));
        $node = $navref->add($linkname, $link, navigation_node::TYPE_SETTING, null, null, $icon);
    }
}
