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
 * This file is used to remove exercises and lessons.
 *
 * Called from exercises.php when clicking on Remove all from 'xxxx' or
 * one of the remove icon/link for an individual exercise.
 *
 * @package    mod_roshine
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

use \mod_roshine\event\exercise_deleted;
use \mod_roshine\event\lesson_deleted;

// Changed to this newer format 03/01/2019.
require(__DIR__ . '/../../config.php');

require_login($course, true, $cm);

global $DB;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
$context = context_course::instance($id);

// If r is set we remove an exercise.
// if l is set we remove a lesson and all its exercises.
$exerciseid = optional_param('r', '', PARAM_TEXT);
$lessonid = optional_param('l', '', PARAM_TEXT);

if ($exerciseid) {
    $lessonpo = optional_param('lesson', '', PARAM_INT);
    $DB->delete_records('roshine_exercises', array('id' => $exerciseid));
    // Trigger module exercise_deleted event.
    $params = array(
        'objectid' => $course->id,
        'context' => $context,
        'other' => array(
            'lesson' => $lessonpo,
            'exercise' => $exerciseid
        )
    );
    $event = exercise_deleted::create($params);
    $event->trigger();
} else if ($lessonid) {
    $DB->delete_records('roshine_exercises', array('lesson' => $lessonid));
    $DB->delete_records('roshine_lessons', array('id' => $lessonid));
    $lessonpo = 0;
    // Trigger module lesson_deleted event.
    $params = array(
        'objectid' => $course->id,
        'context' => $context,
        'other' => $lessonid
    );
    $event = lesson_deleted::create($params);
    $event->trigger();
}

$cid = optional_param('id', 0, PARAM_INT);
$webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$cid.'&lesson='.$lessonpo;
header('Location: '.$webdir);

