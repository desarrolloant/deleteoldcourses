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
     */
    public function test_cvh_client() {

        $this->set_cvhclient();

        $cvhclient = $this->get_cvhclient();
        $this->assertInstanceOf(CVH_client::class, $this->get_cvhclient());
        $this->assertSame('json', $cvhclient->get_returnformat());

        $this->expectException(moodle_exception::class);
        $this->expectExceptionMessage('Invalid return format.');
        $this->set_cvhclient('dummy');
    }

    /**
     * Test request function.
     *
     * @since Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::check_course
     */
    public function test_request() {

        $this->set_cvhclient();

        $this->assertInstanceOf(CVH_client::class, $this->get_cvhclient());

        // This course exists in Campus Virtual Historia.
        $course1 = $this->getDataGenerator()->create_course();
        $checkresult = $this->cvhclient->request();
        $this->assertIsBool($checkresult);
        $this->assertSame(true, $checkresult);

        // This course not exists in Campus Virtual Historia.
        $course2 = $this->getDataGenerator()->create_course();
        $checkresult = $this->cvhclient->request();
        $this->assertIsBool($checkresult);
        $this->assertSame(false, $checkresult);
    }

    /**
     * Get cvhclient instance
     *
     * @return CVH_client
     * @since Moodle 3.10
     */
    public function get_cvhclient() {
        return $this->cvhclient;
    }

    /**
     * Set instance of cvhclient
     *
     * @param string $returnformat Reponse format. Default JSON.
     * @return CVH_client_test
     * @since Moodle 3.10
     */
    public function set_cvhclient($returnformat = null) {

        if ($returnformat) {
            $this->cvhclient = new CVH_client($returnformat);
        } else {
            $this->cvhclient = new CVH_client();
        }

        return $this;
    }

    protected function setUp(): void
    {

    }
}
