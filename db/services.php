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
 * Version information for deletecourses - used services.
 *
 * @package    local_deleteoldcourses
 * @since   Moodle 3.6.6
 * @author     2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$services = [
    'local_deleteoldcourses_ws' => [
        'functions' => ['local_deleteoldcourses_get_course',
                        'local_deleteoldcourses_add_course',
                        'local_deleteoldcourses_remove_course'],
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1
    ]
];

$functions = array (
    'local_deleteoldcourses_get_course' => array(
        'classname'   => 'local_deleteoldcourses_external',
        'methodname'  => 'get_course',
        'classpath'   => 'local/deleteoldcourses/externallib.php',
        'description' => 'Get a course by id.',
        'type'        => 'read',
        'ajax'          => true,
        'loginrequired' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ),
    'local_deleteoldcourses_add_course' => array(
        'classname'   => 'local_deleteoldcourses_external',
        'methodname'  => 'add_course',
        'classpath'   => 'local/deleteoldcourses/externallib.php',
        'description' => 'Add a course to delete list.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ),
    'local_deleteoldcourses_remove_course' => array(
        'classname'   => 'local_deleteoldcourses_external',
        'methodname'  => 'remove_course',
        'classpath'   => 'local/deleteoldcourses/externallib.php',
        'description' => 'remove a course to delete list.',
        'type'        => 'write',
        'ajax'          => true,
        'loginrequired' => true,
        'services' => [MOODLE_OFFICIAL_MOBILE_SERVICE]
    ),
);
