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
 * Shows the setup of a particular instance of roshine setup.
 * You can set whether this instance is a lesson or exam,
 * select the exercise category, required precision, as
 * well as which keyboard to show and use.
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $USER;


$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // Roshine instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('roshine', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $roshine    = $DB->get_record('roshine', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $roshine    = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $roshine->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('roshine', $roshine->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Get the default config for Roshine.
$roscfg = get_config('roshine');
// Enable-disable flag.
$epo = optional_param('e', 0, PARAM_INT);
// Get settings for current roshine activity.
// Get settings for current roshine activity.
$modepo = optional_param('mode', $roshine->isexam, PARAM_INT);
$exercisepo = optional_param('exercise', $roshine->exercise, PARAM_INT);
$textalign = optional_param('textalign', $roshine->textalign, PARAM_INT);
$lessonpo = optional_param('lesson', $roshine->lesson, PARAM_INT);
$showkeyboardpo = optional_param('showkeyboard', "off", PARAM_CLEAN);
$continuoustypepo = optional_param('continuoustype', "off", PARAM_CLEAN);
$countmistypedspacespo = optional_param('countmistypedspaces', "off", PARAM_CLEAN);
$countmistakespo = optional_param('countmistakes', "off", PARAM_CLEAN);
$statscolor = optional_param('statscolor', $roshine->statsbgc, PARAM_CLEAN);
$keytopcolor = optional_param('keytopcolor', $roshine->keytopbgc, PARAM_CLEAN);
$backgroundcolor = optional_param('backgroundcolor', $roshine->keybdbgc, PARAM_CLEAN);
$cursorcolor = optional_param('cursorcolor', $roshine->cursorcolor, PARAM_CLEAN);
$textbgc = optional_param('textbgc', $roshine->textbgc, PARAM_CLEAN);
$texterrorcolor = optional_param('texterrorcolor', $roshine->texterrorcolor, PARAM_CLEAN);
// Check to see if current MooTyper precision goal is empty.
if ($roshine->requiredgoal == null || is_null($roshine->requiredgoal)) {
    // Current MooTyper precision goal is empty so set it to the site default.
    $dfgoal = $roscfg->defaultprecision;
} else {
    // Otherwise use current MooTyper precision goal.
    $dfgoal = $roshine->requiredgoal;
}
$goalpo = optional_param('requiredgoal', $dfgoal, PARAM_INT); // Display with default or current setting.
// Check to see if current MooTyper activity textalign is empty.
if ($roshine->textalign == null || is_null($roshine->textalign)) {
    // Current MooTyper textalign is empty so set it to the site default.
    $dftextalign = $roscfg->defaulttextalign;
} else {
    // Otherwise use current MooTyper textalign.
    $dftextalign = $roshine->textalign;
}
$textalignpo = optional_param('textalign', $dftextalign, PARAM_INT); // Display with default or current setting.
// Check to see if current MooTyper continuoustype is empty.
if ($roshine->continuoustype == null || is_null($roshine->continuoustype)) {
    $dfct = "off";
} else if ($roshine->continuoustype) {
    // Otherwise use current MooTyper continuoustype.
    $dfct = "on";
} else {
    $dfct = "off";
}
$continuoustypepo = optional_param('continuoustype', $dfct, PARAM_CLEAN); // Display with default or current setting.
// Check to see if the current MooTyper countmistypedspaces is empty.
if ($roshine->countmistypedspaces == null || is_null($roshine->countmistypedspaces)) {
    // Current MooTyper continuoustype is empty so set it to the site default.
    $dfms = "off";
} else if ($roshine->countmistypedspaces) {
    // Otherwise use current MooTyper countmistypedspaces.
    $dfms = "on";
} else {
    $dfms = "off";
}
$countmistypedspacespo = optional_param('countmistypedspaces', $dfms, PARAM_CLEAN); // Display with default or current setting.
// Check to see if the current MooTyper countmistakes is empty.
if ($roshine->countmistakes == null || is_null($roshine->countmistakes)) {
    // Current MooTyper continuoustype is empty so set it to the site default.
    $dfcm = "on";
} else if ($roshine->countmistakes) {
    // Otherwise use current MooTyper countmistakes.
    $dfcm = "on";
} else {
    $dfcm = "off";
}
$countmistakespo = optional_param('countmistakes', $dfcm, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper showkeyboard is empty.
if ($roshine->showkeyboard == null || is_null($roshine->showkeyboard)) {
    $dfkb = "off";
} else if ($roshine->showkeyboard) {
    // Otherwise use current MooTyper showkeyboard.
    $dfkb = "on";
} else {
    $dfkb = "off";
}
$showkeyboardpo = optional_param('showkeyboard', $dfkb, PARAM_CLEAN);
// Check to see current MooTyper layout is empty.
if ($roshine->layout == null || is_null($roshine->layout)) {
    // Current MooTyper layout is empty so set it to the site default.
    $dfly = $roscfg->defaultlayout;
} else {
    // Otherwise use current MooTyper layout.
    $dfly = $roshine->layout;
}
$layoutpo = optional_param('layout', $dfly, PARAM_INT); // Display with default or current setting.
// Check to see if current MooTyper statsbgc is empty.
if ($roshine->statsbgc == null || is_null($roshine->statsbgc)) {
    // Current MooTyper statsbgc is empty so set it to the sites statscolor default.
    $dfstatscolor = $roscfg->statscolor;
} else {
    $dfstatscolor = $roshine->statsbgc;
}
$statscolorpo = optional_param('statsbgc', $dfstatscolor, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper keytopbgc is empty.
if ($roshine->keytopbgc == null || is_null($roshine->keytopbgc)) {
    // Current MooTyper keytopbgc is empty so set it to the sites normalkeytops default.
    $dfkeytopcolor = $roscfg->normalkeytops;
} else {
    $dfkeytopcolor = $roshine->keytopbgc;
}
$keytopcolorpo = optional_param('keytopbgc', $dfkeytopcolor, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper keybdbgc is empty.
if ($roshine->keybdbgc == null || is_null($roshine->keybdbgc)) {
    // Current MooTyper keybdbgc is empty so set it to the sites keyboardbgc default.
    $dfbackgroundcolor = $roscfg->keyboardbgc;
} else {
    $dfbackgroundcolor = $roshine->keybdbgc;
}
$backgroundcolorpo = optional_param('keybdbgc', $dfbackgroundcolor, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper cursorcolor is empty.
if ($roshine->cursorcolor == null || is_null($roshine->cursorcolor)) {
    // Current MooTyper cursorcolor is empty so set it to the sites cursorcolor default.
    $dfcursorcolor = $roscfg->keyboardbgc;
} else {
    $dfcursorcolor = $roshine->cursorcolor;
}
$cursorcolorpo = optional_param('cursorcolor', $dfcursorcolor, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper textbgc is empty.
if ($roshine->textbgc == null || is_null($roshine->textbgc)) {
    // Current MooTyper textbgc is empty so set it to the sites textbgc default.
    $dftextbgc = $roscfg->textbgc;
} else {
    $dftextbgc = $roshine->textbgc;
}
$textbgcpo = optional_param('textbgc', $dftextbgc, PARAM_CLEAN); // Display with default or current setting.
// Check to see if current MooTyper text error color is empty.
if ($roshine->texterrorcolor == null || is_null($roshine->texterrorcolor)) {
    // Current MooTyper texterrorcolor is empty so set it to the sites texterrorcolor default.
    $dftexterrorcolor = $roscfg->texterrorcolor;
} else {
    $dftexterrorcolor = $roshine->texterrorcolor;
}
$texterrorcolorpo = optional_param('texterrorcolor', $dftexterrorcolor, PARAM_CLEAN); // Display with default or current setting.
// Check to see if Confirm button is clicked and returning 'Confirm' to trigger insert record.
$param1 = optional_param('button', '', PARAM_TEXT);
if (isset($param1) && get_string('fconfirm', 'roshine') == $param1) {
    $modepo = optional_param('mode', null, PARAM_INT);
    $lessonpo = optional_param('lesson', null, PARAM_INT);
    $goalpo = optional_param('requiredgoal', $roscfg->defaultprecision, PARAM_INT);
    if ($goalpo == 0) {
        $goalpo = $roscfg->defaultprecision;
    }
    $textalignpo = optional_param('textalign', $dftextalign, PARAM_INT); // Display with default or current setting.
    $continuoustypepo = optional_param('continuoustype', null, PARAM_CLEAN);
    $countmistypedspacespo = optional_param('countmistypedspaces', null, PARAM_CLEAN);
    $countmistakespo = optional_param('countmistakes', null, PARAM_CLEAN);
    $showkeyboardpo = optional_param('showkeyboard', null, PARAM_CLEAN);
    $layoutpo = optional_param('layout', 0, PARAM_INT);
    $statscolorpo = optional_param('statsbgc', $dfstatscolor, PARAM_CLEAN);
    $keytopcolorpo = optional_param('keytopbgc', $dfkeytopcolor, PARAM_CLEAN);
    $backgroundcolorpo = optional_param('keybdbgc', $dfbackgroundcolor, PARAM_CLEAN);
    $cursorcolorpo = optional_param('cursorcolor', $dfcursorcolor, PARAM_CLEAN);
    $textbgcpo = optional_param('textbgc', $dftextbgc, PARAM_CLEAN);
    $texterrorcolorpo = optional_param('texterrorcolor', $dftexterrorcolor, PARAM_CLEAN);
    global $DB, $CFG;
    // Update all the settings for this MooTyper instance when Confirm is clicked.
    $roshine  = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $roshine->lesson = $lessonpo;
    $roshine->isexam = $modepo;
    if ($modepo == 1) {
        $exercisepo = optional_param('exercise', null, PARAM_INT);
        $roshine->exercise = $exercisepo;
    }
    $roshine->requiredgoal = $goalpo;
    $roshine->textalign = $textalignpo;
    $roshine->continuoustype = $continuoustypepo == 'on';
    $roshine->countmistypedspaces = $countmistypedspacespo == 'on';
    $roshine->countmistakes = $countmistakespo == 'on';
    $roshine->showkeyboard = $showkeyboardpo == 'on';
    $roshine->layout = $layoutpo;
    $roshine->statsbgc = $statscolorpo;
    $roshine->keytopbgc = $keytopcolorpo;
    $roshine->keybdbgc = $backgroundcolorpo;
    $roshine->cursorcolor = $cursorcolorpo;
    $roshine->textbgc = $textbgcpo;
    $roshine->texterrorcolor = $texterrorcolorpo;
    $DB->update_record('roshine', $roshine);
    header('Location: '.$CFG->wwwroot.'/mod/roshine/view.php?n='.$n);
}
// Print the page header.
$PAGE->set_url('/mod/roshine/mod_setup.php', array('id' => $cm->id));
$PAGE->set_title(format_string($roshine->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);
$PAGE->set_cacheable(false);
echo $OUTPUT->header();
echo $OUTPUT->heading($roshine->name);
$htmlout = '';
$htmlout .= '<script type="text/javascript">
function removeAtts()
{
    document.getElementById("lesson").disabled = false;
    document.getElementById("mode").disabled = false;
    document.getElementById("exercise").disabled = false;
}
</script>';
$htmlout .= '<form id="setupform" onsubmit="removeAtts();" name="setupform" method="POST">';
$disselect = $epo == 1 ? ' disabled="disabled"' : '';
$htmlout .= '<table><tr><td>'.get_string('fmode', 'roshine').'</td>
                        <td><select'.$disselect.' onchange="this.form.submit()" name="mode" id="mode">';
// 3/22/16 Modified to use only improved function get_roshinelessons.
if (has_capability('mod/roshine:aftersetup', context_module::instance($cm->id))) {
    $lessons = get_roshinelessons($USER->id, $course->id);
}
// Start building htmlout for this page based on exam or lesson exercise.
if ($modepo == 0 || is_null($modepo)) { // If mode is 0, this is a lesson?
    $htmlout .= '<option selected="true" value="0">'.get_string('sflesson', 'roshine').'</option>
                 <option value="1">'.get_string('isexamtext', 'roshine').'</option>
                 <option value="2">'.get_string('practice', 'roshine').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('excategory', 'roshine').'</td>
                <td><select'.$disselect.' onchange="this.form.submit()" id="lesson" name="lesson">';
    for ($ij = 0; $ij < count($lessons); $ij++) {
        if ($lessons[$ij]['id'] == $lessonpo) {
            $htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        } else {
            $htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        }
    }
    $htmlout .= '</select></td></tr><tr><td>'.get_string('requiredgoal', 'roshine').'</td>
                 <td><input value="'.$goalpo.'" style="width: 35px;" type="text" name="requiredgoal"> % </td></tr>';
} else if ($modepo == 1) { // Or, if mode is 1, this is an exam?
    $htmlout .= '<option value="0">'.get_string('sflesson', 'roshine').'</option>
                 <option value="1" selected="true">'.get_string('isexamtext', 'roshine').'</option>
                 <option value="2">'.get_string('practice', 'roshine').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('flesson', 'roshine').'</td>
                <td><select'.$disselect.' onchange="this.form.submit()" id="lesson" name="lesson">';
    for ($ij = 0; $ij < count($lessons); $ij++) {
        if ($lessons[$ij]['id'] == $lessonpo) {
            $htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        } else {
            $htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        }
    }
    $htmlout .= '</select></td></tr>';
    $exercises = ros_get_exercises_by_lesson($lessonpo);
    $htmlout .= '<tr><td>'.get_string('fexercise', 'roshine').'</td><td><select'.$disselect.' name="exercise" id="exercise">';
    for ($ik = 0; $ik < count($exercises); $ik++) {
        if ($exercises[$ik]['id'] == $exercisepo) {
            $htmlout .= '<option selected="true" value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
        } else {
            $htmlout .= '<option value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
        }
    }
    $htmlout .= '</select></td></tr>';
} else if ($modepo == 2) { // If mode is 2, this is a practice lesson?
    $htmlout .= '<option selected="true" value="0">'.get_string('sflesson', 'roshine').'</option>
                 <option value="1">'.get_string('isexamtext', 'roshine').'</option>
                 <option value="2" selected="true">'.get_string('practice', 'roshine').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('excategory', 'roshine').'</td>
                <td><select'.$disselect.' onchange="this.form.submit()" id="lesson" name="lesson">';
    for ($ij = 0; $ij < count($lessons); $ij++) {
        if ($lessons[$ij]['id'] == $lessonpo) {
            $htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        } else {
            $htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        }
    }
    $htmlout .= '</select></td></tr><tr><td>'.get_string('requiredgoal', 'roshine').'</td>
                 <td><input value="'.$goalpo.'" style="width: 35px;" type="text" name="requiredgoal"> % </td></tr>';
}
// Add a selector for text alignment.
$aligns = array(get_string('defaulttextalign_left', 'mod_roshine'),
              get_string('defaulttextalign_center', 'mod_roshine'),
              get_string('defaulttextalign_right', 'mod_roshine'));
$defaulttextalign = $roscfg->defaulttextalign;
$htmlout .= '<tr><td>'.get_string('defaulttextalign', 'roshine').'</td><td><select name="textalign">';
// Get the ID and name of each alignment in the DB.
foreach ($aligns as $akey => $aval) {
    // The first if is executed ONLY when, Text alignment, is
    // clicked to change alignment.
    if ($akey == $defaulttextalign) {
        $htmlout .= '<option value="'.$akey.'" selected="true">'.$aval.'</option>';
    } else if ($akey == $textalignpo) {
        // This part of the if is reached when going to setup with an
        // alignment already selected and it is the one already in use.
        $htmlout .= '<option value="'.$akey.'" selected="true">'.$aval.'</option>';
    } else {
        // This part of the if is reached the most and its when an alignment
        // is already selected but it is not the one being selected.
        $htmlout .= '<option value="'.$akey.'">'.$aval.'</option>';
    }
}
$htmlout .= '</select>';
// Need to keep the next line as it is helping get rid of _POST in line 267.
$tempchkkb = optional_param('showkeyboard', 0, PARAM_BOOL);
// Add the check box to enable continuous typing.
$htmlout .= '<tr><td>'.get_string('continuoustype', 'roshine').'</td><td>';
$continuoustypechecked = $continuoustypepo == 'on' ? ' checked="checked"' : '';
$htmlout .= '<input type="checkbox"'.$continuoustypechecked.' " name="continuoustype">';
// Add the check box to enable counting mistyped spaces.
$htmlout .= '<tr><td>'.get_string('countmistypedspaces', 'roshine').'</td><td>';
$countmistypedspaceschecked = $countmistypedspacespo == 'on' ? ' checked="checked"' : '';
$htmlout .= '<input type="checkbox"'.$countmistypedspaceschecked.' " name="countmistypedspaces">';
// Add the check box to enable counting multiple keystrokes for one error.
$htmlout .= '<tr><td>'.get_string('countmistakes', 'roshine').'</td><td>';
$countmistakeschecked = $countmistakespo == 'on' ? ' checked="checked"' : '';
$htmlout .= '<input type="checkbox"'.$countmistakeschecked.' " name="countmistakes">';
// Add the check box for show keyboard.
$htmlout .= '<tr><td>'.get_string('showkeyboard', 'roshine').'</td><td>';
$showkeyboardchecked = $showkeyboardpo == 'on' ? ' checked="checked"' : '';
$htmlout .= '<input type="checkbox"'.$showkeyboardchecked.' " name="showkeyboard">';
// Add the dropdown slector for keyboard layouts.
$layouts = ros_get_keyboard_layouts_db();
$deflayout = $roscfg->defaultlayout;
$htmlout .= '<tr><td>'.get_string('layout', 'roshine').'</td><td><select name="layout">';
// Get the ID and name of each keyboard layout in the DB.
foreach ($layouts as $lkey => $lval) {
    // The first if is executed ONLY when Showkeyboard is
    // clicked to turn it on or off. It seems to have the
    // the job of selecting our default layout when turned ON.
    if (($tempchkkb) && ($lkey == $deflayout)) {
        $htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
    } else if ($lkey == $layoutpo) {
        // This part of the if is reached when going to setup with a
        // keyboard layout already selected and it is the one already in use.
        $htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
    } else {
        // This part of the if is reached the most and its when a keyboard layout
        // is already selected but it is not the one being checked.
        $htmlout .= '<option value="'.$lkey.'">'.$lval.'</option>';
    }
}
// Add input box for statistics background color.
$htmlout .= '</td></tr><tr><td>'.get_string('statsbgc', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$statscolorpo.'" style="width: 135px;" type="text" name="statsbgc"></td></tr>';
// Add input box for normal keytop color.
$htmlout .= '</td></tr><tr><td>'.get_string('keytopbgc', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$keytopcolorpo.'" style="width: 135px;" type="text" name="keytopbgc"></td></tr>';
// Add input box for keyboard background color.
$htmlout .= '</td></tr><tr><td>'.get_string('keybdbgc', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$backgroundcolorpo.'" style="width: 135px;" type="text" name="keybdbgc"></td></tr>';
// Add input box for cursorcolor.
$htmlout .= '</td></tr><tr><td>'.get_string('cursorcolor', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$cursorcolorpo.'" style="width: 135px;" type="text" name="cursorcolor"></td></tr>';
// Add input box for textbgc.
$htmlout .= '</td></tr><tr><td>'.get_string('textbgc', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$textbgcpo.'" style="width: 135px;" type="text" name="textbgc"></td></tr>';
// Add input box for texterrorcolor.
$htmlout .= '</td></tr><tr><td>'.get_string('texterrorcolor', 'roshine').'</td><td>';
$htmlout .= '<input value="'.$texterrorcolorpo.'" style="width: 135px;" type="text" name="texterrorcolor"></td></tr>';
// Finish adding html to our page.
$htmlout .= '</select>';
$htmlout .= '</td></tr>';
$htmlout .= '</table>';
$htmlout .= '<br><input name="button" onclick="this.form.submit();" value="'.get_string('fconfirm', 'roshine').'" type="submit">';
$htmlout .= '</form>';
// Finally show the complete page.
echo $htmlout;
// Finish the page by adding a footer.
echo $OUTPUT->footer();
