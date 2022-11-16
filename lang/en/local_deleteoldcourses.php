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
 * Plugin strings, language 'en'.
 *
 * @package     local_deleteoldcourses
 * @author      2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Delete Old Courses';
$string['deleteoldcourses:viewreports'] = 'Admin view reports';
$string['user_fullname'] = 'Name';
$string['user_username'] = 'Card ID';
$string['course_shortname'] = 'Course shortname';
$string['course_fullname'] = 'Course full name';
$string['course_datecreation'] = 'Created ago';
$string['table_option'] = 'Option';
$string['coursescount'] = 'Number of courses: ';

// Date filter.
$string['more_than_1_year_ago'] = 'Created more than 1 year ago';
$string['more_than_n_years_ago'] = 'Created more than {$a} years ago';

// Modal delete strings.
$string['modal_delete_title'] = 'Delete the course';
$string['modal_delete_danger_body'] = '<strong>Attention!</strong> this course has other teachers';
$string['modal_delete_accept'] = 'Are you sure to add the course to the queue of courses to delete?<br> Remember that the courses will be eliminated at 00:00 am the next day';
$string['modal_delete_no_teacher'] = 'You are not a teacher of this course.';
$string['modal_delete_save_button'] = 'Yes, delete';
$string['modal_delete_cancel_button'] = 'No, cancel';
$string['modal_delete_close_button'] = 'Close';

// Events strings.
$string['old_courses_list_viewed_name'] = 'Old courses list viewed';
$string['course_delete_options_viewed'] = 'Alert for delete course viewed';
$string['course_sent_delete'] = 'Course was sent to be deleted';
$string['course_remove_delete'] = 'Course was removed from the deletion queue';

// Tasks.
$string['task_delete_course'] = 'Task for deleting courses';
$string['enqueue_courses_task'] = 'Enqueue courses to delete';
$string['delete_courses_task'] = 'Delete enqueued courses';
$string['number_courses_excluded_by_categories'] = 'Number of courses excluded by the "Course categories excluded" criterion: {$a}';
$string['number_courses_excluded_by_new_sections'] = 'Number of courses excluded by the "New sections added" criterion: {$a} ';
$string['number_courses_excluded_by_new_participants'] = 'Number of courses excluded by the "New participants added" criterion: {$a}';
$string['number_courses_excluded_by_new_modules'] = 'Number of courses excluded by the "New modules added" criterion: {$a}';
$string['number_courses_excluded_by_cvh'] = 'Number of courses excluded by the "Campus Virtual Historia" criterion: {$a}';
$string['course_excluded_by_categories'] = 'Course excluded by the "Course categories excluded" criterion: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_sections'] = 'Course excluded by the "New sections added" criterion: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_participants'] = 'Course excluded by the "New participants added" criterion: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_modules'] = 'Course excluded by the "New modules added" criterion: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_cvh'] = 'Course excluded by the "Campus Virtual Historia" criterion: {$a->shortname} {$a->coursecategory}';

// Deleted table.
$string['sent_to_delete'] = 'Sent to delete';
$string['course_timedeleted'] = 'Time at deleted';
$string['more_than_1_month_ago'] = 'Deleted less than 1 month ago';
$string['more_than_n_months_ago'] = 'Deleted less than {$a} months ago';
$string['deleted_courses'] = 'Deleted courses';
$string['pending_courses'] = 'Pending courses';

// Alert in dashborad.
$string['alert_delete_content'] = 'If you wish to delete any of your courses, please go to the section';
$string['delete_courses'] = 'Delete Courses';
$string['alert_delete_recent_courses_content'] = 'To delete courses created less than a year ago, please complete the following';
$string['alert_delete_recent_courses_link'] = 'form';

// Plugin settings.
$string['manage'] = 'Delete Old Courses';
$string['criteriatab'] = 'Deletion criteria';

$string['courses_creation_date_criteria_heading'] = 'Criteria: course creation date';
$string['courses_creation_date_criteria_heading_desc'] = 'Courses to be queued and subsequently deleted are selected prior to a creation date.';
$string['year_creation_date_desc'] = 'Example: courses that are created before the year 2010 are selected.';
$string['month_creation_date_desc'] = 'Example: courses that are created before the month of December are selected.';
$string['day_creation_date_desc'] = 'Example: courses that are created before the 31th day of the month are selected.';
$string['hour_creation_date_desc'] = 'Example: courses that are created before the 23 hour of the day are selected.';
$string['minutes_creation_date_desc'] = 'Example: courses that are created before the 59 minutes of the hour are selected.';
$string['seconds_creation_date_desc'] = 'Example: courses that are created before the 59 seconds of the minute are selected.';

$string['courses_last_modification_date_criteria_heading'] = 'Criteria: course last modification date';
$string['courses_last_modification_date_criteria_heading_desc'] = 'In addition to the previous criteria, the course last modification date is also taken into account.';
$string['year_last_modification_date_desc'] = 'Example: courses that are not modified before the year 2009 are selected.';
$string['month_last_modification_date_desc'] = 'Example: courses that are not modified before the month of December are selected.';
$string['day_last_modification_date_desc'] = 'Example: courses that are not modified before the 31th day of the month are selected.';
$string['hour_last_modification_date_desc'] = 'Example: courses that are not modified before the 23 hour of the day are selected.';
$string['minutes_last_modification_date_desc'] = 'Example: courses that are not modified before the 59 minutes of the hour are selected.';
$string['seconds_last_modification_date_desc'] = 'Example: courses that are not modified before the 59 seconds of the minute are selected.';

