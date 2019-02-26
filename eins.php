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
 * This is is used to add a new lesson/category.
 *
 * Settings for category name, visibility and who can edit the exercise, are included.
 *
 * @package    mod_roshine
 * @copyright  2011 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $USER, $DB;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.

if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true);
$lessonpo = optional_param('lesson', -1, PARAM_INT);
if (isset($_POST['button'])) {
    $param1 = $_POST['button'];
}
$context = context_course::instance($id);

// DB insert.
if (isset($param1) && get_string('fconfirm', 'roshine') == $param1 ) {

    $texttotypeepo = $_POST['texttotype'];

    if ($lessonpo == -1) {
        $lsnnamepo = optional_param('lessonname', '', PARAM_TEXT);
        $lsnrecord = new stdClass();
        $lsnrecord->lessonname = $lsnnamepo;
        $lsnrecord->visible = optional_param('visible', '', PARAM_TEXT);
        $lsnrecord->editable = optional_param('editable', '', PARAM_TEXT);
        $lsnrecord->authorid = $USER->id;
        $lsnrecord->courseid = $course->id;
        $lessonid = $DB->insert_record('roshine_lessons', $lsnrecord, true);
    } else {
        $lessonid = $lessonpo;
    }
    $snum = ros_get_new_snumber($lessonid);
    $erecord = new stdClass();
    $erecord->exercisename = "".$snum;
    $erecord->snumber = $snum;
    $erecord->lesson = $lessonid;
    $erecord->texttotype = str_replace("\r\n", '\n', $texttotypeepo);
    $DB->insert_record('roshine_exercises', $erecord, false);

    // Trigger module exercise_added event.
    $event = \mod_roshine\event\exercise_added::create(array(
        'objectid' => $course->id,
        'context' => $context
    ));

    $webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$id;
    echo '<script type="text/javascript">window.location="'.$webdir.'";</script>';  // Go to page excercise.php.
}

// Print the page header.

$PAGE->set_url('/mod/roshine/eins.php', array('id' => $course->id));
$PAGE->set_title(get_string('etitle', 'roshine'));
$PAGE->set_heading(get_string('eheading', 'roshine'));

// Other things you may want to set - remove if not needed.
$PAGE->set_cacheable(false);

// Output starts here.
echo $OUTPUT->header();
$lessonsg = ros_get_typerlessons();
if (has_capability('mod/roshine:editall', context_course::instance($course->id))) {
    $lessons = $lessonsg;
} else {
    $lessons = array();
    foreach ($lessonsg as $lsng) {
        if (rosiseditablebyme($USER->id, $lsng['id'])) {
            $lessons[] = $lsng;
        }
    }
}
echo '<div align="center" style="font-size:20px;
     font-weight:bold;background:#CCC;border:2px solid #8eb6d8;-webkit-border-radius:16px;
     -moz-border-radius:16px;border-radius:16px;">';
echo '<br>';
echo '<form method="POST">';
echo '<table><tr><td>';
echo get_string('fnewexercise', 'roshine').'&nbsp;';
echo '</td><td>';
echo '<select onchange="this.form.submit()" name="lesson">';
echo '<option value="-1">'.get_string('fnewlesson', 'roshine').'</option>';
for ($ij = 0; $ij < count($lessons); $ij++) {
    if ($lessons[$ij]['id'] == $lessonpo) {
        echo '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
    } else {
        echo '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
    }
}
echo '</select>';
if ($lessonpo == -1) {
    echo '</td></tr>';
    echo '<tr><td>...'.get_string('lsnname', 'roshine').':</td>
          <td><input type="text" name="lessonname" id="lessonname"><span style="color:red;" id="namemsg">
          </span></td></tr>';
    echo '<tr><td>'.get_string('visibility', 'roshine').':</td><td> <select name="visible">';
    echo '<option value="2">'.get_string('vaccess2', 'roshine').'</option>';
    echo '<option value="1">'.get_string('vaccess1', 'roshine').'</option>';
    echo '<option value="0">'.get_string('vaccess0', 'roshine').'</option>';
    echo '</select></td></tr>';
    echo '<tr><td>'.get_string('editable', 'roshine').':</td><td> <select name="editable">';
    echo '<option value="2">'.get_string('eaccess2', 'roshine').'</option>';
    echo '<option value="1">'.get_string('eaccess1', 'roshine').'</option>';
    echo '<option value="0">'.get_string('eaccess0', 'roshine').'</option>';
    echo '</select>';

}
echo '</td></tr></table>';
?>

<script type="text/javascript">
function clClick()
{
    if(document.getElementById("lessonname").value == ""){
        document.getElementById("namemsg").innerHTML = '<?php echo get_string('reqfield', 'roshine'); ?>';
        return false;
    }
    else
        return true;
}
</script>

<?php
// echo '<br><br>'.get_string('ename', 'roshine').'<input type="text" name="exercisename">';
echo get_string('fexercise', 'roshine').':<br>'.
     '<textarea name="texttotype"  style="width: 1000px; height: 300px;"></textarea><br>'.
     '<br><input name="button" onClick="return clClick()" type="submit" value="'.get_string('fconfirm', 'roshine').'">'.
     '</form>';
echo '</div>';
echo $OUTPUT->footer();
