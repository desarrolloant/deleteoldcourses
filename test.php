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
 * Version information for deletecourses.
 *
 * @package    local_deleteoldcourses
 * @author 2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG, $PAGE, $USER, $DB;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

const COURSE_TIME_CREATED = '2018-12-31 23:59:59';
const COURSE_LAST_MODIFICATION = '2020-06-31 23:59:59';


$PAGE->set_url('/local/deleteoldcourses/test.php', array());

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_login();
require_capability('local/deleteoldcourses:viewreport', context_system::instance());

$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'local_deleteoldcourses'));
$PAGE->navbar->add(get_string('pluginname', 'local_deleteoldcourses'));
echo $OUTPUT->header();
echo $OUTPUT->heading('Cursos a eliminar bajo el criterio abril 22, 2021');

$output = $PAGE->get_renderer('local_deleteoldcourses');

/*global $DB;
$course = $DB->get_record('course', array('id' => 47613 ));

var_dump(course_was_updated($course, COURSE_LAST_MODIFICATION));
echo '<br>';
var_dump(course_sections_was_updated($course, COURSE_LAST_MODIFICATION));
echo '<br>';
var_dump(course_modules_was_updated($course, COURSE_LAST_MODIFICATION));
echo '<br>';
var_dump(course_roles_was_updated($course, COURSE_LAST_MODIFICATION));
echo '<br>';
var_dump(course_user_enrolments_was_updated($course, COURSE_LAST_MODIFICATION));
echo '<br>';
echo get_courses_sql(COURSE_TIME_CREATED);
echo '<br>';*/
add_courses_to_delete(COURSE_TIME_CREATED, COURSE_LAST_MODIFICATION, 500, true);
