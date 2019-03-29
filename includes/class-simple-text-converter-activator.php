<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/hisaveliy
 * @since      1.0.0
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/includes
 * @author     Saveliy D. <dzvonkevich@gmail.com>
 */
class Simple_Text_Converter_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	  global $user_ID;

    $new_post = array(
      'post_title' => 'Simple Text Converter',
      'post_content' => '[simple-page-converter]',
      'post_status' => 'publish',
      'post_date' => date('Y-m-d H:i:s'),
      'post_author' => $user_ID,
      'post_type' => 'page',
    );

    wp_insert_post($new_post);
	}
}
