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

/**
 * Course enqueuer tests
 *
 * @group local_deleteoldcourses
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_enqueuer_test extends \advanced_testcase {

    /** @var int Number of category to exclude in these tests. */
    const NUMBER_OF_CATEGORIES_TO_EXCLUDE = 4;

    /** @var int Number of courses to create in course categories excluded. */
    const NUMBER_OF_COURSES_IN_CATEGORIES_TO_EXCLUDE = 25;

    /** @var int Creation time criteria. Thursday, December 30th 2010 23:59:59 GMT-05:00.*/
    const CREATION_TIME_CRITERIA = 1293771599;

    /** @var int Last modification time criteria. Monday, December 31th 2012 23:59:59 GMT-05:00.*/
    const LAST_MODIFICATION_TIME_CRITERIA = 1357016399;

    /** @var int Minumt timestamp. Saturday, 1 january 2005 0:00:00 GMT-05:00.*/
    const MINTIMESTAMP = 1104555600;

    /** @var int Last modification time criteria. Saturday, 31 december 2050 23:59:59 GMT-05:00.*/
    const MAXTIMESTAMP = 2556161999;

    /** @var array Array with course shortnames that exist in Campus Virtual Historia.*/
    const COURSE_SHORTNAMES_IN_CVH = array(
        '00-740142M-01-201910071',
        '01-201238M-50-202011051',
        '00-740050M-01-201910071',
        '04-745035M-40-202011051',
        '07-745045M-50-201910061',
        '14-760155M-57-202011051',
        '05-760117M-50-202011051_1',
        '00-760100-03-202001041',
        '00-780053M-01-202008041_1',
        '00-780001-01-201308041',
        '14-106065M-50-201910041',
        '00-106023M-01-201910041',
        '00-761103-01-201802041',
        '00-761078-01-201802041',
        '01-503032M-50-202011051'
    );

    /** @var array Array with course shortnames that exist in Campus Virtual Historia and have already been queued.*/
    const COURSE_SHORTNAMES_IN_CVH_QUEUED = array(
        '00-750098M-01-201702041',
        'PRUEBAS_NOTIFICACIONES'
    );

    /** @var int Number of courses that will be deleted. Courses Type A.*/
    const NUMBER_OF_COURSES_TO_DELETE = 15;

    /** @var int Number of courses with sections added after the last modification criteria. Courses Type B.*/
    const NUMBER_OF_COURSES_WITH_SECTIONS_ADDED = 20;

    /** @var int Number of courses with participants enroll or unenroll after the last modification criteria. Courses Type C.*/
    const NUMBER_OF_COURSES_WITH_PARTICIPANTS_ADDED = 20;

    /** @var int Number of courses with modules (activities or resources) added after the last modification criteria. Courses Type D.*/
    const NUMBER_OF_COURSES_WITH_MODULES_ADDED = 10;

    /** @var int Number of courses with creation date greater than the creation criteria. Courses Type E.*/
    const NUMBER_OF_COURSES_WITH_CREATION_DATE_GREATER = 20;

    /** @var int Number of courses with creation date greater than the creation criteriaand last modification date greater tha the criteria. Courses Type F. **/
    const NUMBER_OF_COURSES_WITH_LAST_MODIFICATION_DATE_GREATER = 40;

    /** @var int Number of courses that will be deleted that have already been queued. Courses Type G. **/
    const NUMBER_OF_COURSES_TO_DELETE_QUEUED = 2;

    /** @var int Limit query for the query enqueuer **/
    const LIMIT_QUERY = 500;

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

        // Plugin settings.
        $datetimemanager = new datetime_manager();
        $timecreatedcriteria = $datetimemanager->date_config_to_timestamp('creation');
        $timemodificationcriteria = $datetimemanager->date_config_to_timestamp('last_modification');
        $limitquery = get_config('local_deleteoldcourses', 'limit_query');

        $numbercategoriesexcluded = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $categoriesexcluded = array();

        for ($i = 1; $i < $numbercategoriesexcluded + 1; $i++) {
            array_push($categoriesexcluded, get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i));
        }

        $courseenqueuer = new course_enqueuer();
        $courseenqueuer->get_courses_to_enqueue(0, $timecreatedcriteria, $timemodificationcriteria,
                                                $limitquery, $categoriesexcluded);

        $numberofcoursestodelete = $DB->count_records_sql('SELECT COUNT(*) FROM {local_delcoursesuv_todelete}');

        $this->assertSame(self::NUMBER_OF_COURSES_TO_DELETE + self::NUMBER_OF_COURSES_TO_DELETE_QUEUED, $numberofcoursestodelete);
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

        $this->resetAfterTest(true);

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
        $courseenqueuer->enqueue_courses_to_delete($courses, $user1->id, false);

        $this->assertCount(4, $DB->get_records('local_delcoursesuv_todelete'));
        $this->assertTrue($DB->record_exists('local_delcoursesuv_todelete', array('courseid' => $course1->id)));
        $this->assertTrue($DB->record_exists('local_delcoursesuv_todelete', array('courseid' => $course2->id)));
        $this->assertCount(2, $DB->get_records('local_delcoursesuv_todelete', array('userid' => $user1->id)));
    }

    /**
     * Set up the test environment.
     *
     * Actions:
     *
     *  - Create course categories that their courses will be excluded in the enqueue process.
     *  - Create courses in these course categories.
     *  - Create courses that will be deleted. Courses Type A.
     *  - Create courses with sections added after the last modification criteria. Courses Type B.
     *  - Create courses with participants enroll or unenroll after the last modification criteria. Courses Type C.
     *  - Create courses with modules (activities or resources) added after the last modification criteria. Courses Type D.
     *  - Create courses with creation date greater than the creation criteria. Courses Type E.
     *  - Create courses with creation date greater than the creation criteria
     *    and last modification date greater tha the criteria. Courses Type F.
     *
     * @return void
     * @since Moodle 3.10
     */
    protected function setUp(): void {

        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Plugin Settings.
        // Creation date criteria: 31-12-2010. Timestamp local time: 1293771600.
        $plugingenerator->update_setting('year_creation_date', '2010');
        $plugingenerator->update_setting('month_creation_date', '12');
        $plugingenerator->update_setting('day_creation_date', '31');
        $plugingenerator->update_setting('hour_creation_date', '23');
        $plugingenerator->update_setting('minutes_creation_date', '59');
        $plugingenerator->update_setting('seconds_creation_date', '59');

        // Last modification date criteria: 31-12-2012. Timestamp local time: 1357016399.
        $plugingenerator->update_setting('year_last_modification_date', '2012');
        $plugingenerator->update_setting('month_last_modification_date', '12');
        $plugingenerator->update_setting('day_last_modification_date', '31');
        $plugingenerator->update_setting('hour_last_modification_date', '23');
        $plugingenerator->update_setting('minutes_last_modification_date', '59');
        $plugingenerator->update_setting('seconds_last_modification_date', '59');

        // Criteria: Categories to exclude.
        $plugingenerator->update_setting('number_of_categories_to_exclude', self::NUMBER_OF_CATEGORIES_TO_EXCLUDE);

        // Create course categories to exclude and their courses.
        $coursecategoriesexcluded = $this->create_course_categories_to_exclude($plugingenerator);
        $this->create_courses_in_categories_excluded(self::NUMBER_OF_COURSES_IN_CATEGORIES_TO_EXCLUDE, $coursecategoriesexcluded);

        // Create courses type A.
        $this->create_courses_to_delete(self::NUMBER_OF_COURSES_TO_DELETE);

        // Create courses type B.
        $this->create_courses_with_sections_added(self::NUMBER_OF_COURSES_WITH_SECTIONS_ADDED);

        // Create courses type C.
        $this->create_courses_with_partipants_added(self::NUMBER_OF_COURSES_WITH_PARTICIPANTS_ADDED);

        // Create courses type D.
        $this->create_courses_with_modules_added(self::NUMBER_OF_COURSES_WITH_MODULES_ADDED);

        // Create courses type E.
        $this->create_courses_with_creation_date_greater(self::NUMBER_OF_COURSES_WITH_CREATION_DATE_GREATER);

        // Create courses type F.
        $this->create_courses_with_last_modification_greater(self::NUMBER_OF_COURSES_WITH_LAST_MODIFICATION_DATE_GREATER);

        // Create courses type G.
        $this->create_courses_to_delete_queued(self::NUMBER_OF_COURSES_TO_DELETE_QUEUED);

        // Criteria: Categories to exclude.
        $plugingenerator->update_setting('limity_query', self::LIMIT_QUERY);

        $plugingenerator->update_setting('ws_url',
                          'https://campusvirtualhistoria.univalle.edu.co/moodle');
        $plugingenerator->update_setting('ws_user_token',
                          'de4549d7a1d8aaa27ed4abfb213339f1');
        $plugingenerator->update_setting('ws_function_name', 'core_course_get_courses_by_field');
    }

    /**
     * Create course categories to exclude
     *
     * @param component_generator_base $plugingenerator
     * @return array
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_course_categories_to_exclude($plugingenerator): array {

        $coursecategoriesexcluded = array();

        for ($i = 1; $i <= self::NUMBER_OF_CATEGORIES_TO_EXCLUDE; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i)));
            $plugingenerator->update_setting('excluded_course_categories_' . $i, $excludedcategory->id);
            array_push($coursecategoriesexcluded, $excludedcategory);
        }

        return $coursecategoriesexcluded;
    }

    /**
     * Create course categories to exclude
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_in_categories_excluded(int $numberofcourses, array $coursecategoriesexcluded): void {

        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategoriesexcluded[rand(0, 3)]->id,
                      "numsections" => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);

            $DB->update_record('course_sections', $coursesection);
        }
    }

    /**
     * Create courses that will be deleted. Courses Type A.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_to_delete($numberofcourses): void {

        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('shortname' => self::COURSE_SHORTNAMES_IN_CVH[$i],
                      'numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);

            $DB->update_record('course_sections', $coursesection);
        };
    }

    /**
     * Create courses that will be deleted that have already been queued. Courses Type G.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_to_delete_queued($numberofcourses): void {

        global $DB, $USER;

        for ($i = 0; $i < $numberofcourses; $i++) {

            $date = new DateTime();

            $course = $this->getDataGenerator()->create_course(
                array('shortname' => self::COURSE_SHORTNAMES_IN_CVH_QUEUED[$i],
                      'numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);

            $DB->update_record('course_sections', $coursesection);

            $DB->update_record('course_sections', $coursesection);

            $record = new stdClass();
            $record->shortname = $course->shortname;
            $record->fullname = $course->fullname;
            $record->courseid = $course->id;
            $record->userid = $USER->id;
            $record->size = 0;
            $record->coursecreatedat = $course->timecreated;
            $record->timecreated = $date->getTimestamp();
            $record->manuallyqueued = false;

            $DB->insert_record('local_delcoursesuv_todelete', $record);
        };
    }

    /**
     * Create courses with sections added after the last modification criteria. Courses Type B.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_with_sections_added($numberofcourses): void {
        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('shortname' => 'TestingCourse ' . $i,
                      'fullname' => 'Testing Course ' . $i,
                      'numsections' => 2),
                array('createsections' => true));

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $sections = $DB->get_records('course_sections', array('course' => $course->id));

            foreach ($sections as $section) {
                $section->timemodified = rand(self::LAST_MODIFICATION_TIME_CRITERIA, self::MAXTIMESTAMP);
                $DB->update_record('course_sections', $section);
            }
        }
    }

    /**
     * Create courses with participants enroll or unenroll after the last modification criteria. Courses Type C.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_with_partipants_added($numberofcourses): void {
        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $DB->update_record('course_sections', $coursesection);

            $user = $this->getDataGenerator()->create_user();
            $this->getDataGenerator()->enrol_user($user->id, $course->id);

            $userenrol = $DB->get_record('user_enrolments', array('userid' => $user->id));
            $userenrol->timemodified = rand(self::LAST_MODIFICATION_TIME_CRITERIA, self::MAXTIMESTAMP);
            $DB->update_record('user_enrolments', $userenrol);
        }
    }

    /**
     * Create courses with modules (activities or resources) added after the last modification criteria. Courses Type D.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_with_modules_added($numberofcourses): void {
        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::MINTIMESTAMP, self::CREATION_TIME_CRITERIA);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $DB->update_record('course_sections', $coursesection);

            $assign = $this->getDataGenerator()->get_plugin_generator('mod_assign');
            $assign->create_instance(array('course' => $course->id));
        }
    }

    /**
     * Create courses with creation date is greater than the creation criteria. Courses Type E.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_with_creation_date_greater($numberofcourses): void {
        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::CREATION_TIME_CRITERIA, self::MAXTIMESTAMP);
            $course->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $DB->update_record('course_sections', $coursesection);
        }

    }

    /**
     * Create courses with creation date is greater than the creation criteria and the
     * last modification date is greater than the last modification criteria. Courses Type F.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    private function create_courses_with_last_modification_greater($numberofcourses): void {
        global $DB;

        for ($i = 0; $i < $numberofcourses; $i++) {
            $course = $this->getDataGenerator()->create_course(
                array('numsections' => 0),
                array('createsections' => false)
            );

            $course->timecreated = rand(self::CREATION_TIME_CRITERIA, self::MAXTIMESTAMP);
            $course->timemodified = rand(self::LAST_MODIFICATION_TIME_CRITERIA, self::MAXTIMESTAMP);
            $course->idnumber = $course->shortname;
            $DB->update_record('course', $course);

            $coursesection = $DB->get_record('course_sections', array('course' => $course->id));
            $coursesection->timemodified = rand(self::MINTIMESTAMP, self::LAST_MODIFICATION_TIME_CRITERIA);
            $DB->update_record('course_sections', $coursesection);
        }
    }
}
