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
 * Prints a particular instance of roshine setup
 *
 * @package    mod
 * @subpackage roshine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $USER;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // roshine instance ID - it should be named as the first character of the module
$mooCFG = get_config('roshine');
if(isset($_POST['button']))
$param1 = $_POST['button'];
if(isset($param1) && get_string('fconfirm', 'roshine') == $param1)
{
    $modePO = optional_param('mode', null, PARAM_INT);
    $lessonpo = optional_param('lesson', null, PARAM_INT);
    //$mooCFG = get_config('roshine');
    //$defLayout = $mooCFG->defaultlayout;
    
    $goalPO = optional_param('requiredgoal', $mooCFG->defaultprecision, PARAM_INT);
    if($goalPO == 0) $goalPO = $mooCFG->defaultprecision;
    $layoutPO = optional_param('layout', 0, PARAM_INT);
    $showKeyboardPO = optional_param('showkeyboard', null, PARAM_CLEAN);
    global $DB, $CFG;
    $roshine  = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $roshine->lesson = $lessonpo;
    $roshine->showkeyboard = $showKeyboardPO == 'on';
    $roshine->layout = $layoutPO;
    $roshine->isexam = $modePO;
    $roshine->requiredgoal = $goalPO;
    if($modePO == 1){
        $exercisePO = optional_param('exercise', null, PARAM_INT);
        $roshine->exercise = $exercisePO;
    }
    $DB->update_record('roshine', $roshine);
    header('Location: '.$CFG->wwwroot.'/mod/roshine/view.php?n='.$n);
}

$modePO = optional_param('mode', null, PARAM_INT);
$exercisePO = optional_param('exercise', null, PARAM_INT);
$lessonpo = optional_param('lesson', null, PARAM_INT);
$showKeyboardPO = optional_param('showkeyboard', null, PARAM_CLEAN);
$layoutPO = optional_param('layout', 0, PARAM_INT);
$goalPO = optional_param('requiredgoal', $mooCFG->defaultprecision, PARAM_INT);

if ($id) {
    $cm         = get_coursemodule_from_id('roshine', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $roshine  = $DB->get_record('roshine', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $roshine  = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $roshine->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('roshine', $roshine->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
//$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$context = context_module::instance($cm->id);

//add_to_log($course->id, 'roshine', 'view', "view.php?id={$cm->id}", $roshine->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/roshine/mod_setup.php', array('id' => $cm->id));
$PAGE->set_title(format_string($roshine->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('tb1');
//$PAGE->add_body_class('roshine-'.$somevar);
// Output starts here
echo $OUTPUT->header();
// Replace the following lines with you own code
echo $OUTPUT->heading($roshine->name);
//get_record('roshine_exercises', array('id' => $eid));
//$roshine = jget_roshine_record($n);
//$exerciseid = $roshine->exercise;
//$exercise = ros_get_exercise_record($exerciseid);
//$textToEnter = $exercise->texttotype; //"N=".$n." exerciseid=".$roshine->exercise." fjajfjfjfj name=".$roshine->name." fjfjfjfjfj";

//onload="initTextToEnter('')"

//$grds = ros_get_typergradesfull($_GET['sid']);

$htmlout = '';
$htmlout .= '<div align="center" style="font-size:20px;font-weight:bold;background:#CCC;border:2px solid #8eb6d8;-webkit-border-radius:16px;-moz-border-radius:16px;border-radius:16px;">';
$htmlout .= '<form id="setupform" name="setupform" method="POST">';
$htmlout .= '<table><tr><td>'.get_string('fmode', 'roshine').'</td><td><select onchange="this.form.submit()" name="mode">';
//$lessons = ros_get_typerlessons();

//if (has_capability('mod/roshine:editall', get_context_instance(CONTEXT_COURSE, $course->id))) {
if (has_capability('mod/roshine:editall', context_course::instance($course->id))) {
    $lessons = ros_get_typerlessons();
} else {
    $lessons = get_roshinelessons($USER->id, $course->id);
}
if ($modePO == 0 || is_null($modePO)) {
    $htmlout .= '<option selected="true" value="0">'.
            get_string('sflesson', 'roshine').'</option><option value="1">'.
            get_string('isexamtext', 'roshine').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('excategory', 'roshine').'</td><td><select onchange="this.form.submit()" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++) {
        if ($lessons[$ij]['id'] == $lessonpo) {
            $htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        } else {
            $htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        }
    }
    $htmlout .= '</select></td></tr><tr><td>'.get_string('requiredgoal', 'roshine').'</td><td><input value="'.$goalPO.'" style="width: 40px;" type="text" name="requiredgoal"> % </td></tr>';
}
else if($modePO == 1)
{
    $htmlout .= '<option value="0">'.
            get_string('sflesson', 'roshine').'</option><option value="1" selected="true">'.
            get_string('isexamtext', 'roshine').'</option>';
    $htmlout .= '</select></td></tr><tr><td>';
    $htmlout .= get_string('flesson', 'roshine').'</td><td><select onchange="this.form.submit()" name="lesson">';
    for($ij=0; $ij<count($lessons); $ij++)
    {
        if($lessons[$ij]['id'] == $lessonpo)
            $htmlout .= '<option selected="true" value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
        else
            $htmlout .= '<option value="'.$lessons[$ij]['id'].'">'.$lessons[$ij]['lessonname'].'</option>';
    }
    $htmlout .= '</select></td></tr>';
    $exercises = ros_get_exercises_by_lesson($lessonpo);
    $htmlout .= '<tr><td>'.get_string('fexercise', 'roshine').'</td><td><select name="exercise">';
    for($ik=0; $ik<count($exercises); $ik++)
    {
        if($exercises[$ik]['id'] == $exercisePO)
            $htmlout .= '<option selected="true" value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
        else
            $htmlout .= '<option value="'.$exercises[$ik]['id'].'">'.$exercises[$ik]['exercisename'].'</option>';
    }
    $htmlout .= '</select></td></tr>';
}
$htmlout .= '<tr><td>'.get_string('showkeyboard', 'roshine').'</td><td>';
if($showKeyboardPO == 'on'){
    $htmlout .= '<input type="checkbox" checked="checked" onchange="this.form.submit()" name="showkeyboard">';
    $layouts = ros_get_keyboard_layouts_db();
    //$mform->addElement('select', 'layout', get_string('layout', 'roshine'), $layouts);
    $defLayout = $mooCFG->defaultlayout;
    $htmlout .= '<tr><td>'.get_string('layout', 'roshine').'</td><td><select name="layout">';
    foreach($layouts as $lkey => $lval)
    {
        if((count($_POST) > 1) && ($lkey == $defLayout))
            $htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
        else if($lkey == $layoutPO)
            $htmlout .= '<option value="'.$lkey.'" selected="true">'.$lval.'</option>';
        else
            $htmlout .= '<option value="'.$lkey.'">'.$lval.'</option>';
    }
    $htmlout .= '</select>';
}
else
    $htmlout .= '<input type="checkbox" onchange="this.form.submit()" name="showkeyboard">';
$htmlout .= '</td></tr>';    

$htmlout .= '</table>';
$htmlout .= '<br><input name="button" value="'.get_string('fconfirm', 'roshine').'" type="submit">';
$htmlout .= '</form>';
$htmlout .= '</div>';
echo $htmlout;
// Ket Thuc Trang
echo $OUTPUT->footer();


