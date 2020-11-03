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

ini_set('max_execution_time', 14400);
raise_memory_limit(MEMORY_HUGE);
set_time_limit(300);

use DateTime;

/*****************************************/
const COURSES_FOR_QUEUE = 500;
const DATE_FOR_QUEUE = '2010-12-31 23:59';
/*****************************************/

require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

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
     * @var int $deleted_courses
     */
    protected $deleted_courses;


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

        //System load courses
        $num_pending_courses = $DB->count_records('deleteoldcourses');

        //Complete queue for deletetion list
        $num_courses_for_queue = 0;
        if ($num_pending_courses < COURSES_FOR_QUEUE) {
            $num_courses_for_queue = COURSES_FOR_QUEUE - $num_pending_courses;
        }

        $queue_started = date('H:i:s');
        mtrace("Completing queue Started at: {$queue_started}");
        //--------------------------------------------------------
        if ($num_courses_for_queue > 0) {
            queue_the_courses(DATE_FOR_QUEUE, $num_courses_for_queue);
        }
        //--------------------------------------------------------
        $queue_finished = date('H:i:s');
        mtrace("Queue completed at: {$queue_finished}");

        //-----------------------------------------
        //return;

        // Delete all courses in list.
        $this->delete_courses_in_list();
        if ($this->deleted_courses>0) {
            $difftime = microtime_diff($starttime, microtime());
            mtrace("Cron took " . $difftime . " seconds deleting {$this->deleted_courses} courses.");
            mtrace("Fixing course sort order");
            fix_course_sortorder();
        }

        $difftime = microtime_diff($starttime, microtime());
        mtrace("Cron took " . $difftime . " seconds to finish.");
        mtrace("Total deleted courses: {$this->deleted_courses}");

        //Send email
        $coursesToDelete = $DB->count_records('deleteoldcourses');

        delete_old_courses_send_email( '66996031' , 'administrador', $coursesToDelete, $this->deleted_courses );
        delete_old_courses_send_email( '1144132883' , 'administrador', $coursesToDelete, $this->deleted_courses );
        delete_old_courses_send_email( '1130589899' , 'administrador', $coursesToDelete, $this->deleted_courses);
    }

    /**
     * Delete all courses in the list.
     *
     * @param array $list The rows to be deleted.
     */
    protected function delete_courses_in_list() {
        global $DB;

        $sql = "SELECT * FROM {deleteoldcourses} WHERE size >= 0
                UNION
                SELECT * FROM {deleteoldcourses} WHERE size = -1
                ORDER BY size ASC, id ASC";

        //Get queryset
        $rs = $DB->get_recordset_sql($sql,0,2);

        $lockfactory = \core\lock\lock_config::get_lock_factory('local_deleteoldcourses_delete_course_task');
        $this->deleted_courses = 0;
        foreach ($rs as $item) {

            $hour       = intval(date('H'));
            $day        = intval(date('N')); //6->sat, 7->sun
            $minutes    = intval(date('i'));
            
            // Run only between 0:15 and 5:30
            if ($hour > 6 && $day > 1 && $day < 6 ) {
                break;
            }

            if ($hour == 5 && $minutes > 30 && $day > 1 && $day < 6) {
                break;
            }

            $size = $item->size;

            //Size when the course was send for an user
            if ($item->size == -1) {
                $size = courseCalculateSize($item->courseid);
            }else{
                //!!!!!!!!!!!!Confirm date!!!!!!!!!!!!!!!!
                $dt   = new DateTime(DATE_FOR_QUEUE);
                if ($item->coursecreatedat > $dt->getTimestamp()) {
                    continue;
                }
                //!!!!!!!!!!!!Confirm date!!!!!!!!!!!!!!!!
            }

            $lockkey = "course{$item->courseid}";
            $lock = $lockfactory->get_lock($lockkey, 0);

            // Guard against multiple workers in cron.
            if ($lock !== false) {
                mtrace("Deleting course with id {$item->courseid} - {$item->shortname}-{$item->fullname}");
                $startedat = date('H:i:s');
                mtrace("Started at: {$startedat}");
                if ($coursedb = $DB->get_record('course', array('id' => $item->courseid))) {
                    if (!delete_course($coursedb, false)) {
                        mtrace("Failed to delete course {$item->courseid}");
                    }else{
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
                        if($DB->delete_records('deleteoldcourses', array('id' => $item->id))){
                            mtrace("Course with id {$item->courseid} has been deleted");
                            $this->deleted_courses++;
                            
                            $DB->insert_record('deleteoldcourses_deleted', $record);
                        }
                    }
                }
                $endedat = date('H:i:s');
                mtrace("Ended at: {$endedat}");
                $memoryusage = display_size(memory_get_usage());
                mtrace("Memory usage: {$memoryusage}");
                $lock->release();
            }
        }
        $rs->close();
    }
}
