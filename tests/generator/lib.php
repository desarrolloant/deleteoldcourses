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
 * Course dispatcher class.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Generator class for Delete old courses.
 *
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_deleteoldcourses_generator extends testing_module_generator {

    /**
     * Insert plugin config for tests.
     *
     * @param  stdClass $record plugin config record
     * @return stdClass $record
     */
    public function insert_config($record = null) {

        global $DB;

        $record->id = $DB->insert_record('config_plugins', $record);

        return $record;
    }

    /**
     * Update plugin config for tests.
     *
     * @param stdClass $record plugin config record
     */
    public function update_config($record = null) {

        global $DB;

        $sql = "UPDATE {config_plugins}
                SET value = ?
                WHERE plugin = ? AND name = ?";

        $DB->execute($sql, array($record->value, $record->plugin, $record->name));
    }

    /**
     * Update plugin setting for tests.
     *
     * @param  string   $namesetting
     * @param  string   $value
     * @return stdClass $record
     */
    public function update_setting($settingname, $settingvalue) {

        global $DB;

        $idsetting = $DB->get_record('config_plugins', array('name' => $settingname), 'id')->id;

        $record = new stdClass();
        $record->id = $idsetting;
        $record->value = $settingvalue;

        $DB->update_record('config_plugins', $record);

        $setting = $DB->get_record('config_plugins', array('name' => $settingname));

        return $setting;
    }
}
