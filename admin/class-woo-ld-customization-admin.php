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
			'ajax_url' => admin_url( "admin-ajax.php" ),
		);
		wp_localize_script( $this->plugin_name, 'WOOLD', $translation_array );

		wp_enqueue_script( $this->plugin_name );
	}


	public function get_quiz_questions() {
		
		$question_args = array(
			'post_type'   		=> 'sfwd-question',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
			'meta_key'			=>	'quiz_id',
			'meta_value'		=>	$_POST["quiz_id"]
		);

		$questions_query = new WP_Query( $question_args );
		if ( $questions_query->have_posts() ) {
			$questions_data = $questions_query->posts;
			if( !empty($questions_data) ) {
				foreach ($questions_data as $key => $question) {
					$questions[$key]["id"] 			= 	$question->ID;
					$questions[$key]["title"] 		= 	$question->post_title;
					$questions[$key]["quiz_id"] 	= 	get_post_meta($question->ID, "quiz_id", true);
				}
			}
		}

		if(!empty( $questions )) {
			wp_send_json_success( $questions );
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

		dd($questions);
			
	}


	public function learndash_quiz_selection_add_meta_box() {
		add_meta_box( 'learndash-quiz-selection', __( 'Conditional Course Path', $this->lang ), array( $this, 'learndash_course_grid_output_meta_box'), array( 'sfwd-courses', 'sfwd-lessons' ), 'advanced', 'low' );
	}

	public function learndash_course_grid_output_meta_box($post) {
		global $wpdb;
		$post_id = $post->ID;
		$course_id = learndash_get_course_id($post_id);
		$quizzes = array();
		$args = array(
			'post_type'   		=> 'sfwd-quiz',
			'post_status' 		=> 'publish',
			'order'             => 'DESC',
			'orderby'           => 'date',
			'posts_per_page'    => -1,
			/*'meta_key'       	=> 'course_id',
			'meta_value'     	=> $course_id*/
		);
		
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
						<label for="">
							<input type="checkbox" name="woo_ld[post_restrict]" class="woo_ld_restrict_post" value="1" <?php checked( $enable_video, 1, true ); ?>>
							Restrict
						</label>
					</div>
					<div class="sfwd_help_text_div" style="display:none" id="learndash_woo_course_post_restrict">
						<label class="sfwd_help_text"><?php _e( 'By checking this checkbox, the current post will be restricted to users. The post will be available to user if the below quiz is passed with selected answer.', $this->lang ); ?></label>
					</div>
				</span>
				<p style="clear:left"></p>
			</div>

			<div class="sfwd_input sfwd_input_type_select woo_ld_quiz_selection" style="display: none;">
				<span class="sfwd_option_label" style="text-align:right;vertical-align:top;">
					<a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('learndash_woo_quiz_select');">
						<img src="<?php echo LEARNDASH_LMS_PLUGIN_URL . 'assets/images/question.png' ?>">
					<label class="sfwd_label textinput"><?php _e( 'Select Quiz/Question/Answer', $this->lang ); ?></label></a>
				</span>
				<span class="sfwd_option_input">
					<div class="sfwd_option_div">
						<span class="ld-select ld-select2">
							<select name="woo_ld[quiz_id]" id="woo_ld_quiz" data-ld-select2="1" class="learndash-section-field learndash-section-field-select" required="required" oninvalid="this.setCustomValidity(<?php _e("Please select quiz first", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<option value=""><?php _e("Please Select Quiz", $this->lang); ?></option>
								<?php
								foreach ($quizzes as $quiz) {
									?>
									<option value="<?php echo $quiz->ID; ?>"><?php echo $quiz->post_title; ?></option>
									<?php
								}
								?>
							</select>
						</span>
						<p></p>
						<span class="ld-select ld-select2">
							<select name="woo_ld[question_id]" id="woo_ld_question" data-ld-select2="1" class="learndash-section-field learndash-section-field-select" required="required" oninvalid="this.setCustomValidity(<?php _e("Please select question", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<option value=""><?php _e("Please Select Question", $this->lang); ?></option>
							</select>
						</span>
						<p></p>
						<span class="ld-select ld-select2">
							<select name="woo_ld[answer_id]" id="woo_ld_answer" data-ld-select2="1" class="learndash-section-field learndash-section-field-select" required="required" oninvalid="this.setCustomValidity(<?php _e("Please select answer", $this->lang);?>)" oninput="this.setCustomValidity('')">
								<option value=""><?php _e("Please Select Answer", $this->lang); ?></option>
								
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

	public function learndash_save_post_options() {

		$post_type = get_post_type($post_id);

		if ( !in_array($post_type, array( 'sfwd-courses', 'sfwd-lessons' )) ) return;

		if( !isset($_POST["woo_ld_cus_wpnonce"]) && !wp_verify_nonce( $_POST["woo_ld_cus_wpnonce"], "woo_ld_cus_action" ) ) {
			wp_die("Something is wrong!");
		}

	}

}
