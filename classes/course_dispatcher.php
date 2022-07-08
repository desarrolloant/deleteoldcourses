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

    protected int $timecreationcriteria;
    protected int $timemodificationcriteria;
    protected int $limitquery;
    protected array $categoriestoexclude;

    /**
     * __construct.
     */
    public function __construct() {

        $datemanager = new datetime_manager;

        $this->timecreationcriteria = $datemanager->date_config_to_timestamp('creation');
        $this->timemodificationcriteria = $datemanager->date_config_to_timestamp('last_modification');
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

        $datetimemanager = new datetime_manager();
        $timecreatedcriteria = $datetimemanager->date_config_to_timestamp('creation');
        $timemodificationcriteria = $datetimemanager->date_config_to_timestamp('last_modification');

        $coursestodelete = array();

        $sqlquery = "SELECT *
                     FROM {course}
                     WHERE timecreated <= ?
                        AND timemodified >= ?
                        AND id <> 1";

        $coursestodelete = $DB->get_records_sql($sqlquery, array($timecreatedcriteria, $timemodificationcriteria));

        return $coursestodelete;
    }

    /**
     * Get the value of timecreationcriteria.
     *
     * @return int $timecreationcriteria
     * @since  Moodle 3.10
     */
    public function get_timecreation_criteria() {
        return $this->timecreationcriteria;
    }

    /**
     * Get the value of timemodifiedcriteria.
     *
     * @return int $timemodificationcriteria
     * @since  Moodle 3.10
     */
    public function get_timemodification_criteria() {
        return $this->timemodificationcriteria;
    }

    /**
     * Get the value of limitquery.
     *
     * @return int $limitquery
     * @since  Moodle 3.10
     */
    public function get_limitquery() {
        return $this->limitquery;
    }

    /**
     * set_categoriestoexclude
     *
     * @return array $categoriestoexclude
     * @since  Moodle 3.10
     */
    public function set_categoriestoexclude() {

        $categoriestoexclude = array();
        $numbercategoriestoexclude = intval(get_config('local_deleteoldcourses', 'number_of_categories_to_exclude'));

        for ($i = 1; $i <= $numbercategoriestoexclude; $i++) {
            array_push($categoriestoexclude, get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i));
        }

        $this->categoriestoexclude = $categoriestoexclude;
    }

    /**
     * Get the value of categoriestoexclude.
     */
    public function get_categories_to_exclude() {
        return $this->categoriestoexclude;
    }
}
