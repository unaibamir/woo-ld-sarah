(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


	$('.woo_ld_restrict_post').change(function(){
		if($(this).prop("checked")) {
			$('.woo_ld_quiz_selection').show("slow");
		} else {
			$('.woo_ld_quiz_selection').hide("slow");
		}
	});

	$('#woo_ld_quiz').on('select2:select', function (e) {
		var data 			= e.params.data;
		var quiz_id 		= data.id;
		var questions_data 	= [];

		var data = {
			'action'	: 'get_quiz_questions',
			'quiz_id'	: quiz_id
		};

		jQuery.post(WOOLD.ajax_url, data, function(response){
			if( response.success ) {
				if(response.data) {
					$.each(response.data, function (index) {
			            questions_data.push({
			                id: response.data[index].id,
			                text: response.data[index].title
			            });
			        });

			        $('#woo_ld_question').select2({data: questions_data});
				}
			}
		});
	});

	$('#woo_ld_question').on('select2:select', function (e) {
		var data 			= e.params.data;
		var quiz_id 		= data.id;
		var questions_data 	= [];

		var data = {
			'action'	: 'get_question_answers',
			'quiz_id'	: quiz_id
		};

		jQuery.post(WOOLD.ajax_url, data, function(response){
			if( response.success ) {
				if(response.data) {
					$.each(response.data, function (index) {
			            questions_data.push({
			                id: response.data[index].id,
			                text: response.data[index].title
			            });
			        });

			        $('#woo_ld_question').select2({data: questions_data});
				}
			}
		});
	});

})( jQuery );
