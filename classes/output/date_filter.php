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

namespace local_deleteoldcourses\output;

use moodle_url;
use renderable;
use renderer_base;
use stdClass;
use templatable;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing the filter options data for rendering the date filter element for the old courses page.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class date_filter implements renderable, templatable {

    /** @var array $filteroptions The filter options. */
    protected $filteroptions;

    /** @var int $selectedoption The selected filter option value. */
    protected $selectedoption;

    /** @var moodle_url|string $baseurl The url with params needed to call up this page. */
    protected $baseurl;

    /**
     * date_filter constructor.
     *
     * @param array $filteroptions The filter options.
     * @param int $selectedoption The selected filter option value.
     * @param string|moodle_url $baseurl The url with params needed to call up this page.
     */
    public function __construct($filteroptions, $selectedoption, $baseurl = null) {
        $this->filteroptions = $filteroptions;
        $this->selectedoption = $selectedoption;
        if (!empty($baseurl)) {
            $this->baseurl = new moodle_url($baseurl);
        }

    }

    /**
     * Function to export the renderer data in a format that is suitable for a mustache template.
     *
     * @param renderer_base $output Used to do a final render of any components that need to be rendered for export
     * @return stdClass|array
     */
    public function export_for_template(renderer_base $output) {
        global $PAGE;
        $data = new stdClass();
        if (empty($this->baseurl)) {
            $this->baseurl = $PAGE->url;
        }

        $data->action = $this->baseurl->out(false);

        if ($this->baseurl->get_param('userid')) {
            $data->userid = $this->baseurl->get_param('userid');
        }

        if ($this->baseurl->get_param('action')) {
            $data->action_page = $this->baseurl->get_param('action');
        }

        if (!isset($this->filteroptions[$this->selectedoption])) {
            $this->filteroptions[$this->selectedoption] = 0;
        }

        $data->filteroptions = [];
        $originalfilteroptions = [];
        foreach ($this->filteroptions as $value => $label) {
            $selected = ($value == $this->selectedoption);
            $filteroption = (object)[
                'value' => $value,
                'label' => $label
            ];
            $originalfilteroptions[] = $filteroption;
            $filteroption->selected = $selected;
            $data->filteroptions[] = $filteroption;
        }
        $data->originaloptionsjson = json_encode($originalfilteroptions);
        return $data;
    }
}
