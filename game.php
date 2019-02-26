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
 * @package    mod_roshine
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
//$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$context = context_module::instance($cm->id);
//add_to_log($course->id, 'roshine', 'view', "view.php?id={$cm->id}", $roshine->name, $cm->id);

/// Print the page header.

$PAGE->set_url('/mod/roshine/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($roshine->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Include jquery.

$PAGE->requires->js('/mod/roshine/game/jquery-1.5.min.js', true);
$PAGE->requires->js('/mod/roshine/game/jquery-ui-1.8.9.custom.min.js', true);

// other things you may want to set - remove if not needed
$PAGE->set_cacheable(false);

// Output starts here.
echo $OUTPUT->header();


//Khong cho hien thi menu trai -Do not show the left menu.
//echo $OUTPUT->footer();

?>


 <script type="text/javascript">
    function randomFromTo(from, to){
        return Math.floor(Math.random() * (to - from + 1) + from);
    }

<?php
$file = "game/gameTextSource.txt";
$php_array = file($file);

echo 'var arrstring = new Array("' . implode('","',$php_array).'");';
?>

    var score = 0;

    $(document).ready(function() {

        var children = $("#container").children();
        var child = $("#container div:first-child");

        var currentEl;
        var currentElPress;

        var win_width = $(window).width();
        var text_move_px = 500;
        var box_left = (win_width / 2) - (text_move_px / 2);

        var playGame;
        var stop;
        
        $(".animatedbox").css("left", box_left+"px");

        $("#btnplay").click(function() {

            if ($(this).text() == "<?php echo get_string('gstart', 'roshine'); ?>") {
                startPlay();
                playGame = setInterval(startPlay, 23000);
                $(this).text("<?php echo get_string('gpause', 'roshine'); ?>");
            } else if ($(this).text() == "<?php echo get_string('gpause', 'roshine'); ?>") {
                stop = true;
                if ($("#container").find(".current").length == 0) {
                  $(this).text("<?php echo get_string('gstart', 'roshine'); ?>");
                } else {
                  $(this).text("<?php echo get_string('gwait', 'roshine'); ?>");
                }
                clearInterval(playGame);
            }
            return false;
        });

        var con_height = $("#container").height();
        var con_pos = $("#container").position();
        var min_top = con_pos.top;

        // 56 = animated box top & bottom padding + font size
        var max_top = min_top + con_height - 56;

        function startPlay() {

            child = $("#container div:first-child");
            child.addClass("current");
            currentEl = $(".current");
            
            for (i=0; i<children.length; i++) {
                var delaytime = i * 3500;
                setTimeout(function() {
                    randomIndex = randomFromTo(0, arrstring.length - 1);
                    randomTop = randomFromTo(min_top, max_top);
                    child.animate({"top": randomTop+"px"}, 'slow');
                    child.find(".match").text("");
                    child.find(".unmatch").text(arrstring[randomIndex]);
                    child.show();
                    child.animate({
                       left: "+="+text_move_px
                    }, 8000, function() {
                        currentEl.removeClass("current");
                        currentEl.fadeOut('fast');
                        currentEl.animate({
                            left: box_left+"px"
                        }, 'fast');
                        if (currentEl.attr("id") == "last") {
                            child.addClass("current");
                            currentEl = $(".current");
                            if (stop) {
                               $("#btnplay").text("<?php echo get_string('gstart', 'roshine'); ?>");
                            }
                        } else {
                            currentEl.next().addClass("current");
                            currentEl = currentEl.next();
                        }
                    });
                    child = child.next();
                }, delaytime);
            }            
        }

       
        $(document).keypress(function(event) {
            currentElPress = $(".current");
            
            var matchSpan = currentElPress.find(".match");
            var unmatchSpan = currentElPress.find(".unmatch");
            var unmatchText = unmatchSpan.text();
            var inputChar;

            if ( $.browser.msie || $.browser.opera ) {
                inputChar = String.fromCharCode(event.which);
            } else {
                inputChar = String.fromCharCode(event.charCode);
            }

            if (inputChar == unmatchText.charAt(0)) {
                unmatchSpan.text(unmatchText.replace(inputChar, ""));
                matchSpan.append(inputChar);
                if (unmatchText.length == 1) {
                    currentElPress.stop().effect("explode", 500);
                    currentElPress.animate({
                        left: box_left+"px"
                    }, 'fast');
                    if (currentElPress.attr("id") == "last" && stop) {
                        $("#btnplay").text("<?php echo get_string('gstart', 'roshine'); ?>");
                    }

                    currentElPress.removeClass("current");
                    currentElPress = currentElPress.next();
                    currentElPress.addClass("current");
                    currentEl = currentElPress;
                    score += 50;
                    $("#score").text(score).effect("highlight", { 
                        color: '#000000'
                    }, 1000);
                } else {
                    score += 10;
                    $("#score").text(score);
                }
            }
        });
    });
    </script>
    <style type="text/css">
    * {
        font-family: Arial;
    }
    #container {
        background: #333;
        width: 500px;
        height: 230px;
        margin: 0 auto;
        -webkit-border-radius: 0.7em;
        -moz-border-radius: 0.7em;
        border-radius: 0.7em;
        padding: 20px 0;
    }
    .animatedbox {
        background: #ff0084;
        position: absolute;
        padding: 8px 20px 12px 15px;
        font-size: 36px;
        color: #ffffff;
        -webkit-border-radius: 0.5em 2.5em 2.5em 0.5em;
        -moz-border-radius: 0.5em 2.5em 2.5em 0.5em;
        border-radius: 0.5em 2.5em 2.5em 0.5em;
        left: 500px;
        letter-spacing: 3px;
        display: none;
    }
    .match {
        color: #000000;
    }
    .current {
        background: #0099cc;
    }
    #toolbar {
        background: #ff0084;
        -webkit-border-radius: 1.0em;
        -moz-border-radius: 1.0em;
        border-radius: 1.0em;
        width: 500px;
        padding: 10px 0 10px 0;
        margin: 0 auto;
        text-align: center;
        margin-bottom: 10px;
        margin-top: 10px;
    }
    #boxscore {
        float: left;
        font-size: 22px;
        margin-left: 15px;
        color: #ffffff;
    }
    #score {
        font-weight: bold;
        font-size: 24px;
        padding: 0 3px;
    }
    #boxcontrol {
        float: right;
        margin-right: 15px;
    }
    #boxcontrol a {
        font-size: 22px;
        color: #ffffff;
    }
    .clear {
        margin-top: 5px;
        font-size: 11px;
        font-weight: bold;
        color: #ffffff;
        clear: both;
    }
    #main {
        background: #0099cc;
        margin-top: 0;
        padding: 2px 0 4px 0;
        text-align: center;
    }
    #main a {
        color: #ffffff;
        text-decoration: none;
        font-size: 12px;
        font-weight: bold;
        font-family: Arial;
    }
    #main a:hover {
        text-decoration: underline;
    }
    body {
        margin: 0;
        padding: 0;
    }
    </style>
<div id="main">
          
          <a href="view.php?id=<?php echo $id ?>"><?php echo get_string('gexit', 'roshine'); ?></a>
      </div>

      <div id="toolbar">
          <div id="boxscore">
            <span><?php echo get_string('gscore', 'roshine'); ?></span>&nbsp;<span id="score">0</span>
          </div>
          <div id="boxcontrol">
            <a href="" id="btnplay"><?php echo get_string('gstart', 'roshine'); ?></a>
          </div>
          <div class="clear">
              <?php echo get_string('gintro', 'roshine'); ?>
          </div>
      </div>
      <div id="container">
          <div class="animatedbox">
              <span class="match"></span><span class="unmatch"></span>
          </div>

          <div class="animatedbox">
              <span class="match"></span><span class="unmatch"></span>
          </div>

          <div class="animatedbox">
              <span class="match"></span><span class="unmatch"></span>
          </div>

          <div class="animatedbox">
              <span class="match"></span><span class="unmatch"></span>
          </div>

          <div class="animatedbox" id="last">
              <span class="match"></span><span class="unmatch"></span>
          </div>
      </div>