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
 * Unit tests for course dispatcher class.
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use advanced_testcase;

defined('MOODLE_INTERNAL') || die();

global $CFG;

class course_dispatcher_test extends advanced_testcase {

    /**
     * Test course dispatcher
     */
    public function test_course_dispatcher() {

        global $DB;

        $this->resetAfterTest(false);

        $numberofcategoriesexcluded = 4;
        $coursecategoriesexcluded = array();
        $numberofcoursesexcluded = 50;
        $numberofcoursesok = 100;

        $configgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        // Test environment.
        // Plugin Settings.
        // Creation date: 01-01-2005. Timestamp local time: 1122872400.
        $configgenerator->update_setting('year_creation_date', '2005');
        $configgenerator->update_setting('month_creation_date', '01');
        $configgenerator->update_setting('day_creation_date', '01');
        $configgenerator->update_setting('hour_creation_date', '00');
        $configgenerator->update_setting('minutes_creation_date', '00');
        $configgenerator->update_setting('seconds_creation_date', '00');

        // Last modification date: 31-12-2010. Timestamp local time: 1293857999.
        $configgenerator->update_setting('year_last_modification_date', '2010');
        $configgenerator->update_setting('month_last_modification_date', '12');
        $configgenerator->update_setting('day_last_modification_date', '31');
        $configgenerator->update_setting('hour_last_modification_date', '23');
        $configgenerator->update_setting('minutes_last_modification_date', '59');
        $configgenerator->update_setting('seconds_last_modification_date', '59');

        // Categories to exclude.
        $configgenerator->update_setting('number_of_categories_to_exclude', $numberofcategoriesexcluded);

        // Course categories.
        for ($i = 1; $i <= $numberofcategoriesexcluded; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i)));
            $configgenerator->update_setting('excluded_course_categories_' . $i, $excludedcategory->id);
            array_push($coursecategoriesexcluded, $excludedcategory);
        }

        // 25 courses that belgon to categories excluded from a plugin setting.
        for ($i = 0; $i < 25; $i++) {
            $course = $this->getDataGenerator()->create_course(array("category" => $coursecategoriesexcluded[rand(0, 3)]->id));
        }

        // 15 courses whose creation date is less than the criteria and the modification date is less than the criteria.
        for ($i = 0; $i < 15; $i++) {
            $course = $this->getDataGenerator()->create_course();

            $course->timecreated = rand(1104555600, 1122872399);
            $course->timemodified = rand(1104555600, 1122872399);
            $DB->update_record('course', $course);
        };

        // 10 courses whose last modification date is greater than the criteria.
        for ($i = 0; $i < 10; $i++) {
            $course = $this->getDataGenerator()->create_course();
        }

        $coursedispatcher = new course_dispatcher();
        $course->timecreated = rand(1104555600, 1122872399);
        $DB->update_record('course', $course);

    }
}
