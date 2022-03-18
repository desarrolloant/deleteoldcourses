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
 * Unit tests for lib.
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_iracv;

use advanced_testcase;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

class local_deleteoldcourses_lib_testcase extends advanced_testcase {

    /**
     * test_student_generate_username
     */
    public function test_lib_get_years() {

        $years = get_years();
        $this->assertIsArray($years);
        $this->assertSame(count($years), 36);
        $this->assertSame($years[0], 2005);
        $this->assertSame(end($years), 2040);
    }

    /**
     * test_lib_date_config_to_timestamp
     */
    public function test_lib_date_config_to_timestamp() {

        $this->resetAfterTest(true);

        $configgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        $config = new stdClass();
        $config->plugin = 'local_deleteoldcourses';
        $config->name = 'year_creation_date';
        $config->value = '2005';

        $configgenerator->insert_config($config);

        $config->name = 'month_creation_date';
        $config->value = '08';

        $configgenerator->insert_config($config);

        $config->name = 'day_creation_date';
        $config->value = '01';

        $configgenerator->insert_config($config);

        $config->name = 'hour_creation_date';
        $config->value = '23';

        $configgenerator->insert_config($config);

        $config->name = 'minutes_creation_date';
        $config->value = '59';

        $configgenerator->insert_config($config);

        $config->name = 'seconds_creation_date';
        $config->value = '59';

        $configgenerator->insert_config($config);

        $timestamp = date_config_to_timestamp();

        //print_r("Fecha " . $timestamp);

        $this->assertSame($timestamp, 1122958799);

    }
}
