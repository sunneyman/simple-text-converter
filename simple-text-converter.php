<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/hisaveliy
 * @since             1.2.0
 * @package           Simple_Text_Converter
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Text Converter
 * Plugin URI:        https://github.com/hisaveliy
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.2.0
 * Author:            Saveliy D.
 * Author URI:        https://github.com/hisaveliy
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       simple-text-converter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-simple-text-converter-activator.php
 */
function activate_simple_text_converter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-text-converter-activator.php';
	Simple_Text_Converter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-text-converter-deactivator.php
 */
function deactivate_simple_text_converter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-simple-text-converter-deactivator.php';
	Simple_Text_Converter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_simple_text_converter' );
register_deactivation_hook( __FILE__, 'deactivate_simple_text_converter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-simple-text-converter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_text_converter() {

	$plugin = new Simple_Text_Converter();
	$plugin->run();

}

add_filter('template_include', 'simple_text_converter_template');
function simple_text_converter_template( $template ) {

  global $post;
  if ( strpos($post->post_content, '[simple-page-converter') !== false ) :
    return plugin_dir_path( __FILE__ ) . 'public/partials/simple-text-converter-public-display.php';
  endif;

  return $template;
}

function get_simple_text_converter_url() {
  return plugins_url() . '/simple-text-converter';
}

run_simple_text_converter();

require plugin_dir_path( __FILE__ ) . 'includes/delete-post.php';

require plugin_dir_path( __FILE__ ) . 'includes/save-current-post-to-cookie.php';
