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
 * Course enqueuer class.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use DateTime;

/**
 * Course enqueuer class for Delete old courses.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 */
class course_enqueuer {

    /** @var int  Time creation criteria for course elimination */
    protected $timecreationcriteria;

    /** @var int  Time creation  modification for course elimination */
    protected $timemodificationcriteria;

    /** @var int  Limit for query to get courses */
    protected $limitquerytoenqueuecourses;

    /** @var array  Course categories to exclude */
    protected $categoriestoexclude;

    /**
     * __construct.
     */
    public function __construct() {

        $datemanager = new datetime_manager;

        $this->timecreationcriteria = $datemanager->date_config_to_timestamp('creation');
        $this->timemodificationcriteria = $datemanager->date_config_to_timestamp('last_modification');
        $this->limitquerytoenqueuecourses = get_config('local_deleteoldcourses', 'limit_query_to_enqueue_courses');
    }

    /**
     * Get courses to enqueue according to elimination criteria.
     *
     * @param   int   $courseidinit                   Course ID for init course to check query.
     * @param   int   $timecreatedcriteria            Timestamp for creation date criteria.
     * @param   int   $timemodificationcriteria       Timestamp for modification date criteria.
     * @param   int   $limitquerytoenqueuecourses     Limit for SQL Query.
     * @param   array $categoriesexcluded             Array with course categories excluded.
     * @param   int   $coursesexcludedbycategory      Counter for courses excluded by course categories criteria.
     * @param   int   $coursesexcludednewsections     Counter for courses excluded by "new sections" criteria.
     * @param   int   $coursesexcludednewparticipants Counter for courses excluded by "new participants" criteria.
     * @param   int   $coursesexcludednewmodules      Counter for courses excluded by "new modules" criteria.
     * @param   int   $coursesexcludedcvh             Counter for courses excluded by "exists in CVH" criteria.
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function get_courses_to_enqueue(int $courseidinit = 0,
                                           int $timecreatedcriteria,
                                           int $timemodificationcriteria,
                                           int $limitquerytoenqueuecourses,
                                           array $categoriesexcluded,
                                           int &$coursesexcludedbycategory = 0,
                                           int &$coursesexcludednewsections = 0,
                                           int &$coursesexcludednewparticipants = 0,
                                           int &$coursesexcludednewmodules = 0,
                                           int &$coursesexcludedcvh = 0) {

        global $DB, $USER;

        $coursestocheck = array();

        $sqlquery = "SELECT id, shortname, idnumber, fullname, timecreated, category
                    FROM {course}
                    WHERE timecreated <= ?
                        AND timemodified <= ?
                        AND id <> 1
                        AND id NOT IN (SELECT DISTINCT courseid
                                        FROM {local_delcoursesuv_todelete})
                        AND id > ?
                    ORDER BY id ASC
                    LIMIT ?";

        $coursestocheck = $DB->get_records_sql($sqlquery, array($timecreatedcriteria, $timemodificationcriteria,
                                                                 $courseidinit, $limitquerytoenqueuecourses));

        // Update courseidinit.
        if (empty($coursestocheck)) {
            mtrace("\n" . get_string('number_courses_excluded_by_categories' , 'local_deleteoldcourses', $coursesexcludedbycategory));
            mtrace(get_string('number_courses_excluded_by_new_sections', 'local_deleteoldcourses', $coursesexcludednewsections));
            mtrace(get_string('number_courses_excluded_by_new_participants',
                              'local_deleteoldcourses', $coursesexcludednewparticipants));
            mtrace(get_string('number_courses_excluded_by_new_modules', 'local_deleteoldcourses',
                              $coursesexcludednewmodules));
            mtrace(get_string('number_courses_excluded_by_cvh', 'local_deleteoldcourses', $coursesexcludedcvh));
            return 1;
        }

        $courseidinit = end($coursestocheck)->id;

        $cvhwsclient = new cvh_ws_client();
        $wsfunctionname = get_config('local_deleteoldcourses', 'ws_function_name');

        foreach ($coursestocheck as $key => $course) {

            // Check course category.
            if ($this->check_excluded_course_categories($course->id, $categoriesexcluded)) {
                mtrace(get_string('course_excluded_by_categories', 'local_deleteoldcourses',
                                  array('shortname' => $course->shortname, 'coursecategory' => $course->category)));
                $coursesexcludedbycategory += 1;
                unset($coursestocheck[$key]);
            };

            // Check if the course have new sections.
            $havenewsections = $this->have_new_sections($course->id, $timemodificationcriteria);

            if ($havenewsections) {
                mtrace(get_string('course_excluded_by_new_sections', 'local_deleteoldcourses',
                                  array('shortname' => $course->shortname, 'coursecategory' => $course->category)));
                $coursesexcludednewsections += 1;
                unset($coursestocheck[$key]);
            }

            // Check if the course have new participants.
            $havenewparticipants = $this->have_new_participants($course->id, $timemodificationcriteria);

            if ($havenewparticipants) {
                mtrace(get_string('course_excluded_by_new_participants', 'local_deleteoldcourses',
                                  array('shortname' => $course->shortname, 'coursecategory' => $course->category)));
                $coursesexcludednewparticipants += 1;
                unset($coursestocheck[$key]);
            }

            // Check if the course have new modules.
            $havenewmodules = $this->have_new_modules($course->id, $timemodificationcriteria);

            if ($havenewmodules) {
                mtrace(get_string('course_excluded_by_new_modules', 'local_deleteoldcourses',
                                  array('shortname' => $course->shortname, 'coursecategory' => $course->category)));
                $coursesexcludednewmodules += 1;
                unset($coursestocheck[$key]);
            }

            // Check if the course exists in Campus Virtual Historia.

            $parameterstorequest = array('idnumber' => $course->idnumber);

            $idnumberresponse = $cvhwsclient->request_to_service($wsfunctionname, $parameterstorequest);
            $idnumberresponse = json_decode($idnumberresponse);

            if (empty($idnumberresponse->courses)) {

                $parameterstorequest = array('shortname' => $course->shortname);

                $shortnameresponse = $cvhwsclient->request_to_service($wsfunctionname, $parameterstorequest);
                $shortnameresponse = json_decode($shortnameresponse);

                if (empty($shortnameresponse->courses)) {

                    mtrace(get_string('course_excluded_by_cvh', 'local_deleteoldcourses',
                                  array('shortname' => $course->shortname, 'coursecategory' => $course->category)));
                    $coursesexcludedcvh += 1;
                    unset($coursestocheck[$key]);
                }
            }
        }

        // Insert courses into deleteoldcourses table.
        $this->enqueue_courses_to_delete($coursestocheck, $USER->id, 0);

        $this->get_courses_to_enqueue($courseidinit, $timecreatedcriteria, $timemodificationcriteria, $limitquerytoenqueuecourses,
                                      $categoriesexcluded, $coursesexcludedbycategory, $coursesexcludednewsections,
                                      $coursesexcludednewparticipants, $coursesexcludednewmodules, $coursesexcludedcvh);
    }

    /**
     * Get the value of timecreationcriteria.
     *
     * @return  int $timecreationcriteria
     */
    public function get_timecreation_criteria():int {
        return $this->timecreationcriteria;
    }

