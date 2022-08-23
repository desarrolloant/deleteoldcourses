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
 * Report manager class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     <camilo.mezu@correounivalle.edu.co>
 * @author     <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

class report_manager {

    /**
     * report_manager class constructor.
     */
    public function __construct() {
    }

    // LINE TO DELETE: Report #1. Criterios de eliminación actuales: fecha de creación, fecha de la última modificación, categorías excluidas.
    /**
     * Get current course deletion criteria settings: course creation date, course last modification date, and excluded categories.
     *
     * @return array course deletion criteria settings
     */
    public function get_course_deletion_criteria_settings(): array {
        global $DB;
        // TODO: get course deletion criterias from plugin settings.
        $creationdate = [
            'yearcreationdate'    => get_config('local_deleteoldcourses', 'year_creation_date'),
            'monthcreationdate'   => get_config('local_deleteoldcourses', 'month_creation_date'),
            'daycreationdate'     => get_config('local_deleteoldcourses', 'day_creation_date'),
            'hourcreationdate'    => get_config('local_deleteoldcourses', 'hour_creation_date'),
            'minutescreationdate' => get_config('local_deleteoldcourses', 'minutes_creation_date'),
            'secondscreationdate' => get_config('local_deleteoldcourses', 'seconds_creation_date')
        ];

        $lastmodificationdate = [
            'yearlastmodificationdate'    => get_config('local_deleteoldcourses', 'year_last_modification_date'),
            'monthlastmodificationdate'   => get_config('local_deleteoldcourses', 'month_last_modification_date'),
            'daylastmodificationdate'     => get_config('local_deleteoldcourses', 'day_last_modification_date'),
            'hourlastmodificationdate'    => get_config('local_deleteoldcourses', 'hour_last_modification_date'),
            'minuteslastmodificationdate' => get_config('local_deleteoldcourses', 'minutes_last_modification_date'),
            'secondslastmodificationdate' => get_config('local_deleteoldcourses', 'seconds_last_modification_date')
        ];

        $numberofcategoriestoexclude = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $categoriestoexclude = [];
        for ($i = 1; $i <= $numberofcategoriestoexclude; $i++) {
            $categorynumber = get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i);
            $categorydata = $DB->get_record('course_categories', ['id' => $categorynumber], 'id, name');
            $categoriestoexclude[$categorydata->id] = $categorydata->name;
        }

        $deletioncriteriasettings = [
            'creationdate'         => $creationdate,
            'lastmodificationdate' => $lastmodificationdate,
            'categoriestoexclude'  => $categoriestoexclude
        ];

        return $deletioncriteriasettings;
    }

    // LINE TO DELETE: Report #2. Cantidad de cursos encolados por profesores.
    /**
     * Get total number of enqueued courses by teachers.
     *
     * @return int number of courses
     */
    public function get_total_enqueued_courses_by_teachers(): int {
        // TODO: count courses from todelete table filtering those not enqueued by the admin.
        global $DB;
        $adminid = 2;
        return $DB->count_records_select('local_delcoursesuv_todelete', 'id != ?', [$adminid]);
    }

    // LINE TO DELETE: Report #3. Cantidad de cursos encolados automáticamente.
    /**
     * Get total number of enqueued courses automatically.
     *
     * @return int number of courses
     */
    public function get_total_enqueued_courses_automatically(): int {
        // TODO: count courses from todelete table filtering those enqueued by the admin.
        global $DB;
        $adminid = 2;
        return $DB->count_records('local_delcoursesuv_todelete', ['userid' => $adminid]);
    }

    // LINE TO DELETE: Report #4. Total de cursos eliminados en un periodo de tiempo.
    /**
     * Get total number of deleted courses during a time period.
     *
     * @param string $startdate
     * @param string $enddate
     * @return int number of courses
     */
    public function get_total_deleted_courses_during_time_period($startdate, $enddate): int {
        // TODO: count deleted courses from deleted table filtering by a time period.
        global $DB;
        return $DB->count_records_select("local_delcoursesuv_deleted", 'timecreated >= ? AND timecreated <= ?', [$startdate, $enddate]);
    }

    // LINE TO DELETE: Report #5. Total de cursos pendientes de eliminación.
    /**
     * Get total number of enqueued courses.
     *
     * @return int number of courses
     */
    public function get_total_enqueued_courses(): int {
        // TODO: count enqueued courses from todelete table.
        global $DB;
        return $DB->count_records('local_delcoursesuv_todelete');
    }

    // LINE TO DELETE: Report #6. Cantidad de cursos por facultad o unidad académica a eliminar (opcional)
    /**
     * Get total enqueued courses grouped by faculty.
     *
     * @return array enqueued courses grouped by faculty
     */
    public function get_total_enqueued_courses_by_faculty() {
        // TODO: count enqueued courses from todelete table grouping them by faculty.
        // {..todelete} x {course} x {course_category} --> Group by faculty + Aggregate by courseid
        return;
    }
}
