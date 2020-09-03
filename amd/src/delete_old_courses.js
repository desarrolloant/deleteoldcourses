define(
	[
		'jquery',
		'core/ajax',
		'core/modal_factory',
		'core/modal_events',
		'core/loadingicon',
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

		var getModal = function(){
			return ModalFactory.create({
				type: ModalFactory.types.SAVE_CANCEL,
				title: '',
				body: '',
				large: true
			});
		}

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
		var create_modal_promise = getModal();
		var get_strings_promise = getStrings();

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

		var addCoursesToList = function(course, button){
			$(button).attr("disabled", true);
			var request = {
	            methodname: 'local_deleteoldcourses_add_course',
	            args: {
					courseid:course.id,
					shortname:course.shortname,
					fullname:course.fullname,
				}
	        };
	        var promise = Ajax.call([request])[0];
	        $("button[course-id='"+course.id+"']").html("<i class='icon fa fa-circle-o-notch fa-spin'></i>");
	        promise.then(function(response){
	        	my_modal.getRoot().unbind();
	        	if (response.success) {
	        		$("button[course-id='"+course.id+"']").removeClass("btn-primary");
	        		$("button[course-id='"+course.id+"']").removeClass("add-course");
	        		$("button[course-id='"+course.id+"']").addClass("btn-danger");
	        		$("button[course-id='"+course.id+"']").addClass("remove-course");
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");

	        		$(button).unbind();
	        		removeCoursesFromList();

	        	}else{
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");
	        	}
	        	$(button).attr("disabled", false);
	        })
		}

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
			            	//Remove modal confirmation events
			            	my_modal.getRoot().unbind();
			            	//Add course to delete list
			                addCoursesToList(course, button);
			            });
			            root.on(ModalEvents.cancel, function() {
			            	//Remove modal confirmation events
			            	my_modal.getRoot().unbind();
			            });
					}else{
						my_modal.setTitle(my_strings[5]);
						my_modal.setBody("<button type='button' class='btn btn-secondary' data-action='cancel'>"+my_strings[6]+"</button>");
						my_modal.header.removeClass('hidden');
					}
				}).catch;
			});
		}

		var openModal = function(){
			$("button.add-course").click(function(){
				my_modal.getRoot().unbind();
				var courseid = $(this).attr("course-id");
				var gerCoursePromise = getCourse(courseid);
				buildModal(gerCoursePromise, this);
			});
		}

		var removeCoursesFromList = function(){
			$("button.remove-course").click(function(){
				my_modal.getRoot().unbind();
				var button = $(this);
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
		        	my_modal.getRoot().unbind();
		        	if (response.success) {
		        		$("button[course-id='"+courseid+"']").removeClass("btn-danger");
		        		$("button[course-id='"+courseid+"']").removeClass("remove-course");
		        		$("button[course-id='"+courseid+"']").addClass("btn-primary");
		        		$("button[course-id='"+courseid+"']").addClass("add-course");
		        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");

		        		button.unbind();
	        			openModal();
		        	}else{
		        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");
		        	}
		        	button.attr("disabled", false);
		        })
		    });
		}

		var init = function(){
			//Execute get modal promise
			create_modal_promise.then(function(modal){
				my_modal = modal;
				//Execute get strings promise
				get_strings_promise.then(function(strings){
					my_strings = strings;
					var openmodal = openModal();
					var removeCourse = removeCoursesFromList();
				});
			});
	
		}

		return {
			'init':init,
		}
});