<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wooninjas.com
 * @since      1.0.0
 *
 * @package    Woo_Ld_Customization
 * @subpackage Woo_Ld_Customization/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Ld_Customization
 * @subpackage Woo_Ld_Customization/admin
 * @author     WooNinjas <unaib.webxity@gmail.com>
 */
class Woo_Ld_Customization_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->lang = "woo-ld-customization";

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Ld_Customization_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Ld_Customization_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-ld-customization-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Ld_Customization_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Ld_Customization_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-ld-customization-admin.js', array( 'jquery' ), time(), true );

		
		$translation_array = array(
			'ajax_url' 			=> 	admin_url( "admin-ajax.php" ),
			"select_quiz"		=>	__("Please Select Quiz", $this->lang),
			"select_question"	=>	__("Please Select Question", $this->lang),
			"select_answer"		=>	__("Please Select Answer", $this->lang),
		);
		wp_localize_script( $this->plugin_name, 'WOOLD', $translation_array );

		wp_enqueue_script( $this->plugin_name );

	}


	public function get_quiz_questions( $quiz_id = false ) {

		$quiz_id 		= isset($_POST["quiz_id"]) ? $_POST["quiz_id"] : $quiz_id;
		$doing_ajax 	= isset($_POST["quiz_id"]) ? true : false;
		$questions 		= array();

		if( !$quiz_id )  return $questions;
		
		$question_args = array(
			'post_type'   		=> 'sfwd-question',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
			'meta_key'			=>	'quiz_id',
			'meta_value'		=>	$quiz_id
		);

		$questions_query = new WP_Query( $question_args );
		if ( $questions_query->have_posts() ) {
			$questions_data = $questions_query->posts;
			if( !empty($questions_data) ) {
				
				// Placeholder 
				$questions[0]["id"] 	= 	"";
				$questions[0]["title"] 	= 	__("Please Select Question", $this->lang);

				$counter = 1;
				foreach ($questions_data as $key => $question) {
					$q_type 							= get_post_meta($question->ID, "question_type", true);
					if( $q_type != "single" ) {
						continue;
					}
					$questions[$counter]["id"] 			= 	$question->ID;
					$questions[$counter]["title"] 		= 	$question->post_title;
					$questions[$counter]["quiz_id"] 	= 	get_post_meta($question->ID, "quiz_id", true);
					$counter++;
				}
			}
		}

		if(!empty( $questions )) {
			if( $doing_ajax ) {
				wp_send_json_success( $questions );
			} else {
				return $questions;
			}
		} else {
			wp_send_json_error( null );
		}
	}


	public function get_question_answers( $question_id = false ) {

		$answers 					= 	array();
		$question_id 				= 	isset($_POST["question_id"]) ? $_POST["question_id"] : $question_id;
		$doing_ajax 				= 	isset($_POST["question_id"]) ? true : false;
		
		$question_mapper 			= 	new WpProQuiz_Model_QuestionMapper();
		$proquiz_controller_question= 	new WpProQuiz_Controller_Question();
		$question_pro_id 			= 	(int) get_post_meta( $question_id, 'question_pro_id', true );

		if(empty($question_pro_id)) 
			if( $doing_ajax ) wp_send_json_error( null );
			else return null;

		$pro_question_edit 			= $question_mapper->fetch( $question_pro_id );

		if ( ( $pro_question_edit ) && is_a( $pro_question_edit, 'WpProQuiz_Model_Question' ) ) {
			$pro_question_data 		= $proquiz_controller_question->setAnswerObject( $pro_question_edit );
		} else {
			$pro_question_data 		= $proquiz_controller_question->setAnswerObject();
		}


		$counter = 1;
		if(!empty($pro_question_data["classic_answer"])) {
			// Placeholder 
			$answers[0]["id"] 			= 	"";
			$answers[0]["title"] 		= 	__("Please Select Answer", $this->lang);

			foreach ($pro_question_data["classic_answer"] as $key => $value) {
				$answers[$counter]["id"] 			= 	$value->getAnswer();
				$answers[$counter]["title"] 		= 	$value->getAnswer();
				$counter++;
			}
		}

		if(!empty( $answers )) {
			if( $doing_ajax ) {
				wp_send_json_success( $answers );
			} else {
				return $answers;
			}
		} else {
			wp_send_json_error( null );
		}
	}

	public function getAllQuestions() {


		$questions = $quizzes = array();

		$quiz_args = array(
			'post_type'   		=> 'sfwd-quiz',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
			/*'meta_key'       	=> 'course_id',
			'meta_value'     	=> $course_id*/
		);

		$quiz_query = new WP_Query( $quiz_args );
		if ( $quiz_query->have_posts() ) {
			$quizzes = $quiz_query->posts;
			if( !empty($quizzes) ) {
				foreach ($quizzes as $key => $quiz) {
					$quiz_id 	= 	$quiz->ID;
				}
			}
		}

		$question_args = array(
			'post_type'   		=> 'sfwd-question',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
		);


		
		$questions_query = new WP_Query( $question_args );
		if ( $questions_query->have_posts() ) {
			$questions_data = $questions_query->posts;
			foreach ($questions_data as $key => $question) {
				$questions[$key]["id"] 			= 	$question->ID;
				$questions[$key]["title"] 		= 	$question->post_title;
				$questions[$key]["quiz_id"] 	= 	get_post_meta($question->ID, "quiz_id", true);
			}
		}

		//dd($questions);


		/*$id = 35;
		$quiz_post_id = 27139;
		$view = new WpProQuiz_View_FrontQuiz();
		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$quiz = $quizMapper->fetch( $id );
		if ( $quiz_post_id !== absint( $quiz->getPostId() ) ) {
			$quiz->setPostId( $quiz_post_id );
		}

		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		$formMapper     = new WpProQuiz_Model_FormMapper();

		$questionModels = $questionMapper->fetchAll( $quiz );

		$view->quiz     = $quiz;
		$view->question = $questionModels;
		$view->category = $categoryMapper->fetchByQuiz( $quiz );

		$question_count = count( $questionModels );
		ob_start();
		$quizData = $view->showQuizBox( $question_count );
		ob_get_clean();

		$json    = $quizData['json'];
		$obj  = $questionModels[27152];
		dd(($obj->getAnswerData())[0]);
		$results = array();
		$question_index = 0;*/

		$question_mapper = new WpProQuiz_Model_QuestionMapper();
		$proquiz_controller_question = new WpProQuiz_Controller_Question();
		$pro_question_edit = $question_mapper->fetch( 468 );

		if ( ( $pro_question_edit ) && is_a( $pro_question_edit, 'WpProQuiz_Model_Question' ) ) {
			$pro_question_data = $proquiz_controller_question->setAnswerObject( $pro_question_edit );
		} else {
			$pro_question_data = $proquiz_controller_question->setAnswerObject();
		}

		$view = new WpProQuiz_View_QuestionEdit();

		//dd($pro_question_data);
		foreach ($pro_question_data["classic_answer"] as $value) {
			//dd($value->getAnswer(), false);
		}
		//dd("sdas");
			
	}


	public function learndash_quiz_selection_add_meta_box() {
		add_meta_box( 'learndash-quiz-selection', __( 'Conditional Course Path', $this->lang ), array( $this, 'learndash_course_grid_output_meta_box'), array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic' ), 'advanced', 'low' );
	}

	public function learndash_course_grid_output_meta_box($post) {
		global $wpdb;
		$post_id 				= $post->ID;
		$course_id 				= learndash_get_course_id($post_id);
		$woo_restrict_post 		= get_post_meta($post_id, "_woo_restrict_post", true);
		$woo_quiz_id 			= get_post_meta($post_id, "_woo_quiz_id", true);
		$woo_question_id 		= get_post_meta($post_id, "_woo_question_id", true);
		$woo_answer_id 			= get_post_meta($post_id, "_woo_answer_id", true);

		$questions 				= $this->get_quiz_questions($woo_quiz_id);
		$answers 				= $this->get_question_answers($woo_question_id);
		
		$quizzes = $args = $course_args = array();
		$args = array(
			'post_type'   		=> 'sfwd-quiz',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
			//'meta_key'       	=> 'course_id',
		);

		if( $post->post_type == "sfwd-courses" ) {
			$course_args = array(
				/*'meta_value'     	=> $course_id,
				'meta_compare'     	=> '!=',*/
			);
		}

		$args = wp_parse_args($course_args, $args);
		//dd($args, false);
		$quiz_query = new WP_Query( $args );
		if ( $quiz_query->have_posts() ) {
			$quizzes = $quiz_query->posts;
		}

		if(empty($quizzes)) {
			return;
		}
		
		?>
		<div class="sfwd sfwd_options learndash-course-display-content-settings">
			<div class="sfwd_input">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;">
					<a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('learndash_woo_course_post_restrict');"><img src="<?php echo LEARNDASH_LMS_PLUGIN_URL . 'assets/images/question.png' ?>">
					<label class="sfwd_label textinput"><?php _e( 'Restrict Current Post?', $this->lang ); ?></label></a>
				</span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div">
						<input type="hidden" name="woo_ld[post_restrict]" value="0">
						<label for="woo_ld_restrict_post">
							<input type="checkbox" name="woo_ld[post_restrict]" id="woo_ld_restrict_post" class="woo_ld_restrict_post" value="1" <?php checked( $woo_restrict_post, 1, true ); ?>>
							Restrict
						</label>
					</div>
					<div class="sfwd_help_text_div" style="display:none" id="learndash_woo_course_post_restrict">
						<label class="sfwd_help_text"><?php _e( 'By checking this checkbox, the current post will be restricted to users. The post will be available to user if the below quiz is passed with selected answer.', $this->lang ); ?></label>
					</div>
				</span>
				<p style="clear:left"></p>
			</div>

			<div class="sfwd_input sfwd_input_type_select woo_ld_quiz_selection" style="display: <?php echo $woo_restrict_post == "1" ? "block" : "none"; ?>">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;">
					<a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('learndash_woo_quiz_select');">
						<img src="<?php echo LEARNDASH_LMS_PLUGIN_URL . 'assets/images/question.png' ?>">
					<label class="sfwd_label textinput"><?php _e( 'Select Quiz/Question/Answer', $this->lang ); ?></label></a>
				</span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div">
						<span class="ld-select ld-select2">
							<select name="woo_ld[quiz_id]" id="woo_ld_quiz" data-ld-select2="1" class="learndash-section-field learndash-section-field-select"  oninvalid="this.setCustomValidity(<?php _e("Please select quiz first", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<option value=""><?php _e("Please Select Quiz", $this->lang); ?></option>
								<?php
								foreach ($quizzes as $quiz) {
									?>
									<option value="<?php echo $quiz->ID; ?>" <?php selected( $woo_quiz_id, $quiz->ID ); ?>>
										<?php echo $quiz->post_title; ?>
									</option>
									<?php
								}
								?>
							</select>
						</span>
						<p></p>
						<span class="ld-select ld-select2">
							<select name="woo_ld[question_id]" id="woo_ld_question" data-ld-select2="1" class="learndash-section-field learndash-section-field-select"  oninvalid="this.setCustomValidity(<?php _e("Please select question", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<?php
								if( !empty($questions) ) {
									foreach ($questions as $key => $question) {
										?>
										<option value="<?php echo $question["id"]; ?>" <?php selected( $question["id"], $woo_question_id ); ?>>
											<?php echo $question["title"]; ?>
										</option>
										<?php
									}
								} else {
									?>
									<option value=""><?php _e("Please Select Question", $this->lang); ?></option>
									<?php
								}
								?>
							</select>
						</span>
						<p></p>
						<span class="ld-select ld-select2">
							<select name="woo_ld[answer_id]" id="woo_ld_answer" data-ld-select2="1" class="learndash-section-field learndash-section-field-select"  oninvalid="this.setCustomValidity(<?php _e("Please select answer", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<?php
								if( !empty($answers) ) {
									foreach ($answers as $key => $answer) {
										
										$selected = sanitize_text_field($answer["title"]) == $woo_answer_id ? 'selected="selected"' : '';
										?>
										<option value="<?php echo sanitize_text_field($answer["title"]); ?>" <?php echo $selected; ?>>
											<?php echo sanitize_text_field($answer["title"]); ?>
										</option>
										<?php
									}
								} else {
									?>
									<option value=""><?php _e("Please Select Answer", $this->lang); ?></option>
									<?php
								}
								?>
							</select>
						</span>
					</div>
					<div class="sfwd_help_text_div" style="display:none" id="learndash_woo_course_post_restrict">
						<label class="sfwd_help_text"><?php _e( 'By checking this checkbox, the current post will be restricted to users. The post will be available to user if the below quiz is passed with selected answer.', $this->lang ); ?></label>
					</div>
				</span>
				<p style="clear:left"></p>
			</div>
		</div>
		<?php

		wp_nonce_field( "woo_ld_cus_action", 'woo_ld_cus_wpnonce' );
	}

	public function learndash_save_post_options($post_id) {
		
		$post_type 	= get_post_type($post_id);

		if ( !in_array($post_type, array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic' )) ) return;

		if( !isset($_POST["woo_ld_cus_wpnonce"]) || !wp_verify_nonce( $_POST["woo_ld_cus_wpnonce"], "woo_ld_cus_action" ) ) {
			wp_die("Something is wrong!");
		}

		update_post_meta( $post_id, "_woo_restrict_post", filter_var( $_POST["woo_ld"]["post_restrict"], FILTER_SANITIZE_NUMBER_INT ) );
		if( $_POST["woo_ld"]["post_restrict"] == 1 ) {
			update_post_meta( $post_id, "_woo_quiz_id", 	sanitize_text_field( $_POST["woo_ld"]["quiz_id"] ) );
			update_post_meta( $post_id, "_woo_question_id", sanitize_text_field( $_POST["woo_ld"]["question_id"] ) );
			update_post_meta( $post_id, "_woo_answer_id", 	sanitize_text_field( $_POST["woo_ld"]["answer_id"] ) );
		} else {
			update_post_meta( $post_id, "_woo_quiz_id", 	"" );
			update_post_meta( $post_id, "_woo_question_id", "" );
			update_post_meta( $post_id, "_woo_answer_id", 	"" );
		}
	}



	public function learndash_has_access( $has_access, $post_id, $user_id ) {

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = 0;
		}

		if( is_admin() ) {
			return $has_access;
		}

		$logged_in              = is_user_logged_in();
		$course_id 				= learndash_get_course_id($post_id);
		$woo_restrict_post 		= get_post_meta($post_id, "_woo_restrict_post", true);
		$woo_quiz_id 			= get_post_meta($post_id, "_woo_quiz_id", true);
		$woo_question_id 		= get_post_meta($post_id, "_woo_question_id", true);
		$woo_answer_id 			= get_post_meta($post_id, "_woo_answer_id", true);

		$has_passed 			= $this->getCheckUserAnswer( $user_id, $post_id );

		if ( $logged_in ) {
			if ( learndash_is_admin_user( $user_id ) ) {
				$bypass_course_limits_admin_users = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'bypass_course_limits_admin_users' );
				if ( 'yes' === $bypass_course_limits_admin_users ) {
					$bypass_course_limits_admin_users = true;
				} else {
					$bypass_course_limits_admin_users = false;
				}
			} else {
				$bypass_course_limits_admin_users = false;
			}
		} else {
			$bypass_course_limits_admin_users = false;
		}


		if( learndash_is_admin_user( $user_id ) && $bypass_course_limits_admin_users ) {
			$has_access = true;
		} else if( !learndash_is_admin_user( $user_id ) && $woo_restrict_post == 1 ) {
			if($has_passed) {
				$has_access = true;
			} else {
				$has_access = false;
			}
		} else {
			$has_access = true;
		}
		
		return $has_access;
	}


	public function learndash_quiz_completed( $quizdata, $user ) {
		$user_id  	=	$user->ID;
	}

	public function getCheckUserAnswer($user_id, $post_id) {

		$logged_in              = is_user_logged_in();
		$course_id 				= learndash_get_course_id($post_id);
		$woo_restrict_post 		= get_post_meta($post_id, "_woo_restrict_post", true);
		$woo_quiz_id 			= get_post_meta($post_id, "_woo_quiz_id", true);
		$woo_question_id 		= get_post_meta($post_id, "_woo_question_id", true);
		$woo_answer_id 			= get_post_meta($post_id, "_woo_answer_id", true);
		$user_quizzes 			= get_user_meta($user_id, "_sfwd-quizzes", true);
		$question_pro_id 		= get_post_meta($woo_question_id, 'question_pro_id', true);

		if( empty($user_quizzes) )
			return false;
		
		$user_quizzes 			= array_reverse($user_quizzes);
		foreach ($user_quizzes as $key => $user_quiz) {
			if( $user_quiz["quiz"] == $woo_quiz_id ) {

				$quiz_id 			= $woo_quiz_id;
				$quiz_pro_id 		= get_post_meta($quiz_id, "quiz_pro_id", true);
				$quiz_stat_ref_ids 	= $user_quiz["statistic_ref_id"];

				$view = new WpProQuiz_View_FrontQuiz();
				$quizMapper = new WpProQuiz_Model_QuizMapper();
				$quiz = $quizMapper->fetch( $quiz_pro_id );
				if ( $quiz_id !== absint( $quiz->getPostId() ) ) {
					$quiz->setPostId( $quiz_id );
				}

				$questionMapper = new WpProQuiz_Model_QuestionMapper();
				$categoryMapper = new WpProQuiz_Model_CategoryMapper();
				$formMapper     = new WpProQuiz_Model_FormMapper();

				$questionModels = $questionMapper->fetchAll( $quiz );

				$view->quiz     = $quiz;
				$view->question = $questionModels;
				$view->category = $categoryMapper->fetchByQuiz( $quiz );

				$question_count = count( $questionModels );
				ob_start();
				$quizData = $view->showQuizBox( $question_count );
				ob_get_clean();

				$json    = $quizData['json'];
				$results = array();
				$question_index = 0;
				$questionData           = $json[ $question_pro_id ];
				$q_answers 				= $questionModels[$woo_question_id]->getAnswerData();
				foreach ($q_answers as $q_answer) {
					if( $q_answer->isCorrect() == 1 ) {
						if( $q_answer->getAnswer() == $woo_answer_id ) {
							return true;
						} else {
							return false;
						}
					}
				}
				break;
			} else {
				return false;
			}
		}
		return false;
	}

}
