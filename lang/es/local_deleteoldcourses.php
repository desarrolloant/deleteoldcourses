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
$string['course_shortname'] = 'Nombre Corto';
$string['course_fullname'] = 'Nombre Completo';
$string['course_datecreation'] = 'Creado Hace';
$string['table_option'] = 'Opción';
$string['coursescount'] = 'Número de cursos: ';

// Date filter.
$string['more_than_1_year_ago'] = 'Creados hace más de 1 año';
$string['more_than_n_years_ago'] = 'Creados hace más de {$a} años';

// Modal delete strings.
$string['modal_delete_title'] = 'Eliminar el curso';
$string['modal_delete_danger_body'] = '<strong>¡Atención!</strong> Este curso tiene otros profesores.';
$string['modal_delete_accept'] = '¿Esta Seguro de añadir el curso a la lista de cursos a eliminar?<br> Recuerde que los cursos serán eliminados a las 00:00 horas del siguiente día.';
$string['modal_delete_no_teacher'] = 'No eres profesor de este curso.';
$string['modal_delete_save_button'] = 'Si, eliminar';
$string['modal_delete_cancel_button'] = 'No, cancelar';
$string['modal_delete_close_button'] = 'Cerrar';

// Events strings.
$string['old_courses_list_viewed_name'] = 'Listado de cursos viejos visto';
$string['course_delete_options_viewed'] = 'Alerta para eliminar curso vista';
$string['course_sent_delete'] = 'Curso enviado para ser eliminado';
$string['course_remove_delete'] = 'Curso removido de la lista para eliminar';

// Tasks.
$string['task_delete_course'] = 'Tarea para eliminar cursos';
$string['enqueue_courses_task'] = 'Encolar cursos a eliminar';

// Deleted table.
$string['sent_to_delete'] = 'Enviado para eliminar';
$string['course_timedeleted'] = 'Fecha de eliminación';
$string['more_than_1_month_ago'] = 'Eliminados hace menos de 1 mes';
$string['more_than_n_months_ago'] = 'Eliminados hace menos de {$a} meses';
$string['deleted_courses'] = 'Cursos Eliminados';
$string['pending_courses'] = 'Cursos Pendientes';

// Alert in dashborad.
$string['alert_delete_content'] = 'Si usted desea eliminar alguno de sus cursos, por favor diríjase a la sección';
$string['delete_courses'] = 'Eliminar Cursos';
$string['alert_delete_recent_courses_content'] = 'Para eliminar los cursos creados hace menos de un año, por favor diligenciar el siguiente';
$string['alert_delete_recent_courses_link'] = 'formulario';

// Plugin settings.
$string['manage'] = 'Eliminación de Cursos Antiguos';
$string['courses'] = 'Eliminación de cursos antiguos';
$string['criteriatab'] = 'Criterios';
$string['criteriasettingsheading'] = 'Fechas para la eliminación de cursos';
$string['criteriasettingsheading_desc'] = 'Fecha desde la cual se seleccionan los cursos a eliminar';

