<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/hisaveliy
 * @since      1.0.0
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/admin
 * @author     Saveliy D. <dzvonkevich@gmail.com>
 */
class Simple_Text_Converter_Admin {

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

    add_action('wp_ajax_save_sta_document', __CLASS__ . '::save_sta_document');
    add_action('wp_ajax_nopriv_save_sta_document', __CLASS__ . '::save_sta_document');

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
		 * defined in Simple_Text_Converter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simple_Text_Converter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simple-text-converter-admin.css', array(), $this->version, 'all' );

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
		 * defined in Simple_Text_Converter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Simple_Text_Converter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/simple-text-converter-admin.js', array( 'jquery' ), $this->version, false );

	}

	public static function save_sta_document() {

	  $response    = array();
	  $document    = isset($_POST['document']) ? $_POST['document'] : '';
	  $post_status = isset($_POST['post_status']) ? $_POST['post_status'] : 'private';
	  $post_id     = isset($_POST['post_id']) ? $_POST['post_id'] : '';
	  $post_title  = isset($_POST['post_title']) ? $_POST['post_title'] : '';

	  if ( ! $document ) :
      $response['error'] = 'A document can not be empty.';
	  else :

      $post_data = array(
        'post_title'   => $post_title,
        'post_content' => $document,
	      'post_author'  => get_current_user_id(),
        'post_type'    => 'document',
        'post_status'  => $post_status,
      );

	    if ( $post_id )
        $post_data['ID'] = $post_id;

      $response['post_id']  = wp_insert_post( $post_data );
      $response['redirect'] = get_permalink( $response['post_id'] );

    endif;

	  echo json_encode($response);
    wp_die();

  }

}
