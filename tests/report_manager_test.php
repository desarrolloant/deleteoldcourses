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
 * Unit tests for notifier class (email notifications).
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Camilo J. Mezú Mina <camilo.mezu@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;


defined('MOODLE_INTERNAL') || die();

class report_managerTest extends \base_testcase
{

    private $startddate;
    private $enddate;

    public function test_get_number_courses_to_delete()
    {
        $report_manager = new report_manager();
        $this->assertInstanceOf(report_manager::class, $report_manager);

    }

    public function test_get_number_courses_deleted()
    {

    }

    public function test_get_list_of_courses_to_delete()
    {

    }
}
