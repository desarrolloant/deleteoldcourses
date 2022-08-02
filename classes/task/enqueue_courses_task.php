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
 * Enqueue courses task.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses\task;

use local_deleteoldcourses\course_dispatcher;

defined('MOODLE_INTERNAL') || die();

/**
 * Enqueue courses task.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enqueue_courses_task extends \core\task\scheduled_task {

    /**
     * Return the name of the component.
     *
     * @return string The name of the component.
     */
    public function get_component() {
        return 'local_deleteoldcourses';
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('enqueue_courses_task', 'local_deleteoldcourses');
    }

    /**
     * Execute the task.
     */
    public function execute() {

        $timenow = time();
        $starttime = microtime();

        mtrace("Update cron started at: " . date('r', $timenow) . "\n");

        $coursedispatcher = new course_dispatcher();
        $coursedispatcher->get_courses_to_delete();

        // Enqueue courses completed.
        mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n");
        mtrace('Memory used: ' . display_size(memory_get_usage())."\n");
        $difftime = microtime_diff($starttime, microtime());
        mtrace("Scheduled task took " . $difftime . " seconds to finish.\n");
    }
}
