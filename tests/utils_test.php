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
 * Unit tests for utils class.
 *
 * @package    local_deleteoldcourses
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_deleteoldcourses;

/**
 * Unit tests for utils class
 *
 * @group      local_deleteoldcourses
 * @package    local_deleteoldcourses
 * @since      Moodle 3.10
 * @author     Iader E. García Gómez <iadergg@gmail.com>
 * @copyright  2022 Área de Nuevas Tecnologías - Universidad del Valle <desarrollo.ant@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class utils_test extends \advanced_testcase {

    /**
     * Test get courses to delete
     *
     * @since  Moodle 3.10
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::calculate_course_size
     */
    public function test_calculate_course_size() {
        $this->resetAfterTest(false);

        $course1 = $this->getDataGenerator()->create_course();
        $file1 = $this->create_stored_file('content', 'testfile.txt', [], $course1);

        $utils = new utils();
        $coursesize = $utils->calculate_course_size($course1->id);

        $this->assertIsInt($coursesize);
        $this->assertSame(7, $coursesize);
    }

    /**
     * Test migrate records
     *
     * @since  Moodle 3.10
     * @author  Juan Felipe Orozco Escobar <juan.orozco.escobar@correounivalle.edu.co>
     * @author Iader E. Garcia Gomez <iadergg@gmail.com>
     * @covers ::migrate_records
     */
    public function test_migrate_records() {
        global $DB;

        $this->resetAfterTest(false);

        // Migrate deleteoldcourses_deleted table.
        $user = $this->getDataGenerator()->create_user();

        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 1';
        $record->fullname = 'COURSE FULLNAME 1';
        $record->courseid = '1';
        $record->userid = $user->id;
        $record->size = '10';
        $record->coursecreatedat = 123456789;
        $record->timesenttodelete = 123456789;
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses_deleted', $record);

        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 2';
        $record->fullname = 'COURSE FULLNAME 2';
        $record->courseid = '2';
        $record->userid = 123;
        $record->size = '10';
        $record->coursecreatedat = 123456789;
        $record->timesenttodelete = 123456789;
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses_deleted', $record);

        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 3';
        $record->fullname = 'COURSE FULLNAME 3';
        $record->courseid = '3';
        $record->userid = 128;
        $record->size = '';
        $record->coursecreatedat = '';
        $record->timesenttodelete = '';
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses_deleted', $record);

        // Migrate deleteoldcourses table.
        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 1';
        $record->fullname = 'COURSE FULLNAME 1';
        $record->courseid = '1';
        $record->userid = $user->id;
        $record->size = '10';
        $record->coursecreatedat = 123456789;
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses', $record);

        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 2';
        $record->fullname = 'COURSE FULLNAME 2';
        $record->courseid = '2';
        $record->userid = '128';
        $record->size = '10';
        $record->coursecreatedat = 123456789;
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses', $record);

        $record = new \stdClass();
        $record->shortname = 'COURSE SHORTNAME 3';
        $record->fullname = 'COURSE FULLNAME 3';
        $record->courseid = '3';
        $record->userid = 0;
        $record->size = '10';
        $record->coursecreatedat = 123456789;
        $record->timecreated = time();

        $DB->insert_record('deleteoldcourses', $record);


        $utils = new utils();
        $utils->migrate_records();

        $numberofrecords = $DB->count_records('local_delcoursesuv_deleted');

        $this->assertSame(3, $numberofrecords);

    }

    /**
     * Helper to create a stored file object with the given supplied content.
     *
     * @param   string $filecontent The content of the mocked file
     * @param   string $filename The file name to use in the stored_file
     * @param   string $filerecord Any overrides to the filerecord
     * @param   stdClass $course The course in which the file will be attached
     * @return  stored_file
     */
    protected function create_stored_file($filecontent = 'content', $filename = 'testfile.txt', $filerecord = [], $course) {
        $filerecord = array_merge([
                'contextid' => \context_course::instance($course->id)->id,
                'component' => 'assignfeedback_comments',
                'filearea'  => 'feedback',
                'itemid'    => 0,
                'filepath'  => '/',
                'filename'  => $filename,
            ], $filerecord);

        $fs = get_file_storage();
        $file = $fs->create_file_from_string($filerecord, $filecontent);

        return $file;
    }
}
