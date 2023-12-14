<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.
global $DB;

/**
 * Report page for administrators.
 *
 * @package    local_deleteoldcourses
 * @copyright  2022 Brayan Sanchez <brayan.sanchez.leon@correounivalle.edu.co>
 * @copyright  2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_deleteoldcourses\report_manager;

require_login();

if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$context = context_system::instance();
$PAGE->set_context($context);

require_capability('local/deleteoldcourses:viewreports', $context);

$PAGE->set_url(new moodle_url('/local/deleteoldcourses/reports.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_deleteoldcourses'));

$reportmanager = new report_manager();
$coursedeletioncriterias = $reportmanager->get_course_deletion_criteria_settings();

$courses = $DB->get_records_sql("
  SELECT mc.id, mc.fullname, mc.shortname, courseid, pg_size_pretty(coursesize)as coursesize, to_timestamp(mc.timecreated) as timecreated_course, to_timestamp(td.timecreated) as time_added_to_delete
FROM mdl_local_delcoursesuv_todelete td
         JOIN mdl_course mc on td.courseid = mc.id
ORDER BY timecreated_course
"
);

$data = new stdClass();
$data->course_creation_date = $coursedeletioncriterias['creationdate'];
$data->course_last_modification_date = $coursedeletioncriterias['lastmodificationdate'];
$data->excluded_categories = $coursedeletioncriterias['excludedcategories'];
$data->manually_enqueued_courses = $reportmanager->get_total_enqueued_courses(true);
$data->automatically_enqueued_courses = $reportmanager->get_total_enqueued_courses(false);
$data->total_enqueued_courses = $reportmanager->get_total_enqueued_courses();
$data->total_deleted_courses = $reportmanager->get_total_deleted_courses_during_time_period();
$data->courses = array();

// Prepare data for the template
foreach ($courses as $course) {
    $data->courses[] = (object)array(
        'url' => new moodle_url('/course/view.php', array('id' => $course->id)),
        'fullname' => format_string($course->fullname),
        'id' => format_string($course->id),
        'shortname' => format_string($course->shortname),
        'timecreated_course' => format_string($course->timecreated_course),
        'time_added_to_delete' => format_string($course->time_added_to_delete),
        'coursesize' => format_string($course->coursesize),
    );
}

// Generate CSV data
$csv_data = "Course Name,Course Shortname,Course ID,Course Size,Course Time Created,Course Added to Queue\n";
foreach ($courses as $course) {
    $csv_data .= "{$course->fullname},{$course->shortname},{$course->id},{$course->coursesize},{$course->timecreated_course},{$course->time_added_to_delete}\n";
}

// Pass CSV data to template
$data->csv_data = $csv_data;

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_deleteoldcourses/reports', $data);
echo $OUTPUT->footer();
