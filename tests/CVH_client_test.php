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
 * Unit tests for CVH_client class.
 *
 * @package    local_deleteoldcourses
 * @category   phpunit
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

class CVH_client_test extends \advanced_testcase {

    /** @var CVH_client Object client for connection to Campus Virtual Historia */
    private CVH_client $cvhclient;

    /**
     * Test CVH Client class instantiation.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::construct
     */
    public function test_cvh_client_default(): void {

        $this->resetAfterTest(true);

        $this->set_cvhclient();

        $cvhclient = $this->get_cvhclient();
        $this->assertInstanceOf(CVH_client::class, $this->get_cvhclient());
        $this->assertSame('json', $cvhclient->get_returnformat());

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: Request method invalid.');
        $this->set_cvhclient('dummy');

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: Invalid return format.');
        $this->set_cvhclient('get', 'dummy');
    }

    /**
     * Test CVH Client class instantiation with 1 parameter.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::construct
     */
    public function test_cvh_client_one_parameter(): void {

        $this->resetAfterTest(true);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: Request method invalid.');
        $this->set_cvhclient('dummy');
    }

    /**
     * Test CVH Client class instantiation with 2 parameter.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::construct
     */
    public function test_cvh_client_two_parameters(): void {

        $this->resetAfterTest(true);

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('CVH Client: Invalid return format.');
        $this->set_cvhclient('get', 'dummy');
    }

    /**
     * Test request function.
     *
     * @return void
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::request
     */
    public function test_request(): void {

        $this->resetAfterTest(true);

        $this->set_cvhclient();

        $this->assertInstanceOf(CVH_client::class, $this->get_cvhclient());

        $function = get_config('local_deleteoldcourses', 'function_name');

        $parameters = array(
            'shortname' => '01-201238M-50-202011051'
        );

        $response = $this->get_cvhclient()->request($function, $parameters);

        $this->assertJson($response);

        $parameters = array(
            'shortname' => 'dummy'
        );

        $response = $this->get_cvhclient()->request($function, $parameters);

        $this->assertJson($response);
    }

    /**
     * Get cvhclient instance
     *
     * @return CVH_client
     * @since Moodle 3.10
     */
    public function get_cvhclient(): CVH_client {
        return $this->cvhclient;
    }

    /**
     * Set instance of cvhclient
     *
     * @param string $returnformat Reponse format. Default JSON.
     * @return CVH_client_test
     * @since Moodle 3.10
     */
    public function set_cvhclient($method = null, $returnformat = null): CVH_client_test {

        if ($method) {
            $this->cvhclient = new CVH_client($method);

            if ($returnformat) {
                $this->cvhclient = new CVH_client($method, $returnformat);
            }
        } else {
            $this->cvhclient = new CVH_client();
        }

        return $this;
    }

    /**
     * Set up the test environment.
     *
     * @return void
     * @since Moodle 3.10
     */
    protected function setUp(): void {

        $configgenerator = $this->getDataGenerator()->get_plugin_generator('local_deleteoldcourses');

        $configgenerator->update_setting('url_to_service',
                          'https://campusvirtualhistoria.univalle.edu.co/moodle/webservice/rest/server.php');
        $configgenerator->update_setting('token_user',
                          'de4549d7a1d8aaa27ed4abfb213339f1');
        $configgenerator->update_setting('function_name', 'core_course_get_courses_by_field');
    }
}
