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
 * Renderers.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses\output;

use renderable;
use context;
use DateTime;

defined('MOODLE_INTERNAL') || die;

global $CFG;

require_once($CFG->libdir . '/tablelib.php');
require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

/**
 * Class for displaying the courses table.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_pending_table extends \table_sql implements renderable {

    /**
     * Sets up the table.
     *
     * @param bool $selectall Has the user selected all users on the page?
     */
    public function __construct() {
        global $CFG;

        parent::__construct('pending-courses');

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('course_shortname', 'local_deleteoldcourses');
        $columns[] = 'c_shortname';

        $headers[] = get_string('course_fullname', 'local_deleteoldcourses');
        $columns[] = 'c_fullname';

        $headers[] = get_string('username', 'moodle');
        $columns[] = 'u_username';

        $headers[] = get_string('user_fullname', 'local_deleteoldcourses');
        $columns[] = 'u_fullname';

        $headers[] = get_string('sent_to_delete', 'local_deleteoldcourses');
        $columns[] = 'c_timesenttodelete';

        $this->define_columns($columns);
        $this->define_headers($headers);

        // The name column is a header.
        // $this->define_header_column('c_fullname');

        // Make this table sorted by last name by default.
        $this->sortable(true, 'c_timedeleted', SORT_DESC);

        $this->set_attribute('id', 'courses');
    }

    /**
     * Render theold  courses table.
     *
     * @param int $pagesize Size of page for paginated displayed table.
     * @param bool $useinitialsbar Whether to use the initials bar which will only be used if there is a fullname column defined.
     * @param string $downloadhelpbutton
     */
    public function out($pagesize, $useinitialsbar, $downloadhelpbutton = '') {
        global $PAGE;

        parent::out($pagesize, $useinitialsbar, $downloadhelpbutton);
    }

    /**
     * Generate the code column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_c_shortname($data) {
        return $data->c_shortname;
    }

    /**
     * Generate the name column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_c_fullname($data) {
        $url = new \moodle_url('/course/view.php', array('id' => $data->courseid));
        return '<a href="'.$url->out(false).'" target="_blank">'.$data->c_fullname.'</a>';
    }

    /**
     * Generate the identification column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_u_username($data) {
        return $data->u_username;
    }

    /**
     * Generate the fullname column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_u_fullname($data) {
        $url = new \moodle_url('/user/profile.php', array('id' => $data->userid));
        return '<a href="'.$url->out(false).'" target="_blank">'.$data->u_fullname.'</a>';
    }

    /**
     * Generate the date column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_c_timesenttodelete($data) {
        if ($data->c_timesenttodelete) {
            $dateformat = get_string('strftimedatetime', 'core_langconfig');
            // $dateformat = get_string('strftimedatetimeshort', 'core_langconfig');
            return userdate($data->c_timesenttodelete, $dateformat);
        }
        return '-------';
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        // list($twhere, $tparams) = $this->get_sql_where();

        $total = count_pending_courses();

        $this->pagesize($pagesize, $total);

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = 'ORDER BY ' . $sort;
        }

        $rawdata = get_pending_courses($sort, $this->get_page_start(), $this->get_page_size());
        $this->rawdata = [];
        foreach ($rawdata as $course) {
            $this->rawdata[$course->id] = $course;
        }

        // Set initial bars.
        if ($useinitialsbar) {
            $this->initialbars(true);
        }
    }
}
