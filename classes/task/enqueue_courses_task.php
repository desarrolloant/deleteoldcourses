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
 * @package     local_deleteoldcourses
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses\task;

use local_deleteoldcourses\course_enqueuer;
use local_deleteoldcourses\datetime_manager;

defined('MOODLE_INTERNAL') || die();

/**
 * Enqueue courses task.
 *
 * @package     local_deleteoldcourses
 * @author      2022 Iader E. García Gómez <iadergg@gmail.com>
 * @copyright   2022 Área de Nuevas Tecnologías - DINTEV - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class enqueue_courses_task extends \core\task\scheduled_task {

    /**
     * Return the name of the component.
     *
     * @return  string The name of the component.
     */
    public function get_component() {
        return 'local_deleteoldcourses';
    }

    /**
     * Return the task's name as shown in admin screens.
     *
     * @return  string
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

        // Plugin settings.
        $datetimemanager = new datetime_manager();
        $timecreatedcriteria = $datetimemanager->date_config_to_timestamp('creation');
        $timemodificationcriteria = $datetimemanager->date_config_to_timestamp('last_modification');
        $limitquerytoenqueuecourses = get_config('local_deleteoldcourses', 'limit_query_to_enqueue_courses');

        $numbercategoriesexcluded = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');
        $categoriesexcluded = array();

        for ($i = 1; $i < $numbercategoriesexcluded + 1; $i++) {
            array_push($categoriesexcluded, get_config('local_deleteoldcourses', 'excluded_course_categories_' . $i));
        }

        $coursedispatcher = new course_enqueuer();
        $coursedispatcher->get_courses_to_enqueue(0, $timecreatedcriteria, $timemodificationcriteria, $limitquerytoenqueuecourses, $categoriesexcluded);

        // Enqueue courses completed.
        mtrace("\n" . 'Cron completed at: ' . date('r', time()) . "\n");
        mtrace('Memory used: ' . display_size(memory_get_usage())."\n");
        $difftime = microtime_diff($starttime, microtime());
        mtrace("Scheduled task took " . $difftime . " seconds to finish.\n");
    }
}
