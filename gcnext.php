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
 * This file adds grade and performance info to mdl_mootyper_grades after an exercise.
 *
 * @package    mod_roshine
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/locallib.php');

global $DB;

require_login(0, true, null, false);
if (optional_param('rpAccInput', '', PARAM_FLOAT) >= optional_param('rpGoal', '', PARAM_FLOAT)) {
    $passfield = 1;
} else {
    $passfield = 0;
}
$record = new stdClass();
$record->roshine = optional_param('rpSityperId', '', PARAM_INT);
$record->userid = optional_param('rpUser', '', PARAM_INT);
// Gradebook entry has not been implemented, 10/10/17.
$record->grade = 0;
// Temp change to put precision in gradebook for exam.
$record->grade = optional_param('rpAccInput', '', PARAM_FLOAT);
$record->mistakes = optional_param('rpMistakesInput', '', PARAM_INT);
$record->timeinseconds = optional_param('rpTimeInput', '', PARAM_INT);
$record->hitsperminute = optional_param('rpSpeedInput', '', PARAM_FLOAT);
$record->fullhits = optional_param('rpFullHits', '', PARAM_INT);
$record->precisionfield = optional_param('rpAccInput', '', PARAM_FLOAT);
$record->timetaken = time();
$record->exercise = optional_param('rpExercise', '', PARAM_INT);
$record->pass = $passfield;
$record->attemptid = optional_param('rpAttId', '', PARAM_INT);
$record->wpm = (max(0, optional_param('rpWpmInput', '', PARAM_FLOAT)));

$DB->insert_record('roshine_grades', $record, false);

$webdir = $CFG->wwwroot . '/mod/roshine/view.php?n='.$record->roshine;
echo '<script type="text/javascript">window.location="'.$webdir.'";</script>';
