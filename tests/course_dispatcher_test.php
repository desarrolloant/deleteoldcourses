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

        // Test environment.

        // Course categories.
        for ($i = 0; $i < $numberofcategoriesexcluded; $i++) {
            $excludedcategory = $this->getDataGenerator()->create_category(array("name" => "Excluded category " . strval($i + 1)));
            array_push($coursecategoriesexcluded, $excludedcategory);
        }

        for ($i = 0; $i < $numberofcoursesexcluded / 2; $i++) {
            $course = $this->getDataGenerator()->create_course(array("category" => $coursecategoriesexcluded[rand(0, 3)]->id));
        }

        $coursedispatcher = new course_dispatcher();

    }
}
