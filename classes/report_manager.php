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
     *              'creationdate' => [
     *                                  'yearcreationdate' => 2010,
     *                                  'monthcreationdate' => 12,
     *                                  'daycreationdate' => 31,
     *                                  'hourcreationdate' => 23,
     *                                  'minutescreationdate' => 59,
     *                                  'secondscreationdate => 59,
     *              ],
     *              'lastmodificationdate' => [
     *                                  'yearlastmodificationdate' => 2012,
     *                                  'monthlastmodificationdate' => 12,
     *                                  'daylastmodificationdate' => 31,
     *                                  'hourlastmodificationdate' => 23,
     *                                  'minuteslastmodificationdate' => 59,
     *                                  'secondslastmodificationdate' => 59
     *              ],
     *              'excludedcategories' => [
     *                                  '189000' => 'Excluded category 1',
     *                                  '189001' => 'Excluded category 2',
     *                                  '189002' => 'Excluded category 3',
     *              ]
     *      );
     */
    public function get_course_deletion_criteria_settings(): array {

        global $DB;

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

        $numberofexcludedcategories = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $excludedcategories = [];

        for ($i = 1; $i <= $numberofexcludedcategories; $i++) {
            $categorynumber = get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i);
            $categorydata = $DB->get_record('course_categories', ['id' => $categorynumber], 'id, name');
            $excludedcategories[$categorydata->id] = $categorydata->name;
        }

        $deletioncriteriasettings = [
            'creationdate'         => $creationdate,
            'lastmodificationdate' => $lastmodificationdate,
            'excludedcategories'   => $excludedcategories
        ];

        return $deletioncriteriasettings;
    }

    /**
     * Get total number of enqueued courses.
     *
     * @param string $bywhom optional parameter for 'teachers' or 'admin', all by default
     * @return int number of courses
     */
    public function get_total_enqueued_courses($bywhom = null): int {

        global $DB;

        if ($bywhom == 'teachers') {
            return $DB->count_records_select('local_delcoursesuv_todelete', 'userid != ?', [2]);
        }

        if ($bywhom == 'admin') {
            return $DB->count_records_select('local_delcoursesuv_todelete', 'userid = ?', [2]);
        }

        return $DB->count_records('local_delcoursesuv_todelete');
    }

    /**
     * Get total number of deleted courses during a time period.
     *
     * @param string $startdate
     * @param string $enddate
     * @return int number of courses
     */
    public function get_total_deleted_courses_during_time_period($startdate, $enddate): int {

        global $DB;
        return $DB->count_records_select("local_delcoursesuv_deleted",
                                            'timecreated >= ? AND timecreated <= ?', [$startdate, $enddate]);
    }

    /**
     * Get total enqueued courses grouped by faculty and in descending sorted.
     *
     * @return array enqueued courses grouped by faculty
     *  E.g.
     *      array(
     *              [
     *                  'category' => 'Category name 5',
     *                  'courses' => 6,
     *              ],
     *              [
     *                  'category' => 'Category name 2',
     *                  'courses' => 3,
     *              ],
     *              [
     *                  'category' => 'Category name 3',
     *                  'courses' => 1,
     *              ]
     *      );
     */
    public function get_total_enqueued_courses_grouped_by_faculty() {

        global $DB;

        $sqlquery = "SELECT cc.name category, COUNT(ldt.id) courses
                FROM {local_delcoursesuv_todelete} ldt
                INNER JOIN {course} c on ldt.courseid = c.id
                INNER JOIN {course_categories} cc on c.category = cc.id
                GROUP BY cc.name
                ORDER BY COUNT(ldt.id) DESC";

        $result = $DB->get_records_sql($sqlquery);
        $result = array_values($result);

        return $result;
    }
}
