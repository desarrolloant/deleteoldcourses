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
 * Version information for deletecourses.
 *
 * @package	local_deleteoldcourses
 * @author 	2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

/**
 * deleteoldcourses local renderer
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

	/**
     * Html to show number of courses.
     *
     * @param int $number_of_courses Total courses.
     * @return string html for show total courses.
     */
	public function render_number_of_courses($number_of_courses){
		$o = \html_writer::tag('p', get_string('coursescount', 'local_deleteoldcourses').$number_of_courses);
		return $o;
	}

	/**
     * Html to show old courses table of a teacher.
     *
     * @param list_courses_table $renderable The courses table.
     * @param int $perpage Number of courses per page.
     * @return string html for the old courses table.
     */
	public function render_courses_table(list_courses_table $renderable, $perpage) {
		ob_start();
        $renderable->out($perpage, true);
        $o = ob_get_contents();
        ob_end_clean();
        return $o;
	}

    /**
     * Html to show a all rows in old courses table.
     *
     * @param moodle_url $perpageurl The url of course list page with $perpage parameter.
     * @param int $page_size Number of courses to show in a page.
     * @param int $number_of_courses Total Number of courses.
     * @param int $perpage Variable number of courses to show in a page.
     * @return string html for display the link of show all courses.
     */
	public function render_courses_show_all_link(\moodle_url $perpageurl, $page_size, $number_of_courses, $perpage) {
		$perpageurl->remove_params('perpage');
        if ($perpage == SHOW_ALL_PAGE_SIZE && $number_of_courses > DEFAULT_PAGE_SIZE) {
            $perpageurl->param('perpage', DEFAULT_PAGE_SIZE);
            return $this->container(\html_writer::link($perpageurl, get_string('showperpage', '', DEFAULT_PAGE_SIZE)), array(), 'showall');

        } else if ($page_size < $number_of_courses) {
            $perpageurl->param('perpage', SHOW_ALL_PAGE_SIZE);
            return $this->container(\html_writer::link($perpageurl, get_string('showall', '', $number_of_courses)),
                array(), 'showall');
        }
	}

    /**
     * Html render page scripts.
     *
     * @param string $page Name of required page eg:page_list_courses.
     * @return string html for display the page scripts.
     */
    public function render_scripts($page){
        return $this->render_from_template('local_deleteoldcourses/course_options', []);
    }


}
