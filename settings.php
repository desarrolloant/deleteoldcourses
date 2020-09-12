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
 * Add page to admin menu.
 *
 * @package	local_deleteoldcourses
 * @author  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @since   Moodle 3.6.6
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $pluginname = get_string('pluginname', 'local_deleteoldcourses');

    $ADMIN->add('courses', new admin_externalpage('local_deleteoldcourses',
            $pluginname,
            new moodle_url('/local/deleteoldcourses/report.php'),
            "local/deleteoldcourses:viewreport"));

    $settings = new admin_settingpage('local_deleteoldcourses_settings', $pluginname);
    $ADMIN->add('localplugins', $settings);

    $configs = array();

    $configs[] = new admin_setting_heading('local_deleteoldcourses', get_string('settings'), '');

    // Put all settings into the settings page.
    foreach ($configs as $config) {
        $config->plugin = 'local_deleteoldcourses';
        $settings->add($config);
    }
}