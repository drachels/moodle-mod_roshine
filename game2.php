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
 * Prints a particular instance of roshine
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage roshine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $USER, $CFG;
require_once(dirname(dirname(dirname(__FILE__))).'/config.php'); // lenh require_once chi thuc hien 1 lan
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // roshine instance ID - it should be named as the first character of the module

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
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
add_to_log($course->id, 'roshine', 'view', "view.php?id={$cm->id}", $roshine->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/roshine/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($roshine->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

//include jquery

$PAGE->requires->js('/mod/roshine/jquery/jquery.js', true);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);

// Output starts here
echo $OUTPUT->header();


//Khong cho hien thi menu trai
//echo $OUTPUT->footer();

?>


        <script type="text/javascript">
            $(document).ready(function()
            {
                // Getting screen resolutions and positioning the start button
                var width = screen.width - 100;
                var height = screen.height - 200;
                var code = 0;
                $('#start').css({ "top" : (height/2)+'px', "left" : (width/2)+'px' });

                $('#start').click( function(){
                    $(this).fadeOut('slow');
                    $('#score').show();
                    genLetter();
                });

                // Dealing KeyEvents and fading out matched bubble
                $(document).keydown( function(event) {
                    var keycode = event.keyCode;
                    $('.bubb'+keycode).animate({ "top" : height+"px", "opacity" : 0 }, 'slow'); $('.bubb'+keycode).fadeOut('slow').hide( 'slow', function()
                    {
                        code += 20;
                        $('#score').html(code);
                        $(this).remove();
                    }
                );
                });

                // Generating a random alphabet between A-Z
                function genLetter(){
                    var color = randomColor();
                    var k = Math.floor(Math.random() * ( 90 - 65 + 1 )) + 65;
                    var ch = String.fromCharCode(k);
                    var top = Math.floor(Math.random() * height );
                    var left = Math.floor(Math.random() * width );
                    $('#main').append('<span class="bubb bubb'+ k +'" style="background-color:'+ color +'">'+ ch +'</span>');
                    setTimeout(genLetter, 1000);
                }

                // Generating a random color
                function randomColor(){
                    var color = '';
                    var values = ['a', 'b', 'c', 'd', 'e', 'f', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
                    for (c = 0; c < 6; c++) {
                        no = Math.floor(Math.random() * 15);
                        color += values[no];
                    }
                    return color;
                }
            });
        </script>
        <style type="text/css">
            body
            {
                width: 100%;
                margin: 0 auto;
                padding: 0;
            }
            
             #main
            {
                width: 1000px;
                height: 1000px;
                margin: 0 auto;
                padding: 0;
                background-color: blue;
            }

            .bubb
            {
                *position: absolute;
                width:30px;
                height: 30px;
                font: bold 14px verdana;
                background-color: red;
                text-align: center;
                -webkit-border-radius: 20px;
                -moz-border-radius: 20px;
                vertical-align: middle;
                padding: 5px;
            }

            #score
            {
                font-size:46px;
                top: 25px;
                right: 50px;
                display: none;
                text-align:right;
            }

            #start{
                width: 50px;
                padding: 10px 15px;
                text-align: center;
                font:bold 15px arial;
                background-color: #dedede;
                color: #000;
                -webkit-border-radius: 6px;
                -moz-border-radius: 6px;
                position: absolute;
            }

            #start:hover{
                cursor: pointer;
            }

        </style>
        <div id="main"></div>
        <div id="score">0</div>
        <div id="start">Start</div>
    
    