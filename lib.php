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
 * @package	local_deleteoldcourses
 * @author 2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Editingteacher roleid.
 */
const EDITING_TEACHER_ROLE_ID = 3;

function getSince($ago=1){
  $since = CREATED_MORE_THAT_1_YEAR_AGO;
  switch ($ago) {
    case 2:
      $since = CREATED_MORE_THAT_2_YEARS_AGO;
      break;
    case 3:
      $since = CREATED_MORE_THAT_3_YEARS_AGO;
      break;
    case 4:
      $since = CREATED_MORE_THAT_4_YEARS_AGO;
      break;
    case 5:
      $since = CREATED_MORE_THAT_5_YEARS_AGO;
      break;
    default:
      $since = CREATED_MORE_THAT_1_YEAR_AGO;
      break;
  }
  return $since;
}

/**
 * Check if a user is teacher of a course
 *
 * @param int $userid ID of the user
 * @return int
 */
function user_count_courses($userid, $now, $ago = 1){
	global $DB;
  $since = strtotime(date("Y-m-d H:i:s", $now) .getSince($ago));
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



function user_get_courses($userid, $sort, $limitfrom=0, $limitnum=0, $now, $ago = 1){
  global $DB;
  $since = strtotime(date("Y-m-d H:i:s", $now) .getSince($ago));

  list($select, $from, $join, $params) = user_get_courses_sql(EDITING_TEACHER_ROLE_ID, $userid, $since);
  
  $courses = $DB->get_recordset_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
  return $courses;
}

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
 * @return int
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
 * Trigger deleteolcourses viewed event,
 *
 * @param stdClass  $context page context object
 * @since Moodle 3.7
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

    $textToSendHtml = "El módulo de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
    $textToSendHtml .= "Cantidad de cursos pendientes: " . $coursesToDelete . "<br>";
    $textToSendHtml .= "Cantidad de cursos borrados: ". $coursesDeleted ."<br><br>";
    $textToSendHtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.";

    $textToSend = html_to_text($textToSendHtml);

    echo $textToSend;

    $completeFilePath = "/home/admincampus/";
    //$completeFilePath = "/Users/diego/Documents/";

    if (intval(date('H')) >= 1 && intval(date('H')) < 4) {
        $nameFile = 'log_delete_courses_0000.log';
    } elseif (intval(date('H')) >= 7) {
        $nameFile = 'log_delete_courses_0400.log';
    } else {
        $nameFile = 'log_delete_courses_test.log';
    }

    $completeFilePath .= $nameFile;
    echo $completeFilePath;

    $resultSendMessage = email_to_user($toUser, $fromUser, $subject, $textToSend, $textToSendHtml, $completeFilePath, $nameFile, true);
}