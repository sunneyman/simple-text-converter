<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/hisaveliy
 * @since      1.0.0
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/includes
 * @author     Saveliy D. <dzvonkevich@gmail.com>
 */
class Simple_Text_Converter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Simple_Text_Converter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'simple-text-converter';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

    add_shortcode( 'simple-page-converter', array($this, 'simple_text_editor_shortcode') );
    add_action( 'template_redirect', array($this, 'no_access_redirect') );
    add_action( 'wp_head', array($this, 'enqueue_assets') );
    add_filter('body_class',array($this, 'update_body_class'));


    add_action( 'init', __CLASS__ . '::register_documents_post_type' );
	}



  /**
   * Register post type
   */
  public static function register_documents_post_type() {


    /**
     * @link https://wp-kama.ru/function/register_post_type
     */
    register_post_type('document', array(
      'label'  => null,
      'labels' => array(
        'name'               => __('Documents'),
        'singular_name'      => __('Document'),
        'add_new'            => __('Add Document'),
        'add_new_item'       => __('Add new Document'),
        'edit_item'          => __('Edit Document'),
        'new_item'           => __('New Document'),
        'view_item'          => __('See Document'),
        'search_items'       => __('Search Document'),
        'not_found'          => __('Not Found'),
        'not_found_in_trash' => __('Not Found in Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Documents'),
      ),
      'description'         => '',
      'public'              => true,
      'publicly_queryable'  => true,
      'exclude_from_search' => false,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_admin_bar'   => true,
      'show_in_nav_menus'   => true,
      'show_in_rest'        => true,
      'rest_base'           => true,
      'menu_position'       => null,
      'menu_icon'           => 'dashicons-media-document',
      'hierarchical'        => true,
      'supports'            => array( 'title', 'editor', 'author' ), // 'title','editor','author','thumbnail','excerpt','trackbacks','custom-fields','comments','revisions','page-attributes','post-formats'
      'has_archive'         => 'documents',
      'query_var'           => true,
    ) );


  }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Simple_Text_Converter_Loader. Orchestrates the hooks of the plugin.
	 * - Simple_Text_Converter_i18n. Defines internationalization functionality.
	 * - Simple_Text_Converter_Admin. Defines all hooks for the admin area.
	 * - Simple_Text_Converter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-text-converter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-simple-text-converter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-simple-text-converter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-simple-text-converter-public.php';

		$this->loader = new Simple_Text_Converter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Simple_Text_Converter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Simple_Text_Converter_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Simple_Text_Converter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Simple_Text_Converter_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Simple_Text_Converter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

  /**
   * @param $atts
   * @return mixed
   */
  public function simple_text_editor_shortcode( $atts ) {
    $atts = shortcode_atts( array(
      'memberpress-rule' => '',
    ), $atts );

    return $atts['memberpress-rule'];
  }

	public function no_access_redirect(){
		global $post;

    $has_access  = current_user_can('mepr-active', 'rule: 5830') || current_user_can('edit_posts');

    if (strlen(do_shortcode( $post->post_content )) === 4 || get_post_type() === 'document')
      if ((is_user_logged_in() && !$has_access || !is_user_logged_in()))
        header('Location: ' . get_bloginfo('url') . '/register/vreadu-membership/?action=checkout&txn=m#mepr_jump');
	}

	public function enqueue_assets() {
		global $post;

		$code_access = do_shortcode($post->post_content);
		$has_access  = current_user_can('mepr-active', "rule: {$code_access}") || current_user_can('edit_posts');

    if ($has_access) :
      ?>

      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
      <link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">

      <?php
    endif;
	}

	public function update_body_class( $classes ) {
		global $post;

		$code_access = do_shortcode($post->post_content);
		$has_access  = current_user_can('mepr-active', "rule: {$code_access}") || current_user_can('edit_posts');

  		if ($has_access) 
  			$classes[] = 'js-simple-text-converter';

		return $classes;
	}
}
