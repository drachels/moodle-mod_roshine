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

use \mod_roshine\event\exercise_added;

// Changed to this newer format 03/01/2019.
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');

global $USER, $DB;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
$lsnnamepo = optional_param('lesson', '', PARAM_TEXT);

if ($id) {
    $course     = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true);
$lessonpo = optional_param('lesson', -1, PARAM_INT);

$context = context_course::instance($id);

// Check to see if Confirm button is clicked and returning 'Confirm' to trigger insert record.
$param1 = optional_param('button', '', PARAM_TEXT);;

// DB insert.
if (isset($param1) && get_string('fconfirm', 'roshine') == $param1 ) {

    $texttotypeepo = optional_param('texttotype', '', PARAM_RAW);

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
    $webdir = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$id.'&lesson='.$lessonid;

    // If adding a new lesson and first exercise, get lesson name.
    if ($lsnnamepo) {
        $lesson = $lsnnamepo;
    } else {
        // If adding an exercise to existing lesson, get the lesson id.
        $lesson = $lessonpo;
    }

    echo '<script type="text/javascript">window.location="'.$webdir.'";</script>';  // Go to page excercise.php.

    // Trigger module exercise_added event.
    $params = array(
        'objectid' => $course->id,
        'context' => $context,
        'other' => array(
            'lesson' => $lesson,
            'exercisename' => $erecord->exercisename
        )
    );
    $event = exercise_added::create($params);
    $event->trigger();
}

// Get all the default configuration settings for Roshine.
$roscfg = get_config('mod_roshine');

// Check to see if configuration for Roshine defaulteditalign is set.
if (isset($roscfg->defaulteditalign)) {
    // Current Roshine edittalign is set so use it.
    $editalign = optional_param('editalign', $roscfg->defaulteditalign, PARAM_INT);
    $align = $editalign;
} else {
    // Current Roshine edittalign is NOT set so set it to left.
    $editalign = optional_param('editalign', 0, PARAM_INT);
    $align = $editalign;
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
        if (rosiseditablebyme($USER->id, $id, $lsng['id'])) {
            $lessons[] = $lsng;
        }
    }
}

$color3 = $roscfg->keyboardbgc;
echo '<div align="center" style="font-size:20px;
    font-weight:bold;background: '.$color3.';
    border:2px solid #8eb6d8;-webkit-border-radius:16px;
    -moz-border-radius:16px;border-radius:16px;">';

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
    if (is_siteadmin()) {
        echo '<option value="0">'.get_string('vaccess0', 'roshine').'</option>';
    }
    echo '</select></td></tr>';
    echo '<tr><td>'.get_string('editable', 'roshine').':</td><td> <select name="editable">';
    echo '<option value="2">'.get_string('eaccess2', 'roshine').'</option>';
    echo '<option value="1">'.get_string('eaccess1', 'roshine').'</option>';
    if (is_siteadmin()) {
        echo '<option value="0">'.get_string('eaccess0', 'roshine').'</option>';
    }
    echo '</select>';
}
echo '</td></tr></table>';
?>

<script type="text/javascript">
function isLetter(str) {
    var pattern = /[!-ﻼ]/i;
    return str.length === 1 && str.match(pattern);
}
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

var ok = true;

function clClick() {
    var exercise_text = document.getElementById("texttotype").value;
    var allowed_chars = ['\\', '~', '!', '@', '#', '$', '%', '^', '&', '(', ')',
                         '*', '_', '+', ':', ';', '"', '{', '}', '>', '<', '?', '\'',
                         '-', '/', '=', '.', ',', ' ', '|', '¡', '`', 'ç', 'ñ', 'º',
                         '¿', 'ª', '·', '\n', '\r', '\r\n', '\n\r', ']', '[', '¬',
                         '´', '`', '§', '°', '€', '¦', '¢', '£', '₢', '¹', '²', '³',
                         '¨', 'Ё', '№', 'ё', 'ë', 'ù', 'µ', 'ï','÷', '×', 'ł', 'Ł', 'ß',
                         '¤', '«', '»', '₪', '־', 'װ', 'ױ', 'ײ', 'ˇ', '½'];
    var shown_text = "";
    ok = true;
    for(var i=0; i<exercise_text.length; i++) {
        if(!isLetter(exercise_text[i]) && !isNumber(exercise_text[i]) && allowed_chars.indexOf(exercise_text[i]) == -1) {
            shown_text += '<span style="color: red;">'+exercise_text[i]+'</span>';
            ok = false;
        }
        else
            shown_text += exercise_text[i];
    }
    if(!ok) {
        document.getElementById('text_holder_span').innerHTML = shown_text;
        return false;
    }
    if (document.getElementById("lessonname").value == "") {
        document.getElementById("namemsg").innerHTML = '<?php echo get_string('reqfield', 'roshine'); ?>';
        return false;
    } else {
        return true;
    }
}
</script>

<?php
// Get our alignment strings and add a selector for text alignment.
$aligns = array(get_string('defaulttextalign_left', 'mod_roshine'),
              get_string('defaulttextalign_center', 'mod_roshine'),
              get_string('defaulttextalign_right', 'mod_roshine'));
echo '<br><br><span id="editalign" class="">'.get_string('defaulttextalign', 'roshine').': ';
echo '<select onchange="this.form.submit()" name="editalign">';
// This will loop through ALL three alignments and show current alignment setting.
foreach ($aligns as $akey => $aval) {
    // The first if is executed ONLY when, when defaulttextalign matches one of the alignments
    // and it will then show that alignment in the selector.
    if ($akey == $editalign) {
        echo '<option value="'.$akey.'" selected="true">'.$aval.'</option>';
        $align = $aval;
    } else {
        // This part of the if is reached the most and its when an alignment
        // is is not the one selected.
        echo '<option value="'.$akey.'">'.$aval.'</option>';
    }
}

echo '</select></span>'.get_string('defaulttextalign_warning', 'roshine');

// Create a link back to where we came from in case we want to cancel.
if ($lessonpo == -1) {
    $url = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$id;
} else {
    $url = $CFG->wwwroot . '/mod/roshine/exercises.php?id='.$id.'&lesson='.$lessonpo;
}
echo '<br><span id="text_holder_span" class=""></span><br>'.get_string('fexercise', 'roshine').':<br>'
    .'<textarea rows="4" cols="60" name="texttotype" id="texttotype"style="text-align:'.$align.'"></textarea><br>'
    .'<br><input class="btn btn-primary" name="button" onClick="return clClick()" type="submit" value="'
    .get_string('fconfirm', 'roshine').'"> <a href="'.$url.'" class="btn btn-secondary" role="button">'
    .get_string('cancel', 'roshine').'</a>'.'</form>';
echo '<br></div>';
echo $OUTPUT->footer();
