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
 * Course dispatcher class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/deleteoldcourses/locallib.php');

use moodle_exception;

/**
 * Course dispatcher class for Delete old courses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_dispatcher {

    protected int $timecreatedcriteria;
    protected int $timemodifiedcriteria;
    protected int $limitquery;
    protected array $categoriestoignore;

    /**
     * __construct.
     */
    public function __construct() {

        $this->timecreatedcriteria = date_config_to_timestamp('creation');
        $this->timemodifiedcriteria = date_config_to_timestamp('last_modification');
        $this->limitquery = get_config('local_deleteoldcourses', 'limit_query');
    }

    /**
     * Get courses to delete according to elimination criteria.
     *
     * @return array $courses
     * @since  Moodle 3.10
     */
    public function get_courses_to_delete() {

        global $DB;

        if (empty($this->timecreatedcriteria)) {
            throw new moodle_exception('timecreated_criteria_is_empty', 'local_deleteoldcourses');
        }

        if (empty($this->timemodifiedcriteria)) {
            throw new moodle_exception('timemodified_criteria_is_empty', 'local_deleteoldcourses');
        }

        if (empty($this->limitquery)) {
            throw new moodle_exception('limit_query_is_empty', 'local_deleteoldcourses');
        }

        // Admin user.
        $user = $DB->get_record('user', array('username' => 'desadmin'));
        $count = 0;
        $order = 'ASC';

        // Traer todos los cursos de la base datos cuya fecha de creacion sea menor al criterio.

        $conditions = 'timecreated <= ' . $this->timecreatedcriteria;
        $conditions .= ' AND timemodified <= ' . $this->timemodifiedcriteria;
        $conditions .= ' AND id > 1';
        $conditions .= ' AND id NOT IN (SELECT courseid FROM {deleteoldcourses})';

        $order = 'id ASC';

        $coursestoreview = $DB->get_records_select('course', $conditions, null, $order, '*', 0, $this->limitquery);

        return $coursestoreview;

        // list($sql, $params) = get_courses_sql($timecreated, $order);
        // $rs = $DB->get_recordset_sql($sql, $params, 0, $limit_query);
        // foreach ($rs as $row) {

        //     // Get first category parent of this course category.
        //     $first_category_parent = recursive_parent_category($row->category);
        //     // Exclude regular courses on categories with id < 30000.
        //     if ($row->category < 30000 && $first_category_parent == 6) {
        //         continue;
        //     }
        //     // Exclude Cursos Abiertos.
        //     if ($row->category == 109) {
        //         continue;
        //     }
        //     // Exclude Cursos de Extensión.
        //     // if ($first_category_parent == 7) { continue; }-->
        //     // Exclude Cursos Virtuales y Mixtos (blended).
        //     if ($first_category_parent == 110) {
        //         continue;
        //     }
        //     // Exclude Categoría DEMO.
        //     if ($row->category == 5) {
        //         continue;
        //     }
        //     // Exclude Cursos Capacitación.
        //     if ($row->category == 51) {
        //         continue;
        //     }
        //     // Exclude Medios Educativos-AMED.
        //     // if ($first_category_parent == 43) { continue; } -->
        //     // Exclude Formación Docente en Integración Pedagógica de las TIC.
        //     // if ($row->category == 89) { continue; } -->
        //     // Exclude Elecciones Electrónicas.
        //     // if ($row->category == 145) { continue; } -->
        //     // Exclude Cursos Permanentes.
        //     if ($row->category == 148) {
        //         continue;
        //     }

        //     // Exclude ases courses.
        //     if ($row->category == 81 || $row->category == 82
        //         || $row->category == 83 || $row->category == 146) {

        //         continue;
        //     }

        //     $course_updated = course_was_updated($row, $timemodified);
        //     $sections_updated = course_sections_was_updated($row, $timemodified);
        //     $modules_updated = course_modules_was_updated($row, $timemodified);
        //     $roles_updated = course_roles_was_updated($row, $timemodified);
        //     $user_enrolments = course_user_enrolments_was_updated($row, $timemodified);

        //     // If this course was updated after date.
        //     if ($course_updated || $sections_updated || $modules_updated || $roles_updated || $user_enrolments) {
        //         continue;
        //     }

        //     $count ++;

        //     // Show test queries - Confirm creation date.
        //     if ($test) {
        //         echo $count . ' - ' . $row->id . ' - ' . $row->fullname . ' - ' . userdate($row->timecreated) . '<br>';
        //         continue;
        //     }

        //     // Add course to queue for delete.
        //     $record = (object) array(
        //     'courseid'          => $row->id,
        //     'shortname'         => $row->shortname,
        //     'fullname'          => $row->fullname,
        //     'userid'            => $user->id,
        //     'size'              => course_calculate_size($row->id),
        //     'coursecreatedat'   => $row->timecreated,
        //     'timecreated'       => time()
        //     );
        //     // Add to deletion list.
        //     $DB->insert_record('deleteoldcourses', $record);

        //     // If is reach quantity break.
        //     if ($count >= $quantity) {
        //         break;
        //     }
        // }
        // $rs->close();

    }

    // Metodo que encole los cursos a borrar.

    /**
     * Get the value of timecreatedcriteria.
     */
    public function get_timecreated_criteria() {
        return $this->timecreatedcriteria;
    }

    /**
     * Get the value of timemodifiedcriteria.
     */
    public function get_timemodified_criteria() {
        return $this->timemodifiedcriteria;
    }

    /**
     * Get the value of limitquery.
     */
    public function get_limitquery() {
        return $this->limitquery;
    }

    /**
     * Get the value of categoriestoignore.
     */
    public function get_categories_to_ignore() {
        return $this->categoriestoignore;
    }

}
