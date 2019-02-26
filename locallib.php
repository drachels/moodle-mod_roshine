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
 * Internal library of functions for module roshine.
 *
 * All the roshine specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Get a list of Roshine keyboards.
 * @return array
 */
function ros_get_keyboard_layouts_db() {
    global $DB;
    $lss = array();
    if ($layouts = $DB->get_records('roshine_layouts')) {
        foreach ($layouts as $ex) {
            $lss[$ex->id] = $ex->name;
        }
    }
    return $lss;
}

/**
 * Get the Roshine keyboard definition to use.
 * @param int $lid
 */
function ros_get_instance_layout_file($l_id) {
    global $DB;
    $db_rec = $DB->get_record('roshine_layouts', array('id' => $l_id));
    return $db_rec->filepath;
}

/**
 * Get the Roshine keyboard keystroke checker to use.
 * @param int $lid
 */
function ros_get_instance_layout_js_file($l_id) {
    global $DB;
    $db_rec = $DB->get_record('roshine_layouts', array('id' => $l_id));
    return $db_rec->jspath;
}

/**
 * Get the last keystroke and check if correct.
 * @param int $mid
 * @return array
 */
function ros_get_last_check($m_id) {
    global $USER, $DB, $CFG;
    $sql = "SELECT * FROM ".$CFG->prefix."roshine_checks".
           " JOIN ".$CFG->prefix."roshine_attempts ON ".$CFG->prefix."roshine_attempts.id = ".$CFG->prefix."roshine_checks.attemptid".
           " WHERE ".$CFG->prefix."roshine_attempts.roshineid = ".$m_id." AND ".$CFG->prefix."roshine_attempts.userid = ".$USER->id.
           " AND ".$CFG->prefix."roshine_attempts.inprogress = 1".
           " ORDER BY ".$CFG->prefix."roshine_checks.checktime DESC LIMIT 1"; 
    if ($rec = $DB->get_record_sql($sql, array())) {
        return $rec;
    } else {
        return null;
    }
}

/**
 * Check for suspicous results.
 * @param int $checks
 * @param int $starttime
 * @return boolean
 */
function ros_suspicion($checks, $starttime) {
    for($i=1; $i<count($checks); $i++) {
        $udarci1 = $checks[$i]['mistakes'] + $checks[$i]['hits'];
        $udarci2 = $checks[($i-1)]['mistakes'] + $checks[($i-1)]['hits'];
        if ($udarci2 > ($udarci1+60)) {
            return true;
        }
        if ($checks[($i-1)]['checktime'] > ($starttime + 300)) {
            return true;
        }
    }
    return false;
}

function ros_get_typerlessons() {
    global $CFG, $DB;
    $params = array();
    $lsToReturn = array();
    $sql = "SELECT id, lessonname
              FROM ".$CFG->prefix."roshine_lessons
              ORDER BY id";
    if ($lessons = $DB->get_records_sql($sql, $params)) {
        foreach ($lessons as $ex) {
            $lss = array();
            $lss['id'] = $ex->id;
            $lss['lessonname'] = $ex->lessonname;
            $lsToReturn[] = $lss;
        }
    }
    return $lsToReturn;
} 

//Improved ros_get_typerlessons() function
function get_roshinelessons($u, $c) {
    global $CFG, $DB;
    $params = array();
    $lsToReturn = array();           // DETERMINE IF USER IS INSIDE A COURSE???
    $sql = "SELECT id, lessonname
              FROM ".$CFG->prefix."roshine_lessons
              WHERE (((visible = 2 AND authorid = ".$u.") OR
                    (visible = 1 AND ".ros_is_user_enrolled($u, $c).") OR
                    (visible = 0)) OR ".ros_can_view_edit_all($u, $c).")
              ORDER BY id";
    if ($lessons = $DB->get_records_sql($sql, $params)) {
        foreach ($lessons as $ex) {
            $lss = array();
            $lss['id'] = $ex->id;
            $lss['lessonname'] = $ex->lessonname;
            $lsToReturn[] = $lss;
        }
    }
    return $lsToReturn;
}
/**
 * This is very different than MooTyper 3.3
 * Need to change it and test!
 *
 * Check if admin or other user.
 * @param int $usr
 * @param int $c
 * @return boolean
 */
function ros_can_view_edit_all($usr, $c) {
    if ($c == 0) {
        //$cnt = get_context_instance (CONTEXT_SYSTEM);
        $cnt = context_system::instance();
    } else {
        //$cnt = get_context_instance(CONTEXT_COURSE, $c);
        $cnt = context_course::instance($c);
    }
    if (has_capability('mod/roshine:editall', $cnt)) {
        return 1;
    } else{
        return 0;
    }
}

/**
 * Check if current user can edit.
 * @param int $usr
 * @param int $lsn
 * @return boolean
 */
