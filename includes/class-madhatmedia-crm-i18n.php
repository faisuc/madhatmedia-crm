<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://madhatmedia.net/
 * @since      1.0.0
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/includes
 * @author     Neil Carlo Sucuangco <necafasu@gmail.com>
 */
class Madhatmedia_Crm_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'madhatmedia-crm',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