$string['excluded_course_categories_criteria_heading'] = 'Criteria: excluded course categories';
$string['excluded_course_categories_criteria_heading_desc'] = 'Those courses that belong to the selected course categories will not be taken into account in the process of deletion.';
$string['number_of_categories_to_exclude'] = 'Number of course categories to exclude';
$string['number_of_categories_to_exclude_desc'] = 'Select the number of course categories to exclude and save the changes to reload the page with the new fields to select.';
$string['excluded_course_categories'] = 'Excluded course category {$a}';
$string['excluded_course_categories_desc'] = 'Select a course category to exclude.';

// Advanced settings.
$string['advancedtab'] = 'Advanced settings';
$string['advanced_settings_heading'] = 'Advanced settings for deleting courses';
$string['advanced_settings_heading_desc'] = 'These settings should be changed as long as you know what you are doing.';
$string['limit_query_to_enqueue_courses'] = 'Limit SQL query to enqueue courses';
$string['limit_query_to_enqueue_courses_desc'] = 'When the task of enqueuing courses to delete is executed, this value allows it to do it by blocks of n courses in order not
                                                    to overload their processing (Note: the course table is fully processed regardless of the configured value).';
$string['deletion_task_queue_size'] = 'Deletion task queue size';
$string['deletion_task_queue_size_desc'] = 'When the task of deleting enqueued courses is executed, this value allows it to do it by blocks
                                            of n courses in order not to overload their processing.';

// Notification settings.
$string['notification_settings_tab'] = 'Notifications';
$string['notification_settings_heading'] = 'Notifications settings';
$string['notification_settings_heading_desc'] = 'Add users who will be sent email notifications about course deletions.';
$string['users_to_notify'] = 'Users to notify';
$string['users_to_notify_desc'] = 'Add, separated by commas, the usernames (username) to notify.';

// Client settings.
$string['ws_client_settings_tab'] = 'Client for web service';
$string['ws_client_settings_heading'] = 'Client for Campus Virtual Historia web service';
$string['ws_client_settings_heading_desc'] = 'Client parameters for Campus Virtual Historia web service. These settings avoid the deletion of courses that are not backed up yet in Campus Virtual Historia.';
$string['ws_url'] = 'URL to Campus Virtual Historia';
$string['ws_url_desc'] = 'Add the URL to Campus Virtual Historia.';
$string['ws_function_name'] = 'Web service function name';
$string['ws_function_name_desc'] = 'Add the function name used by the web service.';
$string['courseid'] = 'Course ID';
$string['ws_user_token'] = 'Web service authorized user token';
$string['ws_user_token_desc'] = 'It can be obtained from the user\'s security keys page: {$a}';

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

// Exceptions.
$string['timecreated_criterion_is_empty'] = 'Time created criterion cannot be empty';
$string['timemodified_criterion_is_empty'] = 'Time modified criterion cannot be empty.';
$string['limit_query_to_enqueue_courses_is_empty'] = 'Limit SQL query to enqueue courses criterion cannot be empty.';

// Notifier.
$string['message_to_send'] = 'The delete courses UV plugin has detected that there are still pending courses to be deleted. \n';
$string['message_to_send'] .= 'Summary: \n';
$string['message_to_send'] .= '<pre>';
$string['message_to_send'] .= '- Number of deleted courses: {$a->deletedcourses}';
$string['message_to_send'] .= '- Number of pending courses to delete: {$a->pendingcourses}';
$string['message_to_send'] .= '</pre>';
$string['message_to_send'] .= 'This message has been generated automatically, <b>please do not reply</b> to this message.';
$string['notification_subject'] = 'Campus Virtual notification: courses pending to be deleted.';

// Moodle exceptions.
$string['invalid_input_datetimetype'] = 'Entered input: {$a}. Valid inputs: monthsoftheyear, daysofthemonth, hoursinaday or minutesinanhour';
$string['invalid_return_format'] = 'CVH Client: invalid return format.';
$string['empty_ws_url'] = 'CVH Client: empty URL to web service.';
$string['empty_return_format'] = 'CVH Client: empty return format.';
$string['empty_ws_user_token'] = 'CVH Client: empty user token.';
$string['clientcvh_invalid_parameters'] = 'CVH Client: the second parameter of request() should be an array.';
$string['empty_ws_function_name'] = 'CVH Client: the web service function name is empty.';
$string['request_error'] = 'CVH Client: request error.';
$string['request_method_invalid'] = 'CVH Client: invalid request method.';

// Reports.
$string['reports_dashboard_heading'] = 'Reports dashboard';
$string['deletion_criteria_desc'] = 'Current course deletion criteria';
$string['course_creation_date'] = 'Course creation date (DD/MM/YYYY HH:MM:SS)';
$string['course_last_modification_date'] = 'Course last modification date (DD/MM/YYYY HH:MM:SS)';
$string['excluded_categories'] = 'Excluded course categories';
$string['enqueued_courses_desc'] = 'Currently enqueued courses to delete';
$string['manually_enqueued_courses'] = 'Manually enqueued courses';
$string['automatically_enqueued_courses'] = 'Automatically enqueued courses';
$string['total_enqueued_courses'] = 'Total enqueued courses';
$string['deleted_courses_desc'] = 'Currently deleted courses';
$string['total_deleted_courses'] = 'Total deleted courses';
