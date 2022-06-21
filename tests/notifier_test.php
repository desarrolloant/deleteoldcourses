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

        // TO DO
        // Mejorar la prueba con la cantidad de cursos borrados y a borrar.

        $notifier = new notifier();
        $this->assertInstanceOf(notifier::class, $notifier);

        $messagetosend = $notifier->generate_text_to_send();
        $this->assertIsString($messagetosend);

        $expectedmessage = 'El módulo de eliminación de cursos ha detectado que aún quedan cursos pendientes por eliminar. \n';
        $expectedmessage .= 'Resumen de la ejecución: \n';
        $expectedmessage .= '<pre>';
        $expectedmessage .= '- Cantidad de cursos borrados: 2';
        $expectedmessage .= '- Cantidad de cursos pendientes: 3';
        $expectedmessage .= '</pre>';
        $expectedmessage .= 'Este mensaje ha sido generado automáticamente, <b>por favor no responda</b> a este mensaje.';

        $this->assertSame($expectedmessage, $messagetosend);
    }

    /**
     * Test case for sending notification.
     *
     * @return void
     */
    public function test_send_notification() {

        $this->resetAfterTest(true);

        // Creating users.
        $user1 = $this->getDataGenerator()->create_user(array('email' => 'user1@example.com', 'username' => 'desadmin2021'));
        $user2 = $this->getDataGenerator()->create_user(array('email' => 'user2@example.com', 'username' => 'desadmin2022'));

        // Call to plugin generator.
        $testgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        $userstonotifysetting = $testgenerator->update_setting('users_to_notify', 'desadmin2021, desadmin2022');

        $notifier = new notifier();

        $userstonotify = $notifier->get_userstonotify();

        $this->assertInstanceOf(notifier::class, $notifier);
        $this->assertIsString($notifier->get_text_to_send());
        $this->assertIsArray($userstonotify);
        $this->assertSame(2, count($userstonotify));
        $this->assertSame($userstonotify[0]->username, 'desadmin2021');
        $this->assertSame($userstonotify[1]->username, 'desadmin2022');

        unset_config('noemailever');
        $sink = $this->redirectEmails();

        $notifier->send_notification();

        $messages = $sink->get_messages();
        $this->assertEquals(2, count($messages));
    }
}
