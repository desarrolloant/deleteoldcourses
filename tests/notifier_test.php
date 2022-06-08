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
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use advanced_testcase;
use moodle_exception;
use stdClass;

defined('MOODLE_INTERNAL') || die();

class local_deleteoldcourses_notifier_tests extends advanced_testcase {

    /**
     * Test for text generation for a notification.
     *
     * @return void
     */
    public function test_generate_text_to_send() {

        $this->resetAfterTest(true);

        global $CFG;

        $user1 = $this->getDataGenerator()->create_user(array('email'=>'user1@example.com', 'username'=>'desadmin2021'));
        $user2 = $this->getDataGenerator()->create_user(array('email'=>'user2@example.com', 'username'=>'desadmin2022'));

        // Los usuarios vienen en forma de arreglo de nombres de usuario.
        $userstonotify = array('desadmin2021', 'desadmin2022');

        $this->resetAfterTest(true);

        $notifier = new notifier();
        $this->assertInstanceOf(notifier::class, $notifier);

        $messagetosend = $notifier->generate_text_to_send();
        $this->assertIsString($messagetosend);
    }
}
