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

use stdClass;
use DateTime;

/**
 * Course dispatcher class for Delete old courses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 */
class course_dispatcher {

    /** @var int  Time creation criteria for course elimination */
    protected $timecreationcriteria;

    /** @var int  Time creation  modification for course elimination */
    protected $timemodificationcriteria;

    /** @var int  Limit for query to get courses */
    protected $limitquery;

    /** @var array  Course categories to exclude */
    protected $categoriestoexclude;

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

        global $DB, $USER;

        $datetimemanager = new datetime_manager();
        $timecreatedcriteria = $datetimemanager->date_config_to_timestamp('creation');
        $timemodificationcriteria = $datetimemanager->date_config_to_timestamp('last_modification');

        $numbercategoriesexcluded = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $categoriesexcluded = array();

        for ($i = 1; $i < $numbercategoriesexcluded + 1; $i++) {
            array_push($categoriesexcluded, get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i));
        }

        $coursestodelete = array();

        $sqlquery = "SELECT id, shortname, fullname, timecreated, category
                     FROM {course}
                     WHERE timecreated <= ?
                        AND timemodified <= ?
                        AND id <> 1
                        AND id NOT IN (SELECT DISTINCT courseid
                                       FROM {deleteoldcourses})";

        $coursestodelete = $DB->get_records_sql($sqlquery, array($timecreatedcriteria, $timemodificationcriteria));

        if ($coursestodelete) {
            foreach ($coursestodelete as $key => $course) {

                // Check category.
                // TODO: #64 Check child categories.
                if (in_array($course->category, $categoriesexcluded)) {
                    unset($coursestodelete[$key]);
                };

                $havenewsections = $this->have_new_sections($course->id, $timemodificationcriteria);

                if ($havenewsections) {
                    unset($coursestodelete[$key]);
                }

                // TODO: #63 Check role change in courses.

                $havenewparticipants = $this->have_new_participants($course->id, $timemodificationcriteria);

                if ($havenewparticipants) {
                    unset($coursestodelete[$key]);
                }

                $havenewmodules = $this->have_new_modules($course->id, $timecreatedcriteria);

                if ($havenewmodules) {
                    unset($coursestodelete[$key]);
                }

                // TODO: Check if the course has been backed up.
            }
        }

        // Insert courses into deleteoldcourses table.
        $this->enqueue_courses_to_delete($coursestodelete, $USER->id);

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
     * Get the value of categoriestoexclude.
     *
     * @since  Moodle 3.10
     */
    public function get_categories_to_exclude() {
        return $this->categoriestoexclude;
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
     * Returns true if the course have new sections after a modification date.
     *
     * @param  int  $courseid
     * @param  int  $timemodified
     * @return bool $havenewsections
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_sections(int $courseid, int $timemodified) {
        global $DB;

        $havenewsections = false;

        $sqlquery = "SELECT COUNT(id)
                     FROM {course_sections} cs
                     WHERE cs.course = ?
                           AND cs.timemodified >= ?";

        $coursesections = $DB->count_records_sql($sqlquery, array($courseid, $timemodified));

        $havenewsections = ($coursesections) ? true : false;

        return $havenewsections;
    }

    /**
     * Returns true if the course have new students enrolled after a modification date.
     *
     * @param  int  $courseid
     * @param  int  $timemodified
     * @return bool $havenewparticipants
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_participants(int $courseid, int $timemodified) {
        global $DB;

        $havenewparticipants = false;

        $sqlquery = "SELECT COUNT(ue.id)
                     FROM {enrol} e
                          INNER JOIN {user_enrolments} ue ON e.id = ue.enrolid
                     WHERE e.courseid = ?
                           AND ue.timemodified >= ?";

        $enrolments = $DB->count_records_sql($sqlquery, array($courseid, $timemodified));

        $havenewparticipants = ($enrolments) ? true : false;

        return $havenewparticipants;
    }

    /**
     * Returns true if the course have new activity or resource modules after a modification date.
     *
     * @param  int  $courseid
     * @param  int  $timemodified
     * @return bool $havenewmodules
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_modules(int $courseid, int $timemodified) {
        global $DB;

        $havenewmodules = false;

        $sqlquery = "SELECT COUNT(cm.id)
                     FROM {course_modules} cm
                     WHERE cm.course = ?
                           AND cm.added >= ?";

        $modules = $DB->count_records_sql($sqlquery, array($courseid, $timemodified));

        $havenewmodules = ($modules) ? true : false;

        return $havenewmodules;
    }

    /**
     * Returns true if a course belongs to an exlcuded category.
     *
     * @param  int  $courseid
     * @param  array  $coursecategories
     * @return bool $belongtocategory
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function check_excluded_course_categories(int $courseid, array $coursecategories) {
        global $DB;

        $belongtocategory = false;

        $categoryid = $DB->get_record('course', array('id' => $courseid), 'category')->category;
        $categorypath = $DB->get_record('course_categories', array('id' => $categoryid), 'path')->path;

        $pathroot = explode('/', substr($categorypath, 1))[0];

        $belongtocategory = in_array($pathroot, $coursecategories);

        return $belongtocategory;
    }

    /**
     * Function that insert the courses to delete in courses_to_delete table.
     *
     * @param  array $courses Array containing the courses to delete.
     * @param  int $userid ID of the user who queued the course.
     * @return void
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function enqueue_courses_to_delete($courses, $userid) {
        global $DB;

        $date = new DateTime();
        $utils = new utils();

        foreach ($courses as $course) {

            $record = new stdClass();
            $record->courseid = $course->id;
            $record->userid = $userid;
            $record->coursesize = $utils->calculate_course_size($course->id);
            $record->timecreated = $date->getTimestamp();

            if ($course) {
                $DB->insert_record('local_delcoursesuv_todelete', $record);
            }
        }
    }
}
