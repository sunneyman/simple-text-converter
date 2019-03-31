<?php

add_action( 'wp_ajax_custom_delete_post', 'custom_delete_post' );
function custom_delete_post(){

    $permission = check_ajax_referer( 'custom_delete_post_nonce', 'nonce', false );
    if( $permission == false ) {
        echo 'error';
    }
    else {
        wp_delete_post( $_REQUEST['id'] );
        echo 'success';
    }
    die();
}


global $wp;
$page = home_url(add_query_arg($wp->request));
$needle = "/documents/";

if ( strpos( $page, $needle ) !== false ) :

    wp_enqueue_script('delete-button', plugins_url() . '/simple-text-converter/public/delete-post-button.js', array('jquery'), 1.0, true);

    add_action( 'wp_enqueue_scripts', 'delete_button_ajax_data', 99 );
    function delete_button_ajax_data(){
        wp_localize_script( 'delete-button', 'delBtnAjax',
            array(
                'url' => admin_url('admin-ajax.php')
            )
        );
    }
endif;
