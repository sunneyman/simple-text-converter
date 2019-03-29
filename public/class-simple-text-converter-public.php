<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/hisaveliy
 * @since      1.0.0
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/public
 * @author     Saveliy D. <dzvonkevich@gmail.com>
 */
class Simple_Text_Converter_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

    add_filter('script_loader_tag', __CLASS__ . '::add_async_attribute', 10, 2);

    add_action( 'wp_enqueue_scripts', __CLASS__ . '::localize_script', 999 );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

    global $post;
    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/simple-text-converter-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

    global $post;

    $code_access = do_shortcode($post->post_content);
    $has_access  = current_user_can('mepr-active', "rule: {$code_access}") || current_user_can('edit_posts') || (int)$post->post_author === get_current_user_id();

    if ($has_access) :

      wp_enqueue_script( 'simple-text-converter', get_simple_text_converter_url() . '/public/js/simple-text-converter-public.js', array(), 1, true );
      
    endif;
	}

	public function does_have_access() {
    $code_access = do_shortcode(get_the_content());
    return current_user_can('mepr-active', "rule: {$code_access}") || current_user_can('edit_posts') || get_current_user_id() == 2;
  }

  public static function add_async_attribute($tag, $handle) {
    if ( !in_array($handle, [
      'simple-text-converter',
    ]) )
      return $tag;

    return str_replace( ' src', ' defer src', $tag );
  }

  public static function localize_script() {

    wp_localize_script('simple-text-converter', 'sta',
      array(
        'ajax' => admin_url('admin-ajax.php'),
      )
    );

  }

  public static function get_view() {

	  $post_id = '';
	  $title   = '';
    $status   = 'private';
    $content  = '';
    $author   = null;
    $post_id_attr = '';

	  if ( get_post_type() === 'document' ) :

      $post_id = get_the_ID();

      $post_id_attr = 'data-post-id="' . $post_id . '"';

	    $post_object = get_post( $post_id );

      $title   = $post_object->post_title;
      $status  = $post_object->post_status;
      $content = $post_object->post_content;
      $author  = $post_object->post_author;

    endif;

    $public_select = $status === 'publish' ? 'checked' : '';
    $can_modify    = $author ? (int) get_current_user_id() === (int) $author : true;
    $mode          = ! $can_modify ? 'readonly' : '';
	  ?>

    <div id="wrapper" class="stc--vain-view">

      <?php if ( $can_modify ) : ?>
        <header class="header">
        <div class="header--box box">

          <div class="header--save-panel hidden">
            <div class="header--save-panel__title">
              <input type="text" name="post_title" value="<?php echo $title; ?>" placeholder="Enter your filename here and click save">
              <label for="make-public">
                <input
                  type="checkbox"
                  id="make-public"
                  name="post_public"
                  <?php echo $public_select; ?>>
                Make document public for all VreadU users
              </label>
            </div>

            <button class="button button_primary button_save" data-stc-action="save">
              <i class="fa fa-save"></i>
              Save
            </button>
          </div>

          <div class="header--control-panel">
            <a href="<?php echo get_bloginfo('url'); ?>/help/" target="_blank">How to use</a>
            <button class="button button_primary button_wide" data-stc-action="convert">Convert</button>
          </div>
        </div>
      </header>
      <?php endif; ?>

      <main>
        <div class="box">
          <div
            id="stc"
            data-mode="<?php echo $mode; ?>"
            <?php echo $post_id_attr; ?>><?php echo $content; ?></div>
        </div>
      </main>

    </div>

    <?php
  }
}
