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
 * @package local_deleteoldcourses - Local Library
 * @author  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
function user_count_courses($userid, $now, $ago = 1) {
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
function user_get_courses($userid, $sort, $limitfrom=0, $limitnum=0, $now, $ago = 1) {
    global $DB;
    $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' year');

    list($select, $from, $join, $params) = user_get_courses_sql(EDITING_TEACHER_ROLE_ID, $userid, $since);

    $courses = $DB->get_records_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
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
function user_get_courses_sql($roleid, $userid, $since) {

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
function course_in_delete_list($courseid) {
    global $DB;
    // Get the editing teacher role
    $record = $DB->get_record('deleteoldcourses', array('courseid' => $courseid));
    if (!$record) {
        return false;
    }

    return true;
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

    $texttosendhtml = '';
    if ($coursesToDelete > 0) {
        $texttosendhtml .= "El plugin de eliminación de cursos ha detectado que el día de hoy quedan cursos pendientes por borrar.<br><br>";
    } else {
        $texttosendhtml .= "El módulo de eliminación de cursos ha detectado que el día de hoy <strong>NO</strong> quedan cursos pendientes por borrar.<br><br>";
    }
    $texttosendhtml .= "Cantidad de cursos pendientes: " . $coursesToDelete . "<br>";
    $texttosendhtml .= "Cantidad de cursos borrados: ". $coursesDeleted ."<br><br>";
    $texttosendhtml .= "Este mensaje ha sido generado automáticamente, por favor no responda a este mensaje.<br>";

    $textToSend = html_to_text($texttosendhtml);

    echo $textToSend;

    $completeFilePath = "/vhosts/campus/moodledata/temp/backup/";

    $nameFile = 'deleteoldcourses.log';

    $completeFilePath .= $nameFile;

    echo $completeFilePath;

    $resultSendMessage = email_to_user($toUser, $fromUser, $subject, $textToSend, $textToSendHtml, $completeFilePath, $nameFile, true);
}

/*******************************************************************************************
 * ***********************************   Admin Functions  ************************************
 *******************************************************************************************/

/**
 * Count all deleted courses
 *
 * @param int $userid id of user who deleted a course
 * @param int $now date now
 * @param int $ago years deleted ago
 */
function count_deleted_courses($userid, $now, $ago = 0) {
    global $DB;
    $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' month');
    $params = array();
    $sql = "SELECT COUNT(cd.id)
              FROM {deleteoldcourses_deleted} cd
             WHERE cd.timecreated >=:since";
    if ($userid > 0) {
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
function get_deleted_courses_sql($userid, $since) {

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
    if ($userid > 0) {
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
function get_deleted_courses($userid, $sort, $limitfrom=0, $limitnum=0, $now, $ago = 0) {
    global $DB;

    $since = strtotime(date("Y-m-d H:i:s", $now) .' -'.$ago.' month');

    list($select, $from, $join, $params) = get_deleted_courses_sql($userid, $since);

    $courses = $DB->get_records_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
    return $courses;
}

/**
 * Count all pending courses
 *
 * @return int number of pending courses
 */
function count_pending_courses() {
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
function get_pending_courses_sql() {

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
function get_pending_courses($sort, $limitfrom=0, $limitnum=0) {
    global $DB;

    list($select, $from, $join, $params) = get_pending_courses_sql();

    $courses = $DB->get_records_sql("$select $from $join $sort", $params, $limitfrom, $limitnum);
    return $courses;
}

/**************************************************************************************
 * ***************************** Course deletion automation *****************************
 **************************************************************************************/

/**
 * get sql string for query queue of courses
 * query for select courses by user last course access
 * @return string for query
 */
function get_queue_courses_sql($regular_timecreated, $no_regular_timecreated, $no_regular_timemodified) {
    $regular_timecreated   = new DateTime($regular_timecreated);
    $no_regular_timecreated   = new DateTime($no_regular_timecreated);
    $no_regular_timemodified   = new DateTime($no_regular_timemodified);

    $params              = array();
    $params['context1']   = CONTEXT_COURSE;
    $params['context2']   = CONTEXT_COURSE;
    $params['regular_timecreated']      = $regular_timecreated->getTimestamp();
    $params['regular_timeaccess']      = $no_regular_timemodified->getTimestamp();
    $params['no_regular_timecreated']      = $no_regular_timecreated->getTimestamp();
    $params['no_regular_timemodified']      = $no_regular_timemodified->getTimestamp();
    $params['no_regular_timeaccess']      = $no_regular_timemodified->getTimestamp();

    $orderby = "ASC";

    // Day of week
    $day = date('N');
    // If is Sat, Sun or Mon, change size to DESC
    if ($day == 1 || $day == 6 || $day == 7) {
        $orderby = "DESC";
    }

    $sql = '(SELECT
            f.contextid,
            x.instanceid AS courseid,
            c.fullname AS fullname,
            c.shortname AS shortname,
            c.category,
            sum(f.filesize) AS size_in_bytes,
            sum(case when (f.filesize > 0) then 1 else 0 end) AS number_of_files,
            c.timemodified,
            c.timecreated
          FROM {files} f
          INNER JOIN {context} x
            ON f.contextid = x.id
            AND x.contextlevel = :context1
          INNER JOIN {course} c
            ON c.id = x.instanceid
            AND c.id > 1
            AND c.category >= 30000
            AND c.timecreated < :regular_timecreated
          INNER JOIN (
            SELECT courseid, max(timeaccess) AS timeaccess
            FROM   {user_lastaccess}
            GROUP BY courseid
          ) AS ul ON ul.courseid=c.id AND ul.timeaccess < :regular_timeaccess
          WHERE c.id NOT IN (SELECT courseid FROM {deleteoldcourses})
          GROUP BY f.contextid, x.instanceid, c.fullname, c.shortname, c.category, c.timemodified, c.timecreated
          ORDER BY sum(filesize) '.$orderby.')

          UNION ALL

          (SELECT
            f.contextid,
            x.instanceid AS courseid,
            c.fullname AS fullname,
            c.shortname AS shortname,
            c.category,
            sum(f.filesize) AS size_in_bytes,
            sum(case when (f.filesize > 0) then 1 else 0 end) AS number_of_files,
            c.timemodified,
            c.timecreated
          FROM {files} f
          INNER JOIN {context} x
            ON f.contextid = x.id
            AND x.contextlevel = :context2
          INNER JOIN {course} c
            ON c.id = x.instanceid
            AND c.id > 1
            AND c.category < 30000
            AND c.timecreated < :no_regular_timecreated
            AND c.timemodified < :no_regular_timemodified
          INNER JOIN (
            SELECT courseid, max(timeaccess) AS timeaccess
            FROM   {user_lastaccess}
            GROUP BY courseid
          ) AS ul ON ul.courseid=c.id AND ul.timeaccess < :no_regular_timeaccess
          WHERE c.id NOT IN (SELECT courseid FROM {deleteoldcourses})
          GROUP BY f.contextid, x.instanceid, c.fullname, c.shortname, c.category, c.timemodified, c.timecreated
          ORDER BY sum(filesize) '.$orderby.')

          ORDER BY size_in_bytes '.$orderby;
    return array($sql, $params);
}

/**************************************************************************************
 * ************************** End Course deletion automation ****************************
 **************************************************************************************/

/****************************************************************************
 * ************* New criteria to choose courses April 22, 2021 ****************
 ****************************************************************************/

/**
 * check if a course was updated after date
 * @param object $course
 * @param string $timemodified
 * @return boolean
 */
function course_was_updated($course, $timemodified) {
    global $DB;
    $timemodified = new DateTime($timemodified);
    $timemodified = $timemodified->getTimestamp();
    $result = false;

    if (!$course) {
        return true;
    }

    if ($course) {
        if ($timemodified < $course->timemodified) {
            // echo $course->shortname.' - '.userdate($course->timemodified);
            $result = true;
        }
    }
    return $result;
}

/**
 * check if at least one section of a course was updated after date
 * @param object $course
 * @param string $timemodified
 * @return boolean
 */
function course_sections_was_updated($course, $timemodified) {
    global $DB;
    $timemodified = new DateTime($timemodified);
    $timemodified = $timemodified->getTimestamp();
    $result = false;

    if (!$course) {
        return true;
    }

    $course_sections = $DB->get_records_sql("SELECT cs.summary, cs.timemodified
                                             FROM {course_sections} cs
                                            WHERE cs.course = ?",
                                      array($course->id));
    if ($course_sections) {
        foreach ($course_sections as $course_section) {
            if ($timemodified < $course_section->timemodified) {
                $result = true;
                break;
            }
        }
    }
    return $result;
}

/**
 * check if at least one module of a course was updated after date
 * @param object $course
 * @param string $timemodified
 * @return boolean
 */
function course_modules_was_updated($course, $timemodified) {
    global $DB;
    $timemodified = new DateTime($timemodified);
    $timemodified = $timemodified->getTimestamp();
    $result = false;

    if (!$course) {
        return true;
    }

    $course_mods = $DB->get_records_sql("SELECT cm.instance, m.name as modname
                                         FROM {modules} m, {course_modules} cm
                                        WHERE cm.course = ? AND cm.module = m.id",
                                      array($course->id));
    if ($course_mods) {
        foreach ($course_mods as $course_mod) {
            $course_module_instance = $DB->get_record($course_mod->modname, array('id' => $course_mod->instance ));
            if ($timemodified < $course_module_instance->timemodified) {
                $result = true;
                // echo $course_module_instance->name.' - '.userdate($course_module_instance->timemodified).'<br>';
                break;
            }
        }
    }
    return $result;
}

/**
 * check if at least one role was updated in this course after date
 * @param object $course
 * @param string $timemodified
 * @return boolean
 */
function course_roles_was_updated($course, $timemodified) {
    global $DB;
    $timemodified = new DateTime($timemodified);
    $timemodified = $timemodified->getTimestamp();
    $result = false;

    if (!$course) {
        return true;
    }

    $context = context_course::instance($course->id);

    if (!$context) {
        return true;
    }

    $course_role_assignments = $DB->get_records_sql("SELECT ra.timemodified
                                                     FROM {role_assignments} ra
                                                     JOIN {user} u ON u.id=ra.userid
                                                    WHERE ra.contextid = ?",
                                                  array($context->id));
    if ($course_role_assignments) {
        foreach ($course_role_assignments as $role_assignment) {
            if ($timemodified < $role_assignment->timemodified) {
                $result = true;
                // echo userdate($role_assignment->timemodified).'<br>';
                break;
            }
        }
    }
    return $result;
}

/**
 * check if at least one role was updated in this course after date
 * @param object $course
 * @param string $timemodified
 * @return boolean
 */
function course_user_enrolments_was_updated($course, $timemodified) {
    global $DB;
    $timemodified = new DateTime($timemodified);
    $timemodified = $timemodified->getTimestamp();
    $result = false;

    if (!$course) {
        return true;
    }

    $course_user_enrolments = $DB->get_records_sql("SELECT ue.userid, ue.timecreated, ue.timemodified
                                                    FROM {user_enrolments} ue
                                                    JOIN {enrol} e ON e.id = ue.enrolid
                                                    WHERE e.courseid = ?",
                                                  array($course->id));

    if ($course_user_enrolments) {
        foreach ($course_user_enrolments as $user_enrolment) {
            if ($timemodified < $user_enrolment->timemodified || $timemodified < $user_enrolment->timecreated) {
                $result = true;
                // echo $user_enrolment->userid.' - '.userdate($user_enrolment->timemodified).' - '.userdate($user_enrolment->timemodified).'<br>';
                break;
            }
        }
    }
    return $result;
}

/**
 * Get sql query for get courses created before date
 *
 * @param string $timecreated
 * @return Array $sql, $params[]
 */
function get_courses_sql($timecreated, $order) {
    global $DB;
    $timecreated = new DateTime($timecreated);
    $timecreated = $timecreated->getTimestamp();

    $params = array();
    $params['timecreated'] = $timecreated;

    $sql = '
    SELECT c.id, c.shortname, c.fullname, c.category, c.timemodified, c.timecreated
      FROM {course} c
     WHERE c.id > 1 AND
           c.timecreated < :timecreated AND
           c.id NOT IN (SELECT courseid FROM {deleteoldcourses})
  ORDER BY c.timecreated '.$order;

    return array($sql, $params);
}

/**
 * Add courses to be deleted
 *
 * @param  string $timecreated
 * @param  string $timemodified
 * @param  int $quantity
 * @param  bool $test
 * @return void
 */
function add_courses_to_delete($timecreated, $timemodified, $quantity=0, $test=false) {

    global $DB;

    if ($timecreated == null || $timecreated == '' || $timemodified == null || $timemodified == '') {
        return;
    }

    if ($quantity <= 0) {
        return;
    }

    // Admin user.
    $user = $DB->get_record('user', array('username' => 'desadmin'));
    $count = 0;
    $order = 'ASC';
    $limit_query = 5000;

    // For test queries.
    if ($test) {
        $limit_query = 25000;
        $order = 'DESC';
    }

    list($sql, $params) = get_courses_sql($timecreated, $order);
    $rs = $DB->get_recordset_sql($sql, $params, 0, $limit_query);
    foreach ($rs as $row) {

        // Get first category parent of this course category.
        $first_category_parent = recursiveParentCategory($row->category);
        // Exclude regular courses on categories with id < 30000.
        if ($row->category < 30000 && $first_category_parent == 6) {
            continue;
        }
        // Exclude Cursos Abiertos.
        if ($row->category == 109) {
            continue;
        }
        // Exclude Cursos de Extensión.
        // if ($first_category_parent == 7) { continue; }-->
        // Exclude Cursos Virtuales y Mixtos (blended).
        if ($first_category_parent == 110) {
            continue;
        }
        // Exclude Categoría DEMO.
        if ($row->category == 5) {
            continue;
        }
        // Exclude Cursos Capacitación.
        if ($row->category == 51) {
            continue;
        }
        // Exclude Medios Educativos-AMED.
        // if ($first_category_parent == 43) { continue; } -->
        // Exclude Formación Docente en Integración Pedagógica de las TIC.
        // if ($row->category == 89) { continue; } -->
        // Exclude Elecciones Electrónicas.
        // if ($row->category == 145) { continue; } -->
        // Exclude Cursos Permanentes.
        if ($row->category == 148) {
            continue;
        }

        // Exclude ases courses.
        if ($row->category == 81 || $row->category == 82
            || $row->category == 83 || $row->category == 146) {

            continue;
        }

        $course_updated = course_was_updated($row, $timemodified);
        $sections_updated = course_sections_was_updated($row, $timemodified);
        $modules_updated = course_modules_was_updated($row, $timemodified);
        $roles_updated = course_roles_was_updated($row, $timemodified);
        $user_enrolments = course_user_enrolments_was_updated($row, $timemodified);

        // If this course was updated after date.
        if ($course_updated || $sections_updated || $modules_updated || $roles_updated || $user_enrolments) {
            continue;
        }

        $count ++;

        // Show test queries - Confirm creation date.
        if ($test) {
            echo $count.' - '.$row->id.' - '.$row->fullname.' - '.userdate($row->timecreated).'<br>';
            continue;
        }

        // Add course to queue for delete.
        $record = (object) array(
        'courseid'          => $row->id,
        'shortname'         => $row->shortname,
        'fullname'          => $row->fullname,
        'userid'            => $user->id,
        'size'              => courseCalculateSize($row->id),
        'coursecreatedat'   => $row->timecreated,
        'timecreated'       => time()
        );
        // Add to deletion list.
        $DB->insert_record('deleteoldcourses', $record);

        // If is reach quantity break.
        if ($count >= $quantity) {
            break;
        }
    }
    $rs->close();
}


/****************************************************************************
 * *********** End New criteria to choose courses April 22, 2021 **************
 ****************************************************************************/

/****************************************************************************
 * ****************************** Other Methods *******************************
 ****************************************************************************/
function courseCalculateSize($courseid) {
    global $DB;

    $result = 0;

    $params = [];
    $params['courseid']   = $courseid;
    $params['context']    = CONTEXT_COURSE;
    $sql = "SELECT sum(f.filesize) AS size
          FROM {files} f
          INNER JOIN {context} x ON (f.contextid = x.id AND x.contextlevel = :context)
          INNER JOIN {course} c ON (c.id = x.instanceid AND c.id = :courseid)";

    if ($query = $DB->get_record_sql($sql, $params)) {
        if ($query->size != null) {
            $result = $query->size;
        }
    }

    return $result;
}

// Get first categoryid of parents tree.
function recursiveParentCategory($categoryid) {
    global $DB;
    $category = $DB->get_record('course_categories', array('id' => $categoryid));
    if ($category->parent > 0) {
        return recursiveParentCategory($category->parent);
    } else {
        return $category->id;
    }
}

function countDeletedCourses($starttime) {
    global $DB;
    $params = [];
    $params['starttime']   = $starttime;
    $sql = "SELECT COUNT(*)
          FROM {deleteoldcourses_deleted}
          WHERE timecreated > :starttime";
    $deletedcourses = $DB->count_records_sql($sql, $params);
    return $deletedcourses;
}

/**
 * Return years array
 *
 * @return array $years
 */
function get_years() {
    $years = array();
    $fromyear = 2005;
    $toyear = 2040;
    for ($i = $fromyear; $i <= $toyear; $i++) {
        array_push($years, $i);
    }
    return $years;
}

/**
 * Return a month array
 *
 * @return array $monthsoftheyear
 */
function get_months_of_the_year() {

    $strjanuary = get_string('january', 'local_deleteoldcourses');
    $strfebruary = get_string('february', 'local_deleteoldcourses');
    $strmarch = get_string('march', 'local_deleteoldcourses');
    $strapril = get_string('april', 'local_deleteoldcourses');
    $strmay = get_string('may', 'local_deleteoldcourses');
    $strjune = get_string('june', 'local_deleteoldcourses');
    $strjuly = get_string('july', 'local_deleteoldcourses');
    $straugust = get_string('august', 'local_deleteoldcourses');
    $strseptember = get_string('september', 'local_deleteoldcourses');
    $stroctober = get_string('october', 'local_deleteoldcourses');
    $strnovember = get_string('november', 'local_deleteoldcourses');
    $strdecember = get_string('december', 'local_deleteoldcourses');

    $monthsoftheyear = array($strjanuary, $strfebruary, $strmarch, $strapril, $strmay, $strjune, $strjuly,
                            $straugust, $strseptember, $stroctober, $strnovember, $strdecember);

    return $monthsoftheyear;
}

/**
 * get_days_of__the_month
 *
 * @return array $daysofthemonth
 */
function get_days_of_the_month() {
    $daysofthemonth = array();
    for ($i = 1; $i <= 31; $i++) {
        array_push($daysofthemonth, $i);
    }
    return $daysofthemonth;
}

/**
 * Return hours in a day
 *
 * @return array $hoursinaday
 */
function get_hours_in_day() {
    $hoursinaday = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09');
    for ($i = 10; $i <= 23; $i++) {
        array_push($hoursinaday, strval($i));
    }
    return $hoursinaday;
}

/**
 * Return minutes in a day
 *
 * @return array $minutesinahour
 */
function get_minutes_in_hour() {
    $minutesinahour = array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09');
    for ($i = 10; $i <= 59; $i++) {
        array_push($minutesinahour, strval($i));
    }
    return $minutesinahour;
}

/****************************************************************************
**************************** End Other Methods ******************************
****************************************************************************/
