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
 * Course deleter class.
 *
 * @package    local_deleteoldcourses
 * @author     Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

/**
 * Course deleter class.
 *
 * @package    local_deleteoldcourses
 * @author     Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_deleter {

    /** @var int setting to set how many courses to delete per task */
    private $taskqueuesize;

    /**
     * course_deleter class constructor.
     */
    public function __construct() {
        $this->taskqueuesize = get_config('local_deleteoldcourses', 'task_queue_size');
    }

    /**
     * Delete enqueued courses.
     *
     * @throws \Throwable if the course cannot be deleted
     */
    public function delete_courses() {
        global $DB;

        $rstotalcoursestodel = $this->get_courses_to_delete();

        $lockfactory = \core\lock\lock_config::get_lock_factory('local_deleteoldcourses_course_deleter');

        foreach ($rstotalcoursestodel as $coursetodel) {

            $lockkey = "course{$coursetodel->courseid}";
            $lock = $lockfactory->get_lock($lockkey, 0);

            // Guard against multiple workers in cron.
            if ($lock !== false) {

                try {
                    // Getting extra data to validate and store historical data of a deleted course.
                    $coursetodeldextradata = $DB->get_record('course', array('id' => $coursetodel->courseid),
                                                            'shortname, fullname, timecreated');
                    $userextradata = $DB->get_record('user', array('id' => $coursetodel->userid),
                                                'username, firstname, lastname, email');

                    if (!empty($coursetodeldextradata->shortname) && !empty($userextradata->username)) {
                        if (delete_course($coursetodel->courseid, false)) {
                            $this->store_deleted_course_data($coursetodel, $coursetodeldextradata, $userextradata);
                            $DB->delete_records('local_delcoursesuv_todelete', array('id' => $coursetodel->id));
                            fix_course_sortorder();
                        }
                    }
                } catch (\Throwable $e) {
                    mtrace("Exception error when deleting course with id {$coursetodel->courseid}.");
                }
            }
            $lock->release();
        }
        $rstotalcoursestodel->close();
    }

    /**
     * Get courses to delete.
     *
     * @return moodle_recordset a moodle_recordset instance with all courses to delete
     */
    private function get_courses_to_delete() {
        global $DB;
        $sqlparams = [];
        $sqlparams['task_queue_size'] = $this->taskqueuesize;
        $sqlcoursestodel = "SELECT id, courseid, userid, coursesize, timecreated
                            FROM {local_delcoursesuv_todelete}
                            ORDER BY id ASC
                            LIMIT :task_queue_size";

        $rstotalcoursestodel = $DB->get_recordset_sql($sqlcoursestodel, $sqlparams);
        return $rstotalcoursestodel;
    }

    /**
     * Store historical data of a deleted course.
     *
     * @param object $coursetodel 'courseid', 'coursesize', 'timecreated', and 'userid'
     * @param object $coursetodeldextradata 'shortname', 'fullname', and 'timecreated'
     * @param object $userextradata 'username', 'firstname', 'lastname', and 'email'
     */
    private function store_deleted_course_data($coursetodel, $coursetodeldextradata, $userextradata) {
        global $DB;
        $deletedcourserecord = (object) array(
            'courseid'               => $coursetodel->courseid,
            'courseshortname'        => $coursetodeldextradata->shortname,
            'coursefullname'         => $coursetodeldextradata->fullname,
            'coursesize'             => $coursetodel->coursesize,
            'coursetimecreated'      => $coursetodeldextradata->timecreated,
            'coursetimesenttodelete' => $coursetodel->timecreated,
            'userid'                 => $coursetodel->userid,
            'username'               => $userextradata->username,
            'userfirstname'          => $userextradata->firstname,
            'userlastname'           => $userextradata->lastname,
            'useremail'              => $userextradata->email,
            'timecreated'            => time()
        );
        $DB->insert_record('local_delcoursesuv_deleted', $deletedcourserecord);
    }
}
