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
 * This file handles roshine exercises.
 *
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

global $USER;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.

if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true);
$context = context_course::instance($id);

// Trigger module exercise_viewed event.
$event = \mod_roshine\event\course_exercises_viewed::create(array(
    'objectid' => $course->id,
    'context' => $context
));
$event->trigger();

// Print the page header.
$PAGE->set_url('/mod/roshine/exercises.php', array('id' => $course->id));
$PAGE->set_title(get_string('etitle', 'roshine'));
$PAGE->set_heading(get_string('eheading', 'roshine'));

// Other things you may want to set - remove if not needed.
$PAGE->set_cacheable(false);

// Output starts here.
echo $OUTPUT->header();
require_once(dirname(__FILE__).'/locallib.php');

$lessonpo = optional_param('lesson', 0, PARAM_INT);

echo '<div align="center" style="font-size:20px;
     font-weight:bold;background:#CCC;
     border:2px solid #8eb6d8;-webkit-border-radius:16px;
     -moz-border-radius:16px;border-radius:16px;">';
$jlnk2 = $webdir = $CFG->wwwroot . '/mod/roshine/eins.php?id='.$id;
echo '<br>';
echo '<a href="'.$jlnk2.'">'.get_string('eaddnew', 'roshine').'</a><br><br>';

if (has_capability('mod/roshine:editall', context_course::instance($course->id))) {
    $lessons = ros_get_typerlessons();
} else {
    $lessons = get_roshinelessons($USER->id, $id);
}
if ($lessonpo == 0 && count($lessons) > 0) {
    $lessonpo = $lessons[0]['id'];
}
echo '<form method="post">';
echo get_string('excategory', 'roshine').': <select onchange="this.form.submit()" name="lesson">';
for ($ij = 0; $ij < count($lessons); $ij++) {
    if ($lessons[$ij]['id'] == $lessonpo) {
        echo '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        $selectedlessonindex = $ij;
    } else {
        echo '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
    }
}
echo '</select>';
if (rosiseditablebyme($USER->id, $lessonpo)) {
    // Add a remove all from lesson link.
    echo '<br>';
    echo ' <a onclick="return confirm(\''.get_string('removeconfirm', 'roshine').$lessons[$selectedlessonindex]['lessonname'].
    '\')" href="erem.php?id='.$course->id.'&l='.$lessons[$selectedlessonindex]['id'].'">'.
    get_string('removeall', 'roshine').'\''.$lessons[$selectedlessonindex]['lessonname'].'\'</a>';
    // Add a export lesson link next to the remove all link.



}
echo '</form><br>';
echo '<table style="border: solid;"><tr>
      <td>'.get_string('ename', 'roshine').'</td>
      <td>'.get_string('etext', 'roshine').'</td>
      <td></td></tr>';
// Print table row for each of the exercises in the lesson currently being viewed.
$exercises = ros_get_typerexercisesfull($lessonpo);
foreach ($exercises as $ex) {
    $strtocut = $ex['texttotype'];
    $strtocut = str_replace('\n', '<br>', $strtocut);
    if (strlen($strtocut) > 90) {
        $strtocut = substr($strtocut, 0, 90).'...';
    }
    $jlink = '<a href="erem.php?id='.$course->id.'&r='.$ex['id'].'">'.get_string('eremove', 'roshine').'</a>';
    $jlink2 = '<a href="eedit.php?id='.$course->id.'&ex='.$ex['id'].'">'.get_string('eeditlabel', 'roshine').'</a>';
    echo '<tr style="border-top: solid;"><td>'.$ex['exercisename'].'</td><td>'.$strtocut.'</td>';
    if (rosiseditablebyme($USER->id, $lessonpo)) {
        echo '<td>'.$jlink2.' | '.$jlink.'</td>';
    }
    echo '</tr>';
}
echo '</table>';
echo '</div>';

echo $OUTPUT->footer();
