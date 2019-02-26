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
 * Version information.
 *
 * @package    mod_roshine
 * @copyright  2012 Jaka Luthar (jaka.luthar@gmail.com)
 * @copyright  2016 onwards AL Rachels (drachels@drachels.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version   = 2017103000.6;        // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires  = 2014051215.00;     // Requires Moodle 2.7.15 or higher.
$plugin->release   = '3.3 (Build: 2017103000.2)';
$plugin->cron      = 60;                // Period for cron to check this plugin (secs).
$plugin->maturity  = MATURITY_STABLE;
$plugin->component = 'mod_roshine';     // To check on upgrade, that plugin sits in correct place.
