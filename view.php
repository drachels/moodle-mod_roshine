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
 * Allows viewing a particular instance of roshine.
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $USER, $CFG;

$id = optional_param('id', 0, PARAM_INT); // Course_module ID.
$n  = optional_param('n', 0, PARAM_INT);  // Roshine instance ID - it should be named as the first character of the module.
// Get the current "thisdirection" string from the langconfig.php file, so that
// the status bar and any dual keyboard layouts render correctly.
$directionality = get_string('thisdirection', 'langconfig');
$userpassword = optional_param('userpassword', '', PARAM_RAW);
$backtocourse = optional_param('backtocourse', false, PARAM_RAW);

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

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
// add_to_log($course->id, 'roshine', 'view', "view.php?id={$cm->id}", $roshine->name, $cm->id);

// Print the page header.

$PAGE->set_url('/mod/roshine/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($roshine->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Include jquery.
$PAGE->requires->js('/mod/roshine/jquery/jquery.js', true);
$PAGE->requires->js('/mod/roshine/bootstrap/js/bootstrap.min.js', true);
$PAGE->requires->js('/mod/roshine/javascript/main.js', true);


// Other things you may want to set - remove if not needed.
$PAGE->set_cacheable(false);

// Output starts here
echo $OUTPUT->header();

if ($roshine->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('roshine', $roshine, $cm->id), 'generalbox mod_introbox', 'roshineintro');
}
if($roshine->lesson != NULL) {
    if($roshine->isexam) {
        $insertDir = $CFG->wwwroot . '/mod/roshine/gadd.php';
        $exerciseid = $roshine->exercise;
        $exercise = ros_get_exercise_record($exerciseid);
        $textToEnter = $exercise->texttotype; //"N=".$n." exerciseid=".$roshine->exercise." fjajfjfjfj name=".$roshine->name." fjfjfjfjfj";
    } else {
        $reqiredGoal = $roshine->requiredgoal;
        $insertDir = $CFG->wwwroot . '/mod/roshine/gcnext.php';
        $exercise = get_exercise_from_roshine($roshine->id, $roshine->lesson, $USER->id);
        if($exercise != FALSE){
            $exerciseid = $exercise->id;
            $textToEnter = $exercise->texttotype;}
    }
    if (ros_exam_already_done($roshine, $USER->id) && $roshine->isexam) {
        echo get_string('examdone', 'roshine');
        echo "<br>";
        if (has_capability('mod/roshine:viewgrades', context_module::instance($cm->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/roshine/gview.php?id=' . $id . '&n=' . $roshine->id;
            echo '<a href="'.$jlnk4.'">'.get_string('viewgrades', 'roshine').'</a><br><br>';
        }
// Added code so students can see their own grades
        if (has_capability('mod/roshine:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/roshine/owngrades.php?id=" . $id . "&n=" . $roshine->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'roshine') . '</a><br /><br />';
        }

    }
    else if ($exercise != FALSE) {
        echo '<link rel="stylesheet" type="text/css" href="style.css">';
        //js_init_call !!!!!!!!
        //onload="initTypingText('')"
        if ($roshine->showkeyboard) {
           $keyboard_js = ros_get_instance_layout_js_file($roshine->layout);
           echo '<script type="text/javascript" src="'.$keyboard_js.'"></script>';
        }
        echo '<script type="text/javascript" src="typer.js"></script>';
?>
<div id="mainDiv">
<form name='form1' id='form1' method='post' action='<?php echo $insertDir; ?>'> 
<div id="tipkovnica" style="float: left; text-align:center; margin-left: auto; margin-right: auto;">
<h4 style="color:#FF0066">
        <?php
        if (!$roshine->isexam) {
            // Need to get count of exercises in the current lesson.
            $sqlc = "SELECT COUNT(rte.texttotype)
                    FROM {roshine_lessons} rtl
                    LEFT JOIN {roshine_exercises} rte
                    ON rte.lesson =  rtl.id
                    WHERE rtl.id = $roshine->lesson";

            $count = $DB->count_records_sql($sqlc, $params = null);
            echo get_string('exercise', 'roshine', $exercise->exercisename).$count;
        }
        ?>

</h4>
<br>
    <div id='wrapStats'>
                    <div id='timerDiv' style='float: left; margin-left:300px;'>   
                        <div id="timerText" class="statsText">&nbsp;&nbsp;<?php echo get_string('vtime', 'roshine'); ?>&nbsp;</div>
                        <div id='timer'><span id="jsTime2">00:00</span></div>
                    </div>
                    <div id='wpmDiv' style='float: left;'>                
                        <div id='wpmText' class="statsText">&nbsp;|&nbsp;<?php echo get_string('speedwpm', 'roshine'); ?>&nbsp;</div>
                        <div id='wpmValue'><span id="jsWpm2">0</span></div>
                    </div>
                    <div id='accuracyDiv' style='float: left;'> 
                        <div id='accuracyText' class="statsText">&nbsp;|&nbsp;<?php echo get_string('accuracy', 'roshine'); ?>&nbsp;&nbsp;</div>
                        <div id='accuracyValue'><span id="jsAcc2">0</span><span>%</span></div>
                    </div>
                    <div id='progressDiv' style='float: left;'> 
                        <div id='progressText' class="statsText">&nbsp;|&nbsp;<?php echo get_string('rprogress', 'roshine'); ?>&nbsp;&nbsp;</div>
                        <div id='progressValue'><span id="jsProgress2">0/0</span></div>
                    </div>
    </div>
    <br>
    <div id="textToEnter"></div><br>
    <?php 
    if($roshine->showkeyboard){
        $keyboard = ros_get_instance_layout_file($roshine->layout);
        include($keyboard);
    } else {
        echo '<div id="keyboardContainerRow" style="display:none;"></div>' ;
    }
    ?>
    
<br>
    <textarea name="tb1" wrap="off" id="tb1" class="tb1" onkeypress="return onUserPressKey(event)" onfocus="return focusSet(event)"  
        onpaste="return false" onselectstart="return false"
        onCopy="return false" onCut="return false" 
        onDrag="return false" onDrop="return false" autocomplete="off" style="display:none">
        <?php 
            echo get_string('chere', 'roshine').'...';
        ?>
    </textarea>
                                                    
</div>                
<div id="reportDiv" style="float: right; /*position: relative; right: 90px; top: 35px;*/">
    <?php
        //if (has_capability('mod/roshine:viewgrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
           //if (has_capability('mod/roshine:viewgrades', context_module::instance($cm->id))) { 
           if (has_capability('mod/roshine:viewgrades', context_course::instance($course->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/roshine/gview.php?id='.$id.'&n='.$roshine->id;;
            echo '<a href="' . $jlnk4 . '" style="color:#FF0066">' . get_string('viewgrades', 'roshine') . '</a><br><br>';
        }

// Added code for a link to setup.
        if (has_capability('mod/roshine:aftersetup', context_module::instance($cm->id))) {
            $jlnk6 = $CFG->wwwroot . "/mod/roshine/mod_setup.php?n=" . $roshine->id . "&e=1";
            echo '<a href="' . $jlnk6 . '">' . get_string('fsettings', 'roshine') . '</a><br /><br />';
        }

        if (has_capability('mod/roshine:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/roshine/owngrades.php?id=" . $id . "&n=" . $roshine->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'roshine') . '</a><br /><br />';
        }


    ?>
    <input name='rpCourseId' type='hidden' value='<?php echo $course->id; ?>'>
    <input name='rpSityperId' type='hidden' value='<?php echo $roshine->id; ?>'>
    <input name='rpUser' type='hidden' value='<?php echo $USER->id; ?>'>
    <input name='rpExercise' type='hidden' value='<?php echo $exerciseid; ?>'>
    <input name='rpAttId' type='hidden' value=''>
    <input name='rpFullHits' type='hidden' value=''>
    <input name='rpGoal' type='hidden' value='<?php if(isset($reqiredGoal)) echo $reqiredGoal; ?>'>
    <input name='rpTimeInput' type='hidden'>
    <input name='rpMistakesInput' type='hidden'>
    <input name='rpAccInput' type='hidden'>
    <input name='rpSpeedInput' type='hidden'>
    <input class="btn" style="visibility: hidden;" id="btnContinue" name='btnContinue' type="submit" value=<?php echo "'".get_string('fcontinue', 'roshine')."'"; ?>> 
</div>    
                
<div>
    <button class="btn" id="btnOptions" onclick="return false;"><?php echo get_string('options', 'roshine'); ?></button>
    <button class="btn" id="btnStatus" onclick="return false;"><?php echo get_string('status', 'roshine'); ?></button>
    <a href="game.php?id=<?php echo $id ?>">Game</a>
</div>        
<div id="modalOptions" class="modal hide fade" tabindex=-1 role="dialog" aria-labelledby="mdlOptionsHeader" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="mdlOptionsHeader"><?php echo get_string('options', 'roshine'); ?></h3>
    </div>
    <div class="modal-body">
        <form id="option-form" method="post">
            <div class="opt">
                <label class="checkbox">
                 <input type="checkbox" id="optKBRow" value="1" /><?php echo get_string('showkeyboardandhand', 'roshine'); ?>
                 </label>
            </div>
            <div class="opt">
                <label class="checkbox">
                <input type="checkbox" id="optFullScreen" value="1" /><?php echo get_string('fullscreen', 'roshine'); ?>
                </label>
            </div>
            <div class="opt">
                <label class="checkbox">
                <input type="checkbox" id="optSound" value="0" /><?php echo get_string('soundon', 'roshine'); ?>
                </label>
            </div>
            <div class="opt">
                <label class="checkbox">
                <strong><em><?php echo get_string('optionsalert', 'roshine'); ?></strong></em>
                </label>
            </div>
        </form>
    </div> <!-- /.modal-body -->
    <div class="modal-footer" id="optionFooter">
        <div class="modalOptionFooter">
            <button class="btn btn-primary" id="option-submit"><?php echo get_string('okay', 'roshine'); ?></button>
            <button class="btn" id="option-cancel"><?php echo get_string('cancel', 'roshine'); ?></button>
        </div>
    </div>
</div> <!-- /#modalOptions  -->
                    
<div id="modalLessonComplete" class="modal hide fade" tabindex=-1 role="dialog" aria-labelledby="headerLessonComplete" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="headerLessonComplete"><?php echo get_string('lessionstatistics', 'roshine'); ?></h3>
    </div>
    <div class="modal-body">
        <h4>Lesson Statisticsxx:</h4>
        <strong><?php echo get_string('rtime', 'roshine'); ?></strong> <span id="jsTime">0</span> s<br>
        <strong><?php echo get_string('rprogress', 'roshine'); ?></strong> <span id="jsProgress"> 0</span><br>
        <strong><?php echo get_string('rmistakes', 'roshine'); ?></strong> <span id="jsMistakes">0</span><br>
        <strong><?php echo get_string('rprecision', 'roshine'); ?></strong> <span id="jsAcc"> 0</span>%<br>
        <strong><?php echo get_string('rhitspermin', 'roshine'); ?></strong> <span id="jsSpeed">0</span><br>
        <strong><?php echo get_string('wpm', 'roshine'); ?></strong>: <span id="jsWpm">0</span><br>
        <strong>Mistake details</strong>: <span id="jsDetailMistake"></span>

        <div>Speed: <span id="modalWPM"></span></div>
        <div>Accuracy: <span id="modalAccuracy"></span></div>
        <div class="label label-warning" id="modalError">Your accuracy is too low. Consider redoing the lesson</div>
    </div>
    <div class="modal-footer">
        <div id="modalKey" align="center"></div>
        <br/>
        <div id="modalShare" align="center">
            <div id="modalShareTwitter">Share your achievement!</div>
        </div>
    </div>
</div> <!-- modalLessonComplete -->
                    
<div id="modalLessonComplete2" class="modal hide fade" tabindex=-1 role="dialog" aria-labelledby="headerLessonComplete" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        <h3 id="headerLessonComplete"><?php echo get_string('lessioncomplete', 'roshine'); ?></h3>
    </div>
    <div class="modal-body">
        <div><?php echo get_string('lessioncompletealert', 'roshine'); ?></div>
            <h4>Lesson statistics:</h4>
            <strong><?php echo get_string('rtime', 'roshine'); ?></strong> <span id="jsTime">0</span> s<br>
            <strong><?php echo get_string('rprogress', 'roshine'); ?></strong> <span id="jsProgress"> 0</span><br>
            <strong><?php echo get_string('rmistakes', 'roshine'); ?></strong> <span id="jsMistakes">0</span><br>
            <strong><?php echo get_string('rprecision', 'roshine'); ?></strong> <span id="jsAcc"> 0</span>%<br>
            <strong><?php echo get_string('rhitspermin', 'roshine'); ?></strong> <span id="jsSpeed">0</span><br>
            <strong><?php echo get_string('wpm', 'roshine'); ?></strong>: <span id="jsWpm">0</span><br>
            <strong>Mistake details</strong>: <span id="jsDetailMistake"></span>

            <div>Speed: <span id="modalWPM"></span></div>
            <div>Accuracy: <span id="modalAccuracy"></span></div>
            <div class="label label-warning" id="modalError">Your accuracy is too low. Consider redoing the lesson</div>
    </div>
    <div class="modal-footer">
        <div id="modalKey" align="center"></div>
        <br/>
        <div id="modalShare" align="center">
            <div id="modalShareTwitter">Share your achievement!</div>
        </div>
    </div>
</div> <!-- modalLessonComplete2 -->

</form>
</div>
        <?php
        $textToInit = '';
        for ($it = 0; $it < strlen($textToEnter); $it++) {
            if ($textToEnter[$it] == "\n") {
                $textToInit .= '\n';
            } else if($textToEnter[$it] == '"') {
                $textToInit .= '\"';
            } else if($textToEnter[$it] == "\\") {
                $textToInit .= '\\';
            } else {
                $textToInit .= $textToEnter[$it];
            }
        }

        $record = ros_get_last_check($roshine->id);
        if (is_null($record)) {
            echo '<script type="text/javascript">initTypingText("' . $textToInit . '", 0, 0, 0, 0, 0, "' .
                $CFG->wwwroot . '", ' . $roshine->showkeyboard . ');</script>';
        } else {
            echo '<script type="text/javascript">initTypingText("' . $textToInit . '", 1, ' . $record->mistakes .', ' .
                $record->hits . ', ' . $record->timetaken . ', ' . $record->attemptid . ', "' . $CFG->wwwroot . '", ' .
                $roshine->showkeyboard.');</script>';
        }
    } else {
        echo get_string('endlesson', 'roshine');
        echo "<br>";
        // if (has_capability('mod/roshine:viewgrades', get_context_instance(CONTEXT_COURSE, $course->id))) {
        //if (has_capability('mod/roshine:viewmygrades', context_module::instance($cm->id))) {
        if (has_capability('mod/roshine:viewmygrades', context_course::instance($course->id))) {
            $jlnk4 = $CFG->wwwroot . '/mod/roshine/gview.php?id='.$id.'&n='.$roshine->id;
            echo '<a href="'.$jlnk4.'">'.get_string('viewgrades', 'roshine').'</a><br><br>';
        }
        if (has_capability('mod/roshine:viewmygrades', context_module::instance($cm->id))) {
            $jlnk7 = $CFG->wwwroot . "/mod/roshine/owngrades.php?id=" . $id . "&n=" . $roshine->id;
            echo '<a href="' . $jlnk7 . '">' . get_string('viewmygrades', 'roshine') . '</a><br /><br />';
        }

    }
} else {
    // if (has_capability('mod/roshine:setup', get_context_instance(CONTEXT_COURSE, $course->id)))    {
    //if (has_capability('mod/roshine:setup', context_module::instance($cm->id))) {
    if (has_capability('mod/roshine:setup', context_course::instance($course->id))) {
        $vaLnk = $CFG->wwwroot."/mod/roshine/mod_setup.php?n=".$roshine->id;
        echo '<a href="'.$vaLnk.'">'.get_string('fsetup', 'roshine').'</a>';
    } else {
        echo get_string('notreadyyet', 'roshine');
    }
}
// css dung de hien thi khung tuy chon (Option) Used to display optional frames.
echo '<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">';

// Trigger module viewed event.
$event = \mod_roshine\event\course_module_viewed::create(array(
   'objectid' => $roshine->id,
   'context' => $context
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('roshine', $roshine);
$event->trigger();

//Khong cho hien thi menu trai - Do not display the menu.
//echo $OUTPUT->footer();