function rosiseditablebyme($usr, $lsn) {
    global $DB;
    $lesson = $DB->get_record('roshine_lessons', array('id' => $lsn));
    if (is_null($lesson->courseid)) {
        $crs = 0;
    } else {
        $crs = $lesson->courseid;
    }
    if ((($lesson->editable == 0) ||
        ($lesson->editable == 1 && ros_is_user_enrolled($usr, $crs)) ||
        ($lesson->editable == 2 && $lesson->authorid == $usr))
        || ros_can_view_edit_all($usr, $crs)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Very different to MooTyper 3.3
 * Need to check and test!
 *
 * Check to see if user is enrolled in current course.
 * @param int $usr
 * @param int $crs
 * @return string
 */
function ros_is_user_enrolled($usr, $crs) {
    global $DB, $CFG;
    $sql2 = "SELECT * FROM ".$CFG->prefix."user_enrolments
             WHERE userid = ".$usr." AND modifierid = ".$crs;
    $enrolls = $DB->get_records_sql($sql2, array());
    $rt = count($enrolls) > 0 ? 1 : 0;
    return $rt;
}

/**
 * Calculate averages.
 * @param int $grades
 * @return array
 */
function ros_get_grades_avg($grades) {
    $avg = array();
    $avg['mistakes'] = 0;
    $avg['timeinseconds'] = 0;
    $avg['hitsperminute'] = 0;
    $avg['fullhits'] = 0;
    $avg['precisionfield'] = 0;
    foreach($grades as $g)     {
        $avg['mistakes'] += $g->mistakes;
        $avg['timeinseconds'] += $g->timeinseconds;
        $avg['hitsperminute'] += $g->hitsperminute;
        $avg['fullhits'] += $g->fullhits;
        $avg['precisionfield'] += $g->precisionfield;
    }
    $c = count($grades);
    $avg['mistakes'] = $avg['mistakes'] / $c;
    $avg['timeinseconds'] = $avg['timeinseconds'] / $c;
    $avg['hitsperminute'] = $avg['hitsperminute'] / $c;
    $avg['fullhits'] = $avg['fullhits'] / $c;
    $avg['precisionfield'] = $avg['precisionfield'] / $c;
    
    $avg['mistakes'] = round($avg['mistakes'], 0);
    $avg['timeinseconds'] = round($avg['timeinseconds'], 0);
    $avg['hitsperminute'] = round($avg['hitsperminute'], 2);
    $avg['fullhits'] = round($avg['fullhits'], 0);
    $avg['precisionfield'] = round($avg['precisionfield'], 2);
    return $avg;
}

/**
 * Get current exercise name for current lesson.
 *
 * @return string
 */
function ros_get_typerexercises() {
    global $USER, $CFG, $DB;
    $params = array();
    $exesToReturn = array();
    $sql = "SELECT id, exercisename
              FROM ".$CFG->prefix."roshine_exercises";
    if ($exercises = $DB->get_records_sql($sql, $params)) {
        foreach ($exercises as $ex) {
            $exesToReturn[$ex->id] = $ex->exercisename;
        }
    }
    return $exesToReturn;
}

/**
 * Get current exercise for current lesson.
 *
 * @param int $less
 * @return string
 */
function ros_get_exercises_by_lesson($less) {
    global $USER, $CFG, $DB;
    $params = array();
    $toReturn = array();
    $sql = "SELECT * FROM ".$CFG->prefix."roshine_exercises WHERE lesson=".$less;
    if ($exercises = $DB->get_records_sql($sql, $params)) {
        foreach ($exercises as $ex) {
            $exesToReturn = array();
            $exesToReturn['id'] = $ex->id;
            $exesToReturn['exercisename'] = $ex->exercisename;
            $exesToReturn['snumber'] = $ex->snumber;
            $toReturn[] = $exesToReturn;
        }
    }
    return $toReturn;
}

/**
 * Get keystroke count for this lesson.
 *
 * @param int $lsnid
 * @return int
 */
function ros_get_new_snumber($lsn_id) {
    $exes = ros_get_exercises_by_lesson($lsn_id);
    if (count($exes) == 0) {
        return 1;
    }
    $max = $exes[0]['snumber'];
    for($i=0; $i<count($exes); $i++) {
        if ($exes[$i]['snumber'] > $max) {
            $max = $exes[$i]['snumber'];
        }
    }
    return $max + 1;
}

/**
 * Get info for this lesson.
 *
 * @param int $lsn
 * @return array
 */
function ros_get_typerexercisesfull($lsn = 0) {
    global $USER, $CFG, $DB;
    $params = array();
    $toReturn = array();
    $sql = "SELECT * FROM ".$CFG->prefix."roshine_exercises WHERE lesson=".$lsn." OR 0=".$lsn;
    if ($exercises = $DB->get_records_sql($sql, $params)) {
        foreach ($exercises as $ex) {
            $exesToReturn = array();
            $exesToReturn['id'] = $ex->id;
            $exesToReturn['exercisename'] = $ex->exercisename;
            $exesToReturn['texttotype'] = $ex->texttotype;
            $exesToReturn['snumber'] = $ex->snumber;
            $toReturn[] = $exesToReturn;
        }
    }
    return $toReturn;
}
