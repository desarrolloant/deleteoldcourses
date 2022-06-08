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
 * Notifier class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use stdClass;

defined('MOODLE_INTERNAL') || die;

class notifier {

    protected $userstonotify;

    /**
     * Notifier class constructor.
     */
    public function __construct() {
        $this->set_userstonotify();
    }


    /**
     * Generate text for notify users.
     *
     * @return string $texttosend
     */
    public function generate_text_to_send() {

        // TO DO.
        // Obtener los contadores desde los metodos de la clase report_manager.

        $coursedata = new stdClass();
        $coursedata->deletedcourses = 2;
        $coursedata->pendingcourses = 3;

        $texttosend = get_string('message_to_send', 'local_deleteoldcourses', $coursedata);

        return $texttosend;
    }

    /**
     * Get the value of userstonotify.
     */
    public function get_userstonotify() {
        return $this->userstonotify;
    }

    /**
     * Set the value of userstonotify.
     *
     * @return  self
     */
    public function set_userstonotify() {

        // TO DO.
        // El metodo set debe leer las configuraciones del plugin y luego setear el atributo.

        return $this;
    }
}
