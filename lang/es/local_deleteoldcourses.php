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
 * Plugin strings, language 'es'.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Eliminar Cursos Antiguos';
$string['deleteoldcourses:viewreport'] = 'Vista de reportes para admin';
$string['user_fullname'] = 'Nombre';
$string['user_username'] = 'Cédula';
$string['course_shortname'] = 'Nombre corto del curso';
$string['course_fullname'] = 'Nombre completo del curso';
$string['course_datecreation'] = 'Creado Hace';
$string['table_option'] = 'Opción';
$string['coursescount'] = 'Número de cursos: ';

// Date filter.
$string['more_than_1_year_ago'] = 'Creados hace más de 1 año';
$string['more_than_n_years_ago'] = 'Creados hace más de {$a} años';

// Modal delete strings.
$string['modal_delete_title'] = 'Eliminar el curso';
$string['modal_delete_danger_body'] = '<strong>¡Atención!</strong> Este curso tiene otros profesores.';
$string['modal_delete_accept'] = '¿Esta Seguro de añadir el curso a la cola de cursos a eliminar?<br> Recuerde que los cursos serán eliminados a las 00:00 horas del siguiente día.';
$string['modal_delete_no_teacher'] = 'No eres profesor de este curso.';
$string['modal_delete_save_button'] = 'Si, eliminar';
$string['modal_delete_cancel_button'] = 'No, cancelar';
$string['modal_delete_close_button'] = 'Cerrar';

// Events strings.
$string['old_courses_list_viewed_name'] = 'Listado de cursos viejos visto';
$string['course_delete_options_viewed'] = 'Alerta para eliminar curso vista';
$string['course_sent_delete'] = 'Curso enviado para ser eliminado';
$string['course_remove_delete'] = 'Curso removido de la cola de eliminación';

// Tasks.
$string['task_delete_course'] = 'Tarea para eliminar cursos';
$string['enqueue_courses_task'] = 'Encolar cursos a eliminar';
$string['delete_courses_task'] = 'Eliminar cursos encolados';
$string['number_courses_excluded_by_categories'] = 'Cantidad de cursos excluidos por el criterio "Categorías de curso excluidas": {$a}';
$string['number_courses_excluded_by_new_sections'] = 'Cantidad de cursos excluidos por el criterio "Nuevas secciones añadidas": {$a} ';
$string['number_courses_excluded_by_new_participants'] = 'Cantidad de cursos excluidos por el criterio "Nuevos participantes añadidos": {$a}';
$string['number_courses_excluded_by_new_modules'] = 'Cantidad de cursos excluidos por el criterio "Nuevos módulos añadidos": {$a}';
$string['number_courses_excluded_by_cvh'] = 'Cantidad de cursos excluidos por el criterio "Campus Virtual Historia": {$a}';
$string['course_excluded_by_categories'] = 'Curso excluido por el criterio "Categorías de curso excluidas": {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_sections'] = 'Curso excluido por el criterio "Nuevas secciones añadidas": {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_participants'] = 'Curso excluido por el criterio "Nuevos participantes añadidos": {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_new_modules'] = 'Curso excluido por el criterio "Nuevos módulos añadidos": {$a->shortname} {$a->coursecategory}';
$string['course_excluded_by_cvh'] = 'Cursos excluido por el criterio "Campus Virtual Historia": {$a->shortname} {$a->coursecategory}';

// Deleted table.
$string['sent_to_delete'] = 'Enviado para eliminar';
$string['course_timedeleted'] = 'Fecha de eliminación';
$string['more_than_1_month_ago'] = 'Eliminados hace menos de 1 mes';
$string['more_than_n_months_ago'] = 'Eliminados hace menos de {$a} meses';
$string['deleted_courses'] = 'Cursos eliminados';
$string['pending_courses'] = 'Cursos pendientes';

// Alert in dashborad.
$string['alert_delete_content'] = 'Si usted desea eliminar alguno de sus cursos, por favor diríjase a la sección';
$string['delete_courses'] = 'Eliminar Cursos';
$string['alert_delete_recent_courses_content'] = 'Para eliminar los cursos creados hace menos de un año, por favor diligenciar el siguiente';
$string['alert_delete_recent_courses_link'] = 'formulario';

