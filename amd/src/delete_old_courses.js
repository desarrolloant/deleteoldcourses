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
 * JavaScript for the delete old courses plugin.
 *
 * @module    local_deleteoldcourses/delete_old_courses
 * @package   local_deleteoldcourses
 * @Author 	  Diego Fdo Ruiz <diego.fernando.ruiz@correounivalle.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(
	[
		'jquery',
		'core/ajax',
		'core/modal_factory',
		'core/modal_events',
		'local_deleteoldcourses/loadingicon',
		'core/str'
	], function(
		$,
		Ajax,
		ModalFactory,
		ModalEvents,
		LoadingIcon,
		String
	){

		var SELECTORS = {
	        SAVE_BUTTON: '[data-action="save"]',
	        CANCEL_BUTTON: '[data-action="cancel"]',
	        LOADER_SPAN: 'span.loading-icon.icon-no-margin'
	    };

	    /////////////Initial promises/////////////////
		var getStrings = function (){

			var strings = [
				{
					key: 'modal_delete_title',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_danger_body',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_accept',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_save_button',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_cancel_button',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_no_teacher',
					component: 'local_deleteoldcourses'
				},
				{
					key: 'modal_delete_close_button',
					component: 'local_deleteoldcourses'
				}
			];
			var promise = String.get_strings(strings);
			return promise;
		}

		/**
	     * Get the modal factory promise.
	     *
	     * @param .
	     * @return {promise} Promise for create modal.
	     */
		var getModal = function(){
			return ModalFactory.create({
				type: ModalFactory.types.SAVE_CANCEL,
				title: '',
				body: '',
				large: true
			});
		}

		/**
	     * Get the course info promise.
	     *
	     * @param {int} courseid id of course to delete.
	     * @return {promise} Promise for get course.
	     */
		var getCourse = function(courseid){
			var request = {
	            methodname: 'local_deleteoldcourses_get_course',
	            args: {
					courseid:courseid
				}
	        };
	        var promise = Ajax.call([request])[0];
	        return promise;
		}

		//Variables for initial promises
		var my_modal = null;
		var my_strings = null;
		var get_strings_promise = getStrings();

		/**
	     * Create html text for show alert with other teachers.
	     *
	     * @param {string} message head message.
	     * @param {string[]} teachers others teachers of a course.
	     * @return {string} string for show html alert.
	     */
		var builWarning = function(message, teachers){
			var alert = "<div class='alert alert-warning p-1'>";
			alert += "<i class='fa fa-exclamation-triangle' aria-hidden='true'></i> ";
			alert += message+"<br>";
			var teachers_list = "<ul class='pl-5'>";
			$.each(teachers, function(key, teacher){
				teachers_list += "<li><a href='"+teacher.url+"' target='_blank'>"+teacher.fullname+"</a></li>";
			});
			teachers_list += "</ul>";
			alert += teachers_list;
			alert += "</div>";
			return alert;
		}

		/**
	     * Send course info by ajax to add deletion list.
	     *
	     * @param {jquery} course course object info.
	     * @param {jquery} button pressed button add.
	     * @return
	     */
		var addCoursesToList = function(course, button){
			$(button).attr("disabled", true);
			var request = {
	            methodname: 'local_deleteoldcourses_add_course',
	            args: {
					courseid:course.id,
					shortname:course.shortname,
					fullname:course.fullname,
					coursecreatedat:course.coursecreatedat
				}
	        };
	        var promise = Ajax.call([request])[0];
	        $("button[course-id='"+course.id+"']").html("<i class='icon fa fa-circle-o-notch fa-spin'></i>");
	        promise.then(function(response){
	        	if (response.success) {
	        		$("button[course-id='"+course.id+"']").removeClass("btn-primary");
	        		$("button[course-id='"+course.id+"']").removeClass("add-course");
	        		$("button[course-id='"+course.id+"']").addClass("btn-danger");
	        		$("button[course-id='"+course.id+"']").addClass("remove-course");
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");

	        		//Remove add event
	        		$(button).unbind();

	        		//Add remove event
	        		$(button).click(function(){
	        			removeCoursesFromList(this);
	        		});

	        	}else{
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");
	        	}
	        	$(button).attr("disabled", false);
	        })
		}

		/**
	     * Prepare modal for delete a course.
	     *
	     * @param {promise} gerCoursePromise promise for get course.
	     * @param {jquery} button pressed button add.
	     * @return
	     */
		var buildModal = function(gerCoursePromise, button){
			my_modal.show();
			my_modal.setBody("");
			my_modal.header.addClass('hidden');
			my_modal.footer.addClass('hidden');
			//Execute add loader promise
			LoadingIcon.addIconToContainer(my_modal.body).then(function(loadingIcon){
				//Execute get course promise
				gerCoursePromise.then(function(course) {
					if (course.id) {
						my_modal.setTitle(my_strings[0]+" <strong>"+course.shortname+"</strong>");
						var body_content = "";
						if (course.teachers.length > 0) {
							var warning = builWarning(my_strings[1], course.teachers);
							body_content += warning;
						}
						my_modal.header.removeClass('hidden');
						my_modal.footer.removeClass('hidden');
						my_modal.setSaveButtonText(my_strings[3]);

						var btn_cancel = my_modal.getFooter().find(SELECTORS.CANCEL_BUTTON);
						my_modal.asyncSet(my_strings[4], btn_cancel.text.bind(btn_cancel));

						body_content += "<strong>"+my_strings[2]+"</strong>";
						my_modal.setBody(body_content);
	       				
						//Add buton confirm - cancel event
						var root = my_modal.getRoot();
			            root.on(ModalEvents.save, function() {
			            	//Destroy modal
			            	my_modal.destroy();

			            	//Add course to delete list
			                addCoursesToList(course, button);
			            });
					}else{
						my_modal.setTitle(my_strings[5]);
						my_modal.setBody("<button type='button' class='btn btn-secondary' data-action='cancel'>"+my_strings[6]+"</button>");
						my_modal.header.removeClass('hidden');
					}
				}).catch;
			});
		}

		/**
	     * Execute create modal promise and add its content.
	     *
	     * @param {jquery} button pressed button add.
	     * @return
	     */
		var openModal = function(trigger){
			$("div.modal.moodle-has-zindex").remove();
			var create_modal_promise = getModal();
			//Execute get modal promise
			create_modal_promise.then(function(modal){
				my_modal = modal;
				my_modal.getRoot().on(ModalEvents.cancel, function() {
	            	my_modal.destroy();
	            });
	            my_modal.getRoot().on(ModalEvents.hidden, function() {
	            	my_modal.destroy();
	            });
				var courseid = $(trigger).attr("course-id");
				var gerCoursePromise = getCourse(courseid);
				buildModal(gerCoursePromise, trigger);
			});
		}

		/**
	     * Remove a course added to deletion list by ajax.
	     *
	     * @param {jquery} button pressed button remove.
	     * @return
	     */
		var removeCoursesFromList = function(trigger){
			var button = $(trigger);
			button.attr("disabled", true);
			var courseid = button.attr("course-id");

			var request = {
	            methodname: 'local_deleteoldcourses_remove_course',
	            args: {
					courseid:courseid
				}
	        };
	        var promise = Ajax.call([request])[0];
	        $("button[course-id='"+courseid+"']").html("<i class='icon fa fa-circle-o-notch fa-spin'></i>");
	        promise.then(function(response){
	        	if (response.success) {
	        		$("button[course-id='"+courseid+"']").removeClass("btn-danger");
	        		$("button[course-id='"+courseid+"']").removeClass("remove-course");
	        		$("button[course-id='"+courseid+"']").addClass("btn-primary");
	        		$("button[course-id='"+courseid+"']").addClass("add-course");
	        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");

	        		//Remove remove event
	        		button.unbind();

	        		//Add add event
        			button.click(function(){
        				openModal(this);
        			});
	        	}else{
	        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");
	        	}
	        	button.attr("disabled", false);
	        })
		}

		/**
	     * Trigger the first load of the preview section and then listen for modifications
	     *
	     * @param.
	     */
		var init = function(){
			
			//Execute get strings promise
			get_strings_promise.then(function(strings){

				//Start strings
				my_strings = strings;

				//Buttons start events
				$("button.add-course").click(function(){
					openModal(this);
				});
				$("button.remove-course").click(function(){
					removeCoursesFromList(this);
				});

			});
		
	
		}

		return {
			'init':init,
		}
});