<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! file_exists(ABSPATH . 'wp-content/plugins/madhatmedia-plugin-management/') ) return;

require_once ABSPATH . 'wp-content/plugins/madhatmedia-plugin-management/plugin-updates/plugin-update-checker.php';
require_once plugin_dir_path( __FILE__ ) . 'menu.php';

if ( ! defined( 'MADHATMEDIA_WEBSITE_URL' ) ) {

    define( 'MADHATMEDIA_WEBSITE_URL', 'http://madhatmafia.com' );

}

$madhatmedia_crm_update_checker = Puc_v4_Factory::buildUpdateChecker(
    MADHATMEDIA_WEBSITE_URL . '/wp-update-server/?action=get_metadata&slug=madhatmedia-crm',
    plugin_dir_path( dirname( __FILE__ ) ) . 'madhatmedia-crm.php',
    'madhatmedia-crm'
);

$madhatmedia_crm_update_checker->addQueryArgFilter('wsh_filter_update_checks_madhatmedia_crm');
function wsh_filter_update_checks_madhatmedia_crm($queryArgs) {

    $queryArgs[ 'license_key' ]  = get_option( 'madmatmedia_license_key-madhatmedia-crm' );
    $queryArgs[ 'email' ] = get_option( 'madmatmedia_license_email-madhatmedia-crm' );
    $queryArgs[ 'slug' ] = 'madhatmedia-crm';

    return $queryArgs;

}

add_action( 'init', function() {

    if ( isset( $_POST['madhatmedia_activate_license'] ) && ( isset( $_GET['page'] ) && $_GET['page'] == 'mhm-madhatmedia-madhatmedia-crm' ) ) {

        $email = sanitize_email( $_POST['email'] );
        $license_key = sanitize_text_field( $_POST['license_key'] );
        $product_id = $_POST['product_id'];

        update_option( 'madmatmedia_license_email-madhatmedia-crm', $email );
        update_option( 'madmatmedia_license_key-madhatmedia-crm', $license_key );

        $data = file_get_contents( MADHATMEDIA_WEBSITE_URL . '/woocommerce/?wc-api=software-api&request=activation&email=' . $email . '&license_key=' . $license_key . '&product_id=' . $product_id );


    } else if ( isset( $_POST['madhatmedia_deactivate_license'] ) && ( isset( $_GET['page'] ) && $_GET['page'] == 'mhm-madhatmedia-madhatmedia-crm' ) ) {

        $email = sanitize_email( $_POST['email'] );
        $license_key = sanitize_text_field( $_POST['license_key'] );
        $product_id = $_POST['product_id'];

        update_option( 'madmatmedia_license_email-madhatmedia-crm', $email );
        update_option( 'madmatmedia_license_key-madhatmedia-crm', $license_key );

        $data = file_get_contents( MADHATMEDIA_WEBSITE_URL . '/woocommerce/?wc-api=software-api&request=deactivation&email=' . $email . '&license_key=' . $license_key . '&product_id=' . $product_id );


    }

});

function madhatmedia_crm_post_send_email_staff($post_id) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    $to = get_post_meta( $post_id, '_mhm_crm__assign_staff', true );
    $fname = get_post_meta( $post_id, '_mhmcrm_first_name', true );
    $lname = get_post_meta( $post_id, '_mhmcrm_last_name', true );


    if ( $screen->post_type == 'mhm_crm' && isset($to[0]) && $fname  && $lname) {
        $screen = get_current_screen();
        $post   = get_post( $post_id );
        $subject = 'You have assigned record!';
        $body = 'You have been assigned record '. $fname .' '. $lname .' inside of '. get_bloginfo();

        $response = wp_mail( $to[0], $subject, $body );
    }
    remove_action( 'save_post', 'madhatmedia_crm_post_send_email_staff' );
}

add_action( 'save_post', 'madhatmedia_crm_post_send_email_staff' );


add_action('admin_init', 'mhm_crm_disable_remove_permanent_post');

function mhm_crm_disable_remove_permanent_post(){
      
    // Disable delete functionality for records
    $userID = get_current_user_id(); 
    $user = new WP_User( $userID );
    
    if ( ! empty( $user->roles ) && is_array( $user->roles ) && in_array( 'crm_staff', $user->roles ) ) {

        add_action( 'admin_head-edit.php', 'mhm_crm_row_disable_remove_post' );
        add_filter( 'post_row_actions', 'mhm_crm_row__action_disable_remove_post', 10, 2 );
        add_filter( 'page_row_actions', 'mhm_crm_row__action_disable_remove_post', 10, 2 );

        function mhm_crm_row_disable_remove_post()
        {
            if( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] ) 
            {
                echo "<style>
                    .alignleft.actions:first-child, #delete_all {
                        display: none;
                    }
                    </style>";
            }
        }

        function mhm_crm_row__action_disable_remove_post( $actions, $post ) 
        {
            if( isset( $_REQUEST['post_status'] ) && 'trash' == $_REQUEST['post_status'] ) 
                unset( $actions['delete'] );

            return $actions; 
        }
    }
}


