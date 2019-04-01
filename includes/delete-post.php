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

add_action( 'wp_ajax_custom_find_posts', 'custom_find_posts' );
function custom_find_posts(){

    $user_id = $_REQUEST['id'];

    $user = get_user_by( 'id', $user_id );

    $args = array(
        'author'        =>  $user_id,
        'orderby'       =>  'post_date',
        'order'         =>  'ASC',
        'posts_per_page' => -1,
        'include'     => array(),
        'exclude'     => array(),
        'meta_key'    => '',
        'meta_value'  =>'',
        'post_type'   => 'document',
    );
    $current_user_posts_array = get_posts( $args );

    $users_posts = array();

    foreach($current_user_posts_array as $post_data) {
        array_push($users_posts, $post_data->post_name);
    }

    $report = (object) [
        'user_id' => $user_id,
        'user_role' => $user->roles[0],
        'users_posts'  => $users_posts,
    ];

    echo json_encode( $report );;

    die();
}


add_action( 'wp_ajax_custom_get_nonce', 'custom_get_nonce' );
function custom_get_nonce() {
    $nonce = wp_create_nonce( 'custom-delete-post'.$_REQUEST['id'] );
    echo $nonce;
    die();
}


global $wp;
$page = home_url(add_query_arg($wp->request));
$needle = "/documents/";

if ( strpos( $page, $needle ) !== false ) :

    wp_enqueue_script('delete-button', plugins_url() . '/simple-text-converter/public/delete-post-button.js', array('jquery'), 1.1, true);

    add_action( 'wp_enqueue_scripts', 'delete_button_ajax_data', 99 );
    function delete_button_ajax_data(){

        $current_user_id = get_current_user_id();

        wp_localize_script( 'delete-button', 'delBtnAjax',
            array(
                'url' => admin_url('admin-ajax.php'),
                'userId' => $current_user_id,
            )
        );
    }
endif;
