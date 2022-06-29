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
 * Add page to admin menu.
 *
 * @package    local_deleteoldcourses
 * @copyright  2020 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @author     Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/deleteoldcourses/locallib.php');

$ADMIN->add('localplugins',
        new admin_category('localdeleteoldcoursessettings',
        new lang_string('pluginname', 'local_deleteoldcourses')));

// Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
$settings = new theme_boost_admin_settingspage_tabs('managelocaldeleteoldcourses',
                                                    get_string('manage', 'local_deleteoldcourses'));

if ($ADMIN->fulltree) {

    // First settings tab.
    $settingspage = new admin_settingpage('deletioncriterias', new lang_string('criteriatab', 'local_deleteoldcourses'));

    $datetimemanager = new \local_deleteoldcourses\datetime_manager;

    $years = $datetimemanager->get_datetime('years');
    $monthsoftheyear = $datetimemanager->get_datetime('monthsoftheyear');
    $daysofthemonth = $datetimemanager->get_datetime('daysofthemonth');
    $hoursinaday = $datetimemanager->get_datetime('hoursinaday');
    $minutesinanhour = $datetimemanager->get_datetime('minutesinanhour'); // Also used in secondsstartdate (seconds in a minute).

    // Criteria to courses creation date.
    $settingspage->add(new admin_setting_heading(
        'local_deleteoldcourses/courses_creation_date_criteria_heading',
        new lang_string('courses_creation_date_criteria_heading', 'local_deleteoldcourses'),
        new lang_string('courses_creation_date_criteria_heading_desc', 'local_deleteoldcourses')));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/year_creation_date',
        new lang_string('year', 'local_deleteoldcourses'),
        new lang_string('year_creation_date_desc', 'local_deleteoldcourses'),
        2010,
        $years
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/month_creation_date',
        new lang_string('month', 'local_deleteoldcourses'),
        new lang_string('month_creation_date_desc', 'local_deleteoldcourses'),
        12,
        $monthsoftheyear
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/day_creation_date',
        new lang_string('day', 'local_deleteoldcourses'),
        new lang_string('day_creation_date_desc', 'local_deleteoldcourses'),
        31,
        $daysofthemonth
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/hour_creation_date',
        new lang_string('hour', 'local_deleteoldcourses'),
        new lang_string('hour_creation_date_desc', 'local_deleteoldcourses'),
        23,
        $hoursinaday
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/minutes_creation_date',
        new lang_string('minutes', 'local_deleteoldcourses'),
        new lang_string('minutes_creation_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinanhour
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/seconds_creation_date',
        new lang_string('seconds', 'local_deleteoldcourses'),
        new lang_string('seconds_creation_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinanhour
    ));

    // Criteria to courses modify date.
    $settingspage->add(new admin_setting_heading(
        'local_deleteoldcourses/courses_last_modification_date_criteria_heading',
        new lang_string('courses_last_modification_date_criteria_heading', 'local_deleteoldcourses'),
        new lang_string('courses_last_modification_date_criteria_heading_desc', 'local_deleteoldcourses')));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/year_last_modification_date',
        new lang_string('year', 'local_deleteoldcourses'),
        new lang_string('year_last_modification_date_desc', 'local_deleteoldcourses'),
        2009,
        $years
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/month_last_modification_date',
        new lang_string('month', 'local_deleteoldcourses'),
        new lang_string('month_last_modification_date_desc', 'local_deleteoldcourses'),
        12,
        $monthsoftheyear
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/day_last_modification_date',
        new lang_string('day', 'local_deleteoldcourses'),
        new lang_string('day_last_modification_date_desc', 'local_deleteoldcourses'),
        31,
        $daysofthemonth
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/hour_last_modification_date',
        new lang_string('hour', 'local_deleteoldcourses'),
        new lang_string('hour_last_modification_date_desc', 'local_deleteoldcourses'),
        23,
        $hoursinaday
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/minutes_last_modification_date',
        new lang_string('minutes', 'local_deleteoldcourses'),
        new lang_string('minutes_last_modification_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinanhour
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/seconds_last_modification_date',
        new lang_string('seconds', 'local_deleteoldcourses'),
        new lang_string('seconds_last_modification_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinanhour
    ));

    // Criteria excluded course categories.
    $settingspage->add(new admin_setting_heading(
        'local_deleteoldcourses/course_categories_criteria_heading',
        new lang_string('course_categories_criteria_heading', 'local_deleteoldcourses'),
        new lang_string('course_categories_criteria_heading_desc', 'local_deleteoldcourses')));

    $options = array();

    for ($i = 0; $i <= 10; $i++) {
        array_push($options, $i);
    }

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/number_of_categories_to_exclude',
        new lang_string('number_of_categories_to_exclude', 'local_deleteoldcourses'),
        new lang_string('number_of_categories_to_exclude_desc', 'local_deleteoldcourses'),
        1,
        $options
    ));

    $numbercategoriestoexclude = get_config('local_deleteoldcourses', 'number_of_categories_to_exclude');

    for ($i = 1; $i <= $numbercategoriestoexclude; $i++) {

        $settingspage->add(new admin_settings_coursecat_select(
            'local_deleteoldcourses/excluded_course_categories' . $i,
            new lang_string('excluded_course_categories', 'local_deleteoldcourses'),
            new lang_string('excluded_course_categories_desc', 'local_deleteoldcourses'),
            500,
            PARAM_INT,
            109
        ));
    }

    // Must add the page after definiting all the settings!
    $settings->add($settingspage);

    // Second settings tab: Advanced settings.
    $settingspage = new admin_settingpage('advanced_settings',
                                           new lang_string('advancedtab', 'local_deleteoldcourses'));

    $settingspage->add(new admin_setting_heading(
        'local_deleteoldcourses/advanced_settings_heading',
        new lang_string('advanced_settings_heading', 'local_deleteoldcourses'),
        new lang_string('advanced_settings_heading_desc', 'local_deleteoldcourses')));

    $settingspage->add(new admin_setting_configtext(
        'local_deleteoldcourses/limit_query',
        new lang_string('limit_query', 'local_deleteoldcourses'),
        new lang_string('limit_query_desc', 'local_deleteoldcourses'),
        5000,
        PARAM_INT,
        5
    ));

    $settingspage->add(new admin_setting_configtext(
        'local_deleteoldcourses/course_queue_size',
        new lang_string('course_queue_size', 'local_deleteoldcourses'),
        new lang_string('course_queue_size_desc', 'local_deleteoldcourses'),
        500,
        PARAM_INT,
        5
    ));

    // Must add the page after definiting all the settings!
    $settings->add($settingspage);

    // Third settings tab: Notification settings.
    $settingspage = new admin_settingpage('notification_settings',
                                          new lang_string('notification_settings_tab', 'local_deleteoldcourses'));

    $settingspage->add(new admin_setting_heading(
                       'local_deleteoldcourses/notification_settings_heading',
                       new lang_string('notification_settings_heading', 'local_deleteoldcourses'),
                       new lang_string('notification_settings_heading_desc', 'local_deleteoldcourses')));

    $settingspage->add(new admin_setting_configtext(
                       'local_deleteoldcourses/users_to_notify',
                       new lang_string('users_to_notify', 'local_deleteoldcourses'),
                       new lang_string('users_to_notify_desc', 'local_deleteoldcourses'),
                       '',
                       PARAM_TEXT,
                       50
                    ));

    // Must add the page after definiting all the settings!
    $settings->add($settingspage);
}

$ADMIN->add('localplugins', $settings);
