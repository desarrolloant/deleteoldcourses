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
 * Unit tests for cvh_ws_client class.
 *
 * @package     local_deleteoldcourses
 * @category    PHPUnit
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use moodle_exception;

/**
 * Unit tests for cvh_ws_client class.
 *
 * @package     local_deleteoldcourses
 * @category    PHPUnit
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cvh_ws_client_test extends \advanced_testcase {

    /** @var cvh_ws_client Object client for connection to Campus Virtual Historia */
    private cvh_ws_client $cvhwsclient;

    /**
     * Test CVH WS Client class instantiation.
     *
     * @covers  ::construct
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function test_cvh_wsclient_default(): void {

        $this->resetAfterTest(true);

        $this->set_cvhwsclient();

        $cvhwsclient = $this->get_cvhwsclient();
        $this->assertInstanceOf(cvh_ws_client::class, $this->get_cvhwsclient());
        $this->assertSame('json', $cvhwsclient->get_returnformat());

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: invalid request method.');
        $this->set_cvhwsclient('dummy');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: invalid return format.');
        $this->set_cvhwsclient('get', 'dummy');
    }

    /**
     * Test CVH Client class instantiation with 1 parameter.
     *
     * @covers  ::construct
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function test_cvh_wsclient_one_parameter(): void {

        $this->resetAfterTest(true);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: invalid request method.');
        $this->set_cvhwsclient('dummy');
    }

    /**
     * Test CVH Client class instantiation with 2 parameter.
     *
     * @covers  ::construct
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function test_cvh_wsclient_two_parameters(): void {

        $this->resetAfterTest(true);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: invalid return format.');
        $this->set_cvhwsclient('get', 'dummy');
    }

    /**
     * Test request to service function.
     *
     * @covers  ::request_to_service
     * @author  2022 Iader E. Garcia Gomez <iadergg@gmail.com>
     */
    public function test_request_to_service(): void {

        $this->resetAfterTest(true);

        $this->set_cvhwsclient();

        $this->assertInstanceOf(cvh_ws_client::class, $this->get_cvhwsclient());

        $wsfunction = get_config('local_deleteoldcourses', 'ws_function_name');

        $parameters = array(
            'idnumber' => '01-201238M-50-202011051'
        );

        $response = $this->get_cvhwsclient()->request_to_service($wsfunction, $parameters);

        if (empty($response)) {
            $parameters = array('shortname' => '01-201238M-50-202011051');
            $response = $this->get_cvhwsclient()->request_to_service($wsfunction, $parameters);

            if (empty($response)) {
                $this->assertJson($response);
            }
        }

        $this->assertJson($response);

        $parameters = array(
            'idnumber' => 'dummy'
        );

        $response = $this->get_cvhwsclient()->request_to_service($wsfunction, $parameters);

        if (empty($response)) {
            $parameters = array('shortname' => '01-201238M-50-202011051');
            $response = $this->get_cvhwsclient()->request_to_service($wsfunction, $parameters);

            if (empty($response)) {
                $this->assertJson($response);
            }
        }

        $this->assertJson($response);
    }

    /**
     * Get cvhwsclient instance.
     *
     * @return  cvh_ws_client
     */
    public function get_cvhwsclient(): cvh_ws_client {
        return $this->cvhwsclient;
    }

    /**
     * Set instance of cvhwsclient.
     *
     * @param   string $method Method for the request.
     * @param   string $returnformat Reponse format. Default JSON.
     * @return  cvh_ws_client_test
     */
    public function set_cvhwsclient($method = null, $returnformat = null): cvh_ws_client_test {

        if ($method) {
            $this->cvhwsclient = new cvh_ws_client($method);

            if ($returnformat) {
                $this->cvhwsclient = new cvh_ws_client($method, $returnformat);
            }
        } else {
            $this->cvhwsclient = new cvh_ws_client();
        }

        return $this;
    }

    /**
     * Set up the test environment.
     */
    protected function setUp(): void {

        $configgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        $configgenerator->update_setting('ws_url',
                          'https://campusvirtualhistoria.univalle.edu.co/moodle');
        $configgenerator->update_setting('ws_user_token',
                          'de4549d7a1d8aaa27ed4abfb213339f1');
        $configgenerator->update_setting('ws_function_name', 'core_course_get_courses_by_field');
    }
}
