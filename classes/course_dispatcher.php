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
