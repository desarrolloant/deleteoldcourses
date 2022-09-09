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
 * Unit tests for course_deleter class.
 *
 * @package    local_deleteoldcourses
 * @author     Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

/**
 * Unit test for course_deleter class.
 *
 * @group      local_deleteoldcourses
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_deleter_test extends \advanced_testcase {

    private course_deleter $coursedeleter;
    private int $taskqueuesize;

    /**
     * Initialize $coursedeleter object before calling the testing methods.
     */
    protected function setUp(): void {
        $this->setAdminUser();
        $this->taskqueuesize = 10;
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');
        $plugingenerator->update_setting('task_queue_size', $this->taskqueuesize);
        $this->coursedeleter = new course_deleter;
    }

    /**
     * Test the behaviour of delete_courses method.
     *
     * @covers ::delete_courses
     */
    public function test_delete_courses() {

        global $DB, $USER;

        $this->resetAfterTest(false);

        $originalcoursesbackupdata = [];
        $coursetoenqueuebackupdata = [];

        // 25 possible enqueued courses to delete, but only 10 ($taskqueuesize) of them will be deleted.
        for ($i = 0; $i < 25; $i++) {
            $course = $this->getDataGenerator()->create_course();
            $coursetoenqueue = (object) array(
                'courseid'    => $course->id,
                'userid'      => $USER->id,
                'coursesize'  => rand(500, 2000),
                'timecreated' => time()
            );
            $DB->insert_record('local_delcoursesuv_todelete', $coursetoenqueue);
            if ($i < 10) {
                array_push($originalcoursesbackupdata, $course);
                array_push($coursetoenqueuebackupdata, $coursetoenqueue);
            }
        }

        $this->coursedeleter->delete_courses();

        // Number of deleted courses must be equal to the configured task queue size.
        $numberofdeletedcourses = $DB->count_records('local_delcoursesuv_deleted');
        $this->assertSame($this->taskqueuesize, $numberofdeletedcourses);

        // Number of remaining courses must be equal to the equeued courses.
        $totalcourses = $DB->count_records('course');
        $totalcourses -= 1; // Do not count main course site.
        $totalcoursestodelete = $DB->count_records('local_delcoursesuv_todelete');
        $this->assertSame($totalcourses, $totalcoursestodelete);

        // Original deleted courses and user data must match historical data of deleted courses.
        for ($i = 0; $i < 10; $i++) {
            $deletedcourse = $DB->get_record('local_delcoursesuv_deleted', ['courseid' => $originalcoursesbackupdata[$i]->id],
                                        'courseshortname, coursefullname, coursetimecreated, coursesize, coursetimesenttodelete,
                                        username, userfirstname, userlastname, useremail');
            $userdata = $DB->get_record('user', ['id' => $coursetoenqueuebackupdata[$i]->userid],
                                        'username, firstname, lastname, email');

            $this->assertSame($deletedcourse->courseshortname, $originalcoursesbackupdata[$i]->shortname);
            $this->assertSame($deletedcourse->coursefullname, $originalcoursesbackupdata[$i]->fullname);
            $this->assertSame($deletedcourse->coursetimecreated, $originalcoursesbackupdata[$i]->timecreated);

            $this->assertSame($deletedcourse->coursesize, strval($coursetoenqueuebackupdata[$i]->coursesize));
            $this->assertSame($deletedcourse->coursetimesenttodelete, strval($coursetoenqueuebackupdata[$i]->timecreated));

            $this->assertSame($deletedcourse->username, $userdata->username);
            $this->assertSame($deletedcourse->userfirstname, $userdata->firstname);
            $this->assertSame($deletedcourse->userlastname, $userdata->lastname);
            $this->assertSame($deletedcourse->useremail, $userdata->email);
        }
    }
}
