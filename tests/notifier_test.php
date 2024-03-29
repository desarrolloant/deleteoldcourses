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
 * @package     local_deleteoldcourses
 * @category    PHPUnit
 * @author      2022 Juan Felipe Orozco Escobar <juanfe.ores@gmail.com>
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

/**
 * Unit tests for notifier class.
 *
 * @package     local_deleteoldcourses
 * @category    PHPUnit
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @author      2022 Juan Felipe Orozco <juanfe.ores@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notifier_test extends \advanced_testcase {

    /**
     * Test for text generation for a notification.
     *
     * @covers  ::generate_text_to_send
     */
    public function test_generate_text_to_send() {

        $this->resetAfterTest(true);

        // TO DO
        // Mejorar la prueba con la cantidad de cursos borrados y a borrar.

        $notifier = new notifier();
        $this->assertInstanceOf(notifier::class, $notifier);

        $messagetosend = $notifier->generate_text_to_send();
        $this->assertIsString($messagetosend);

        $expectedmessage = 'The delete courses UV plugin has detected that there are still pending courses to be deleted. \n';
        $expectedmessage .= 'Summary: \n';
        $expectedmessage .= '<pre>';
        $expectedmessage .= '- Number of deleted courses: 2';
        $expectedmessage .= '- Number of pending courses to delete: 3';
        $expectedmessage .= '</pre>';
        $expectedmessage .= 'This message has been generated automatically, <b>please do not reply</b> to this message.';

        $this->assertSame($expectedmessage, $messagetosend);
    }

    /**
     * Test case for sending notification.
     *
     * @covers  ::send_notification
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
