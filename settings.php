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
 * @package local_deleteoldcourses
 * @author  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @since   Moodle 3.6.6
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/deleteoldcourses/locallib.php');

$ADMIN->add('localplugins',
        new admin_category('localdeleteoldcoursessettings',
        new lang_string('pluginname', 'local_deleteoldcourses')));

// Boost provides a nice setting page which splits settings onto separate tabs. We want to use it here.
$settings = new theme_boost_admin_settingspage_tabs('managelocaldeleteoldcourses',
                                                    get_string('manage', 'local_deleteoldcourses'));

if ($ADMIN->fulltree) {

    $settingspage = new admin_settingpage('deletioncriterias', new lang_string('criteriatab', 'local_deleteoldcourses'));

    $years = get_years();
    $monthsoftheyear = get_months_of_the_year();
    $daysofthemonth = get_days_of_the_month();
    $hoursinaday = get_hours_in_day();
    $minutesinahour = get_minutes_in_hour(); // Also used for the secondsstartdate option (seconds in a minute).

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
        $minutesinahour
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/seconds_creation_date',
        new lang_string('seconds', 'local_deleteoldcourses'),
        new lang_string('seconds_creation_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinahour
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
        $minutesinahour
    ));

    $settingspage->add(new admin_setting_configselect(
        'local_deleteoldcourses/seconds_last_modification_date',
        new lang_string('seconds', 'local_deleteoldcourses'),
        new lang_string('seconds_last_modification_date_desc', 'local_deleteoldcourses'),
        59,
        $minutesinahour
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
        new lang_string('number_of_categories', 'number_of_categories_to_exclude'),
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

    // Configuraciones para la fecha de modificacion de los cursos.

    $settingspage = new admin_settingpage('advanced_settings',
                                        new lang_string('advancedtab', 'local_deleteoldcourses'));

    // Advanced settings.
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
}

$ADMIN->add('localplugins', $settings);
