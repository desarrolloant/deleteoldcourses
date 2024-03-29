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
 * @package     local_deleteoldcourses
 * @author      2022 Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use stdClass;

defined('MOODLE_INTERNAL') || die();

class notifier {

    /** @var string User list to notify */
    protected $userstonotify;

    /** @var string Text to send by email */
    protected string $texttosend;

    /**
     * Notifier class constructor.
     */
    public function __construct() {
        $this->set_userstonotify();
        $this->texttosend = $this->generate_text_to_send();
    }

    /**
     * Generate text for notify users.
     *
     * @return  string $texttosend
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
     * Send notify to users.
     *
     * @return  bool $notificationsent
     */
    public function send_notification() {

        global $DB;

        // TO DO.
        // Revisar path de los logs de la ejecución.
        // Revisar logs de la ejecución.
        $notificationsent = false;

        $userfrom = $DB->get_record('user', array('username' => 'administrador'));
        $subject = get_string('notification_subject', 'local_deleteoldcourses');
        $completefilepath = "/vhosts/campus/moodledata/temp/backup/";
        $filename = 'deleteoldcourses.log';

        $userstonotify = $this->get_userstonotify();
        $texttosend = $this->get_text_to_send();

        foreach ($userstonotify as $user) {
            $notificationsent = email_to_user($user,
                                              $userfrom,
                                              $subject,
                                              $texttosend,
                                              html_to_text($texttosend),
                                              $completefilepath,
                                              $filename,
                                              true);
        }

        return $notificationsent;
    }

    /**
     * Get the value of userstonotify.
     */
    public function get_userstonotify() {
        return $this->userstonotify;
    }

    /**
     * Set the value of userstonotify.
     * Set an empty array if the config field is empty.
     *
     * @return  self
     */
    public function set_userstonotify() {

        global $DB;

        $userstonotifysetting = get_config('local_deleteoldcourses', 'users_to_notify');

        $usertonotifyusernames = explode(',', $userstonotifysetting);

        $userstonotify = array();

        foreach ($usertonotifyusernames as $username) {
            $user = $DB->get_record('user', array('username' => trim($username)), 'id, username, email');

            if ($user) {
                array_push($userstonotify, $user);
            }
        }

        $this->userstonotify = $userstonotify;

        return $this;
    }

    /**
     * Get the value of texttosend.
     */
    public function get_text_to_send() {
        return $this->texttosend;
    }
}
