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

require_once("$CFG->libdir/externallib.php");

/**
 * Local deleteoldcourses external functions
 *
 * @package    local_deleteoldcourses
 * @category   external
 * @author     2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6.6
 */

/**
 * Delete one or more courses.
 *
 * @throws invalid_parameter_exception
 * @param array $courses An array of courses to delete.
 * @return array An array of arrays
 * @since      Moodle 3.6.6
 */
class local_deleteoldcourses_external extends external_api {

    /**
     * Describes the parameters for get_course.
     *
     * @return external_function_parameters
     * @since  Moodle 3.6.6
     */
    public static function get_course_parameters() {
        return new \external_function_parameters(
            [
                'courseid' => new \external_value(PARAM_INT, 'Course id')
            ]
        );
    }

    /**
     * Describes the get_course_returns return value.
     *
     * @return external_single_structure
     * @since  Moodle 3.6.6
     */
    public static function get_course_returns() {
        return new external_single_structure(
            array(
                'id' => new \external_value(PARAM_INT, 'id of course'),
                'fullname' => new \external_value(PARAM_TEXT, 'couse full name'),
                'shortname' => new \external_value(PARAM_TEXT, 'couse short name'),
                'teachers' => new \external_multiple_structure(
                    new \external_single_structure(
                        array(
                            'id' => new \external_value(PARAM_INT, 'teacher id'),
                            'fullname' => new \external_value(PARAM_TEXT, 'teacher full name'),
                            'url' => new \external_value(PARAM_TEXT, 'teacher profile url')
                        )
                    ),
                    'The data to be show', VALUE_DEFAULT, []   
                )
            )
        );
    }

    /**
     * Get course informatión
     *
     * @param  int $courseid the course id
     * @return array course information and teachers
     * @since  Moodle 3.6.6
     */
    public static function get_course($courseid) {
        global $CFG, $DB, $USER;

        $params = self::validate_parameters(self::get_course_parameters(), ['courseid' => $courseid]);
        if(!$params) {
            throw new invalid_parameter_exception("Course not found");
        }

        //Get the editing teacher role
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        //Get the course context
        $context = get_context_instance(CONTEXT_COURSE, $params['courseid']);
        //Get the course teachers
        $teachers = get_role_users($role->id, $context);

        $isteacher = FALSE;
        //Get teachers
        $temp = [];
        foreach ($teachers as $teacher) {
            $url_user = $CFG->wwwroot . "/user/view.php?id=" . $teacher->id . "&course=" . $courseid;
            if ($USER->id != $teacher->id) {
                array_push($temp, array(
                    'id' => $teacher->id,
                    'fullname' => fullname($teacher),
                    'url' => $url_user
                ));
            }else{
                $isteacher = TRUE;
            }
        }

        //Confirm if is a teacher of this course
        if (!$isteacher) {
            return array(
                'id' => NULL,
                'fullname' => NULL,
                'shortname' => NULL,
                'teachers' => array()
            );
        }

        if ($course = $DB->get_record('course', array('id' => $params['courseid']))){

            //Create event for show course alert options
            $event = \local_deleteoldcourses\event\course_delete_options_viewed::create(array(
                'objectid' => $course->id,
                'context' => $context,
                'other' => array(),
                'relateduserid' => $USER->id,
            ));
            $event->trigger();

            return array(
                'id' => $course->id,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'teachers' => $temp
            );
        }
    }

    //---------------------------Add course to deletetion list -------------------------------------

    /**
     * Describes the parameters for add_course.
     *
     * @return external_function_parameters
     * @since  Moodle 3.6.6
     */
    public static function add_course_parameters() {
        return new \external_function_parameters(
            [
                'courseid' => new \external_value(PARAM_INT, 'Course id'),
                'shortname' => new \external_value(PARAM_TEXT, 'Course short name'),
                'fullname' => new \external_value(PARAM_TEXT, 'Course full name')
            ]
        );
    }

    /**
     * Describes the add_course_returns return value.
     *
     * @return external_single_structure
     * @since  Moodle 3.6.6
     */
    public static function add_course_returns() {
        return new external_single_structure(
            array(
                'success' => new \external_value(PARAM_BOOL, 'Success of transation'),
                'record_id' => new \external_value(PARAM_INT, 'id of course delete object')
            )
        );
    }

