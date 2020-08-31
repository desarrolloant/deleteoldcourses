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

		var buildModal = function(gerCoursePromise){
			ModalFactory.create({
				type: ModalFactory.types.SAVE_CANCEL,
				title: '',
				body: '',
				large: true
			}).done(function(modal) {
				modal.show();
				modal.header.addClass('hidden');
				modal.footer.addClass('hidden');
				//Execute add loader promise
				LoadingIcon.addIconToContainer(modal.body).then(function(loadingIcon){
					//Execute get course promise
					gerCoursePromise.then(function(course) {
							var get_strings_promise = getStrings();
							//Execute get strings promise
							get_strings_promise.then(function (strings) {
								if (course.id) {
									modal.setTitle(strings[0]+" <strong>"+course.shortname+"</strong>");
									var body_content = "";
									if (course.teachers.length > 0) {
										var warning = builWarning(strings[1], course.teachers);
										body_content += warning;
									}
									body_content += "<strong>"+strings[2]+"</strong>";
									modal.setBody(body_content);
									modal.setSaveButtonText(strings[3]);
									var btn_cancel = modal.getFooter().find(SELECTORS.CANCEL_BUTTON);
				       				modal.asyncSet(strings[4], btn_cancel.text.bind(btn_cancel));
				       				modal.header.removeClass('hidden');
									modal.footer.removeClass('hidden');
									//Add buton confirm event
									var root = modal.getRoot();
						            root.on(ModalEvents.save, function() {
						                addCoursesToList(course);
						                // Do something to delete item
						            });
								}else{
									modal.setTitle(strings[5]);
									modal.setBody("<button type='button' class='btn btn-secondary' data-action='cancel'>"+strings[6]+"</button>");
									modal.header.removeClass('hidden');
								}
							});
					}).catch;
				});
			});
		}

		var openModal = function(){
			$("button.add-course").click(function(){
				var courseid = $(this).attr("course-id");
				var gerCoursePromise = getCourse(courseid);
				buildModal(gerCoursePromise);
			});
		}


		var addCoursesToList = function(course){
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
	        	if (response.success) {
	        		$("button[course-id='"+course.id+"']").removeClass("btn-primary");
	        		$("button[course-id='"+course.id+"']").addClass("btn-danger");
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");
	        	}else{
	        		$("button[course-id='"+course.id+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");
	        	}
	        })
		}

		var removeCoursesFromList = function(courseid){
			$("button.add-course").click(function(){
				var courseid = $(this).attr("course-id");

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
		        		$("button[course-id='"+courseid+"']").addClass("btn-primary");
		        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-trash' aria-hidden='true'></i>");
		        	}else{
		        		$("button[course-id='"+courseid+"']").html("<i class='fa fa-check' aria-hidden='true'></i>");
		        	}
		        })
		    });
		}




		var init = function(){
			var openmodal = openModal();
		}

		return {
			'init':init,
		}
});