$string['courses_creation_date_criteria_heading'] = 'Criterio: fecha de inicio de los cursos';
$string['courses_creation_date_criteria_heading_desc'] = 'Descripción del criterio fecha de inicio';
$string['year_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde el año 2005 en adelante.';
$string['month_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde el mes de Enero en adelante.';
$string['day_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde el día 1 del mes en adelante.';
$string['hour_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde la hora 00 del día en adelante.';
$string['minutes_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde el minuto 00 de la hora en adelante.';
$string['seconds_creation_date_desc'] = 'Ejemplo: se seleccionan cursos desde el segundo 00 del minuto en adelante.';

$string['courses_last_modification_date_criteria_heading'] = 'Criterio: fecha de última modificación de los cursos';
$string['courses_last_modification_date_criteria_heading_desc'] = 'Descripción del criterio última fecha de modificación';
$string['year_last_modification_date_desc'] = 'Ejemplo';

$string['course_categories_criteria_heading'] = 'Categorías de curso excluidas';
$string['course_categories_criteria_heading_desc'] = 'Categorías que no se tendrán en cuenta en el proceso de eliminación automática de cursos.';
$string['number_of_categories'] = 'Cantidad de categorías a excluir';
$string['number_of_categories_desc'] = 'Seleccione la cantidad de categorías a excluir.';
$string['excluded_course_categories'] = 'Categorías';
$string['excluded_course_categories_desc'] = 'Seleccione las categorias de curso';

// Advanced settings.
$string['advancedtab'] = 'Configuraciones avanzadas';
$string['advanced_settings_heading'] = 'Configuraciones avanzadas para la eliminación de cursos';
$string['advanced_settings_heading_desc'] = 'Estas configuraciones se deben modificar siempre y cuando esté seguro de lo que está haciendo';
$string['limit_query'] = 'Limite de la consulta';
$string['limit_query_desc'] = 'Ejemplo: Se consultan 5000 cursos para procesarlos y decidir si se añaden o no a la cola de eliminación.';
$string['course_queue_size'] = 'Tamaño de la cola de cursos a eliminar';
$string['course_queue_size_desc'] = 'Ejemplo: los cursos son eliminados en una cola de tamaño máximo de 500 cursos por ejecución.';

// Notification settings.
$string['notification_settings_tab'] = 'Notificaciones';
$string['notification_settings_heading'] = 'Configuraciones para notificaciones';
$string['notification_settings_heading_desc'] = 'Configuraciones para notificaciones sobre eliminación de cursos.';
$string['users_to_notify'] = 'Usuarios a notificar';
$string['users_to_notify_desc'] = 'Ingrese, separados por comas, los nombres de usuario (username) de los usuarios a notificar.';

// Client settings.
$string['client_settings_tab'] = 'Cliente';
$string['client_settings_heading'] = 'Cliente para servicio en Campus Virtual Historia';
$string['client_settings_heading_desc'] = 'Parámetros del cliente para el servicio en Campus Virtual Historia';
$string['url_to_service'] = 'URL del Campus Virtual Historia';
$string['url_to_service_desc'] = 'URL a los servicios en Campus Virtual Historia';
$string['function_name'] = 'Nombre de la función';
$string['function_name_desc'] = 'Nombre de la función utilizada por el servicio';
$string['client_course_field'] = 'Campo de la tabla cursos';
$string['client_course_field_desc'] = 'Campo utilizado para filtrar los cursos en Campus Virtual Historia';
$string['courseid'] = 'Identificador del curso';
$string['course_shortname'] = 'Nombre corto del curso';
$string['user_token'] = 'Clave privada del usuario autorizado';
$string['user_token_desc'] = 'Se puede recuperar de la página de claves privadas del usuario';

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
$string['timecreated_criteria_is_empty'] = 'El criterio fecha de creación no puede estar vacio.';
$string['timemodified_criteria_is_empty'] = 'El criterio fecha de modificación no puede estar vacio.';
$string['limit_query_is_empty'] = 'El limite de la consulta no puede estar vacio.';

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
$string['invalid_input_datetimetype'] = 'Entered input: {$a}. Valid inputs: monthsoftheyear, daysofthemonth, hoursinaday or minutesinanhour';
$string['invalid_return_format'] = 'Cliente CVH: Formato de retorno invalido.';
$string['empty_url_to_service'] = 'Cliente CVH: URL al servicio vacía.';
$string['empty_return_format'] = 'Cliente CVH: Formato de retorno vacío.';
$string['empty_user_token'] = 'Cliente CVH: Token de usuario vacío.';
$string['clientcvh_invalid_parameters'] = 'Cliente CVH: El segundo parámetro del método request() debe ser un arreglo.';
$string['empty_function_name'] = 'Cliente CVH: El nombre de la función está vacío.';
$string['request_error'] = 'Cliente CVH: Error en la petición.';
$string['request_method_invalid'] = 'Cliente CVH: Método de la petición invalido.';
