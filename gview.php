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
 * This file displays all the grades of the paricular roshine exercise.
 *
 * It is also possible to remove the results of any individual attempt.
 *
 * @package    mod_roshiner
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $USER;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // roshine instance ID - it should be named as the first character of the module
$se = optional_param('exercise', 0, PARAM_INT);
$md = optional_param('jmode', 0, PARAM_INT);
$us = optional_param('juser', 0, PARAM_INT);
$orderby = optional_param('orderby', -1, PARAM_INT);
$des = optional_param('desc', -1, PARAM_INT);
if ($md == 1) {
    $us = 0;
} else if ($md == 0 || $md == 2) {
    $se = 0;
}
if ($id) {
    $cm       = get_coursemodule_from_id('roshine', $id, 0, false, MUST_EXIST);
    $course   = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $roshine  = $DB->get_record('roshine', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $roshine  = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $course   = $DB->get_record('course', array('id' => $roshine->course), '*', MUST_EXIST);
    $cm       = get_coursemodule_from_instance('roshine', $roshine->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}




require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Prevent students from typing in address to view all grades.
if (!has_capability('mod/roshine:viewgrades', context_module::instance($cm->id))) {
    redirect('view.php?id='.$id, get_string('invalidaccess', 'roshine'));
} else {

    $PAGE->set_url('/mod/roshine/gview.php', array('id' => $cm->id));
    $PAGE->set_title(format_string($roshine->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_context($context);
    $PAGE->set_cacheable(false);
    echo $OUTPUT->header();
    echo $OUTPUT->heading($roshine->name);
    $htmlout = '';
    $htmlout .= '<div align="center" style="font-size:20px;font-weight:bold;background:#CCC;border:2px solid #8eb6d8;-webkit-border-radius:16px;-moz-border-radius:16px;border-radius:16px;">';

    if ($roshine->isexam) {
        $grds = ros_get_typergradesfull($_GET['n']);
        if ($grds != FALSE) {
            $htmlout .= '<table style="border-style: solid;"><tr><td>'.
                get_string('student', 'roshine').'</td><td>'.
                get_string('vmistakes', 'roshine').'</td><td>'.
                get_string('timeinseconds', 'roshine').'</td><td>'.
                get_string('hitsperminute', 'roshine').'</td><td>'.
                get_string('fullhits', 'roshine').
                '</td><td>'.get_string('precision', 'roshine').
                '</td><td>'.get_string('timetaken', 'roshine').'</td></tr>';
            foreach ($grds as $gr) {
                if ($gr->ros_suspicion) {
                    $klicaj = '<span style="color: red;">!!!!!</span>';
                } else {
                    $klicaj = '';
                }

                $removelnk = '<a href="'.$CFG->wwwroot . '/mod/roshine/attrem.php?c_id='.optional_param('id', 0, PARAM_INT)
                             .'&m_id='.optional_param('n', 0, PARAM_INT).'&g='.$gr->id.'">'
                             .get_string('eremove', 'roshine').'</a>';

            $htmlout .= '<tr style="border-top-style: solid;">
                <td>'.$klicaj.' '.$gr->firstname.' '
                .$gr->lastname.'</td><td>'
                .$gr->mistakes.'</td><td>'.
                $gr->timeinseconds.' s</td><td>'
                .$gr->hitsperminute.'</td><td>'
                .$gr->fullhits.'</td><td>'
                .$gr->precisionfield.'%</td><td>'
                .date('d. M Y G:i', $gr->timetaken).'</td><td>'
                .$removelnk.'</td></tr>';

                // Get the information to draw the chart for this exam.
                $labels[] = $gr->firstname.' '.$gr->lastname.' Ex-'.$gr->exercisename;  // This gets the exercise number.
                $serieshitsperminute[] = format_float($gr->hitsperminute); // Get the hits per minute value.
                $seriesprecision[] = format_float($gr->precisionfield);  // Get the precision percentage value.
                // $serieswpm[] = $gr->wpm; // Get the corrected words per minute rate.

        }
        $avg = ros_get_grades_avg($grds);
        $htmlout .= '<tr style="border-top-style: solid;"><td><strong>'.get_string('average', 'roshine').': </strong></td><td>'.$avg['mistakes'].'</td><td>'.$avg['timeinseconds'].' s</td><td>'.$avg['hitsperminute'].'</td><td>'.$avg['fullhits'].'</td><td>'.$avg['precisionfield'].'%</td><td></td></tr>';
        $htmlout .= '</table>';
    }
    else
        echo get_string('nogrades', 'roshine');
}
else
{
    
    
    $htmlout .= '<form method="post">';
    $htmlout .= '<table><tr><td>'.get_string('gviewmode', 'roshine').'</td><td>';
    $htmlout .= '<select onchange="this.form.submit()" name="jmode"><option value="0">'.get_string('byuser', 'roshine').'</option>';
    if($md == 1)
        $htmlout .= '<option value="1" selected="true">'.get_string('byroshine', 'roshine').'</option>';
    else
        $htmlout .= '<option value="1">'.get_string('byroshine', 'roshine').'</option>';
    $htmlout .= '</select></td></tr>';
    
    if($md == 0)
    {
        $usrs = ros_get_users_of_one_instance($roshine->id);
        $htmlout .= '<tr><td>'.get_string('student', 'roshine').'</td><td>';
        $htmlout .= '<select name="juser" onchange="this.form.submit()">';
        $htmlout .= '<option value="0">'.get_string('allstring', 'roshine').'</option>';
        if($usrs != FALSE)    
            foreach($usrs as $x)
            {
                if($us == $x->id)
                    $htmlout .= '<option value="'.$x->id.'" selected="true">'.$x->firstname.' '.$x->lastname.'</option>';
                else
                    $htmlout .= '<option value="'.$x->id.'">'.$x->firstname.' '.$x->lastname.'</option>';
            }         
        $htmlout .= '</select>';
        $htmlout .= '</td></tr>';
    }
    else
    {
        $exes = ros_get_exercises_by_lesson($roshine->lesson);
        $htmlout .= '<tr><td>'.get_string('fexercise', 'roshine').'</td><td>';
        $htmlout .= '<select name="exercise" onchange="this.form.submit()">';
        $htmlout .= '<option value="0">'.get_string('allstring', 'roshine').'</option>';
        foreach($exes as $x)
        {
            if($se == $x['id'])
                $htmlout .= '<option value="'.$x['id'].'" selected="true">'.$x['exercisename'].'</option>';
            else
                $htmlout .= '<option value="'.$x['id'].'">'.$x['exercisename'].'</option>';
        }         
        $htmlout .= '</select>';
        $htmlout .= '</td></tr>';        
    }
    $grds = ros_get_typer_grades_adv($roshine->id, $se, $us);
    if($grds != FALSE){
        $htmlout .= '<table style="border-style: solid;"><tr><td>'.get_string('student', 'roshine').'</td><td>'.
        get_string('fexercise', 'roshine').'</td><td>'.get_string('vmistakes', 'roshine').'</td><td>'.
        get_string('timeinseconds', 'roshine').'</td><td>'.get_string('hitsperminute', 'roshine').'</td><td>'.
        get_string('fullhits', 'roshine').'</td><td>'.get_string('precision', 'roshine').'</td><td>'.
        get_string('timetaken', 'roshine').'</td></tr>';
        foreach ($grds as $gr) {
            if($gr->ros_suspicion)
                $klicaj = '<span style="color: red;">!!!!!</span>';
            else
                $klicaj = '';
            if($gr->pass)
                $stil = 'background-color: #7FEF6C;';
            else
                $stil = 'background-color: #FF6C6C;';
            $htmlout .= '<tr style="border-top-style: solid;'.$stil.'"><td>'.$klicaj.' '.$gr->firstname.' '.$gr->lastname.'</td><td>'.$gr->exercisename.'</td><td>'.$gr->mistakes.'</td><td>'.
            $gr->timeinseconds.' s</td><td>'.$gr->hitsperminute.'</td><td>'.$gr->fullhits.'</td><td>'.$gr->precisionfield.'%</td><td>'.date('d. M Y G:i', $gr->timetaken).'</td></tr>';

                // Get information to draw the chart for all exercises in this lesson.
                $labels[] = $gr->firstname.' '.$gr->lastname.' Ex-'.$gr->exercisename;  // This gets the exercise number.
                $serieshitsperminute[] = format_float($gr->hitsperminute); // Get the hits per minute value.
                $seriesprecision[] = format_float($gr->precisionfield);  // Get the precision percentage value.
                // $serieswpm[] = $gr->wpm; // Get the corrected words per minute rate.

        }
        $avg = ros_get_grades_avg($grds);
        $htmlout .= '<tr style="border-top-style: solid;"><td><strong>'.get_string('average', 'roshine').': </strong></td><td>&nbsp;</td><td>'.$avg['mistakes'].'</td><td>'.$avg['timeinseconds'].' s</td><td>'.$avg['hitsperminute'].'</td><td>'.$avg['fullhits'].'</td><td>'.$avg['precisionfield'].'%</td><td></td></tr>';
        $htmlout .= '</table>';
    }
    else
        echo get_string('nogrades', 'roshine');
    $htmlout .= '</table>';
    $htmlout .= '</form>';
}
$htmlout .= '</div>';
}
echo $htmlout;

if ($grds != false) {  // If there are NOT any grades, DON'T draw the chart.
    // Create the info the api needs passed to it for each series I want to chart.
    $serie1 = new core\chart_series(get_string('hitsperminute', 'roshine'), $serieshitsperminute);
    $serie2 = new core\chart_series(get_string('precision', 'roshine'), $seriesprecision);
//    $serie3 = new core\chart_series(get_string('wpm', 'roshine'), $serieswpm);

    $chart = new core\chart_bar();  // Tell the api I want a bar chart.
    $chart->set_horizontal(true); // Calling set_horizontal() passing true as parameter will display horizontal bar charts.
    $chart->set_title(get_string('charttitleallgrades', 'roshine')); // Tell the api what I want for a the chart title.
    $chart->add_series($serie1);  // Pass the hits per minute data to the api.
    $chart->add_series($serie2);  // Pass the precision data to the api.
//    $chart->add_series($serie3);  // Pass the words per minute data to the api.
    $chart->set_labels($labels);  // Pass the exercise number data to the api.
    $chart->get_xaxis(0, true)->set_label("Range");  // Pass a label to add to the x-axis.
    $chart->get_yaxis(0, true)->set_label(get_string('fexercise', 'roshine')); // Pass the label to add to the y-axis.
    echo $OUTPUT->render($chart); // Draw the chart on the output page.
}

echo $OUTPUT->footer();


