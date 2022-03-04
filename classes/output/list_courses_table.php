<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderers.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
 * Class for the displaying the courses table.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.6.6
 * @author  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class list_courses_table extends \table_sql implements renderable {

    /**
     * @var int $userid The course id
     */
    protected $userid;

    /**
     * @var int $ago The number of years for created ago
     */
    protected $ago;

    /**
     * @var string $search The string being searched.
     */
    protected $search;

    /**
     * @var bool $selectall Has the user selected all users on the page?
     */
    protected $selectall;





    /**
     * Sets up the table.
     *
     * @param int $userid
     * @param bool $selectall Has the user selected all users on the page?
     */
    public function __construct($userid, $ago) {
        global $CFG;

        parent::__construct('user-old-courses-' .'user-id');

        $this->userid = $userid;

        $this->ago = $ago;

        // Define the headers and columns.
        $headers = [];
        $columns = [];

        $headers[] = get_string('course_shortname', 'local_deleteoldcourses');
        $columns[] = 'c_shortname';

        $headers[] = get_string('course_fullname', 'local_deleteoldcourses');
        $columns[] = 'c_fullname';

        $headers[] = get_string('user_username', 'local_deleteoldcourses');
        $columns[] = 'u_username';

        $headers[] = get_string('user_fullname', 'local_deleteoldcourses');
        $columns[] = 'u_fullname';

        $headers[] = get_string('course_datecreation', 'local_deleteoldcourses');
        $columns[] = 'c_timecreated';

        $headers[] = get_string('table_option', 'local_deleteoldcourses');
        $columns[] = 'table_option';

        $this->define_columns($columns);
        $this->define_headers($headers);

        // The name column is a header.
        // $this->define_header_column('c_fullname');

        // Make this table sorted by last name by default.
        $this->sortable(true, 'c_shortname');

        $this->no_sorting('u_username');
        $this->no_sorting('u_fullname');
        $this->no_sorting('table_option');

        $this->set_attribute('id', 'courses');

        /*
        // Set the variables we need to use later.
        $this->search = $search;
        $this->selectall = $selectall;*/
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
        global $CFG;
        $courselink = $CFG->wwwroot . "/course/view.php?id=" . $data->id;
        return '<a href="'.$courselink.'" target="_blank">'.$data->c_fullname.'</a>';
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
    public function col_user_fullname($data) {
        return $data->u_fullname;
    }

    /**
     * Generate the date column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_c_timecreated($data) {
        if ($data->c_timecreated) {
            return format_time(time() - $data->c_timecreated);
        }
        return '-------';
    }

    /**
     * Generate the option column.
     *
     * @param \stdClass $data
     * @return string
     */
    public function col_table_option($data) {
        $class = 'btn btn-primary add-course';
        $icon = '<i class="fa fa-trash" aria-hidden="true"></i>';
        if (course_in_delete_list($data->id)) {
            $class = 'btn btn-danger remove-course';
            $icon = '<i class="fa fa-check" aria-hidden="true"></i>';
        }
        return '<button
                    class="'.$class.'"
                    course-id="'.$data->id.'"
                    action="delete">
                    '.$icon.'
                </button>';
    }

    /**
     * Query the database for results to display in the table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        // list($twhere, $tparams) = $this->get_sql_where();

        $now = time();

        $total = user_count_courses($this->userid, $now, $this->ago);

        $this->pagesize($pagesize, $total);

        $sort = $this->get_sql_sort();
        if ($sort) {
            $sort = 'ORDER BY ' . $sort;
        }

        $rawdata = user_get_courses($this->userid, $sort, $this->get_page_start(), $this->get_page_size(), $now, $this->ago);
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

