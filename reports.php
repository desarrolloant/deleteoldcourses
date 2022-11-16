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

/**
 * Report page for administrators.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Brayan Sanchez <brayan.sanchez.leon@correounivalle.edu.co>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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

$templatecontext = new stdClass();
$templatecontext->course_creation_date = $coursedeletioncriterias['creationdate'];
$templatecontext->course_last_modification_date = $coursedeletioncriterias['lastmodificationdate'];
$templatecontext->excluded_categories = $coursedeletioncriterias['excludedcategories'];
$templatecontext->manually_enqueued_courses = $reportmanager->get_total_enqueued_courses(true);
$templatecontext->automatically_enqueued_courses = $reportmanager->get_total_enqueued_courses(false);
$templatecontext->total_enqueued_courses = $reportmanager->get_total_enqueued_courses();
$templatecontext->total_deleted_courses = $reportmanager->get_total_deleted_courses_during_time_period();

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_deleteoldcourses/reports', $templatecontext);
echo $OUTPUT->footer();
