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
 * This file is used to edit exercise content. Called from exercises.php.
 *
 * @package    mod_roshine
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $DB, $USER;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
$exerciseid = optional_param('ex', 0, PARAM_INT);

if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
if ($exerciseid == 0) {
    error('No exercise to edit!');
}

require_login($course, true);
if (isset($_POST['button'])) {
    $param1 = $_POST['button'];
}
if (isset($param1) && get_string('fconfirm', 'roshine') == $param1 ) {
    $newtext = optional_param('texttotype', '', PARAM_CLEANHTML);
    $rcrd = $DB->get_record('roshine_exercises', array('id' => $exerciseid), '*', MUST_EXIST);
    $updr = new stdClass();
    $updr->id = $rcrd->id;
    $updr->texttotype = str_replace("\r\n", '\n', $newtext);
    $updr->exercisename = $rcrd->exercisename;
    $updr->lesson = $rcrd->lesson;
    $updr->snumber = $rcrd->snumber;
    $DB->update_record('roshine_exercises', $updr);
    $webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$id;
    echo '<script type="text/javascript">window.location="'.$webdir.'";</script>';
}

$PAGE->set_url('/mod/roshine/eedit.php', array('id' => $course->id, 'ex' => $exerciseid));
$PAGE->set_title(get_string('etitle', 'roshine'));
$PAGE->set_heading(get_string('eheading', 'roshine'));
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
$exercisetoedit = $DB->get_record('roshine_exercises', array('id' => $exerciseid), 'texttotype', MUST_EXIST);
echo '<div align="center" style="font-size:20px;
     font-weight:bold;background:#CCC;
     border:2px solid #8eb6d8;-webkit-border-radius:16px;-moz-border-radius:16px;border-radius:16px;">';
echo '<form method="POST">';
echo '<br>'.get_string('fexercise', 'roshine').':<br>'.
     '<textarea name="texttotype" style="width: 800px; height: 300px;">'.$exercisetoedit->texttotype.'</textarea><br>'.
     '<br><input name="button" type="submit" value="'.get_string('fconfirm', 'roshine').'">'.
     '</form>';
echo '</div>';
echo $OUTPUT->footer();
