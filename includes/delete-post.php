<?php
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

    add_action( 'wp_ajax_delete_post', 'my_action_callback' );
    function my_action_callback() {
        $post_id = intval( $_POST['postId'] );
        $force_delete = false;
        $user = wp_get_current_user();
        $allowed_roles = array('editor', 'administrator');
        $post = get_post($post_id);

        if(get_current_user_id() == $post->post_author || array_intersect($allowed_roles, $user->roles )) :
            wp_delete_post( $post_id, $force_delete );
            echo "Deleted!";
            wp_die();
        else : echo "You are not allowed to do this action"; endif;
    }
endif;
