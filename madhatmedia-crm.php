<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://madhatmafia.com/
 * @since             3.0.0
 * @package           Madhatmedia_Crm
 *
 * @wordpress-plugin
 * Plugin Name:       MadHatMedia CRM
 * Plugin URI:        http://madhatmafia.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.3.0
 * Author:            MadHatMedia
 * Author URI:        http://madhatmafia.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       madhatmedia-crm
 * Domain Path:       /languages
 */

set_time_limit(0);
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if (!in_array('madhatmedia-plugin-management/madhatmedia-plugin-management.php', apply_filters('active_plugins', get_option('active_plugins')))) {



    deactivate_plugins(plugin_basename(__FILE__));

    add_action('load-plugins.php', function() {

        add_filter('gettext', 'change_text_madhatmedia_crm', 99, 3);

    });



    function change_text_madhatmedia_crm($translated_text, $untranslated_text, $domain) {

        $old = array(

            "Plugin <strong>activated</strong>.",

            "Selected plugins <strong>activated</strong>."

        );



        $new = "Please activate <b>MadHatMedia Plugin Management</b> Plugin to use MadHatMedia CRM";



        if (in_array($untranslated_text, $old, true)) {

            $translated_text = $new;

            remove_filter(current_filter(), __FUNCTION__, 99);

        }

        return $translated_text;

    }



    return FALSE;

}

require_once plugin_dir_path( __FILE__ ) . 'madhatmedia/init.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

define( 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-madhatmedia-crm-activator.php
 */

function mhm_crm_role_exists( $role ) {

  if( ! empty( $role ) ) {
    return $GLOBALS['wp_roles']->is_role( $role );
  }
  
  return false;
}

function activate_madhatmedia_crm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-madhatmedia-crm-activator.php';
	Madhatmedia_Crm_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-madhatmedia-crm-deactivator.php
 */
function deactivate_madhatmedia_crm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-madhatmedia-crm-deactivator.php';
	Madhatmedia_Crm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_madhatmedia_crm' );
register_deactivation_hook( __FILE__, 'deactivate_madhatmedia_crm' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-madhatmedia-crm.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-madhatmedia-mailchimp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_madhatmedia_crm() {

	$plugin = new Madhatmedia_Crm();
	$plugin->run();

}
run_madhatmedia_crm();

add_action( 'init', function() {

    ini_set('auto_detect_line_endings',TRUE);

    if ( isset( $_GET['mhm_crm_merge_db'] ) ) {

        $csvFile = plugin_dir_path( __FILE__ ) . 'scraped.csv';

        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle) ) {
            $line_of_text[] = fgetcsv($file_handle, 0);
        }
        fclose($file_handle);

        $num = count( $line_of_text );

        $user_id = get_current_user_id();

        $countries = Madhatmedia_Crm_Admin::countries();

        for ( $i = 1; $i < $num; $i++ ) {

            $company_name   = $line_of_text[$i][0];
            $address        = $line_of_text[$i][1];
            $state          = $line_of_text[$i][2];
            $zip            = $line_of_text[$i][3];
            $country        = $line_of_text[$i][4];



            $countryCode = array_search( $country, $countries );


            $post_id = wp_insert_post(array(
                'post_title'    => $company_name,
                'post_content'  => '',
                'post_date'     => date('Y-m-d H:i:s'),
                'post_author'   => $user_id,
                'post_type'     => 'mhm_crm',
                'post_status'   => 'publish',
            ));





            add_post_meta( $post_id, '_mhmcrm__billing_company', $company_name );
            add_post_meta( $post_id, '_mhmcrm__billing_address_1', $address );
            add_post_meta( $post_id, '_mhmcrm__billing_state', $state );
            add_post_meta( $post_id, '_mhmcrm__billing_zip', $zip );
            add_post_meta( $post_id, '_mhmcrm__billing_country', $countryCode );



        }

    }


});

add_action( 'init123', function() {

    echo '<pre>';
        print_r( get_post_meta( 1305 ) );
        exit;

});

add_action('add_meta_boxes', 'mhm_crm_hide_meta_boxes', 11);
function mhm_crm_hide_meta_boxes() {
    remove_meta_box('wpseo_meta', 'mhm_crm', 'normal');
}

add_action('admin_menu', function () {

    if (current_user_can('crm_admin') ||  current_user_can('crm_staff')){

        /**
         * Keep only specific menu items and remove all others
         */
        global $menu;
        $hMenu = $menu;
        foreach ($hMenu as $nMenuIndex => $hMenuItem) {


            if (in_array($hMenuItem[2], array(
                                             'index.php',
                                             'edit.php?post_type=mhm_crm',
                                             'edit-tags.php'
                                        ))
            ) {
                continue;
            }
            unset($menu[$nMenuIndex]);
        }
        remove_submenu_page( 'index.php', 'update-core.php' );

        remove_menu_page( 'mhm-madhatmedia-plugin-management' );

    }
},999);
