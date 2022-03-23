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

        $coursedispatcher = new course_dispatcher();

        $this->assertInstanceOf(course_distpatcher::class, $coursedispatcher);
        $this->assertIsInt($coursedispatcher->timecreatedcriteria);
        $this->assertIsInt($coursedispatcher->timemodifiedcriteria);

    }
}
