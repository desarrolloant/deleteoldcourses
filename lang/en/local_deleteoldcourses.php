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
 * @author     2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Delete Old Courses';
$string['deleteoldcourses:viewreport'] = 'Admin view reports';
$string['user_fullname'] = 'Name';
$string['user_username'] = 'Card Id';
$string['course_shortname'] = 'Short Name';
$string['course_fullname'] = 'Full Name';
$string['course_datecreation'] = 'Created Ago';
$string['table_option'] = 'Option';
$string['coursescount'] = 'Number of courses: ';

// Date filter.
$string['more_than_1_year_ago'] = 'Created more than 1 year ago';
$string['more_than_n_years_ago'] = 'Created more than {$a} years ago';

// Modal delete strings.
$string['modal_delete_title'] = 'Delete the course';
$string['modal_delete_danger_body'] = '<strong>Attention!</strong> this course has other teachers';
$string['modal_delete_accept'] = 'Are you sure to add the course to the list of courses to delete?<br> Remember that the courses will be eliminated at 00:00 am the next day';
$string['modal_delete_no_teacher'] = 'You are not a teacher of this course.';
$string['modal_delete_save_button'] = 'Yes, delete';
$string['modal_delete_cancel_button'] = 'No, cancel';
$string['modal_delete_close_button'] = 'Close';

// Events strings.
$string['old_courses_list_viewed_name'] = 'Old courses list viewed';
$string['course_delete_options_viewed'] = 'Alert for delete course viewed';
$string['course_sent_delete'] = 'Course was sent to be deleted';
$string['course_remove_delete'] = 'Course was removed from delete list';

// Tasks.
$string['task_delete_course'] = 'Task for deleting courses';

// Deleted table.
$string['sent_to_delete'] = 'Sent to delete';
$string['course_timedeleted'] = 'Time at deleted';
$string['more_than_1_month_ago'] = 'Deleted less than 1 month ago';
$string['more_than_n_months_ago'] = 'Deleted less than {$a} months ago';
$string['deleted_courses'] = 'Deleted Courses';
$string['pending_courses'] = 'Pending Courses';

// Alert in dashborad.
$string['alert_delete_content'] = 'If you wish to delete any of your courses, please go to the section';
$string['delete_courses'] = 'Delete Courses';
$string['alert_delete_recent_courses_content'] = 'To delete courses created less than a year ago, please complete the following';
$string['alert_delete_recent_courses_link'] = 'form';

// Plugin settings.
$string['manage'] = 'Delete Old Courses';
$string['courses'] = 'cursos**';
$string['criteriatab'] = 'Criterias';
$string['criteriasettingsheading'] = 'Date criteria for courses deletion';
$string['criteriasettingsheading_desc'] = 'Date from which the courses to be deleted are selected';

$string['courses_creation_date_criteria_heading'] = 'Criterio: fecha de inicio de los cursos';
$string['courses_creation_date_criteria_heading_desc'] = 'Descripción del criterio fecha de inicio';
$string['year_creation_date_desc'] = 'Example: courses are selected from the year of 2005 onwards.';
$string['month_creation_date_desc'] = 'Example: courses are selected from the month of January onwards.';
$string['day_creation_date_desc'] = 'Example: courses are selected from the 1st day of the month onwards.';
$string['hour_creation_date_desc'] = 'Example: courses are selected from the 00 hour of the day onwards.';
$string['minutes_creation_date_desc'] = 'Example: courses are selected from the 00 minutes of the hour onwards.';
$string['seconds_creation_date_desc'] = 'Example: courses are selected from the 00 seconds of the minute onwards.';

$string['courses_last_modification_date_criteria_heading'] = 'Criterio: fecha de última modificación de los cursos';
$string['courses_last_modification_date_criteria_heading_desc'] = 'Descripción del criterio última fecha de modificación';
$string['year_last_modification_date_desc'] = 'Ejemplo';
$string['month_last_modification_date_desc'] = 'Ejemplo';
$string['day_last_modification_date_desc'] = 'Ejemplo';
$string['hour_last_modification_date_desc'] = 'Ejemplo';
$string['minutes_last_modification_date_desc'] = 'Ejemplo';
$string['seconds_last_modification_date_desc'] = 'Ejemplo';

// Multiple parameter settings.
$string['parameterstab'] = 'Parameters';
$string['settings_parameters_heading'] = 'Parameters of the courses elimination process';
$string['settings_parameters_heading_desc'] = 'Settings of multiple parameters to filter the courses to be deleted';
$string['course_queue_size'] = 'Queue size of courses to delete';
$string['course_queue_size_desc'] = 'Example: courses are deleted in a queue with a maximum size of 500 courses per execution.';

// Date settings.
$string['january'] = 'January';
$string['february'] = 'February';
$string['march'] = 'March';
$string['april'] = 'April';
$string['may'] = 'May';
$string['june'] = 'June';
$string['july'] = 'July';
$string['august'] = 'August';
$string['september'] = 'September';
$string['october'] = 'October';
$string['november'] = 'November';
$string['december'] = 'December';
$string['year'] = 'Year';
$string['month'] = 'Month';
$string['day'] = 'Day';
$string['hour'] = 'Hour';
$string['minutes'] = 'Minutes';
$string['seconds'] = 'Seconds';
