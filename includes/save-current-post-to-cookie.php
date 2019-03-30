<?php

global $wp;
$page = home_url(add_query_arg($wp->request));
$find = '/document/';
$pos = strpos($page, $find);

if($pos) :
    wp_enqueue_script('get-page-position', plugins_url() . '/simple-text-converter/public/get-page-position.js', array('jquery'), 1.0, true);
endif;
