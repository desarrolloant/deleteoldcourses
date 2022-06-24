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
 * Provides the datetime_manager class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use DateTimeZone;
use DateTime;

defined('MOODLE_INTERNAL') || die();

/**
 * DateTime manager class for the plugin Delete old courses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datetime_manager {

    private $datetimezone;
    private array $years;
    private array $monthsoftheyear;
    private array $daysofthemonth;
    private array $hoursinaday;
    private array $minutesinanhour;

    /**
     * datetime_manager class constructor.
     */
    public function __construct() {
        $this->set_years();
        $this->set_months_of_the_year();
        $this->set_days_of_the_month();
        $this->set_hours_in_day();
        $this->set_minutes_in_hour();
    }

    /**
     * Set the value of datetimezone.
     *
     * @return self
     * @since  Moodle 3.10
     */
    public function set_datetimezone() {
        $this->datetimezone = new DateTimeZone('America/Bogota');

        return $this;
    }

    /**
     * Get the value of datetimezone.
     *
     * @return dateTimeZone datetimezone
     * @since  Moodle 3.10
     */
    public function get_datetimezone() {
        return $this->datetimezone;
    }

    /**
     * Set years array.
     */
    private function set_years() {

        $years = array();
        $fromyear = 2005;
        $toyear = 2040;

        for ($i = $fromyear; $i <= $toyear; $i++) {
            $years[$i] = $i;
        }

        $this->years = $years;
    }

    /**
     * Get years array.
     *
     * @return array $years
     */
    public function get_years() {
        return $this->years;
    }

    /**
     * Set months of the year array.
     */
    private function set_months_of_the_year() {

        $strjanuary = get_string('january', 'local_deleteoldcourses');
        $strfebruary = get_string('february', 'local_deleteoldcourses');
        $strmarch = get_string('march', 'local_deleteoldcourses');
        $strapril = get_string('april', 'local_deleteoldcourses');
        $strmay = get_string('may', 'local_deleteoldcourses');
        $strjune = get_string('june', 'local_deleteoldcourses');
        $strjuly = get_string('july', 'local_deleteoldcourses');
        $straugust = get_string('august', 'local_deleteoldcourses');
        $strseptember = get_string('september', 'local_deleteoldcourses');
        $stroctober = get_string('october', 'local_deleteoldcourses');
        $strnovember = get_string('november', 'local_deleteoldcourses');
        $strdecember = get_string('december', 'local_deleteoldcourses');

        $monthsoftheyear = array('01' => $strjanuary,
                                '02' => $strfebruary,
                                '03' => $strmarch,
                                '04' => $strapril,
                                '05' => $strmay,
                                '06' => $strjune,
                                '07' => $strjuly,
                                '08' => $straugust,
                                '09' => $strseptember,
                                '10' => $stroctober,
                                '11' => $strnovember,
                                '12' => $strdecember);

        $this->monthsoftheyear = $monthsoftheyear;
    }

    /**
     * Get months of the year array.
     *
     * @return array $monthsoftheyear
     */
    public function get_months_of_the_year() {
        return $this->monthsoftheyear;
    }

    /**
     * Set days of the month array.
     */
    private function set_days_of_the_month() {

        $daysofthemonth = array();

        for ($i = 1; $i <= 31; $i++) {
            if ($i < 10) {
                $daysofthemonth['0' . $i] = '0' . $i;
            } else {
                $daysofthemonth[$i] = strval($i);
            }
        }

        $this->daysofthemonth = $daysofthemonth;
    }

     /**
      * Get days of the month array.
      *
      * @return array $daysofthemonth
      */
    public function get_days_of_the_month() {
        return $this->daysofthemonth;
    }

    /**
     * Set hours in a day array.
     */
    private function set_hours_in_day() {

        $hoursinaday = array();

        for ($i = 0; $i <= 23; $i++) {
            if ($i < 10) {
                $hoursinaday['0' . $i] = '0' . $i;
            } else {
                $hoursinaday[$i] = strval($i);
            }
        }

        $this->hoursinaday = $hoursinaday;
    }

    /**
     * Get hours in a day array.
     *
     * @return array $hoursinaday
     */
    public function get_hours_in_day() {
        return $this->hoursinaday;
    }

    /**
     * Set minutes in an hour array.
     */
    private function set_minutes_in_hour() {

        $minutesinanhour = array();

        for ($i = 0; $i <= 59; $i++) {
            if ($i < 10) {
                $minutesinanhour['0' . $i] = '0' . $i;
            } else {
                $minutesinanhour[$i] = strval($i);
            }
        }

        $this->minutesinanhour = $minutesinanhour;
    }

    /**
     * Get minutes in an hour array.
     *
     * @return array $minutesinanhour
     */
    public function get_minutes_in_hour() {
        return $this->minutesinanhour;
    }

    /**
     * Return a timestamp given a date setting.
     *
     * @param   string $datesetting 'creation' or 'last_modification'
     * @return  int    $timestamp
     * @since   Moodle 3.10
     * @author  Iader E. García Gómez <iadergg@gmail.com>
     * @author  Juan Felipe Orozco <juan.orozco.escobar@correounivalle.edu.co>
     */
    public function date_config_to_timestamp($datesetting) {

        $year = get_config('local_deleteoldcourses',  'year_' . $datesetting . '_date');
        $month = get_config('local_deleteoldcourses', 'month_' . $datesetting . '_date');
        $day = get_config('local_deleteoldcourses', 'day_' . $datesetting . '_date');
        $hour = get_config('local_deleteoldcourses', 'hour_' . $datesetting . '_date');
        $minutes = get_config('local_deleteoldcourses', 'minutes_' . $datesetting . '_date');
        $seconds = get_config('local_deleteoldcourses', 'seconds_' . $datesetting . '_date');

        $date = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minutes . ":" . $seconds;
        $date = new DateTime($date, new DateTimeZone('America/Bogota'));

        $timestamp = $date->getTimestamp();

        return $timestamp;
    }
}
