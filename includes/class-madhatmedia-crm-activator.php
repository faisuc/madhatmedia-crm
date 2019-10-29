<?php

/**
 * Fired during plugin activation
 *
 * @link       http://madhatmedia.net/
 * @since      1.0.0
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/includes
 * @author     Neil Carlo Sucuangco <necafasu@gmail.com>
 */
class Madhatmedia_Crm_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		remove_role( 'crm_admin' );
		remove_role( 'crm_staff' );
    /* Create Internal Users */
	    if( !mhm_crm_role_exists( 'crm_admin' ) ) {
	        add_role(
	            'crm_admin',
	            __( 'CRM Admin' ),
	            array(
	                'read'         => true,  // true allows this capability 5
	                'edit_posts'   => true,
	                'manage_options' => true,
	                'manage_categories' => true,
	                'publish_posts' =>true,
	                'edit_published_posts' => true,
	                'delete_published_posts' => true,
	                'edit_others_posts' => true,
	                'delete_others_posts' => true,
	                'delete_published_posts' => true,
	                'delete_private_posts' => true,
	                'delete_posts' => true,
	                'edit_private_posts' => true,
	                'read_private_posts' => true,
	                'unfiltered_html' => true,
					'upload_files' => true


	            )
	        );             
	    }

	    if( !mhm_crm_role_exists( 'crm_staff' ) ) {
	        add_role(
	            'crm_staff',
	            __( 'CRM Staff' ),
	            array(
	                'read'         => true,  // true allows this capability 2
	                'edit_posts'   => true,
	                'manage_options' => true,
	                'manage_categories' => true,
	                'publish_posts' =>true,
	                'edit_published_posts' => true,
	                'delete_published_posts' => true,
	                'edit_others_posts' => true,
	                'delete_others_posts' => true,
	                'delete_published_posts' => true,
	                'delete_private_posts' => true,
	                'delete_posts' => true,
	                'edit_private_posts' => true,
	                'read_private_posts' => true,
	                'unfiltered_html' => true,
					'upload_files' => true
	            )
	        );             
	    }

	}

}