    /**
     * Add a course to delete list
     *
     * @param int $courseid the course id
     * @param string $shortname the course shortname
     * @param string $fullname the course fullname
     * @return array course information confirm o reject
     * @since  Moodle 3.6.6
     */
    public static function add_course($course, $shortname, $fullname) {
        global $CFG, $DB, $USER;

        $params = self::validate_parameters(self::add_course_parameters(), [
            'courseid' => $course,
            'shortname' => $shortname,
            'fullname' => $fullname
        ]);
        if(!$params) {
            throw new invalid_parameter_exception("Course not found");
        }

        //Get the editing teacher role
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        //Get the course context
        $context = get_context_instance(CONTEXT_COURSE, $params['courseid']);
        //Get the course teachers
        $teachers = get_role_users($role->id, $context);

        //Confirm if this user is teacher of course
        $isteacher = FALSE;
        foreach ($teachers as $teacher) {
            if ($USER->id == $teacher->id) {
                $isteacher = TRUE;
            }
        }

        $record = (object) array(
            'courseid' => $params['courseid'],
            'shortname' => $params['shortname'],
            'fullname' => $params['fullname'],
            'userid' => $USER->id,
            'timecreated' => time()
        );

        $record_id = NULL;

        if ($isteacher) {
            $record_id = $DB->insert_record('deleteoldcourses', $record);
        }
        
        if ($record_id) {

            //Create event for sent course to be deleted
            $event = \local_deleteoldcourses\event\course_sent_delete::create(array(
                'objectid' => $params['courseid'],
                'context' => $context,
                'other' => array(),
                'relateduserid' => $USER->id,
            ));
            $event->trigger();


            return array(
                'success' => TRUE,
                'record_id' => $record_id
            );
        }

        return array(
            'success' => FALSE,
            'record_id' => 0
        );
    }


    //---------------------------Remove course from list to delete -------------------------------------

    /**
     * Describes the parameters for remove_course.
     *
     * @return external_function_parameters
     * @since  Moodle 3.6.6
     */
    public static function remove_course_parameters() {
        return new \external_function_parameters(
            [
                'courseid' => new \external_value(PARAM_INT, 'Course id')
            ]
        );
    }

    /**
     * Describes the remove_course_returns return value.
     *
     * @return external_single_structure
     * @since  Moodle 3.6.6
     */
    public static function remove_course_returns() {
        return new external_single_structure(
            array(
                'success' => new \external_value(PARAM_BOOL, 'Success of transation')
            )
        );
    }

    /**
     * Remove a course from to delete list
     *
     * @param int $courseid the course id
     * @return array course information confirm o reject
     * @since      Moodle 3.6.6
     */
    public static function remove_course($courseid) {
        global $CFG, $DB, $USER;

        $params = self::validate_parameters(self::remove_course_parameters(), [
            'courseid' => $courseid
        ]);
        if(!$params) {
            throw new invalid_parameter_exception("Course not found");
        }

        //Get the editing teacher role
        $role = $DB->get_record('role', array('shortname' => 'editingteacher'));
        //Get the course context
        $context = get_context_instance(CONTEXT_COURSE, $params['courseid']);
        //Get the course teachers
        $teachers = get_role_users($role->id, $context);

        //Confirm if this user is teacher of course
        $isteacher = FALSE;
        foreach ($teachers as $teacher) {
            if ($USER->id == $teacher->id) {
                $isteacher = TRUE;
            }
        }

        $record = (object) array(
            'courseid' => $params['courseid']
        );

        $deleted = FALSE;

        if ($isteacher) {
            $deleted = $DB->delete_records('deleteoldcourses', array('courseid'=>$params['courseid']));
        }
        
        if ($deleted) {

            //Create event for remove course from delete list
            $event = \local_deleteoldcourses\event\course_remove_delete::create(array(
                'objectid' => $params['courseid'],
                'context' => $context,
                'other' => array(),
                'relateduserid' => $USER->id,
            ));
            $event->trigger();


            return array(
                'success' => TRUE
            );
        }

        return array(
            'success' => FALSE
        );
    }


}