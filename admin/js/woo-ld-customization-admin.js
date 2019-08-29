(function( $ ) {
	'use strict';

	$('.woo_ld_restrict_post').change(function(){
		if($(this).prop("checked")) {
			$('.woo_ld_quiz_selection').show("slow");
			$( "#woo_ld_quiz, #woo_ld_question, #woo_ld_answer" ).attr("required", "required");
		} else {
			$('.woo_ld_quiz_selection').hide("slow");
			$( "#woo_ld_quiz, #woo_ld_question, #woo_ld_answer" ).removeAttr("required");
		}
	});

	$('#woo_ld_quiz').on('select2:select', function (e) {
		var data 			= e.params.data;
		var quiz_id 		= data.id;
		var questions_data 	=  [];
		var answers_data 	=  [{
			"id"	: 	"",
			"text"	: 	WOOLD.select_answer
		}];

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

			        $('#woo_ld_question').empty().select2({data : questions_data});
					$('#woo_ld_answer').empty().select2({data : answers_data });
					console.log(questions_data);
					console.log(answers_data);
				}
			}
		});
	});

	$('#woo_ld_question').on('select2:select', function (e) {
		var data 			= e.params.data;
		var question_id 		= data.id;
		var answerss_data 	= [];

		var data = {
			'action'	: 'get_question_answers',
			'question_id'	: question_id
		};

		jQuery.post(WOOLD.ajax_url, data, function(response){
			if( response.success ) {
				if(response.data) {
					$.each(response.data, function (index) {
			            answerss_data.push({
			                id: response.data[index].id,
			                text: response.data[index].title
			            });
			        });
					$('#woo_ld_answer').empty();
			        $('#woo_ld_answer').select2({data: answerss_data});
				}
			}
		});
	});

})( jQuery );
