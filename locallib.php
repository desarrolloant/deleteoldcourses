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
 * Version information for deletecourses.
 *
 * @package	local_deleteoldcourses - Local Library
 * @author 2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Local deleteoldcourses internal functions
 *
 * @package    local_deleteoldcourses
 * @category   internal
 * @author     2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.6.6
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Editingteacher roleid.
 */
const EDITING_TEACHER_ROLE_ID = 3;

/**
 * Count teacher courses created more than 1 year ago
 *
 * @param int $userid ID of the user
 * @param int $now epoch date no
 * @param int $ago number of years ago
 * @return int
 */
function user_count_courses($userid, $now, $ago = 1){
	global $DB;
  $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' year');
  $params = array();
  $sql = "SELECT COUNT(c.id)
            FROM {user} u
            JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.roleid = :roleid AND u.id = :userid)
            JOIN {context} ct ON (ra.contextid = ct.id)
            JOIN {course} c ON (ct.instanceid = c.id AND c.timecreated < :since)";

  $params['roleid'] = EDITING_TEACHER_ROLE_ID;
  $params['userid'] = $userid;
  $params['since'] = $since;
  return $DB->count_records_sql($sql, $params);
}


/**
 * Get courses for delete courses table
 *
 * @param int $userid ID of the user
 * @param string $sort order registers
 * @param int $limitfrom limit sql 
 * @param int $limitnum limit sql 
 * @param int $now epoch date no
 * @param int $ago number of years ago
 * @return query
 */
function user_get_courses($userid, $sort, $limitfrom=0, $limitnum=0, $now, $ago = 1){
  global $DB;
  $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' year');

  list($select, $from, $join, $params) = user_get_courses_sql(EDITING_TEACHER_ROLE_ID, $userid, $since);
  
  $courses = $DB->get_recordset_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
  return $courses;
}

/**
 * get sql string for query
 *
 * @param int $roleid id of teacher
 * @param int $userid ID of the user
 * @param int $since date ago
 * @return string for query
 */
function user_get_courses_sql($roleid, $userid, $since){

  $params = array();
  $params['roleid'] = $roleid;
  $params['userid'] = $userid;
  $params['since'] = $since;

  $select = "SELECT 
              c.id, c.shortname AS c_shortname, 
              c.fullname AS c_fullname, 
              u.username AS u_username, 
              CONCAT(u.firstname, ' ', u.lastname) AS u_fullname, 
              c.timecreated AS c_timecreated";

  $from = "FROM {user} u";

  $join = "JOIN {role_assignments} ra ON (ra.userid = u.id AND ra.roleid = :roleid AND u.id = :userid)
           JOIN {context} ct ON (ra.contextid = ct.id)
           JOIN {course} c ON (ct.instanceid = c.id AND c.timecreated <= :since)";

  return array($select, $from, $join, $params);
}

/**
 * Check if a course is the list for delete
 *
 * @param int $userid ID of the user
 * @return bool
 */
function course_in_delete_list($courseid){
  global $DB;
  //Get the editing teacher role
  $record = $DB->get_record('deleteoldcourses', array('courseid' => $courseid));
  if (!$record) {
    return FALSE;
  }

  return TRUE;
}


/**
 * Trigger deleteoldcourses viewed event,
 *
 * @param stdClass  $context page context object
 * @since Moodle 3.6.6
 */
function deleteoldcourses_viewed($context, $userid) {
  $event = \local_deleteoldcourses\event\old_courses_list_viewed::create(array(
    'objectid' => $userid,
    'context' => $context,
    'other' => array(),
    'relateduserid' => $userid,
  ));
  $event->trigger();
}


/**
 * Lib for email messages.
 * @author Iader E. Garcia Gomez <iader.garcia@correounivalle.edu.co>
 * Version: 0.1
 **/
function delete_old_courses_send_email( $usernameTo, $usernameFrom, $coursesToDelete, $coursesDeleted) {

    $fromUser = core_user::get_user_by_username(
                                        $usernameFrom,
                                        'id, 
                                        firstname, 
                                        lastname, 
                                        username, 
                                        email, 
                                        maildisplay, 
                                        mailformat,
                                        firstnamephonetic,
                                        lastnamephonetic,
                                        middlename,
                                        alternatename'
                                    );

    $toUser = core_user::get_user_by_username(
                                        $usernameTo,
                                        'id, 
                                        firstname, 
                                        lastname, 
                                        username, 
                                        email, 
                                        maildisplay, 
                                        mailformat,
                                        firstnamephonetic,
                                        lastnamephonetic,
                                        middlename,
                                        alternatename'
                                    );

    
    $subject = "Notificación sobre cursos pendientes por borrar en el Campus Virtual";

    $textToSendHtml = '';
    if ($coursesToDelete > 0) {
      $textToSendHtml .= "El plugin de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
    }else{
      $textToSendHtml .= "El módulo de eliminación de cursos ha detectado que el día de hoy <strong>NO</strong> quedan cursos pendientes por borrar.<br><br>";
    }
    $textToSendHtml .= "Cantidad de cursos pendientes: " . $coursesToDelete . "<br>";
    $textToSendHtml .= "Cantidad de cursos borrados: ". $coursesDeleted ."<br><br>";
    $textToSendHtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.<br>";

    $textToSend = html_to_text($textToSendHtml);

    echo $textToSend;

    $completeFilePath = "/home/admincampus/";
    //$completeFilePath = "/Users/diego/Desktop/";
    
    if (intval(date('H')) >= 1 && intval(date('H')) < 4) {
        $nameFile = 'deleteoldcourses0am.log';
    } elseif (intval(date('H')) >= 7) {
        $nameFile = 'deleteoldcourses4am.log';
    } else {
        $nameFile = 'deleteoldcoursestest.log';
    }

    $completeFilePath .= $nameFile;

    echo $completeFilePath;
    
    $resultSendMessage = email_to_user($toUser, $fromUser, $subject, $textToSend, $textToSendHtml, $completeFilePath, $nameFile, true);
}