    /**
     * Get the value of timemodifiedcriteria.
     *
     * @return  int $timemodificationcriteria
     */
    public function get_timemodification_criteria():int {
        return $this->timemodificationcriteria;
    }

    /**
     * Get the value of limitquerytoenqueuecourses.
     *
     * @return  int $limitquerytoenqueuecourses
     */
    public function get_limit_query_to_enqueue_courses() {
        return $this->limitquerytoenqueuecourses;
    }

    /**
     * Get the value of categoriestoexclude.
     *
     * @return  array Array with categories to exclude.
     */
    public function get_categories_to_exclude():array {
        return $this->categoriestoexclude;
    }

    /**
     * set_categoriestoexclude
     */
    public function set_categoriestoexclude():void {

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
     * @param   int  $courseid
     * @param   int  $timemodified
     * @return  bool $havenewsections
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_sections(int $courseid, int $timemodified):bool {
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
     * @param   int  $courseid
     * @param   int  $timemodified
     * @return  bool $havenewparticipants
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_participants(int $courseid, int $timemodified):bool {
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
     * @param   int  $courseid
     * @param   int  $timemodified
     * @return  bool $havenewmodules
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function have_new_modules(int $courseid, int $timemodified):bool {
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
     * @param   int  $courseid Course ID to check
     * @param   array  $coursecategoriesexcluded Course categories excluded array
     * @return  bool $belongtocategory
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function check_excluded_course_categories(int $courseid, array $coursecategoriesexcluded):bool {
        global $DB;

        $categoryid = $DB->get_record('course', array('id' => $courseid), 'category')->category;
        $categorypath = $DB->get_record('course_categories', array('id' => $categoryid), 'path')->path;

        $coursecategoriespath = explode('/', substr($categorypath, 1));

        $coursecategoriesintersection = array_intersect($coursecategoriesexcluded, $coursecategoriespath);

        return !empty($coursecategoriesintersection);
    }

    /**
     * Function that insert the courses to delete in courses_to_delete table.
     *
     * @param   array $courses Array containing the courses to delete.
     * @param   int $userid ID of the user who queued the course.
     * @param   bool $manuallyqueued Indicates if the register was made manually or automatically.
     *                              0 for automatically enqueue or 1 for manual enqueue.
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function enqueue_courses_to_delete($courses, $userid, $manuallyqueued = 1):void {
        global $DB;

        $date = new DateTime();
        $utils = new utils();

        foreach ($courses as $course) {

            $record = new stdClass();
            $record->courseid = $course->id;
            $record->userid = $userid;
            $record->coursesize = $utils->calculate_course_size($course->id);
            $record->timecreated = $date->getTimestamp();
            $record->manuallyqueued = $manuallyqueued;

            if ($course) {
                $DB->insert_record('local_delcoursesuv_todelete', $record);
            }
        }
    }
}
