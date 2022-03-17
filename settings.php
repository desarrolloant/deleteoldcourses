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

// Fecha de inicio de los cursos a borrar.
// Fecha de modificacion de los cursos a borrar.
// Tamanio de la cola de cursos a borrar.

if ($hassiteconfig) {
    $ADMIN->add('localplugins',
                new admin_category('local_deleteoldcourses_settings',
                new lang_string('pluginname', 'local_deleteoldcourses')));
    $settingspage = new admin_settingpage('managelocaldeleteoldcourses', new lang_string('manage', 'local_deleteoldcourses'));

    if ($ADMIN->fulltree) {

        $daysofmonth = array();

        for ($i = 1; $i <= 31; $i++) {
            array_push($daysofmonth, $i);
        }

        $monthsofyear = array(
            'Enero',
            'Febrero',
            'Marzo',
            'Abril'
        );

        $years = array();
        for ($i = 2005; $i <= 2040; $i++) {
            array_push($years, $i);
        }

        // Criteria to delete.
        $settingspage->add(new admin_setting_heading(
            'criteriasettingsheading',
            new lang_string('criteriasettingsheading', 'local_deleteoldcourses'),
            new lang_string('criteriasettingsheading_desc', 'local_deleteoldcourses')));

        $settingspage->add(new admin_setting_configselect(
            'local_deleteoldcourses/daystartdate',
            new lang_string('daystartdate', 'local_deleteoldcourses'),
            new lang_string('daystartdate_desc', 'local_deleteoldcourses'),
            0,
            $daysofmonth
        ));

        $settingspage->add(new admin_setting_configselect(
            'local_deleteoldcourses/monthstartdate',
            new lang_string('monthstartdate', 'local_deleteoldcourses'),
            new lang_string('monthstartdate_desc', 'local_deleteoldcourses'),
            0,
            $monthsofyear
        ));

        $settingspage->add(new admin_setting_configselect(
            'local_deleteoldcourses/yearstartdate',
            new lang_string('yearstartdate', 'local_deleteoldcourses'),
            new lang_string('yearstartdate_desc', 'local_deleteoldcourses'),
            0,
            $years
        ));

        // Configuraciones de la hora, minutos y segundos.

        // Configuraciones para la fecha de modificacion de los cursos.

        // Parameters of the delete process.
        $settingspage->add(new admin_setting_heading(
            'parameterssettingsheading',
            new lang_string('parameterssettingsheading', 'local_deleteoldcourses'),
            new lang_string('parameterssettingsheading_desc', 'local_deleteoldcourses')));

        $settingspage->add(new admin_setting_configtext(
            'local_deleteoldcourses/sizecoursequeue',
            new lang_string('sizecoursequeue', 'local_deleteoldcourses'),
            new lang_string('sizecoursequeue_desc', 'local_deleteoldcourses'),
            500,
            PARAM_INT,
            3
        ));
    }

    $ADMIN->add('localplugins', $settingspage);

    $ADMIN->add('reports', new admin_category('deleteoldcourses', new lang_string('pluginname', 'local_deleteoldcourses')));

    $ADMIN->add('deleteoldcourses',
        new admin_externalpage('indexdeleteoldcourses', new lang_string('courses', 'local_deleteoldcourses'),
            new moodle_url('/local/deleteoldcourses/index.php'), 'moodle/site:configview'
        )
    );
}
