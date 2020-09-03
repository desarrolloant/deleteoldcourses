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
 * @author 	2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Delete Old Courses';
$string['deleteoldcourses:deleteoldcourses'] = 'Delete Old Courses';
$string['user_fullname'] = 'Name';
$string['user_username'] = 'Card Id';
$string['course_shortname'] = 'Short Name';
$string['course_fullname'] = 'Full Name';
$string['course_datecreation'] = 'Created Ago';
$string['table_option'] = 'Option';
$string['coursescount'] = 'Number of courses: ';

//Date filter
$string['more_than_1_year_ago'] = 'Created more than 1 year ago';
$string['more_than_2_years_ago'] = 'Created more than 2 years ago';
$string['more_than_3_years_ago'] = 'Created more than 3 years ago';
$string['more_than_4_years_ago'] = 'Created more than 4 years ago';
$string['more_than_5_years_ago'] = 'Created more than 5 years ago';

//Modal delete strings
$string['modal_delete_title'] = 'Delete the course';
$string['modal_delete_danger_body'] = '<strong>Attention!</strong> this course has other teachers';
$string['modal_delete_accept'] = 'Are you sure to add the course to the list of courses to delete?<br> Remember that the courses will be eliminated at 00:00 am the next day';
$string['modal_delete_no_teacher'] = 'You are not a teacher of this course.';
$string['modal_delete_save_button'] = 'Yes, delete';
$string['modal_delete_cancel_button'] = 'No, cancel';
$string['modal_delete_close_button'] = 'Close';

//Events strings
$string['old_courses_list_viewed_name'] = 'Old courses list viewed';
$string['course_delete_options_viewed'] = 'Alert for delete course viewed';
$string['course_sent_delete'] = 'Course was sent to be deleted';
$string['course_remove_delete'] = 'Course was removed from delete list';

//Tasks
$string['task_delete_course'] = 'Task for deleting courses';