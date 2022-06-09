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
        $sizefield = $tabledeleteoldcourses->add_field('size', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, '-1');
        $coursecreatedatfield = $tabledeleteoldcourses->add_field('coursecreatedat',
                                                                    XMLDB_TYPE_INTEGER,
                                                                    '10',
                                                                    null,
                                                                    XMLDB_NOTNULL,
                                                                    false,
                                                                    '0');
        $dbman->add_field($tabledeleteoldcourses, $sizefield);
        $dbman->add_field($tabledeleteoldcourses, $coursecreatedatfield);

        // Adding fields to tables deleteoldcourses_deleted.
        $sizefield = $tabledeleteoldcoursesdeleted->add_field('size',
                                                                XMLDB_TYPE_INTEGER,
                                                                '10',
                                                                null,
                                                                XMLDB_NOTNULL,
                                                                false,
                                                                '-1');
        $coursecreatedatfield = $tabledeleteoldcoursesdeleted->add_field('coursecreatedat',
                                                                            XMLDB_TYPE_INTEGER,
                                                                            '10',
                                                                            null,
                                                                            XMLDB_NOTNULL,
                                                                            false,
                                                                            '0');
        $dbman->add_field($tabledeleteoldcoursesdeleted, $sizefield);
        $dbman->add_field($tabledeleteoldcoursesdeleted, $coursecreatedatfield);

        upgrade_plugin_savepoint(true, 20201005100, 'local', 'deleteoldcourses');
    }

    if ($oldversion < 2022060600) {

        // Define table local_delcoursesuv_todelete to be created.
        $todeletetable = new xmldb_table('local_delcoursesuv_todelete');

        // Adding fields to table local_delcoursesuv_todelete.
        $todeletetable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $todeletetable->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $todeletetable->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $todeletetable->add_field('coursesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
        $todeletetable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_delcoursesuv_todelete.
        $todeletetable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $todeletetable->add_key('fk_courseid', XMLDB_KEY_FOREIGN, ['courseid'], 'course', ['id']);
        $todeletetable->add_key('fk_userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        // Conditionally launch create table for local_delcoursesuv_todelete.
        if (!$dbman->table_exists($todeletetable)) {
            $dbman->create_table($todeletetable);
        }

        // Define table local_delcoursesuv_deleted to be created.
        $deletedtable = new xmldb_table('local_delcoursesuv_deleted');

        // Adding fields to table local_delcoursesuv_deleted.
        $deletedtable->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $deletedtable->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('courseshortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('coursefullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('coursesize', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '-1');
        $deletedtable->add_field('coursetimecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $deletedtable->add_field('coursetimesenttodelete', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $deletedtable->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('usershortname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('userfullname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('useremail', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $deletedtable->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table local_delcoursesuv_deleted.
        $deletedtable->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Adding indexes to table local_delcoursesuv_deleted.
        $deletedtable->add_index('courseid', XMLDB_INDEX_UNIQUE, ['courseid']);
        $deletedtable->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);

        // Conditionally launch create table for local_delcoursesuv_deleted.
        if (!$dbman->table_exists($deletedtable)) {
            $dbman->create_table($deletedtable);
        }

        // Deleteoldcourses savepoint reached.
        upgrade_plugin_savepoint(true, 2022060600, 'local', 'deleteoldcourses');
    }

    return true;
}
