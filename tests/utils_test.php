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

        $coursedispatcher = new utils();
        $coursesize = $coursedispatcher->calculate_course_size($course1->id);

        $this->assertIsInt($coursesize);
        $this->assertSame(7, $coursesize);
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
