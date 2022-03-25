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

/**
 * Unit tests for course dispatcher class.
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use advanced_testcase;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

class local_deleteoldcourses_course_dispatcher_testcase extends advanced_testcase {

    /**
     * Test course dispatcher
     */
    public function test_course_dispatcher() {

        global $DB;

        $this->resetAfterTest(false);

        $coursedispatcher = new course_dispatcher();

        // Timecreated ok. Timemodified ok.

        $course1 = $this->getDataGenerator()->create_course(array('timecreated' => 1293856998));
        $course1->timemodified = 1262311998;
        $DB->update_record('course', $course1);

        $course2 = $this->getDataGenerator()->create_course(array('timecreated' => 1293857998));
        $course2->timemodified = 1262221998;
        $DB->update_record('course', $course2);

        // Timecreated ok. Timemodified not.
        $course3 = $this->getDataGenerator()->create_course(array('timecreated' => 1293851998));
        $course3->timemodified = 1262331999;
        $DB->update_record('course', $course3);

        // Timecreated not. Timemodified ok.
        $course4 = $this->getDataGenerator()->create_course(array('timecreated' => 1293859999));
        $course4->timemodified = 1252321999;
        $DB->update_record('course', $course4);

        // Timecreated not. Timemodified not.
        $course5 = $this->getDataGenerator()->create_course(array('timecreated' => 1293867999));
        $course5->timemodified = 1262921999;
        $DB->update_record('course', $course5);

        $this->assertInstanceOf(course_dispatcher::class, $coursedispatcher);
        $this->assertIsInt($coursedispatcher->get_timecreated_criteria());
        $this->assertIsInt($coursedispatcher->get_timemodified_criteria());
        $this->assertIsInt($coursedispatcher->get_limitquery());

        $this->assertEquals(1293857999, $coursedispatcher->get_timecreated_criteria());
        $this->assertEquals(1262321999, $coursedispatcher->get_timemodified_criteria());
        $this->assertEquals(5000, $coursedispatcher->get_limitquery());

        $this->assertIsArray($coursedispatcher->get_courses_to_delete());

        $this->assertEquals(2, count($coursedispatcher->get_courses_to_delete()));
    }
}
