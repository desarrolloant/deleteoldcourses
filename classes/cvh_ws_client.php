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
 * Implement Campus Virtual Historia Web Service Client.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

use moodle_exception;

defined('MOODLE_INTERNAL') || die();

/**
 * Campus Virtual Historia Web Service Client.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 */
class cvh_ws_client {

    /**
     * The constant that defines the JSON return format.
     * @access  public
     */
    const RETURN_JSON = 'json';

    /**
     * The constant that defines the request method using GET.
     * @access  public
     */
    const METHOD_GET = 'get';

    /**
     * The constant that defines the request method using POST.
     * @access  public
     */
    const METHOD_POST = 'post';

    /**
     * The constant that defines the path to services in Moodle.
     * @access  public
     */
    const PATH_TO_SERVICES = '/webservice/rest/server.php';

    /** @var string URL to Campus Virtual Historia web services */
    private $wsurl;

    /** @var string User token to access services */
    private $wsusertoken;

    /** @var string Return format */
    private $returnformat;

    /**
     * Constructor.
     *
     * @param   string $method Method used in the request.
     * @param   string $returntoformat Response format. Default JSON.
     */
    public function __construct($method = self::METHOD_GET, $returnformat = self::RETURN_JSON) {
        $this->wsurl = get_config('local_deleteoldcourses', 'ws_url');
        $this->wsusertoken = get_config('local_deleteoldcourses', 'ws_user_token');

        if (!is_null($method) && $method <> self::METHOD_GET && $method <> self::METHOD_POST) {
            throw new moodle_exception('request_method_invalid', 'local_deleteoldcourses');
        }

        $this->method = $method;

        if (!is_null($returnformat) && $returnformat <> 'json' && $returnformat <> 'xml' && $returnformat <> 'array') {
            throw new moodle_exception('invalid_return_format', 'local_deleteoldcourses');
        }

        $this->returnformat = $returnformat;
    }

    /**
     * Get the value of return format.
     *
     * @return  string $returnformat
     */
    public function get_returnformat() {
        return $this->returnformat;
    }

    /**
     * Make the request to service
     *
     * @param   string $function Function name that will be used by the service.
     * @param   array $parameters Parameters array. field => value.
     *                          Example:
     *                                  [
     *                                    'shortname' => '01-201238M-50-202011051'
     *                                  ]
     * @param   $method Request method
     * @return  string $result
     */
    public function request_to_service($function = '', $parameters = null, $method = self::METHOD_GET) {

        if (empty($this->wsurl)) {
            throw new moodle_exception('empty_ws_url', 'local_deleteoldcourses');
        }

        if (empty($this->wsusertoken)) {
            throw new moodle_exception('empty_ws_user_token', 'local_deleteoldcourses');
        }

        if (empty($this->returnformat)) {
            throw new moodle_exception('empty_return_format', 'local_deleteoldcourses');
        }

        if (!is_null($parameters)) {
            if (!is_array($parameters)) {
                throw new moodle_exception('clientcvh_invalid_parameters', 'local_deleteoldcourses');
            }
        }

        if (empty($function)) {
            throw new moodle_exception('empty_ws_function_name', 'local_deleteoldcourses');
        }

        $requesturl = $this->wsurl .
                      self::PATH_TO_SERVICES .
                      '?wstoken=' . urlencode($this->wsusertoken) .
                      '&wsfunction=' . urlencode($function) .
                      '&moodlewsrestformat=' . urlencode($this->returnformat);

        foreach ($parameters as $key => $parameter) {
            $requesturl .= '&field=' . urlencode($key);
            $requesturl .= '&value=' . urlencode($parameter);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requesturl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $moodleresponse = curl_exec($curl);

        curl_exec($curl);

        if (!$moodleresponse) {
            throw new moodle_exception('request_error', 'local_deleteoldcourses');
        }

        return $moodleresponse;
    }
}
