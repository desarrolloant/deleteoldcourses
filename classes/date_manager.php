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
 * Provides the date_manager class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use stdClass;
use DateTimeZone;

defined('MOODLE_INTERNAL') || die();

/**
 * Date manager class for the plugin Delete old courses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date_manager {

    private $datetimezone;

    /**
     * Set the value of datetimezone
     *
     * @return  self
     * @since  Moodle 3.10
     */
    public function set_datetimezone() {
        $this->datetimezone = new DateTimeZone('America/Bogota');

        return $this;
    }

    /**
     * Get the value of datetimezone
     *
     * @return dateTimeZone datetimezone
     * @since  Moodle 3.10
     */
    public function get_datetimezone() {
        return $this->datetimezone;
    }
}
