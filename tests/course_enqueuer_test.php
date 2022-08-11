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
 * Unit tests for course_enqueuer class.
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
use core_course_category;

/**
 * Course enqueuer tests
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enqueuer_test extends \advanced_testcase {

    /**
     * Test get courses to delete
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::get_courses_to_enqueue
     */
    public function test_get_courses_to_enqueue() {

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

        // Create type C courses for the course enqueuer test environment.
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

        $courseenqueuer = new course_enqueuer();
        $coursestodelete = $courseenqueuer->get_courses_to_enqueue();

        $this->assertIsArray($coursestodelete);
        $this->assertSame($numberofcoursestodelete, count($coursestodelete));
    }

    /**
     * Test get courses to enqueue that are not in the delete table
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::get_courses_to_enqueue
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

            $DB->insert_record('local_delcoursesuv_todelete', $record);

        };

        $courseenqueuer = new course_enqueuer();
        $coursestodelete = $courseenqueuer->get_courses_to_enqueue();

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
        $courseenqueuer = new course_enqueuer();

        $pasttimemodified = 1641013200;   // 2022-01-01 23:59:59

        $havenewsections = $courseenqueuer->have_new_sections($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewsections);

        $futuretimemodified = 1893474000; // 2030-01-01 23:59:59

        $havenewsections = $courseenqueuer->have_new_sections($course->id, $futuretimemodified);
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
        $courseenqueuer = new course_enqueuer();

        $havenewparticipants = $courseenqueuer->have_new_participants($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewparticipants);

        $havenewparticipants = $courseenqueuer->have_new_participants($course->id, $futuretimemodified);
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
        $courseenqueuer = new course_enqueuer();

        $havenewparticipants = $courseenqueuer->have_new_modules($course->id, $pasttimemodified);
        $this->assertSame(true, $havenewparticipants);

        $havenewparticipants = $courseenqueuer->have_new_modules($course->id, $futuretimemodified);
        $this->assertSame(false, $havenewparticipants);
    }

    /**
     * Test check excluded course categories
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::check_excluded_course_categories
     */
    public function test_check_excluded_course_categories() {

        global $DB;

        $this->resetAfterTest(false);

        $this->init_test_environment();

        $categoryid = get_config('local_deleteoldcourses', 'excluded_course_categories_1');
        $course = $DB->get_record_sql('SELECT * FROM {course} WHERE category = ? LIMIT 1', array($categoryid));

        $coursecategories = array($categoryid);

        // Tests.
        $courseenqueuer = new course_enqueuer();
        $result = $courseenqueuer->check_excluded_course_categories($course->id, $coursecategories);

        $this->assertIsBool($result);
        $this->assertSame(true, $result);
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
        $courseenqueuer = new course_enqueuer();
        $courseenqueuer->enqueue_courses_to_delete($courses, $user1->id);

        $this->assertCount(2, $DB->get_records('local_delcoursesuv_todelete'));
        $this->assertTrue($DB->record_exists('local_delcoursesuv_todelete', array('courseid' => $course1->id)));
        $this->assertTrue($DB->record_exists('local_delcoursesuv_todelete', array('courseid' => $course2->id)));
        $this->assertCount(2, $DB->get_records('local_delcoursesuv_todelete', array('userid' => $user1->id)));
    }

    /**
     * Init test environment
     *
     * @param int $timecreationcriteria Criteria: course creation time.
     *                                  Default value 1293771599 Thursday, December 30th 2010 23:59:59 GMT-05:00.
     * @param int $timemodificationcriteria Criteria: last modification time.
     *                                      Default value 1357016399 Monday, December 31th 2012 23:59:59 GMT-05:00.
     * @param int $numberofcategoriesexcluded Setting number of course categories. Default value 4.
     * @param int $mintimestamp Minimun timestamp used to random numbers.
     *                          Default value: 1104555600 Saturday, 1 january 2005 0:00:00 GMT-05:00.
     * @param int $maxtimestamp Maximum timestamp used to random numbers.
     *                          Default value: Saturday, 31 december 2050 23:59:59 GMT-05:00.
     * @return void
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    protected function init_test_environment(int $timecreationcriteria = 1293771599,
                                             int $timemodificationcriteria = 1357016399,
                                             int $numberofcategoriesexcluded = 4,
                                             int $mintimestamp = 1104555600,
                                             int $maxtimestamp = 2556161999) {

        global $DB;
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Plugin Settings.
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

        $excludedcategories = $this->create_excluded_course_categories($numberofcategoriesexcluded);
        $this->create_courses_in_excluded_categories($excludedcategories, $mintimestamp, $timecreationcriteria, $timemodificationcriteria);
        $this->assertCount(100, $DB->get_records_sql('SELECT * FROM {course} WHERE id <> ?', array('1')));
    }

    /**
     * Create excluded course categories.
     *
     * @param  int   $numberofcategoriesexcluded
     * @return array $excludedcoursecategories
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    protected function create_excluded_course_categories($numberofcategoriesexcluded) {

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Categories to exclude.
        $plugingenerator->update_setting('number_of_categories_to_exclude', $numberofcategoriesexcluded);

        $excludedcoursecategories = array();

        // Course categories.
        for ($i = 1; $i <= $numberofcategoriesexcluded; $i++) {
            $excludedcategoryraw = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i)));
            $plugingenerator->update_setting('excluded_course_categories_' . $i, $excludedcategoryraw->id);

            $this->create_node_categories_tree($excludedcategoryraw, $excludedcoursecategories);
        }

        // Child categories level 1.
        $childcategory1araw = $this->getDataGenerator()->create_category(array("name" => "Child category 1",
                                                                               "parent" => $excludedcoursecategories[1]->id));

        $this->create_node_categories_tree($childcategory1araw, $excludedcoursecategories[1]->children);

        // Child categories level 2.
        $childcategory2araw = $this->getDataGenerator()->create_category(array("name" => "Child category 2a",
                                                                               "parent" => $excludedcoursecategories[2]->id));

        $this->create_node_categories_tree($childcategory2araw, $excludedcoursecategories[2]->children);

        $childcategory2braw = $this->getDataGenerator()->create_category(array("name" => "Child category 2b",
                                                                               "parent" => $childcategory2araw->id));

        $this->create_node_categories_tree($childcategory2braw, $excludedcoursecategories[2]->children[0]->children);

        // Child categories level 3.
        $childcategory3araw = $this->getDataGenerator()->create_category(array("name" => "Child category 3a",
                                                                            "parent" => $excludedcoursecategories[3]->id));

        $this->create_node_categories_tree($childcategory3araw, $excludedcoursecategories[3]->children);

        $childcategory3braw = $this->getDataGenerator()->create_category(array("name" => "Child category 3b",
                                                                            "parent" => $childcategory3araw->id));

        $this->create_node_categories_tree($childcategory3braw, $excludedcoursecategories[3]->children[0]->children);

        $childcategory3craw = $this->getDataGenerator()->create_category(array("name" => "Child category 3c",
                                                                            "parent" => $childcategory3araw->id));

        $this->create_node_categories_tree($childcategory3craw, $excludedcoursecategories[3]->children[0]->children[0]->children);

        return $excludedcoursecategories;
    }

    /**
     * Create a course category node.
     *
     * @param  core_course_category $rawcategory
     * @param  array $arraycategories
     * @return bool
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    protected function create_node_categories_tree(core_course_category $rawcategory, array &$arraycategories) {

        $category = new stdClass();
        $category->id = $rawcategory->id;
        $category->name = $rawcategory->name;
        $category->children = array();

        return array_push($arraycategories, $category) ? true : false;
    }

    /**
     * Create 100 courses in excluded categories.
     *
     * @param  array $coursecategories Excluded course categories array.
     * @param  int $mintimestamp Minimun timestamp used to random numbers.
     * @param  int $timecreationcriteria Criteria: course creation time.
     * @param  int $timemodificationcriteria
     * @return bool
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    protected function create_courses_in_excluded_categories(array $coursecategories,
                                                             int $mintimestamp,
                                                             int $timecreationcriteria,
                                                             int $timemodificationcriteria) {

        global $DB;

        $courses = array();
        $courses['firstlevel'] = array();
        $courses['secondlevel'] = array();
        $courses['thirdlevel'] = array();
        $courses['fourthlevel'] = array();

        for ($i = 0; $i < 50; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategories[rand(0, count($coursecategories) - 1)]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            array_push($courses['firstlevel'], $course->id);

            $course->timecreated = rand($mintimestamp, $timecreationcriteria);
            $course->timemodified = rand($mintimestamp, $timemodificationcriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $timemodificationcriteria);

            $DB->update_record('course_sections', $coursesection);
        }

        for ($i = 0; $i < 25; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategories[1]->children[0]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            array_push($courses['secondlevel'], $course->id);

            $course->timecreated = rand($mintimestamp, $timecreationcriteria);
            $course->timemodified = rand($mintimestamp, $timemodificationcriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $timemodificationcriteria);

            $DB->update_record('course_sections', $coursesection);
        }

        for ($i = 0; $i < 15; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategories[2]->children[0]->children[0]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            array_push($courses['thirdlevel'], $course->id);

            $course->timecreated = rand($mintimestamp, $timecreationcriteria);
            $course->timemodified = rand($mintimestamp, $timemodificationcriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $timemodificationcriteria);

            $DB->update_record('course_sections', $coursesection);
        }

        for ($i = 0; $i < 10; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategories[3]->children[0]->children[0]->children[0]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            array_push($courses['fourthlevel'], $course->id);

            $course->timecreated = rand($mintimestamp, $timecreationcriteria);
            $course->timemodified = rand($mintimestamp, $timemodificationcriteria);
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand($mintimestamp, $timemodificationcriteria);

            $DB->update_record('course_sections', $coursesection);
        }
    }
}
