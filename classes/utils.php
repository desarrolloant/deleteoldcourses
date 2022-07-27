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
 * Utils class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

/**
 * Utils class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils {

    /**
     * Get courses to delete according to elimination criteria.
     *
     * @param  int $courseid ID of the course to calculate size
     * @return array $courses
     * @author Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @since  Moodle 3.10
     */
    public function calculate_course_size($courseid) {
        global $DB;

        $result = 0;

        $params = [];
        $params['courseid'] = $courseid;
        $params['context'] = CONTEXT_COURSE;

        $sql = "SELECT sum(f.filesize) AS size
                FROM {files} f
                INNER JOIN {context} x ON (f.contextid = x.id AND x.contextlevel = :context)
                INNER JOIN {course} c ON (c.id = x.instanceid AND c.id = :courseid)";

        if ($query = $DB->get_record_sql($sql, $params)) {
            if ($query->size != null) {
                $result = $query->size;
            }
        }

        return intval($result);
    }
}
