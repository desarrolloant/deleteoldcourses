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
 * @author     Camilo J. Mezú Mina <camilo.mezu@correounivalle.edu.co>
 * @author     Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

class report_manager {

    /**
     * Get current course deletion criteria settings: course creation date, course last modification date, and excluded categories.
     *
     * @return array course deletion criteria settings
     * E.g.
     *      Array(
     *          'creationdate'         => '30/06/2020 23:59:59',
     *          'lastmodificationdate' => '30/06/2021 23:59:59',
     *          'excludedcategories'   => "Excluded category name 1, Excluded category name 2'
     *      );
     */
    public function get_course_deletion_criteria_settings(): array {

        global $DB;

        // Date format: "DD/MM/YYYY HH:MM:SS".
        $creationdate = get_config('local_deleteoldcourses', 'day_creation_date') . '/' .
                        get_config('local_deleteoldcourses', 'month_creation_date') . '/' .
                        get_config('local_deleteoldcourses', 'year_creation_date') . ' ' .
                        get_config('local_deleteoldcourses', 'hour_creation_date') . ':' .
                        get_config('local_deleteoldcourses', 'minutes_creation_date') . ':' .
                        get_config('local_deleteoldcourses', 'seconds_creation_date');

        $lastmodificationdate = get_config('local_deleteoldcourses', 'day_last_modification_date') . '/' .
                                get_config('local_deleteoldcourses', 'month_last_modification_date') . '/' .
                                get_config('local_deleteoldcourses', 'year_last_modification_date') . ' ' .
                                get_config('local_deleteoldcourses', 'hour_last_modification_date') . ':' .
                                get_config('local_deleteoldcourses', 'minutes_last_modification_date') . ':' .
                                get_config('local_deleteoldcourses', 'seconds_last_modification_date');

        $numberofexcludedcategories = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $excludedcategories = [];

        for ($i = 1; $i <= $numberofexcludedcategories; $i++) {
            $categorynumber = get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i);
            $categoryname = $DB->get_record('course_categories', ['id' => $categorynumber], 'name')->name;
            array_push($excludedcategories, $categoryname);
        }

        $excludedcategories = implode(", ", $excludedcategories);

        $deletioncriteriasettings = [
            'creationdate'         => $creationdate,
            'lastmodificationdate' => $lastmodificationdate,
            'excludedcategories'   => $excludedcategories
        ];

        return $deletioncriteriasettings;
    }

    /**
     * Get total number of enqueued courses (optionally by how they were enqueued).
     *
     * @param bool $manuallyqueued true: manually, false: automatically, and null: all
     * @return int total number of enqueued courses
     */
    public function get_total_enqueued_courses($manuallyqueued = null): int {

        global $DB;

        if (is_bool($manuallyqueued)) {
            return $DB->count_records_select('local_delcoursesuv_todelete', 'manuallyqueued = ?', [(int)$manuallyqueued]);
        }

        return $DB->count_records('local_delcoursesuv_todelete');
    }

    /**
     * Get total number of deleted courses during a time period.
     *
     * @param int $startdate UNIX time format
     * @param int $enddate UNIX time format
     * @return int total number of deleted courses
     */
    public function get_total_deleted_courses_during_time_period(int $startdate, int $enddate): int {

        global $DB;
        return $DB->count_records_select("local_delcoursesuv_deleted",
                                            'timecreated >= ? AND timecreated <= ?', [$startdate, $enddate]);
    }

    /**
     * Get total number of enqueued courses grouped by root categories.
     * - Only courses from "Cursos Presenciales" root category will be grouped by their subsequent subcategories (I.e. faculties).
     * - The other courses will be grouped by their root category. Thus, their subcategories will be ignored.
     *
     * @return array total enqueued courses grouped by subsequent subcategories of Cursos Presenciales root category and other root categories
     *  E.g.
     *       array(
     *           'cursos_presenciales_subcategories' => [
     *               'HUMANIDADES' => 3,
     *               'SALUD' => 5,
     *               'CIENCIAS' => 9
     *           ],
     *           'other_categories' => [
     *              'Cursos de Extensión' => 10,
     *              'Cursos Capacitación' => 5
     *           ],
     *       );
     */
    public function get_total_enqueued_courses_grouped_by_root_categories(): array {

        global $DB;

        $enqueuedcourses = $DB->get_records('local_delcoursesuv_todelete', null, '', 'courseid');
        $cursospresencialesrootcategoryid = $DB->get_record('course_categories', array('idnumber' => 'regular_courses'), 'id')->id;
        $cursospresencialessubcategoriesresult = ['cursos_presenciales_subcategories' => []];
        $othercategoriesresult = ['other_categories' => []];

        foreach ($enqueuedcourses as $course) {
            $coursecategoryid = $DB->get_record('course', array('id' => $course->courseid), 'category')->category;
            // Course path e.g. "/6/30006/141" --> "/Cursos Presenciales/SALUD/PSIQUIATRIA".
            $coursecategorypath = $DB->get_record('course_categories', array('id' => $coursecategoryid), 'path')->path;
            // E.g. "/6/30006/141" --> [6, 30006, 141].
            $coursecategoriesbyids = explode('/', substr($coursecategorypath, 1));

            if ($coursecategoriesbyids[0] == $cursospresencialesrootcategoryid) {
                // If the course is in the Cursos Presenciales root category then use its subcategory (I.e. faculty).
                $cursospresencialessubcategoriesresult['cursos_presenciales_subcategories'] = $this->increment_number_of_courses_in_a_category(
                                            $coursecategoriesbyids[1], $cursospresencialessubcategoriesresult['cursos_presenciales_subcategories']);
            } else {
                // If not then use the root category and ignores the rest.
                $othercategoriesresult['other_categories'] = $this->increment_number_of_courses_in_a_category($coursecategoriesbyids[0],
                                                                                        $othercategoriesresult['other_categories']);
            }
        }

        $result = array_merge($cursospresencialessubcategoriesresult, $othercategoriesresult);

        return $result;
    }

    /**
     * Allows to increment the number of courses found in a particular category.
     *
     * @param int $coursecategoryid
     * @param array $partialresult result that is being recomputed
     * @return array partial result of enqueued courses grouped by root and faculty categories
     */
    private function increment_number_of_courses_in_a_category(int $coursecategoryid, array $partialresult): array {

        global $DB;
        $coursecategoryname = $DB->get_record('course_categories', array('id' => $coursecategoryid), 'name')->name;

        if (!isset($partialresult[$coursecategoryname])) {
            $partialresult[$coursecategoryname] = 1;
            return $partialresult;
        }

        $partialresult[$coursecategoryname] += 1;
        return $partialresult;
    }
}