// Plugin settings.
$string['manage'] = 'Eliminación de Cursos Antiguos';
$string['criteriatab'] = 'Criterios de eliminación';

$string['courses_creation_date_criteria_heading'] = 'Criterio: fecha de creación de los cursos';
$string['courses_creation_date_criteria_heading_desc'] = 'Los cursos que van a ser encolados y posteriormente eliminados serán aquellos anteriores a una fecha de creación.';
$string['year_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes del año 2010.';
$string['month_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes del mes de Diciembre.';
$string['day_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes del día 31 del mes.';
$string['hour_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes de la hora 23 del día.';
$string['minutes_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes del minuto 59 de la hora.';
$string['seconds_creation_date_desc'] = 'Ejemplo: se seleccionan cursos creados antes del segundo 59 del minuto.';

$string['courses_last_modification_date_criteria_heading'] = 'Criterio: fecha de última modificación de los cursos';
$string['courses_last_modification_date_criteria_heading_desc'] = 'Adicional al anterior criterio, también se tiene en cuenta la última fecha de modificación del curso.';
$string['year_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes del año 2009.';
$string['month_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes del mes de Diciembre.';
$string['day_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes del día 31 del mes.';
$string['hour_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes de la hora 23 del día.';
$string['minutes_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes del minuto 59 de la hora.';
$string['seconds_last_modification_date_desc'] = 'Ejemplo: se seleccionan cursos no modificados antes del segundo 59 del minuto.';

$string['excluded_course_categories_criteria_heading'] = 'Criterio: categorías de cursos excluidas';
$string['excluded_course_categories_criteria_heading_desc'] = 'Aquellos cursos que pertenezcan a las categorías de cursos seleccionadas no se tendrán en cuenta en el proceso de eliminación.';
$string['number_of_categories_to_exclude'] = 'Cantidad de categorías de cursos a excluir';
$string['number_of_categories_to_exclude_desc'] = 'Seleccione la cantidad de categorías de cursos a excluir y guarde los cambios para recargar la página con los nuevos campos para seleccionar.';
$string['excluded_course_categories'] = 'Categoría de cursos excluida {$a}';
$string['excluded_course_categories_desc'] = 'Seleccione una categoría de cursos a excluir.';

// Advanced settings.
$string['advancedtab'] = 'Configuraciones avanzadas';
$string['advanced_settings_heading'] = 'Configuraciones avanzadas para la eliminación de cursos';
$string['advanced_settings_heading_desc'] = 'Estas configuraciones se deben modificar siempre y cuando sepa lo que está haciendo.';
$string['limit_query_to_enqueue_courses'] = 'Limitar la consulta SQL al encolar cursos';
$string['limit_query_to_enqueue_courses_desc'] = 'Cuando la tarea de encolar cursos a eliminar se ejecuta, este valor permite que lo realice por bloques de n cursos para no
                                                    sobrecargar el procesamiento de los mismos (Nota: la tabla de cursos se procesa por completo independientemente al valor configurado).';
$string['deletion_task_queue_size'] = 'Tamaño de la cola en la tarea de eliminación';
$string['deletion_task_queue_size_desc'] = 'Cuando la tarea de eliminar cursos encolados se ejecuta, este valor permite que lo realice por bloques
                                            de n cursos para no sobrecargar el procesamiento de los mismos.';

// Notification settings.
$string['notification_settings_tab'] = 'Notificaciones';
$string['notification_settings_heading'] = 'Configuraciones para notificaciones';
$string['notification_settings_heading_desc'] = 'Añada los usuarios a quienes se enviarán notificaciones por correo electrónico sobre la eliminación de cursos.';
$string['users_to_notify'] = 'Usuarios a notificar';
$string['users_to_notify_desc'] = 'Ingrese, separados por comas, los nombres de usuario (username) a notificar.';

// Client settings.
$string['ws_client_settings_tab'] = 'Cliente para servicio web';
$string['ws_client_settings_heading'] = 'Cliente para el servicio web en Campus Virtual Historia';
$string['ws_client_settings_heading_desc'] = 'Parámetros del cliente para el servicio web en Campus Virtual Historia.';
$string['ws_url'] = 'URL de Campus Virtual Historia';
$string['ws_url_desc'] = 'Ingrese la URL de Campus Virtual Historia.';
$string['ws_function_name'] = 'Nombre de la función del servicio web';
$string['ws_function_name_desc'] = 'Ingrese el nombre de la función utilizada por el servicio web.';
$string['courseid'] = 'Identificador del curso';
$string['ws_user_token'] = 'Clave privada del usuario autorizado (WS token)';
$string['ws_user_token_desc'] = 'Este se puede obtener de la página de claves de seguridad del usuario: {$a}';

// Date settings.
$string['january'] = 'Enero';
$string['february'] = 'Febrero';
$string['march'] = 'Marzo';
$string['april'] = 'Abril';
$string['may'] = 'Mayo';
$string['june'] = 'Junio';
$string['july'] = 'Julio';
$string['august'] = 'Agosto';
$string['september'] = 'Septiembre';
$string['october'] = 'Octubre';
$string['november'] = 'Noviembre';
$string['december'] = 'Diciembre';
$string['year'] = 'Año';
$string['month'] = 'Mes';
$string['day'] = 'Día';
$string['hour'] = 'Hora';
$string['minutes'] = 'Minutos';
$string['seconds'] = 'Segundos';

// Exceptions.
$string['timecreated_criterion_is_empty'] = 'El criterio fecha de creación no puede estar vacio.';
$string['timemodified_criterion_is_empty'] = 'El criterio fecha de modificación no puede estar vacio.';
$string['limit_query_to_enqueue_courses_is_empty'] = 'El criterio limite de la consulta SQL para encolar cursos no puede estar vacio.';

// Notifier.
$string['message_to_send'] = 'El módulo de eliminación de cursos UV ha detectado que aún quedan cursos pendientes por eliminar. \n';
$string['message_to_send'] .= 'Resumen de la ejecución: \n';
$string['message_to_send'] .= '<pre>';
$string['message_to_send'] .= '- Cantidad de cursos eliminados: {$a->deletedcourses}';
$string['message_to_send'] .= '- Cantidad de cursos pendientes a eliminar: {$a->pendingcourses}';
$string['message_to_send'] .= '</pre>';
$string['message_to_send'] .= 'Este mensaje ha sido generado automáticamente, <b>por favor no responda</b> a este mensaje.';
$string['notification_subject'] = 'Notificación Campus Virtual: Cursos pendientes por eliminar';

// Moodle exceptions.
$string['invalid_input_datetimetype'] = 'Opción ingresada: {$a}. Opciones válidas: monthsoftheyear, daysofthemonth, hoursinaday o minutesinanhour';
$string['invalid_return_format'] = 'Cliente CVH: formato de retorno invalido.';
$string['empty_ws_url'] = 'Cliente CVH: URL al servicio vacía.';
$string['empty_return_format'] = 'Cliente CVH: formato de retorno vacío.';
$string['empty_ws_user_token'] = 'Cliente CVH: llave privada del usuario vacío.';
$string['clientcvh_invalid_parameters'] = 'Cliente CVH: el segundo parámetro del método request() debe ser un arreglo.';
$string['empty_ws_function_name'] = 'Cliente CVH: el nombre de la función del servicio web está vacío.';
$string['request_error'] = 'Cliente CVH: error en la petición.';
$string['request_method_invalid'] = 'Cliente CVH: método de la petición invalido.';

// Reports.
$string['reports_dashboard_heading'] = 'Panel de reportes';
$string['deletion_criteria_desc'] = 'Criterios actuales de eliminación de cursos';
$string['enqueued_courses_desc'] = 'Cursos actualmente encolados para eliminar';
$string['course_creation_date'] = 'Fecha de creación de cursos (DD/MM/AAAA HH:MM:SS)';
$string['course_last_modification_date'] = 'Fecha de última modificación de cursos (DD/MM/AAAA HH:MM:SS)';
$string['excluded_categories'] = 'Categorías de cursos excluidas';
$string['manually_enqueued_courses'] = 'Cursos encolados manualmente';
$string['automatically_enqueued_courses'] = 'Cursos encolados automaticamente';
$string['all_enqueued_courses'] = 'Todos los cursos encolados';
