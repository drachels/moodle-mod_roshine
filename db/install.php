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
 * This file replaces the legacy STATEMENTS section in db/install.xml,
 * lib.php/modulename_install() post installation hook and partially defaults.php
 *
 * @package    mod_roshine
 * @copyright  Nguyen Quang Viet, Nguyen Thi Hong Nhung
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Post installation procedure
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_roshine_install() {
    global $DB, $CFG, $USER;

    $pth = $CFG->dirroot."/mod/roshine/lessons";
    $res = scandir($pth);
    for ($i = 0; $i < count($res); $i++) {
        if (is_file($pth."/".$res[$i])) {
            $fl = $res[$i]; // Argument list dafile, authorid_arg, visible_arg, editable_arg, coursearg.
            ros_read_lessons_file($fl, $USER->id, 0, 2);
        }
    }
    $pth2 = $CFG->dirroot."/mod/roshine/layouts";
    $res2 = scandir($pth2);
    for ($j = 0; $j < count($res2); $j++) {
        if (is_file($pth2."/".$res2[$j]) && ( substr($res2[$j], (strripos($res2[$j], '.') + 1) ) == 'php')) {
            $fl2 = $res2[$j];
            ros_add_keyboard_layout($fl2);
        }
    }
}

/**
 * Install keyboard layouts into the database.
 *
 * @param string $dafile
 */
function ros_add_keyboard_layout($dafile) {
    global $DB, $CFG;
    $thefile = $CFG->dirroot."/mod/roshine/layouts/".$dafile;
    $wwwfile = $CFG->wwwroot."/mod/roshine/layouts/".$dafile;
    $record = new stdClass();
    $pikapos = strrpos($dafile, '.');
    $layoutname = substr($dafile, 0, $pikapos);
    $record->filepath = $thefile;
    $record->name = $layoutname;
    $record->jspath = substr($wwwfile, 0, strripos($wwwfile, '.')).'.js';
    $DB->insert_record('roshine_layouts', $record, true);
}

/**
 * Read lesson file and add into the database.
 *
 * @param string $dafile
 * @param int $authoridarg
 * @param int $visiblearg
 * @param int $editablearg
 * @param int $coursearg
 */
function ros_read_lessons_file($dafile, $authoridarg, $visiblearg, $editablearg, $coursearg=null) {
    global $DB, $CFG;
    $thefile = $CFG->dirroot."/mod/roshine/lessons/".$dafile;
    // Echo the file.
    $record = new stdClass();
    $pikapos = strrpos($dafile, '.');
    $lessonname = substr($dafile, 0, $pikapos);
    // Echo the lesson name.
    $record->lessonname = $lessonname;
    $record->authorid = $authoridarg;
    $record->visible = $visiblearg;
    $record->editable = $editablearg;
    if (!is_null($coursearg)) {
        $record->courseid = $coursearg;
    }
    $lessonid = $DB->insert_record('roshine_lessons', $record, true);
    $fh = fopen($thefile, 'r');
    $thedata = fread($fh, filesize($thefile));
    fclose($fh);
    $haha = "";
    for ($i = 0; $i < strlen($thedata); $i++) {
        $haha .= $thedata[$i];
    }
    $haha = trim($haha);
    $splitted = explode ('/**/' , $haha);
    for ($j = 0; $j < count($splitted); $j++) {
        $vaja = trim($splitted[$j]);
        $nm = "".($j + 1);
        $texttotype = "";
        for ($k = 0; $k < strlen($vaja); $k++) {
            $ch = $vaja[$k];
            if ($ch == "\n") {
                $texttotype .= '\n';
            } else {
                $texttotype .= $ch;
            }
        }
        $erecord = new stdClass();
        $erecord->texttotype = $texttotype;
        $erecord->exercisename = $nm;
        $erecord->lesson = $lessonid;
        $erecord->snumber = $j + 1;
        $DB->insert_record('roshine_exercises', $erecord, false);
    }
}

/**
 * Post installation recovery procedure.
 *
 * @see upgrade_plugins_modules()
 */
function xmldb_roshine_install_recovery() {
}
