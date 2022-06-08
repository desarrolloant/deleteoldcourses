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
 * JavaScript for show alert in mydashboard page.
 *
 * @module     local_deleteoldcourses/show_alert
 * @package    local_deleteoldcourses
 * @copyright  2020 Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
	[
		'jquery'
	], function(
		$
	){

		var SELECTORS = {
	        PAGE_CONTENT: 'section[id="region-main"] .card'
	    };

	    /**
	     * Create html text for show alert in dashboard teacher.
	     *
	     * @param {string} link link to deleteoldcourses plugin
	     * @param {string} str_content traslation content
	     * @return {string} str_link traslation link text
	     */
		var builWarning = function(link, str_content, str_link){
			var alert = '<div class="alert alert-warning text-center" id="alert_delete_courses">';
			alert += '<h5>';
			alert += str_content;
			alert += ' <a href="'+link+'" target="_blank" style="color:#D51B23;">'+str_link+'</a>';
			alert += '</h5>';
			alert += '</div>';
			$($(SELECTORS.PAGE_CONTENT)[0]).prepend(alert);
		}

		/**
	     * Trigger the first load of the preview section and then listen for modifications.
	     *
	     * @param
	     */
		var init = function(link, str_content, str_link){
			builWarning = builWarning(link, str_content, str_link);
		}

		return {
			'init':init,
		}
});
