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
 * Unit tests for course_dispatcher class.
 *
 * @package    local_deleteoldcourses
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use DateTime;

/**
 * Course dispatcher tests
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_dispatcher_test extends \advanced_testcase {

    /**
     * Test get courses to delete
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::get_courses_to_delete
     */
    public function test_get_courses_to_delete() {

        global $DB;

        $this->resetAfterTest(true);

        $numberofcategoriesexcluded = 4;
        $coursecategoriesexcluded = array();
        $numberofcoursestodelete = 15;

        $creationtimecriteria = 1293771599; // Thursday, December 30th 2010 23:59:59 GMT-05:00.
        $lastmodificationtimecriteria = 1357016399; // Monday, December 31th 2012 23:59:59 GMT-05:00.
        $mintimestamp = 1104555600; // Saturday, 1 january 2005 0:00:00 GMT-05:00.
        $maxtimestamp = 2556161999; // Saturday, 31 december 2050 23:59:59 GMT-05:00.

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Test environment. Plugin Settings.
        // Creation date: 31-12-2010. Timestamp local time: 1293771600.
        $plugingenerator->update_setting('year_creation_date', '2010');
        $plugingenerator->update_setting('month_creation_date', '12');
        $plugingenerator->update_setting('day_creation_date', '31');
        $plugingenerator->update_setting('hour_creation_date', '23');
        $plugingenerator->update_setting('minutes_creation_date', '59');
        $plugingenerator->update_setting('seconds_creation_date', '59');

        // Last modification date: 31-12-2012. Timestamp local time: 1357016399.
        $plugingenerator->update_setting('year_last_modification_date', '2012');
        $plugingenerator->update_setting('month_last_modification_date', '12');
        $plugingenerator->update_setting('day_last_modification_date', '31');
        $plugingenerator->update_setting('hour_last_modification_date', '23');
        $plugingenerator->update_setting('minutes_last_modification_date', '59');
        $plugingenerator->update_setting('seconds_last_modification_date', '59');

        // Categories to exclude.
        $plugingenerator->update_setting('number_of_categories_to_exclude', $numberofcategoriesexcluded);

        // Course categories.
        for ($i = 1; $i <= $numberofcategoriesexcluded; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i)));
            $plugingenerator->update_setting('excluded_course_categories_' . $i, $excludedcategory->id);
            array_push($coursecategoriesexcluded, $excludedcategory);
        }

        // 25 courses that belong to categories excluded from a plugin setting.
        for ($i = 0; $i < 25; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategoriesexcluded[rand(0, 3)]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);

            $DB->update_record('course_sections', $coursesection);
        }

        // 15 courses whose creation date is less than the timecreated criteria and the modification date is less than
        // the last modification criteria.
        // Courses type A.
        for ($i = 0; $i < 15; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);

            $DB->update_record('course_sections', $coursesection);
        };

        // 20 courses whose creation date is less than the criteria and sections were added to these courses
        // after the last modification date criteria. Courses type B.
        for ($i = 0; $i < 20; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'TestingCourse ' . $i,
                      'fullname' => 'Testing Course ' . $i,
                      'numsections' => 2),
                array('createsections' => true));

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $sections = $DB->get_records('course_sections', array('course' => $course->id));

            foreach ($sections as $section) {
                $section->timemodified = rand($lastmodificationtimecriteria, $maxtimestamp);
                $DB->update_record('course_sections', $section);
            }
        }

        // Create type C courses for the course dispatcher test environment.
        // 20 courses whose creation date is less than the criteria and some participants enroll or unenroll in these courses
        // after the last modification date criteria. Courses type C.
        for ($i = 0; $i < 20; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course_sections', $coursesection);

            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id);

            $userenrol = $DB->get_record('user_enrolments', array('userid' => $user->id));
            $userenrol->timemodified = rand($lastmodificationtimecriteria, $maxtimestamp);
            $DB->update_record('user_enrolments', $userenrol);
        }

        // 10 courses whose creation date is less than the criteria and activities or resources were created in these courses
        // after the last modification date criteria. Courses type D.
        for ($i = 0; $i < 10; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course_sections', $coursesection);

            $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign');
            $assign->create_instance(array('course' => $course->id));
        }

        // 20 courses whose creation date is greater than the criteria and the last modification date is less than the criteria.
        // Courses type E.
        for ($i = 0; $i < 20; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($creationtimecriteria, $maxtimestamp);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course_sections', $coursesection);
        }

        // 40 courses whose creation date is greater than the criteria and the last modification date is greater than the criteria.
        // Courses type F.
        for ($i = 0; $i < 40; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($creationtimecriteria, $maxtimestamp);
            $course->timemodified = rand($lastmodificationtimecriteria, $maxtimestamp);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course_sections', $coursesection);
        }

        $coursedispatcher = new course_dispatcher();
        $coursestodelete = $coursedispatcher->get_courses_to_delete();

        $this->assertIsArray($coursestodelete);
        $this->assertSame($numberofcoursestodelete, count($coursestodelete));
    }

    /**
     * Test get courses to delete that are not in the delete table
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::get_courses_to_delete
     */
    public function test_get_courses_not_in_delete_table() {
        global $DB, $USER;

        $this->resetAfterTest(true);

        $numberofcategoriesexcluded = 4;
        $coursecategoriesexcluded = array();
        $numberofcoursestodelete = 7;

        $creationtimecriteria = 1293771599; // Thursday, December 30th 2010 23:59:59 GMT-05:00.
        $lastmodificationtimecriteria = 1357016399; // Monday, December 31th 2012 23:59:59 GMT-05:00.
        $mintimestamp = 1104555600; // Saturday, 1 january 2005 0:00:00 GMT-05:00.
        $maxtimestamp = 2556161999; // Saturday, 31 december 2050 23:59:59 GMT-05:00.

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Test environment. Plugin Settings.
        // Creation date: 31-12-2010. Timestamp local time: 1293771600.
        $plugingenerator->update_setting('year_creation_date', '2010');
        $plugingenerator->update_setting('month_creation_date', '12');
        $plugingenerator->update_setting('day_creation_date', '31');
        $plugingenerator->update_setting('hour_creation_date', '23');
        $plugingenerator->update_setting('minutes_creation_date', '59');
        $plugingenerator->update_setting('seconds_creation_date', '59');

        // Last modification date: 31-12-2012. Timestamp local time: 1357016399.
        $plugingenerator->update_setting('year_last_modification_date', '2012');
        $plugingenerator->update_setting('month_last_modification_date', '12');
        $plugingenerator->update_setting('day_last_modification_date', '31');
        $plugingenerator->update_setting('hour_last_modification_date', '23');
        $plugingenerator->update_setting('minutes_last_modification_date', '59');
        $plugingenerator->update_setting('seconds_last_modification_date', '59');

        // Categories to exclude.
        $plugingenerator->update_setting('number_of_categories_to_exclude', $numberofcategoriesexcluded);

        // Course categories.
        for ($i = 1; $i <= $numberofcategoriesexcluded; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i)));
            $plugingenerator->update_setting('excluded_course_categories_' . $i, $excludedcategory->id);
            array_push($coursecategoriesexcluded, $excludedcategory);
        }

        // 7 courses whose creation date is less than the timecreated criteria and the modification date is less than
        // the last modification criteria.
        // Courses type A.
        for ($i = 0; $i < 7; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);

            $DB->update_record('course_sections', $coursesection);
        };

        // 10 courses whose creation date is less than the timecreated criteria and the modification date is less than
        // the last modification criteria.
        // Courses type A.
        for ($i = 0; $i < 10; $i++) {
            $date = new DateTime();

            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand($mintimestamp, $creationtimecriteria);
            $course->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $lastmodificationtimecriteria);

            $DB->update_record('course_sections', $coursesection);

            $record = new stdClass();
            $record->shortname = $course->shortname;
            $record->fullname = $course->fullname;
            $record->courseid = $course->id;
            $record->userid = $USER->id;
            $record->size = 0;
            $record->coursecreatedat = $course->timecreated;
            $record->timecreated = $date->getTimestamp();

            $DB->insert_record('deleteoldcourses', $record);

        };

        $coursedispatcher = new course_dispatcher();
        $coursestodelete = $coursedispatcher->get_courses_to_delete();

        $this->assertIsArray($coursestodelete);
        $this->assertSame($numberofcoursestodelete, count($coursestodelete));
    }

    /**
     * Test have new sections method
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::have_new_sections
     */
    public function test_have_new_sections() {

        $this->resetAfterTest(true);

        // Test environment.
        $course = $this->getDataGenerator()->create_course(
            array('shortname' => 'TestingCourse',
                  'fullname' => 'Testing Course',
                  'numsections' => 2),
            array('createsections' => true));

        // Tests.
        $coursedispatcher = new course_dispatcher();

        $pasttimemodified = 1641013200;   // 2022-01-01 23:59:59

        $havenewsections = $coursedispatcher->have_new_sections($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewsections);

        $futuretimemodified = 1893474000; // 2030-01-01 23:59:59

        $havenewsections = $coursedispatcher->have_new_sections($course->id, $futuretimemodified);
        $this->assertSame(false, $havenewsections);
    }

    /**
     * Test have new participants
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::have_new_participants
     */
    public function test_have_new_participants() {

        $this->resetAfterTest(true);

        $futuretimemodified = 1893474000; // 2030-01-01 23:59:59
        $pasttimemodified = 1641013200;   // 2022-01-01 23:59:59

        // Test environment.
        $course = $this->getDataGenerator()->create_course(
            array('shortname' => 'TestingCourse',
                  'fullname' => 'Testing Course',
                  'numsections' => 2),
            array('createsections' => true));

        $user = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($user->id, $course->id);

        // Tests.
        $coursedispatcher = new course_dispatcher();

        $havenewparticipants = $coursedispatcher->have_new_participants($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewparticipants);

        $havenewparticipants = $coursedispatcher->have_new_participants($course->id, $futuretimemodified);
        $this->assertSame(false, $havenewparticipants);
    }

    /**
     * Test have new activity or resource modules
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::have_new_modules
     */
    public function test_have_new_modules() {
        $this->resetAfterTest(true);

        $futuretimemodified = 1893474000; // 2030-01-01 23:59:59
        $pasttimemodified = 1641013200;   // 2022-01-01 23:59:59

        // Test environment.
        $course = $this->getDataGenerator()->create_course(
            array('shortname' => 'TestingCourse',
                  'fullname' => 'Testing Course',
                  'numsections' => 2),
            array('createsections' => true));

        $assigngenerator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance = $assigngenerator->create_instance(['course' => $course->id]);

        // Tests.
        $coursedispatcher = new course_dispatcher();

        $havenewparticipants = $coursedispatcher->have_new_modules($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewparticipants);

        $havenewparticipants = $coursedispatcher->have_new_modules($course->id, $futuretimemodified);
        $this->assertSame(false, $havenewparticipants);

    }

    /**
     * Test enqueue courses to delete
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::enqueue_courses_to_delete
     */
    public function test_enqueue_courses_to_delete() {

        global $DB;

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $user1 = $this->getDataGenerator()->create_user(array('email' => 'user1@example.com', 'username' => 'user1'));

        $courses = array(
            $course1,
            $course2
        );

        // Tests.
        $coursedispatcher = new course_dispatcher();
        $coursedispatcher->enqueue_courses_to_delete($courses, $user1->id);

        $this->assertCount(2, $DB->get_records('deleteoldcourses'));
        $this->assertTrue($DB->record_exists('deleteoldcourses', array('courseid' => $course1->id)));
        $this->assertTrue($DB->record_exists('deleteoldcourses', array('courseid' => $course2->id)));
        $this->assertCount(2, $DB->get_records('deleteoldcourses', array('userid' => $user1->id)));
    }
}
