<?php
/**
 * Template Name: Simple Text Converter
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/hisaveliy
 * @since      1.0.0
 *
 * @package    Simple_Text_Converter
 * @subpackage Simple_Text_Converter/public/partials
 */

get_header(); 
  ?>

<div class="container-wrap">
    
  <div class="container main-content">
    
    <div class="row">

        <?php while (have_posts()) : the_post(); Simple_Text_Converter_Public::get_view(); endwhile; ?>

    </div>

  </div>

</div>

<?php get_footer(); ?>