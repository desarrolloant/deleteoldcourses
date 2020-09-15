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

        // Finish if there are no rows for delete.
        if (!$list = $this->get_to_delete_list()) {
            mtrace("No rows found in list deleteoldcourses");
            return;
        }

        // Delete all courses in list.
        $this->delete_courses_in_list($list);
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

        //delete_old_courses_send_email( '66996031' , 'administrador', $coursesToDelete, $this->deleted_courses );
        //delete_old_courses_send_email( '1144132883' , 'administrador', $coursesToDelete, $this->deleted_courses );
        delete_old_courses_send_email( '1130589899' , 'administrador', $coursesToDelete, $this->deleted_courses);
    }

    /**
     * Get all rows the list to be deleted.
     *
     * @return array
     */
    protected function get_to_delete_list() {
        global $DB;
        // Get all the list.
        $sql = "select * from {deleteoldcourses}";
        $list = $DB->get_records_sql($sql);
        return $list;
    }

    /**
     * Delete all courses in the list.
     *
     * @param array $list The rows to be deleted.
     */
    protected function delete_courses_in_list($list) {
        global $DB;

        $lockfactory = \core\lock\lock_config::get_lock_factory('local_deleteoldcourses_delete_course_task');
        $this->deleted_courses = 0;
        foreach ($list as $item) {
            // If time now is >= 2am then stop
            if (intval(date('H')) >= 2 && intval(date('H')) < 4) {
                break;
            } elseif (intval(date('H')) >= 7) {
                //break;
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
                            'courseid' => $item->courseid,
                            'shortname' => $item->shortname,
                            'fullname' => $item->fullname,
                            'userid' => $item->userid,
                            'timesenttodelete' => $item->timecreated,
                            'timecreated' => time()
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
    }
}
