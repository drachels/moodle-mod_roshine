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
 * Administration settings definitions for the roshine module.
 *
 * @package    mod
 * @subpackage roshine
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once($CFG->dirroot.'/mod/roshine/lib.php');
    require_once($CFG->dirroot.'/mod/roshine/locallib.php');
    $layouts = ros_get_keyboard_layouts_db();
    $settings->add(new admin_setting_configselect('roshine/defaultlayout', get_string('defaultlayout', 'roshine'), '', 2, $layouts));
    $precs = array();
    for($i=0; $i<=100; $i++)
    {
        $precs[] = $i;
    }
    $settings->add(new admin_setting_configselect('roshine/defaultprecision', get_string('defaultprecision', 'roshine'), '', 97, $precs)); 
    
}
