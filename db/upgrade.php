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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/local/deleteoldcourses/lib.php');

function xmldb_local_deleteoldcourses_upgrade($oldversion=0) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 20201005100) {



    	// Modify table: deleteoldcourses
        $table_deleteoldcourses = new xmldb_table('deleteoldcourses');
        $table_deleteoldcourses_deleted = new xmldb_table('deleteoldcourses_deleted');

        // Adding fields to tables deleteoldcourses and deleteoldcourses_deleted
        $size_field = $table->add_field('size', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, '-1');
        $coursecreatedat_field = $table->add_field('coursecreatedat', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, false, '0');


        $dbman->add_field($table_deleteoldcourses, $size_field); 
        $dbman->add_field($table_deleteoldcourses, $coursecreatedat_field);

        $dbman->add_field($table_deleteoldcourses_deleted, $size_field); 
        $dbman->add_field($table_deleteoldcourses_deleted, $coursecreatedat_field); 

        upgrade_plugin_savepoint(true, 20201005100, 'local', 'deleteoldcourses');
    }

    return true;
}