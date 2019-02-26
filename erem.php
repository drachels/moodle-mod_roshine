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
 * @package    mod_roshine
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_login($course, true, $cm);
global $DB;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
$context = context_course::instance($id);

$exerciseid = optional_param('r', 0, PARAM_INT);

if (isset($exerciseid)) {
    $DB->delete_records('roshine_exercises', array('id' => $exerciseid));
} else {
    $lessonid = optional_param('l', 0, PARAM_INT);
    $DB->delete_records('roshine_exercises', array('lesson' => $lessonid));
    $DB->delete_records('roshine_lessons', array('id' => $lessonid));
}

// Trigger module exercise_removed event.
$event = \mod_roshine\event\exercise_removed::create(array(
    'objectid' => $course->id,
    'context' => $context
));
$event->trigger();

$cid = optional_param('id', 0, PARAM_INT);
$webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$cid;
header('Location: '.$webdir);

