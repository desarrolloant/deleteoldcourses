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
 * Implement Campus Virtual Historia Client
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Campus Virtual Historia Client
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 */
class CVH_client {

    /**
     * The constant that defines the JSON return format
     * @access public
     */
    const RETURN_JSON = 'json';

    /**
     * The constant that defines the request method using GET
     * @access public
     */
    const METHOD_GET = 'get';

    /** @var string URL to Campus Virtual Historia services */
    private $urltoservice;

    /** @var string User token to access services */
    private $usertoken;

    /** @var string User token to access services */
    private $method;

    /** @var string Return format */
    private $returnformat;

    /**
     * Constructor
     *
     * @param string $method Method used in the request.
     * @param string $returntoformat Response format. Default JSON.
     */
    public function __construct($method = self::METHOD_GET, $returnformat = self::RETURN_JSON) {
        $this->urltoservice = get_config('local_deleteoldcourses', 'url_to_service');
        $this->usertoken = get_config('local_deleteoldcourses', 'token_user');
        $this->method = $method;

        if (!is_null($returnformat) && $returnformat <> 'json' && $returnformat <> 'xml' && $returnformat <> 'array') {
            throw new moodle_exception('invalid_format', 'local_deleteoldcourses');
        }

        $this->returnformat = $returnformat;
    }

    /**
     * Get the value of return.
     *
     * @return int $usertoken
     * @since  Moodle 3.10
     */
    public function get_returnformat() {
        return $this->returnformat;
    }

    /**
     * Make the request
     *
     * @param string $function Function name that will be used by the service.
     * @param array $parameters Parameters array.
     * @param $method Request method
     * @return mixed $response
     */
    public function request($function = '', $parameters = null, $method = self::METHOD_GET) {

        if (empty($this->urltoservice)) {
            throw new moodle_exception('empty_url_to_service', 'local_deleteoldcourses');
        }

        if (empty($this->usertoken)) {
            throw new moodle_exception('empty_user_token', 'local_deleteoldcourses');
        }

        if (empty($this->returnformat)) {
            throw new moodle_exception('empty_return_format', 'local_deleteoldcourses');
        }

        if (!is_null($parameters)) {
            if (!is_array($parameters)) {
                throw new moodle_exception('clientcvh_invalid_parameters', 'local_deleteoldcourses');
            }
        }

        $function = get_config('local_delelteoldcourses', 'function_name');

        if (empty($function)) {
            print_r('test');
        }

        $moodlerequest = file_get_contents();

    }
}
