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
 * Unit tests for datetime_manager class.
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

/**
 * Unit tests for datetime_manager class.
 *
 * @group      local_deleteoldcourses
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @author     Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datetime_manager_test extends \advanced_testcase {

    private datetime_manager $datetimemanager;

    /**
     * Initialize $datetimemanager object before calling the testing methods.
     */
    protected function setUp(): void {
        $this->datetimemanager = new datetime_manager;
    }

    /**
     * Test the behaviour of get_datetime method.
     *
     * @covers ::get_datetime
     */
    public function test_get_datetime() {

        $years = $this->datetimemanager->get_datetime('years');
        $this->assertIsArray($years);
        $this->assertSame(count($years), 36);
        $this->assertSame(reset($years), '2005');
        $this->assertSame(end($years), '2040');
        $this->assertContainsOnly('string', $years);
        $this->is_key_equal_to_value($years);

        $daysofthemonth = $this->datetimemanager->get_datetime('daysofthemonth');
        $this->assertIsArray($daysofthemonth);
        $this->assertSame(count($daysofthemonth), 31);
        $this->assertSame(reset($daysofthemonth), '01');
        $this->assertSame(end($daysofthemonth), '31');
        $this->assertContainsOnly('string', $daysofthemonth);
        $this->is_key_equal_to_value($daysofthemonth);

        $hoursinaday = $this->datetimemanager->get_datetime('hoursinaday');
        $this->assertIsArray($hoursinaday);
        $this->assertSame(count($hoursinaday), 24);
        $this->assertSame(reset($hoursinaday), '00');
        $this->assertSame(end($hoursinaday), '23');
        $this->assertContainsOnly('string', $hoursinaday);
        $this->is_key_equal_to_value($hoursinaday);

        $minutesinanhour = $this->datetimemanager->get_datetime('minutesinanhour');
        $this->assertIsArray($minutesinanhour);
        $this->assertSame(count($minutesinanhour), 60);
        $this->assertSame(reset($minutesinanhour), '00');
        $this->assertSame(end($minutesinanhour), '59');
        $this->assertContainsOnly('string', $minutesinanhour);
        $this->is_key_equal_to_value($minutesinanhour);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(get_string('invalid_input_datetimetype', 'local_deleteoldcourses', 'randominvalidinput'));
        $randominvalidinput = $this->datetimemanager->get_datetime('randominvalidinput');
    }

    /**
     * Test the behaviour of get_months_of_the_years method.
     *
     * @covers ::get_months_of_the_years
     */
    public function test_get_months_of_the_years() {
        $monthsoftheyear = $this->datetimemanager->get_datetime('monthsoftheyear');
        $monthnumbers = array('01', '02', '03', '04', '05', '06',
                                '07', '08', '09', '10', '11', '12');
        $this->assertEquals(array_keys($monthsoftheyear), $monthnumbers);
        $this->assertContainsOnly('string', $monthsoftheyear);
    }

    /**
     * Test the behaviour of date_config_to_timestamp method.
     *
     * @covers ::date_config_to_timestamp
     */
    public function test_date_config_to_timestamp() {

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

        $timecreated = $this->datetimemanager->date_config_to_timestamp('creation');
        $timemodified = $this->datetimemanager->date_config_to_timestamp('last_modification');

        $this->assertSame($timecreated, 1122958799);
        $this->assertSame($timemodified, 1641360020);
        $this->assertIsInt($timecreated);
        $this->assertIsInt($timemodified);
    }

    /**
     * Assert that a key is equal to its value in an array.
     *
     * @param array $array
     */
    private function is_key_equal_to_value(array $array) {
        foreach ($array as $key => $value) {
            $this->assertSame(strval($key), $value);
        }
    }
}
