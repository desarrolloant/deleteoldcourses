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
 * Plugin upgrade code.
 *
 * @package    local_deleteoldcourses
 * @copyright  2022 Juan Felipe Orozco Escobar <juan.oroczo.escobar@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_deleteoldcourses\utils;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/deleteoldcourses/lib.php');

/**
 * Function to upgrade the plugin.
 *
 * @param int $oldversion the version it is upgrading from
 * @return bool result
 */
function xmldb_local_deleteoldcourses_upgrade($oldversion=0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2020100510) {

        // Modify tables: deleteoldcourses and deleteoldcourses_deleted.
        $tabledeleteoldcourses = new xmldb_table('deleteoldcourses');
        $tabledeleteoldcoursesdeleted = new xmldb_table('deleteoldcourses_deleted');

        // Adding fields to tables deleteoldcourses.
        $fieldsize = $tabledeleteoldcourses->add_field('size', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, '-1');
        $fieldcoursecreatedat = $tabledeleteoldcourses->add_field('coursecreatedat',
                                                                    XMLDB_TYPE_INTEGER,
                                                                    '10',
                                                                    null,
                                                                    XMLDB_NOTNULL,
                                                                    false,
                                                                    '0');
        $dbman->add_field($tabledeleteoldcourses, $fieldsize);
        $dbman->add_field($tabledeleteoldcourses, $fieldcoursecreatedat);

        // Adding fields to tables deleteoldcourses_deleted.
        $fieldsize = $tabledeleteoldcoursesdeleted->add_field('size',
                                                                XMLDB_TYPE_INTEGER,
                                                                '10',
                                                                null,
                                                                XMLDB_NOTNULL,
                                                                false,
                                                                '-1');
        $fieldcoursecreatedat = $tabledeleteoldcoursesdeleted->add_field('coursecreatedat',
                                                                            XMLDB_TYPE_INTEGER,
                                                                            '10',
                                                                            null,
                                                                            XMLDB_NOTNULL,
                                                                            false,
                                                                            '0');
        $dbman->add_field($tabledeleteoldcoursesdeleted, $fieldsize);
        $dbman->add_field($tabledeleteoldcoursesdeleted, $fieldcoursecreatedat);

        upgrade_plugin_savepoint(true, 20201005100, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022060600) {

        // Define table local_delcoursesuv_todelete to be created.
        $tabletodelete = new xmldb_table('local_delcoursesuv_todelete');

        // Adding fields to table local_delcoursesuv_todelete.
        $tabletodelete->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tabletodelete->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $tabletodelete->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $tabletodelete->add_field('coursesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
        $tabletodelete->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_delcoursesuv_todelete.
        $tabletodelete->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $tabletodelete->add_key('fk_courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $tabletodelete->add_key('fk_userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Conditionally launch create table for local_delcoursesuv_todelete.
        if (!$dbman->table_exists($tabletodelete)) {
            $dbman->create_table($tabletodelete);
        }

        // Define table local_delcoursesuv_deleted to be created.
        $tabledeleted = new xmldb_table('local_delcoursesuv_deleted');

        // Adding fields to table local_delcoursesuv_deleted.
        $tabledeleted->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $tabledeleted->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('courseshortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('coursefullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('coursesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
        $tabledeleted->add_field('coursetimecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $tabledeleted->add_field('coursetimesenttodelete', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $tabledeleted->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('usershortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('userfullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('useremail', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $tabledeleted->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_delcoursesuv_deleted.
        $tabledeleted->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_delcoursesuv_deleted.
        $tabledeleted->add_index('courseid', XMLDB_INDEX_UNIQUE, ['courseid']);
        $tabledeleted->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch create table for local_delcoursesuv_deleted.
        if (!$dbman->table_exists($tabledeleted)) {
            $dbman->create_table($tabledeleted);
        }

        // Deleteoldcourses savepoint reached.
        upgrade_plugin_savepoint(true, 2022060600, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022080500) {

        // Rename fields usershortname and userfullname to username and userfirstname respectively.
        $tabledeleted = new xmldb_table('local_delcoursesuv_deleted');
        $fieldusershortname = new xmldb_field('usershortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, 'userid');
        $fielduserfullname = new xmldb_field('userfullname', XMLDB_TYPE_CHAR,
                                             '255', null, XMLDB_NOTNULL, null, null, 'usershortname');

        // Launch rename fields.
        $dbman->rename_field($tabledeleted, $fieldusershortname, 'username');
        $dbman->rename_field($tabledeleted, $fielduserfullname, 'userfirstname');

        // Define field userlastname to be added to local_delcoursesuv_deleted.
        $fielduserlastname = new xmldb_field('userlastname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null, null);
        // Conditionally launch add field userlastname.
        if (!$dbman->field_exists($tabledeleted, $fielduserlastname)) {
            $dbman->add_field($tabledeleted, $fielduserlastname);
        }

        // Deleteoldcourses savepoint reached.
        upgrade_plugin_savepoint(true, 2022080500, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022082502) {

        // Define field manual to be added to local_delcoursesuv_todelete.
        $table = new xmldb_table('local_delcoursesuv_todelete');
        $field = new xmldb_field('manual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'timecreated');

        // Conditionally launch add field manual.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field manual to be added to local_delcoursesuv_deleted.
        $table = new xmldb_table('local_delcoursesuv_deleted');
        $field = new xmldb_field('manual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'timecreated');

        // Conditionally launch add field manual.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Deleteoldcourses savepoint reached.
        upgrade_plugin_savepoint(true, 2022082502, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022090101) {

        // Rename field manual on table local_delcoursesuv_todelete to NEWNAMEGOESHERE.
        $table = new xmldb_table('local_delcoursesuv_todelete');
        $field = new xmldb_field('manual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'timecreated');

        // Launch rename field manual.
        $dbman->rename_field($table, $field, 'manuallyqueued');

        // Rename field manual on table local_delcoursesuv_deleted to NEWNAMEGOESHERE.
        $table = new xmldb_table('local_delcoursesuv_deleted');
        $field = new xmldb_field('manual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'timecreated');

        // Launch rename field manual.
        $dbman->rename_field($table, $field, 'manuallyqueued');

        // Deleteoldcourses savepoint reached.
        upgrade_plugin_savepoint(true, 2022090101, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022091400) {
        $utils = new utils();

        $utils->migrate_records();
    }

    return true;
}
