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

global $CFG, $PAGE, $USER, $DB;

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot.'/local/deleteoldcourses/lib.php');
require_once($CFG->libdir . '/adminlib.php');

define('DEFAULT_PAGE_SIZE', 15);
define('SHOW_ALL_PAGE_SIZE', 5000);
define('MAX_DELETED_AGO', 60);
define('MIN_DELETED_AGO', 1);

$page         = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage      = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$userid       = optional_param('userid', 0, PARAM_INT);
$ago          = optional_param('ago', MIN_DELETED_AGO, PARAM_INT); // Created ago number of years.
$action       = optional_param('action', 'pending', PARAM_TEXT); // pending or deleted report.

if ($ago < MIN_DELETED_AGO) {
	$ago = MIN_DELETED_AGO;
}

if($ago > MAX_DELETED_AGO){
	$ago = MAX_DELETED_AGO;
}

$PAGE->set_url('/local/deleteoldcourses/report.php', array(
	'action' => $action,
	'page' => $page,
    'perpage' => $perpage,
	'userid' => $userid,
	'ago' => $ago,
));

// Report all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_login();
require_capability('local/deleteoldcourses:viewreport', context_system::instance());

admin_externalpage_setup('local_deleteoldcourses', '', null);

$PAGE->set_pagelayout('admin');
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title($SITE->fullname . ': ' . get_string('pluginname', 'local_deleteoldcourses'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_deleteoldcourses'));

//Get renderer of local_deleteoldcourses
$output = $PAGE->get_renderer('local_deleteoldcourses');

$baseurl = new moodle_url('/local/deleteoldcourses/report.php', array(
	'action' => $action,
	'page' => $page,
    'perpage' => $perpage,
    'ago' => $ago,
    'userid' => $userid
));

//Display action buttons
echo $output->render_buttons($action);

if ($action == 'deleted') {
	//Display date filter
	echo $output->render_date_deleted_filter($ago, $baseurl);

	$coursestable = new \local_deleteoldcourses\output\admin_deleted_table($userid, $ago);
	$coursestable->define_baseurl($baseurl);
	$coursestablehtml = $output->render_deleted_table($coursestable, $perpage);

	//Display number of courses
	echo $output->render_number_of_courses($coursestable->totalrows);

	//Display old courses table
	echo $coursestablehtml;
}else if($action == 'pending'){
	$coursestable = new \local_deleteoldcourses\output\admin_pending_table();
	$coursestable->define_baseurl($baseurl);
	$coursestablehtml = $output->render_pending_table($coursestable, $perpage);

	//Display number of courses
	echo $output->render_number_of_courses($coursestable->totalrows);

	//Display old courses table
	echo $coursestablehtml;
}





//Print footer
echo $OUTPUT->footer();