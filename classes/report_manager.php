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
 * report_manager.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author    <camilo.mezu@correounivalle.edu.co>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use stdClass;

defined('MOODLE_INTERNAL') || die();

class report_manager {
    public function get_number_courses_to_delete($startdate, $enddate): int
    {
        //$userfrom = $DB->get_record('user', array('username' => 'administrador'));

        return 0;
    }

    public function get_number_courses_deleted($startdate, $enddate): int
    {
        return 0;
    }

    public function get_list_of_courses_to_delete($startdate, $enddate): array
    {
        return [];
    }

}
