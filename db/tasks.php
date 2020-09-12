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
 * This file defines tasks performed by the deleteoldcourses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.6.6
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// List of tasks.
$tasks = array(
    array(
        'classname' => 'local_deleteoldcourses\task\delete_courses_task',
        'blocking' => 0,
        'minute' => '0',
        'hour' => '2,4',
        'day' => '*',
        'dayofweek' => '*',
        'month' => '*'
    )
);