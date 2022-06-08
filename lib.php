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
 * This file serves as the interface between Moodle core and the plugin.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

defined('MOODLE_INTERNAL') || die;


/**
 * Add a link into the navigation drawer.
 *
 * @package  local_deleteolcourses
 * @param    global_navigation $navigation Node representing the global navigation tree
 */
function local_deleteoldcourses_extend_navigation(global_navigation $navigation) {

    global $PAGE, $USER;

    // Check if this user is not student -> only for univalle users.
    $username = $USER->username;
    if (strpos($username, '-') !== false) {
        return;
    }

    // Show only in dasboard page.
    if ($PAGE->has_set_url()) {
        if (!$PAGE->url->compare(new moodle_url('/my/'), URL_MATCH_BASE)) {
            return;
        }
    }

    $pluginname = get_string('pluginname', 'local_deleteoldcourses');
    $action = new moodle_url('/local/deleteoldcourses/index.php', array());
    $type = global_navigation::TYPE_CUSTOM;
    $shorttext = 'deleteoldcourses';
    $key = 'deleteoldcourses';
    $icon = new pix_icon('i/trash', '');

    $show_node = false;
    $show_alert = false;
    $total = 0;

    if (has_capability('local/deleteoldcourses:viewreport', context_system::instance())) {
        $action = new moodle_url('/local/deleteoldcourses/report.php', array());
        $show_node = true;
    } else {
        $now = time();
        $total = user_count_courses($USER->id, $now);
        $show_alert = true;
    }

    if ($total > 0) {
        $show_node = true;
    }

    $node = navigation_node::create(
    $pluginname,
    $action,
    $type,
    $shorttext,
    $key,
    $icon
    );

    if ($show_node) {
        if ($show_alert) {
            $PAGE->requires->js_call_amd('local_deleteoldcourses/show_alert', 'init', array(
            'link' => $action->out(false),
            'str_content' => get_string('alert_delete_content', 'local_deleteoldcourses'),
            'str_link' => get_string('delete_courses', 'local_deleteoldcourses')
            ));
        }
        $deleteoldcourses = $navigation->add_node($node);
        $deleteoldcourses->showinflatnavigation = true;
    }

}

/**
 * Helper function to reset the icon system used as updatecallback function when saving some of the plugin's settings.
 */
function local_deleteoldcourses_reset_fontawesome_icon_map() {
    // Reset the icon system cache.
    // There is the function \core\output\icon_system::reset_caches() which does seem to be only usable in unit tests.
    // Thus, we clear the icon system cache brutally.
    $instance = \core\output\icon_system::instance(\core\output\icon_system::FONTAWESOME);
    $cache = \cache::make('core', 'fontawesomeiconmapping');
    $mapkey = 'mapping_'.preg_replace('/[^a-zA-Z0-9_]/', '_', get_class($instance));
    $cache->delete($mapkey);
    // And rebuild it brutally.
    $instance->get_icon_name_map();
}
