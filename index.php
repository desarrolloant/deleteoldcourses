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
 * @package	local_deleteoldcourses
 * @author 2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/deleteoldcourses/lib.php');

global $CFG, $PAGE, $USER, $DB;

define('DEFAULT_PAGE_SIZE', 15);
define('SHOW_ALL_PAGE_SIZE', 5000);

define('COURSE_ONE_YEAR_OLD', ' -1 year');
define('COURSE_TWO_YEAR_OLD', ' -2 year');
define('COURSE_THREE_YEAR_OLD', ' -3 year');
define('COURSE_FOUR_YEAR_OLD', ' -4 year');
define('COURSE_FIVE_YEAR_OLD', ' -5 year');

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$selectall    = optional_param('selectall', false, PARAM_BOOL); // When rendering checkboxes against users mark them all checked.

$PAGE->set_url('/local/deleteoldcourses/index.php', array(
        'page' => $page,
        'perpage' => $perpage,
        'selectall' => $selectall));

// Report all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// Ensure the user can be here.
require_login();


$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'local_deleteoldcourses'));
$PAGE->navbar->add(get_string('pluginname', 'local_deleteoldcourses'));
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_deleteoldcourses'));

//Get renderer of local_deleteoldcourses
$output = $PAGE->get_renderer('local_deleteoldcourses');

$baseurl = new moodle_url('/local/deleteoldcourses/index.php', array());
$coursestable = new \local_deleteoldcourses\output\list_courses_table($USER->id);
$coursestable->define_baseurl($baseurl);
$coursestablehtml = $output->render_courses_table($coursestable, $perpage);

//Display number of courses
echo $output->render_number_of_courses($coursestable->totalrows);

//Display old courses table
echo $coursestablehtml;

//Display show all link
$perpageurl = clone($baseurl);
echo $output->render_courses_show_all_link($perpageurl, $coursestable->get_page_size(), $coursestable->totalrows, $perpage);

//Load scripts
echo $output->render_scripts('');

// Trigger deleteoldcourses viewed event.
deleteoldcourses_viewed($PAGE->context, $USER->id);

//Print footer
echo $OUTPUT->footer();