/*******************************************************************************************
************************************   Admin Functions  ************************************
*******************************************************************************************/

/**
 * Count all deleted courses
 *
 * @param int $userid id of user who deleted a course
 * @param int $now date now
 * @param int $ago years deleted ago
 */
function count_deleted_courses($userid, $now, $ago = 0){
  global $DB;
  $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' month');
  $params = array();
  $sql = "SELECT COUNT(cd.id)
              FROM {deleteoldcourses_deleted} cd
             WHERE cd.timecreated >=:since";
  if ($userid>0) {
    $sql = "SELECT COUNT(cd.id)
              FROM {deleteoldcourses_deleted} cd
             WHERE cd.userid=:userid AND cd.timecreated >=:since";
  }

  $params['userid'] = $userid;
  $params['since'] = $since;
  return $DB->count_records_sql($sql, $params);
}

/**
 * get sql string for query
 *
 * @param int $userid ID of the user
 * @param int $since date ago
 * @return string for query
 */
function get_deleted_courses_sql($userid, $since){

  $params = array();
  $params['userid'] = $userid;
  $params['since'] = $since;

  $select = "SELECT 
              cd.id, cd.shortname AS c_shortname, 
              cd.fullname AS c_fullname, 
              u.username AS u_username, 
              CONCAT(u.firstname, ' ', u.lastname) AS u_fullname,
              cd.timesenttodelete AS c_timesenttodelete,
              cd.timecreated AS c_timedeleted,
              cd.courseid, u.firstname, u.lastname, u.id AS userid";

  $from = "FROM {deleteoldcourses_deleted} cd";

  $join = "JOIN {user} u ON (cd.userid = u.id AND cd.timecreated >=:since)";
  if ($userid>0) {
    $join = "JOIN {user} u ON (cd.userid = u.id AND cd.userid = :userid AND cd.timecreated >=:since)";
  }
  

  return array($select, $from, $join, $params);
}

/**
 * Get courses for deleted courses table
 *
 * @param int $userid ID of the user
 * @param string $sort order registers
 * @param int $limitfrom limit sql 
 * @param int $limitnum limit sql 
 * @param int $now epoch date no
 * @param int $ago number of years ago
 * @return query
 */
function get_deleted_courses($userid, $sort, $limitfrom=0, $limitnum=0, $now, $ago = 0){
  global $DB;
  
  $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' month');

  list($select, $from, $join, $params) = get_deleted_courses_sql($userid, $since);
  
  $courses = $DB->get_recordset_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
  return $courses;
}


/**
 * Count all pending courses
 *
 * @return int number of pending courses
 */
function count_pending_courses(){
  global $DB;
  $sql = "SELECT COUNT(d.id)
              FROM {deleteoldcourses} d";
  return $DB->count_records_sql($sql);
}

/**
 * get sql string for query
 *
 * @return string for query
 */
function get_pending_courses_sql(){

  $params = array();

  $select = "SELECT 
              d.id, d.shortname AS c_shortname, 
              d.fullname AS c_fullname, 
              u.username AS u_username, 
              CONCAT(u.firstname, ' ', u.lastname) AS u_fullname,
              d.timecreated AS c_timesenttodelete,
              d.courseid, u.firstname, u.lastname, u.id AS userid";

  $from = "FROM {deleteoldcourses} d";

  $join = "JOIN {user} u ON (d.userid = u.id)";

  return array($select, $from, $join, $params);
}


/**
 * Get courses for pending courses table
 *
 * @param string $sort order registers
 * @param int $limitfrom limit sql 
 * @param int $limitnum limit sql 
 * @return query
 */
function get_pending_courses($sort, $limitfrom=0, $limitnum=0){
  global $DB;
  
  list($select, $from, $join, $params) = get_pending_courses_sql();
  
  $courses = $DB->get_recordset_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
  return $courses;
}