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

    /**
     * Migrate records
     *
     * @return void
     */
    public function migrate_records() {

        global $DB;

        // Migrate deleteoldcourses_deleted table.
        $numberofrecords = $DB->count_records('deleteoldcourses_deleted');

        for ($i = 0; $i <= $numberofrecords / 1000; $i++) {
            $records = $DB->get_records('deleteoldcourses_deleted', null, 'id ASC', '*', $i * 1000, 1000);

            foreach ($records as $record) {

                $newrecord = new \stdClass();
                $newrecord->courseid = $record->courseid;
                $newrecord->courseshortname = $record->shortname;
                $newrecord->coursefullname = $record->fullname;
                $newrecord->coursesize = $record->size;
                $newrecord->coursetimecreated = $record->coursecreatedat;
                $newrecord->coursetimesenttodelete = $record->timesenttodelete;
                $newrecord->userid = $record->userid;
                $newrecord->username = '';
                $newrecord->userfirstname = '';
                $newrecord->userlastname = '';
                $newrecord->useremail = '';
                $newrecord->timecreated = $record->timecreated;
                $newrecord->manuallyqueued = 1;

                $userrecord = $DB->get_record('user', array('id' => $record->userid));

                if ($userrecord) {
                    $newrecord->username = $userrecord->username;
                    $newrecord->userfirstname = $userrecord->firstname;
                    $newrecord->userlastname = $userrecord->lastname;
                    $newrecord->useremail = $userrecord->email;
                }

                if ($record->userid == '128') {
                    $newrecord->manuallyqueued = 0;
                }

                $DB->insert_record('local_delcoursesuv_deleted', $newrecord);
            }
        }

        // Migrate deleteoldcourses table.
        $records = $DB->get_records('deleteoldcourses');

        foreach ($records as $record) {
            $newrecord = new \stdClass();
            $newrecord->courseid = $record->courseid;
            $newrecord->userid = $record->userid;
            $newrecord->coursesize = $record->size;
            $newrecord->timecreated = $record->timecreated;
            $newrecord->manuallyqueued = 1;

            if ($record->userid == '128') {
                $newrecord->manuallyqueued = 0;
            }

            $DB->insert_record('local_delcoursesuv_todelete', $newrecord);
        }
    }
}
