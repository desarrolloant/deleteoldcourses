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
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
$string['enqueue_courses_task'] = 'Enqueue courses to delete';
$string['delete_courses_task'] = 'Delete enqueued courses';
$string['number_courses_excluded_by_categories'] = 'Number of courses excluded by the "Course categories excluded" criteria: {$a}';
$string['number_courses_excluded_by_new_sections'] = 'Number of courses excluded by the "New sections added" criteria: {$a} ';
$string['number_courses_excluded_by_new_participants'] = 'Number of courses excluded by the "New participants added" criteria: {$a}';
$string['number_courses_excluded_by_new_modules'] = 'Number of courses excluded by the "New modules added" criteria: {$a}';
$string['number_courses_excluded_by_cvh'] = 'Number of courses excluded by the "Campus Virtual Historia" criteria: {$a}';
$string['course_excluded_by_categories'] = 'Course excluded by the "Course categories excluded" criteria: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_sections'] = 'Course excluded by the "New sections added" criteria: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_participants'] = 'Course excluded by the "New participants added" criteria: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_modules'] = 'Course excluded by the "New modules added" criteria: {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_cvh'] = 'Course excluded by the "Campus Virtual Historia" criteria: {$a->shortname} {$a->coursecategory}';

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

$string['course_categories_criteria_heading'] = 'Categorías de curso excluidas';
$string['course_categories_criteria_heading_desc'] = 'Categorías que no se tendrán en cuenta en el proceso de eliminación automática de cursos.';
$string['number_of_categories_to_exclude'] = 'Cantidad de categorías a excluir';
$string['number_of_categories_to_exclude_desc'] = 'Seleccione la cantidad de categorías a excluir.';
$string['excluded_course_categories'] = 'Categorías de curso excluidas';
$string['excluded_course_categories_desc'] = 'Categorías que no se tendrán en cuenta en el proceso de eliminación automática de cursos.';

// Advanced settings.
$string['advancedtab'] = 'Configuraciones avanzadas';
$string['advanced_settings_heading'] = 'Configuraciones avanzadas para la eliminación de cursos';
$string['advanced_settings_heading_desc'] = 'Estas configuraciones se deben modificar siempre y cuando esté seguro de lo que está haciendo';
$string['limit_query_to_enqueue_courses'] = 'Limit query to enqueue courses';
$string['limit_query_to_enqueue_courses_desc'] = 'Cuando la tarea de encolar cursos a eliminar se ejecuta (la tabla de cursos se procesa completamente),
                                este valor permite que lo realice por bloques de n cursos para no sobrecargar el procesamiento de los mismos.';
$string['task_queue_size'] = 'Deletion task queue size';
$string['task_queue_size_desc'] = 'Cuando la tarea de eliminar cursos encolados se ejecuta, este valor permite que lo realice por bloques
                                    de n cursos para no sobrecargar el procesamiento de los mismos.';

// Client settings.
$string['ws_client_settings_tab'] = 'Client';
$string['ws_client_settings_heading'] = 'Client for Campus Virtual Historia service';
$string['ws_client_settings_heading_desc'] = 'Parameters for the client to Campus Virtual Historia service';
$string['ws_url'] = 'URL to Campus Virtual Historia';
$string['ws_url_desc'] = 'URL to Campus Virtual Historia';
$string['ws_function_name'] = 'Function name';
$string['ws_function_name_desc'] = 'Name of the function used by the web service';
$string['courseid'] = 'Course ID';
$string['course_shortname'] = 'Course shortname';
$string['ws_user_token'] = 'User token';
$string['ws_user_token_desc'] = 'Can be obtained from the user\'s private key page';

// Notification settings.
$string['notification_settings_tab'] = 'Notificaciones';
$string['notification_settings_heading'] = 'Configuraciones para notificaciones';
$string['notification_settings_heading_desc'] = 'Configuraciones para notificaciones sobre eliminación de cursos.';
$string['users_to_notify'] = 'Usuarios a notificar';
$string['users_to_notify_desc'] = 'Ingrese, separados por comas, los nombres de usuario (username) de los usuarios a notificar.';

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
$string['timecreated_criteria_is_empty'] = 'Timecreated criteria cannot be empty';
$string['timemodified_criteria_is_empty'] = 'Timecreated criteria cannot be empty';
$string['limit_query_to_enqueue_courses_is_empty'] = 'Limit query criteria cannot be empty';

// Notifier.
$string['message_to_send'] = 'El módulo de eliminación de cursos ha detectado que aún quedan cursos pendientes por eliminar. \n';
$string['message_to_send'] .= 'Resumen de la ejecución: \n';
$string['message_to_send'] .= '<pre>';
$string['message_to_send'] .= '- Cantidad de cursos borrados: {$a->deletedcourses}';
$string['message_to_send'] .= '- Cantidad de cursos pendientes: {$a->pendingcourses}';
$string['message_to_send'] .= '</pre>';
$string['message_to_send'] .= 'Este mensaje ha sido generado automáticamente, <b>por favor no responda</b> a este mensaje.';
$string['notification_subject'] = 'Notificación Campus Virtual: Cursos pendientes por eliminar';

// Moodle exceptions.
$string['invalid_input_datetimetype'] = 'Opción ingresada: {$a}. Opciones válidas: monthsoftheyear, daysofthemonth, hoursinaday o minutesinanhour';
$string['invalid_return_format'] = 'CVH Client: Invalid return format.';
$string['empty_ws_url'] = 'CVH Client: Empty URL to service.';
$string['empty_return_format'] = 'CVH Client: Empty return format.';
$string['empty_ws_user_token'] = 'CVH Client: Empty user token.';
$string['clientcvh_invalid_parameters'] = 'CVH Client: The second parameter of request() should be an array.';
$string['empty_ws_function_name'] = 'CVH Client: The function name setting is empty.';
$string['request_error'] = 'CVH Client: Request error.';
$string['request_method_invalid'] = 'CVH Client: Request method invalid.';

// Reports.
$string['reports_dashboard_heading'] = 'Reports Dashboard';
$string['deletion_criteria_desc'] = 'Current deletion criterias';
$string['enqueued_courses_desc'] = 'Currently enqueued courses';
$string['course_creation_date'] = 'Course creation date (DD/MM/YYYY HH:MM:SS)';
$string['course_last_modification_date'] = 'Course last modification date (DD/MM/YYYY HH:MM:SS)';
$string['excluded_categories'] = 'Excluded course categories';
$string['manually_enqueued_courses'] = 'Manually enqueued courses';
$string['automatically_enqueued_courses'] = 'Automatically enqueued courses';
$string['all_enqueued_courses'] = 'All enqueued courses';
