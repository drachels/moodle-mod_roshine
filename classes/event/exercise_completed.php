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
 * The mod_roshine exercise completed event.
 *
 * @package     mod_roshine
 * @copyright   2016 AL Rachels (drachels@drachels.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace mod_roshine\event;
defined('MOODLE_INTERNAL') || die();

/**
 * The mod_roshine exercises completed event class.
 *
 * @package    mod_roshine
 * @copyright  2019 AL Rachels drachels@drachels.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exercise_completed extends \core\event\base {

    /**
     * Init method.
     */
    protected function init() {
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'roshine';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('exercise_completed', 'mod_roshine');
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' completed exercise id'{$this->other['exercise']}',
            in lesson '{$this->other['lessonname']}' in MT activity '{$this->other['activity']}',
            which uses course module id '$this->contextinstanceid'.";
    }

    /**
     * Returns relevant URL.
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/mod/roshine/exercises.php', array('id' => $this->contextinstanceid));
    }

    /**
     * replace add_to_log() statement.
     *
     * @return array of parameters to be passed to legacy add_to_log() function.
     */
    protected function get_legacy_logdata() {
        $url = new \moodle_url('exercises.php', array('id' => $this->contextinstanceid));
        return array($this->courseid, 'roshine', 'exercises', $url->out(), $this->objectid, $this->contextinstanceid);
    }
}
