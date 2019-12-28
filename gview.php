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
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Changed to this newer format 03/01/2019.
require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once(__DIR__ . '/locallib.php');

global $USER;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // Mootyper instance ID - it should be named as the first character of the module.
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
    $cm         = get_coursemodule_from_id('roshine', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $roshine  = $DB->get_record('roshine', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $roshine  = $DB->get_record('roshine', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $roshine->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('roshine', $roshine->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}
$mtmode = $roshine->isexam;
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Prevent students from typing in address to view all grades.
if (!has_capability('mod/roshine:viewgrades', context_module::instance($cm->id))) {
    redirect('view.php?id='.$id, get_string('invalidaccess', 'roshine'));
} else {
    // The following needs to retrieve leybdbgc for setting this background.
    // $color3 = 'lightgreen';
    $color3 = $roshine->keybdbgc;

    $PAGE->set_url('/mod/roshine/gview.php', array('id' => $cm->id));
    $PAGE->set_title(format_string($roshine->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_context($context);
    $PAGE->set_cacheable(false);
    echo $OUTPUT->header();
    echo '<link rel="stylesheet" type="text/css" href="styles.css">';
    echo $OUTPUT->heading($roshine->name);
    $htmlout = '';
    $htmlout .= '<div align="center" style="font-size:20px;
                font-weight:bold;background: '.$color3.';
                border:2px solid #8eb6d8;
                -webkit-border-radius:16px;
                -moz-border-radius:16px;
                border-radius:16px;">';
//    $htmlout .= '<div id="mainDiv">';

    if ($mtmode == 1) {  // If this is an exam, process the info for the current exercise.
        if ($des == -1) {
            $des = 0;
        }

        $grds = ros_get_typergradesfull($roshine->id, $orderby, $des);

        if ($des == -1 || $des == 1) {
            $lnkadd = "&desc=0";
        } else {
            $lnkadd = "&desc=1";
        }

        $arrtextadds = array();
        $arrtextadds[2] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[4] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[5] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[6] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[7] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[8] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[9] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[12] = '<span class="arrow-s" style="font-size:1em;"></span>';
        $arrtextadds[$orderby] = $des == -1 || $des == 1 ? '<span class="arrow-s" style="font-size:1em;">
                                                            </span>' : '<span class="arrow-n" style="font-size:1em;"></span>';
        if ($grds != false) {
            $htmlout .= '<table style="border-style: solid;"><tr>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=2'.$lnkadd.'">'
                            .get_string('student', 'roshine').'</a>'.$arrtextadds[2].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=4'.$lnkadd.'">'
                            .get_string('vmistakes', 'roshine').'</a>'.$arrtextadds[4].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=5'.$lnkadd.'">'
                            .get_string('timeinseconds', 'roshine').'</a>'.$arrtextadds[5].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=6'.$lnkadd.'">'
                            .get_string('hitsperminute', 'roshine').'</a>'.$arrtextadds[6].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=7'.$lnkadd.'">'
                            .get_string('fullhits', 'roshine').'</a>'.$arrtextadds[7].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=8'.$lnkadd.'">'
                            .get_string('precision', 'roshine').'</a>'.$arrtextadds[8].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=9'.$lnkadd.'">'
                            .get_string('timetaken', 'roshine').'</a>'.$arrtextadds[9].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=12'.$lnkadd.'">'
                            .get_string('wpm', 'roshine').'</a>'.$arrtextadds[12].'</td>
                        <td>'.get_string('eremove', 'roshine').'</td></tr>';
            foreach ($grds as $gr) {
print_object($gr);

                if ($gr->ros_suspicion) {
                    $klicaj = '<span style="color: '.(get_config('mod_roshine', 'suspicion')).';"><b>!!!!!</b></span>';
                } else {
                    $klicaj = '';
                }
                if ($gr->pass) {
                    $stil = 'background-color: '.(get_config('mod_roshine', 'passbgc')).';';
                } else {
                    $stil = 'background-color: '.(get_config('mod_roshine', 'failbgc')).';';
                }

                $removelnk = '<a href="'.$CFG->wwwroot
                              .'/mod/roshine/attrem.php?c_id='.optional_param('id', 0, PARAM_INT)
                             .'&m_id='.optional_param('n', 0, PARAM_INT)
                             .'&g='.$gr->id.'">'.get_string('eremove', 'roshine').'</a>';
                $namelnk = '<a href="'.$CFG->wwwroot
                            .'/user/view.php?id='.$gr->u_id
                            .'&amp;course='.$course->id
                            .'">'.$gr->firstname
                            .' '.$gr->lastname.'</a>';
                $htmlout .= '<tr style="border-top-style: solid;'.$stil.'">
                             <td>'.$klicaj.' '.$namelnk.'</td>
                             <td>'.$gr->mistakes.'</td>
                             <td>'.format_time($gr->timeinseconds).'</td>
                             <td>'.format_float($gr->hitsperminute).'</td>
                             <td>'.$gr->fullhits.'</td>
                             <td>'.format_float($gr->precisionfield).'%</td>
                             <td>'.date(get_config('mod_roshine', 'dateformat'), $gr->timetaken).'</td>
                             <td>'.format_float($gr->wpm).'</td>
                             <td>'.$removelnk.'</td></tr>';
                // Get the information to draw the chart for this exam.
                $labels[] = $gr->firstname.' '.$gr->lastname.' Ex-'.$gr->exercisename;  // This gets the exercise number.
                $serieshitsperminute[] = $gr->hitsperminute; // Get the hits per minute value.
                $seriesprecision[] = $gr->precisionfield;  // Get the precision percentage value.
                $serieswpm[] = $gr->wpm; // Get the corrected words per minute rate.
            }
            $avg = ros_get_grades_avg($grds);
            $htmlout .= '<tr style="border-top-style: solid;">
                         <td><strong>'.get_string('average', 'roshine').': </strong></td>
                         <td>'.$avg['mistakes'].'</td>
                         <td>'.format_time($avg['timeinseconds']).'</td>
                         <td>'.format_float($avg['hitsperminute']).'</td>
                         <td>'.$avg['fullhits'].'</td>
                         <td>'.format_float($avg['precisionfield']).'%</td>
                         a<td></td><td></td><td></td></tr>';
            $htmlout .= '</table>';
        } else {
            echo get_string('nogrades', 'roshine');
        }
    } else {   // Was not an exam so process exercises for the current lesson.
        $htmlout .= '<form method="post">';
        $htmlout .= '<table><tr><td>'.get_string('gviewmode', 'roshine').'</td><td>';
        $htmlout .= '<select onchange="this.form.submit()" name="jmode"><option value="0">'
                    .get_string('byuser', 'roshine').'</option>';
        if ($md == 1) {
            $htmlout .= '<option value="1" selected="true">'.get_string('byroshine', 'roshine').'</option>';
        } else {
            $htmlout .= '<option value="1">'.get_string('byroshine', 'roshine').'</option>';
        }
        $htmlout .= '</select></td></tr>';

        if ($md == 0 || $md == 2) {
            $usrs = ros_get_users_of_one_instance($roshine->id);
            $htmlout .= '<tr><td>'.get_string('student', 'roshine').'</td><td>';
            $htmlout .= '<select name="juser" onchange="this.form.submit()">';
            $htmlout .= '<option value="0">'.get_string('allstring', 'roshine').'</option>';
            if ($usrs != false) {
                foreach ($usrs as $x) {
                    if ($us == $x->id) {
                        $htmlout .= '<option value="'.$x->id.'" selected="true">'.$x->firstname.' '.$x->lastname.'</option>';
                    } else {
                        $htmlout .= '<option value="'.$x->id.'">'.$x->firstname.' '.$x->lastname.'</option>';
                    }
                }
            }
            $htmlout .= '</select>';
            $htmlout .= '</td></tr>';
        } else {
            $exes = ros_get_exercises_by_lesson($roshine->lesson);
            $htmlout .= '<tr><td>'.get_string('fexercise', 'roshine').'</td><td>';
            $htmlout .= '<select name="exercise" onchange="this.form.submit()">';
            $htmlout .= '<option value="0">'.get_string('allstring', 'roshine').'</option>';
            foreach ($exes as $x) {
                if ($se == $x['id']) {
                    $htmlout .= '<option value="'.$x['id'].'" selected="true">'.$x['exercisename'].'</option>';
                } else {
                    $htmlout .= '<option value="'.$x['id'].'">'.$x['exercisename'].'</option>';
                }
            }
            $htmlout .= '</select>';
            $htmlout .= '</td></tr>';
        }
        if ($des == -1) {
            $des = 0;
        }
        $grds = ros_get_typer_grades_adv($roshine->id, $se, $us, $orderby, $des);

        if ($grds != false) {
            if ($des == -1 || $des == 1) {
                $lnkadd = "&desc=0";
            } else {
                $lnkadd = "&desc=1";
            }
            $arrtextadds = array();
            $arrtextadds[2] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[4] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[5] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[6] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[7] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[8] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[9] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[10] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[11] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[12] = '<span class="arrow-s" style="font-size:1em;"></span>';
            $arrtextadds[$orderby] = $des == -1 || $des == 1 ? '<span class="arrow-s" style="font-size:1em;">
                                     </span>' : '<span class="arrow-n" style="font-size:1em;"></span>';

            $htmlout .= '<table style="border-style: solid;"><tr>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=2'.$lnkadd.'">'
                            .get_string('student', 'roshine').'</a>'.$arrtextadds[2].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=10'.$lnkadd.'">'
                            .get_string('fexercise', 'roshine').'</a>'.$arrtextadds[10].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=4'.$lnkadd.'">'
                            .get_string('vmistakes', 'roshine').'</a>'.$arrtextadds[4].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=5'.$lnkadd.'">'
                            .get_string('timeinseconds', 'roshine').'</a>'.$arrtextadds[5].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=6'.$lnkadd.'">'
                            .get_string('hitsperminute', 'roshine').'</a>'.$arrtextadds[6].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=7'.$lnkadd.'">'
                            .get_string('fullhits', 'roshine').'</a>'.$arrtextadds[7].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=8'.$lnkadd.'">'
                            .get_string('precision', 'roshine').'</a>'.$arrtextadds[8].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=9'.$lnkadd.'">'
                            .get_string('timetaken', 'roshine').'</a>'.$arrtextadds[9].'</td>
                        <td><a href="?id='.$id.'&n='.$n.'&orderby=12'.$lnkadd.'">'
                            .get_string('wpm', 'roshine').'</a>'.$arrtextadds[12].'</td>
                        <td>'.get_string('eremove', 'roshine').'</td></tr>';
            foreach ($grds as $gr) {
                if ($gr->ros_suspicion) {
                    $exclamation = '<span style="color: '.(get_config('mod_roshine', 'suspicion')).';"><b>!!!!!</b></span>';
                } else {
                    $exclamation = '';
                }
                if ($gr->pass) {
                    $stil = 'background-color: '.(get_config('mod_roshine', 'passbgc')).';';
                } else {
                    $stil = 'background-color: '.(get_config('mod_roshine', 'failbgc')).';';
                }
                $removelnk = '<a href="'.$CFG->wwwroot . '/mod/roshine/attrem.php?c_id='
                             .optional_param('id', 0, PARAM_INT)
                             .'&m_id='.optional_param('n', 0, PARAM_INT).'&g='.$gr->id.'">'
                             .get_string('eremove', 'roshine').'</a>';
                $namelnk = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$gr->u_id
                           .'&amp;course='.$course->id
                           .'">'.$gr->firstname.' '
                           .$gr->lastname.'</a>';
                $htmlout .= '<tr style="border-top-style: solid;'.$stil.'">
                             <td>'.$exclamation.' '.$namelnk.'</td>
                             <td>'.$gr->exercisename.'</td>
                             <td>'.$gr->mistakes.'</td>
                             <td>'.format_time($gr->timeinseconds).'</td>
                             <td>'.format_float($gr->hitsperminute).'</td>
                             <td>'.$gr->fullhits.'</td>
                             <td>'.format_float($gr->precisionfield).'%</td>
                             <td>'.date(get_config('mod_roshine', 'dateformat'), $gr->timetaken).'</td>
                             <td>'.format_float($gr->wpm).'</td>
                             <td>'.$removelnk.'</td></tr>';
                // Get information to draw the chart for all exercises in this lesson.
                $labels[] = $gr->firstname.' '.$gr->lastname.' Ex-'.$gr->exercisename;  // This gets the exercise number.
                $serieshitsperminute[] = $gr->hitsperminute; // Get the hits per minute value.
                $seriesprecision[] = $gr->precisionfield;  // Get the precision percentage value.
                $serieswpm[] = $gr->wpm; // Get the corrected words per minute rate.
            }
            $avg = ros_get_grades_avg($grds);
            $htmlout .= '<tr style="border-top-style: solid;">
                         <td><strong>'.get_string('average', 'roshine').': </strong></td>
                         <td>&nbsp;</td><td>'.$avg['mistakes'].'</td>
                         <td>'.format_time($avg['timeinseconds']).'</td>
                         <td>'.format_float($avg['hitsperminute']).'</td>
                         <td>'.$avg['fullhits'].'</td>
                         <td>'.format_float($avg['precisionfield']).'%</td>
                         <td></td><td></td><td></td></tr>';
            $htmlout .= '</table>';
        } else {
            echo get_string('nogrades', 'roshine');
        }
            $htmlout .= '</table>';
            $htmlout .= '</form>';
    }

    $htmlout .= '</div>';
    $htmlout .= '<p style="text-align: left;">
                <a href="'.$CFG->wwwroot.'/mod/roshine/csvexport.php?roshineid='
                .$roshine->id.'&isexam='.$roshine->isexam.'">'
                .get_string('csvexport', 'roshine').'</a></p>';
}
echo $htmlout;
if (($grds != false) && ($CFG->branch > 31)) {  // If there are NOT any grades, DON'T draw the chart.
    // Create the info the api needs passed to it for each series I want to chart.
    $serie1 = new core\chart_series(get_string('hitsperminute', 'roshine'), $serieshitsperminute);
    $serie2 = new core\chart_series(get_string('precision', 'roshine'), $seriesprecision);
    $serie3 = new core\chart_series(get_string('wpm', 'roshine'), $serieswpm);

    $chart = new core\chart_bar();  // Tell the api I want a bar chart.
    $chart->set_horizontal(true); // Calling set_horizontal() passing true as parameter will display horizontal bar charts.
    $chart->set_title(get_string('charttitleallgrades', 'roshine')); // Tell the api what I want for a the chart title.
    $chart->add_series($serie1);  // Pass the hits per minute data to the api.
    $chart->add_series($serie2);  // Pass the precision data to the api.
    $chart->add_series($serie3);  // Pass the words per minute data to the api.
    $chart->set_labels($labels);  // Pass the exercise number data to the api.
    $chart->get_xaxis(0, true)->set_label(get_string('xaxislabel', 'roshine'));  // Pass a label to add to the x-axis.
    $chart->get_yaxis(0, true)->set_label(get_string('fexercise', 'roshine')); // Pass the label to add to the y-axis.
    echo $OUTPUT->render($chart); // Draw the chart on the output page.
}
echo $OUTPUT->footer();
