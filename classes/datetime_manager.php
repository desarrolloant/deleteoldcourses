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
 * Date and time manager class for the plugin Delete old courses.
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
        $this->set_months_of_the_year();
        $this->set_datetime('years');
        $this->set_datetime('daysofthemonth');
        $this->set_datetime('hoursinaday');
        $this->set_datetime('minutesinanhour');
    }

    /**
     * Set the value of datetimezone.
     *
     * @return self
     * @since  Moodle 3.10
     */
    private function set_datetimezone() {
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
     * Polymorphic function that creates date and time arrays given a datetime type string.
     *
     * @param string $datetimetype 'years', 'daysofthemonth', 'hoursinaday' or 'minutesinanhour'
     */
    private function set_datetime(string $datetimetype) {

        if ($datetimetype == 'years') {
            $fromindex = 2005;
            $toindex = 2040;
        } else if ($datetimetype == 'daysofthemonth') {
            $fromindex = 1;
            $toindex = 31;
        } else if ($datetimetype == 'hoursinaday') {
            $fromindex = 0;
            $toindex = 23;
        } else if ($datetimetype == 'minutesinanhour') {
            $fromindex = 0;
            $toindex = 59;
        }

        $datetime = array();

        for ($index = $fromindex; $index <= $toindex; $index++) {
            if ($index < 10) {
                $datetime['0' . $index] = '0' . $index;
            } else {
                $datetime[strval($index)] = strval($index);
            }
        }

        if ($datetimetype == 'years') {
            $this->years = $datetime;
        } else if ($datetimetype == 'daysofthemonth') {
            $this->daysofthemonth = $datetime;
        } else if ($datetimetype == 'hoursinaday') {
            $this->hoursinaday = $datetime;
        } else if ($datetimetype == 'minutesinanhour') {
            $this->minutesinanhour = $datetime;
        }
    }

    /**
     * Get date and time arrays given a datetime type string.
     *
     * @param string $datetimetype 'years', 'monthsoftheyear', 'daysofthemonth', 'hoursinaday' or 'minutesinanhour'
     * @return array datetime array
     */
    public function get_datetime(string $datetimetype) {
        if ($datetimetype == 'years') {
            return $this->years;
        } else if ($datetimetype == 'monthsoftheyear') {
            return $this->monthsoftheyear;
        } else if ($datetimetype == 'daysofthemonth') {
            return $this->daysofthemonth;
        } else if ($datetimetype == 'hoursinaday') {
            return $this->hoursinaday;
        } else if ($datetimetype == 'minutesinanhour') {
            return $this->minutesinanhour;
        }
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
