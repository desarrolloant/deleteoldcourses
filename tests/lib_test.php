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
 * Unit tests for lib.
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

require_once($CFG->dirroot . '/local/deleteoldcourses/locallib.php');

class lib_test extends advanced_testcase {

    /**
     * test_student_generate_username
     */
    public function test_lib_get_years() {

        $years = get_years();
        $this->assertIsArray($years);
        $this->assertSame(count($years), 36);
        $this->assertSame(reset($years), 2005);
        $this->assertSame(end($years), 2040);
    }

    /**
     * test_lib_date_config_to_timestamp
     */
    public function test_lib_date_config_to_timestamp() {

        $this->resetAfterTest(true);

        $configgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        $configgenerator->update_setting('year_creation_date', '2005');
        $configgenerator->update_setting('month_creation_date', '08');
        $configgenerator->update_setting('day_creation_date', '01');
        $configgenerator->update_setting('hour_creation_date', '23');
        $configgenerator->update_setting('minutes_creation_date', '59');
        $configgenerator->update_setting('seconds_creation_date', '59');

        $configgenerator->update_setting('year_last_modification_date', '2022');
        $configgenerator->update_setting('month_last_modification_date', '01');
        $configgenerator->update_setting('day_last_modification_date', '05');
        $configgenerator->update_setting('hour_last_modification_date', '00');
        $configgenerator->update_setting('minutes_last_modification_date', '20');
        $configgenerator->update_setting('seconds_last_modification_date', '20');

        $timecreated = date_config_to_timestamp('creation');
        $timemodified = date_config_to_timestamp('last_modification');

        $this->assertSame($timecreated, 1122958799);
        $this->assertSame($timemodified, 1641360020);
    }
}
