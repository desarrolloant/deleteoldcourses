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
$string['manage'] = 'Configuración del plugin Eliminación de Cursos Antiguos';
$string['courses'] = 'Eliminación de cursos antiguos';

// Date settings.
$string['criteriasettingsheading'] = 'Criterios de fecha para la eliminación de cursos';
$string['criteriasettingsheading_desc'] = 'Fecha desde la cual se seleccionan los cursos a eliminar';
$string['yearstartdate'] = 'Año';
$string['yearstartdate_desc'] = 'Ejemplo: se seleccionan cursos desde el año 2005 en adelante.';
$string['monthstartdate'] = 'Mes';
$string['monthstartdate_desc'] = 'Ejemplo: se seleccionan cursos desde el mes de Enero en adelante.';
$string['daystartdate'] = 'Día';
$string['daystartdate_desc'] = 'Ejemplo: se seleccionan cursos desde el día 1 del mes en adelante.';
$string['hourstartdate'] = 'Hora';
$string['hourstartdate_desc'] = 'Ejemplo: se seleccionan cursos desde la hora 00 del día en adelante.';
$string['minutesstartdate'] = 'Minutos';
$string['minutesstartdate_desc'] = 'Ejemplo: se seleccionan cursos desde el minuto 00 de la hora en adelante.';
$string['secondsstartdate'] = 'Segundos';
$string['secondsstartdate_desc'] = 'Ejemplo: se seleccionan cursos desde el segundo 00 del minuto en adelante.';

// Multiple parameter settings.
$string['parameterssettingsheading'] = 'Parametros del proceso de eliminación de cursos';
$string['parameterssettingsheading_desc'] = 'Configuración de múltiples parametros para filtrar los cursos a eliminar';
$string['coursequeuesize'] = 'Tamaño de la cola de cursos a eliminar';
$string['coursequeuesize_desc'] = 'Ejemplo: los cursos son eliminados en una cola de tamaño máximo de 500 cursos por ejecución.';
