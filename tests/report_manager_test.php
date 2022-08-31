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
 * Unit tests for report_manager class.
 *
 * @package    local_deleteoldcourses
 * @category   PHPUnit
 * @author     Camilo J. Mezú Mina <camilo.mezu@correounivalle.edu.co>
 * @author     Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

defined('MOODLE_INTERNAL') || die();

class report_manager_test extends \advanced_testcase {

    public function test_get_course_deletion_criteria_settings() {

        $this->resetAfterTest(true);

        // Test environment: plugin settings.
        $plugingenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

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

        // Number of categories to exclude.
        $numberofcategoriestoexcluded = 4;
        $plugingenerator->update_setting('number_of_categories_to_exclude', $numberofcategoriestoexcluded);

        // Create course categories with their associated plugin settings.
        $excludedcategoriesids = [];
        for ($i = 1; $i <= $numberofcategoriestoexcluded; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . $i));
            $plugingenerator->update_setting('excluded_course_categories_' . $i, $excludedcategory->id);
            array_push($excludedcategoriesids, $excludedcategory->id);
        }

        $expectedresult = [
            'creationdate' => [
                'yearcreationdate' => 2010,
                'monthcreationdate' => 12,
                'daycreationdate' => 31,
                'hourcreationdate' => 23,
                'minutescreationdate' => 59,
                'secondscreationdate' => 59
            ],
            'lastmodificationdate' => [
                'yearlastmodificationdate' => 2012,
                'monthlastmodificationdate' => 12,
                'daylastmodificationdate' => 31,
                'hourlastmodificationdate' => 23,
                'minuteslastmodificationdate' => 59,
                'secondslastmodificationdate' => 59
            ],
            'excludedcategories' => [
                '0' => [
                    'id' => $excludedcategoriesids[0],
                    'name' => 'Excluded category 1'
                ],
                '1' => [
                    'id' => $excludedcategoriesids[1],
                    'name' => 'Excluded category 2'
                ],
                '2' => [
                    'id' => $excludedcategoriesids[2],
                    'name' => 'Excluded category 3'
                 ],
                '3' => [
                    'id' => $excludedcategoriesids[3],
                    'name' => 'Excluded category 4'
                 ],
            ]
        ];

        $reportmanager = new report_manager();
        $result = $reportmanager->get_course_deletion_criteria_settings();

        $this->assertInstanceOf(report_manager::class, $reportmanager);
        $this->assertIsArray($result);
        $this->assertEquals($expectedresult, $result);
    }

    public function test_get_total_enqueued_courses() {

        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setGuestUser();

        // Create 25 enqueued courses: 15 manually and 10 automatically.
        $manual = 1;
        for ($i = 0; $i < 25; $i++) {

            if ($i > 14) {
                $manual = 0;
            }

            $coursetoenqueue = (object) array(
                'courseid'    => $i,
                'userid'      => $USER->id,
                'coursesize'  => 0,
                'timecreated' => 0,
                'manual'      => $manual
            );

            $DB->insert_record('local_delcoursesuv_todelete', $coursetoenqueue);
        }

        $reportmanager = new report_manager();
        $allenqueuedcourses = $reportmanager->get_total_enqueued_courses();
        $manuallyenqueuedcourses = $reportmanager->get_total_enqueued_courses(true);
        $automaticallyenqueuedcourses = $reportmanager->get_total_enqueued_courses(false);

        $this->assertInstanceOf(report_manager::class, $reportmanager);
        $this->assertIsInt($allenqueuedcourses);
        $this->assertIsInt($manuallyenqueuedcourses);
        $this->assertIsInt($automaticallyenqueuedcourses);
        $this->assertEquals(25, $allenqueuedcourses);
        $this->assertEquals(15, $manuallyenqueuedcourses);
        $this->assertEquals(10, $automaticallyenqueuedcourses);
    }


    public function test_get_total_deleted_courses_during_time_period() {

        global $DB;

        $this->resetAfterTest(true);

        $starttime = $time = time();

        // Create 25 deleted courses.
        for ($i = 0; $i < 25; $i++) {
            $deletedcourse = (object) array(
                'courseid'               => $i,
                'courseshortname'        => 'Course shortname ' . $i,
                'coursefullname'         => 'Course fullname ' . $i,
                'coursesize'             => 0,
                'coursetimecreated'      => 0,
                'coursetimesenttodelete' => 0,
                'userid'                 => $i,
                'username'               => 'Username ' . $i,
                'userfirstname'          => 'Userfirstname ' . $i,
                'userlastname'           => 'Userlastname ' . $i,
                'useremail'              => 'Useremail ' . $i,
                'timecreated'            => $time
            );

            $DB->insert_record('local_delcoursesuv_deleted', $deletedcourse);
            $time += 600; // Store the next deleted course within 10 minutes.
        }

        $reportmanager = new report_manager();

        $enddate = $starttime + 6000;
        $result = $reportmanager->get_total_deleted_courses_during_time_period($starttime, $enddate);

        $this->assertInstanceOf(report_manager::class, $reportmanager);
        $this->assertIsInt($result);
        $this->assertEquals(11, $result);
    }

    public function test_get_total_enqueued_courses_grouped_by_faculty() {

        global $DB, $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $numberofcategories = 10;
        $coursecategories = [];

        // Create course categories.
        for ($i = 1; $i <= $numberofcategories; $i++) {
            $newcategory = $this->getDataGenerator()->create_category(array("name" => "Category name " . $i));
            array_push($coursecategories, $newcategory);
        }

        // Create 25 enqueued courses.
        for ($i = 0; $i < 25; $i++) {

            $course = $this->getDataGenerator()->create_course(
                array("category" => $coursecategories[rand(0, $numberofcategories - 1)]->id)
            );

            $coursetoenqueue = (object) array(
                'courseid'    => $course->id,
                'userid'      => $USER->id,
                'coursesize'  => rand(500, 2000),
                'timecreated' => time(),
                'manual'      => rand(0, 1)
            );

            $DB->insert_record('local_delcoursesuv_todelete', $coursetoenqueue);
        }

        // $expectedresult = [];

        $reportmanager = new report_manager();
        $result = $reportmanager->get_total_enqueued_courses_grouped_by_faculty();

        $this->assertInstanceOf(report_manager::class, $reportmanager);
        $this->assertIsArray($result);
        // $this->assertEquals($expectedresult, $result);
    }
}
