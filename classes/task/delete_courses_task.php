<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace local_deleteoldcourses\task;

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

use DateTime;

const COURSES_FOR_QUEUE = 500;
// Criteria April 22, 2021.
const COURSE_TIME_CREATED = '2018-12-31 23:59:59';
const COURSE_LAST_MODIFICATION = '2020-10-10 23:59:59'; // Before Campus Historia Backup.

/**
 * Adhoc task for deleting courses.
 *
 * @package   local_deleteoldcourses
 * @since     Moodle 3.6.6
 * @author    2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_courses_task extends \core\task\scheduled_task {

    /**
     * Return the name of the component.
     *
     * @return string The name of the component.
     */
    public function get_component() {
        return 'local_deleteoldcourses';
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('task_delete_course', 'local_deleteoldcourses');
    }

    /**
     * Execute the task
     */
    public function execute() {
        global $DB;

        $starttime = microtime();
        $starttasktime = time();

        // System load courses.
        $currentcoursestodelete = $DB->count_records('deleteoldcourses');

        // Complete queue for deletetion list.
        $totalcoursestoenqueue = 0;
        if ($currentcoursestodelete < COURSES_FOR_QUEUE) {
            $totalcoursestoenqueue = COURSES_FOR_QUEUE - $currentcoursestodelete;
        }

        // Comenzado el encolamiento automático de cursos.
        $datequeuingstarts = date('H:i:s');
        mtrace("Completing queue Started at: {$datequeuingstarts}");

        if ($totalcoursestoenqueue > 0) {
            // Criteria April 22, 2021.
            add_courses_to_delete(COURSE_TIME_CREATED, COURSE_LAST_MODIFICATION, $totalcoursestoenqueue);
        }

        $datequeuingends = date('H:i:s');
        mtrace("Queue completed at: {$datequeuingends}");

        // Delete all courses in list.
        $this->delete_courses_in_list();

        // Deleted courses in this rutine.
        $deletedcourses = countDeletedCourses($starttasktime);

        if ($deletedcourses > 0) {
            $difftime = microtime_diff($starttime, microtime());
            mtrace("Cron took " . $difftime . " seconds deleting {$deletedcourses} courses.");
            mtrace("Fixing course sort order");
            fix_course_sortorder();
        }

        $difftime = microtime_diff($starttime, microtime());
        mtrace("Cron took " . $difftime . " seconds to finish.");
        mtrace("Total deleted courses: {$deletedcourses}");

        // Send email.
        $coursestodelete = $DB->count_records('deleteoldcourses');

        delete_old_courses_send_email( '66996031' , 'administrador', $coursestodelete, $deletedcourses );
        delete_old_courses_send_email( '1144132883' , 'administrador', $coursestodelete, $deletedcourses );
        delete_old_courses_send_email( '1130589899' , 'administrador', $coursestodelete, $deletedcourses);
    }

    /**
     * Delete all courses in the plugin table.
     *
     * @return
     */
    protected function delete_courses_in_list() {
        global $DB;

        $sql = "SELECT * FROM {deleteoldcourses} WHERE size >= 0
                UNION
                SELECT * FROM {deleteoldcourses} WHERE size = -1
                ORDER BY size ASC, id ASC";

        // Get queryset.
        $rs = $DB->get_recordset_sql($sql);

        $lockfactory = \core\lock\lock_config::get_lock_factory('local_deleteoldcourses_delete_course_task');
        foreach ($rs as $item) {

            $hour       = intval(date('H'));
            $day        = intval(date('N')); // 6 sat, 7 sun.
            $minutes    = intval(date('i'));

            // Run only between 0:15 and 7:00.
            if ($hour > 7 && $day < 6 ) {
                // break;
            }

            // Stop at 23:00.
            if ($hour >= 23) {
                break;
            }

            $size = $item->size;

            // Size when the course was send for an user.
            if ($item->size == -1) {
                $size = courseCalculateSize($item->courseid);
            }

            $lockkey = "course{$item->courseid}";
            $lock = $lockfactory->get_lock($lockkey, 0);

            // Guard against multiple workers in cron.
            if ($lock !== false) {
                mtrace("Deleting course with id {$item->courseid} - {$item->shortname}-{$item->fullname}");
                $startedat = date('H:i:s');
                mtrace("Started at: {$startedat}");

                try {
                    if ($coursedb = $DB->get_record('course', array('id' => $item->courseid))) {
                        if (!delete_course($coursedb, false)) {
                            mtrace("Failed to delete course {$item->courseid}");
                        } else {
                            $record = (object) array(
                                'courseid'          => $item->courseid,
                                'shortname'         => $item->shortname,
                                'fullname'          => $item->fullname,
                                'userid'            => $item->userid,
                                'size'              => $size,
                                'coursecreatedat'   => $item->coursecreatedat,
                                'timesenttodelete'  => $item->timecreated,
                                'timecreated'       => time()
                            );
                            if ($DB->delete_records('deleteoldcourses', array('id' => $item->id))) {
                                mtrace("Course with id {$item->courseid} has been deleted");
                                $DB->insert_record('deleteoldcourses_deleted', $record);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    mtrace("Exception error in deleting course with id {$item->courseid} ");
                }

                $endedat = date('H:i:s');
                mtrace("Ended at: {$endedat}");
                $memoryusage = display_size(memory_get_usage());
                mtrace("Memory usage: {$memoryusage}");
            }
            $lock->release();
        }
        $rs->close();
    }
}
