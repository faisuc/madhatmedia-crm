<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://madhatmedia.net/
 * @since      1.0.0
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/public
 * @author     Neil Carlo Sucuangco <necafasu@gmail.com>
 */
class Madhatmedia_Crm_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_shortcode( 'madhatmedia-crm-form', array( $this, 'display_crm_form' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'action_woocommerce_thankyou' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );

	}

	public function init() {

		/*
		$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

		$mailchimp->subscribeToCampaign( '8e80b063a0', 'necafasu@gmail.com', 'Neil Carlo', 'Sucuangco' );
		exit;
		*/

	}

	public function action_woocommerce_thankyou( $order_id ) {

		global $wpdb;
		
		$order = wc_get_order( $order_id );
		$items = $order->get_items();

		$mailchimpProdLists = get_option( '_mhm_crm_mapMailchimpListsToProduct' );

		$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

		$email = $order->get_billing_email();
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();

		$_mhm_crm_automate_woo_form = get_option( '_mhm_crm_automate_woo_form' );

		foreach ( $items as $item ) {

			$product_name = $item->get_name();
			$product_id = $item->get_product_id();
			$product_variation_id = $item->get_variation_id();
			

			if ( isset( $_mhm_crm_automate_woo_form[ $product_id ] ) ) {

				if ( get_post_meta( $product_id, '_mhm_crm_automate_woo_mailchimp', true ) ) {
					$mailchimpLists = get_post_meta( $product_id, '_mhm_crm_automate_woo_mailchimp', true );

					$condition = $_mhm_crm_automate_woo_form[ $product_id ]['condition'];

					foreach ( $mailchimpLists as $list_id ) {

						if ( $condition == 'add' ) {

							$mailchimp->subscribeToCampaign( $list_id, $email, $first_name, $last_name );

						} elseif ( $condition == 'remove' ) {

							$mailchimp->unsubscribeToCampaign( $list_id, $email );

						}
						

					}

				}

			}

		}

		if ( $email == '' ) {
			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$email = $current_user->user_email;
			}
		}

		if ( $email != '' ) {

			$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='mhm_crm' AND post_status = 'publish'", $email ));

        	if ( $post ) {

				$post = get_post( $post, 'OBJECT' );
				$post_id = $post->ID;

			} else {

				$my_post = array(
					'post_title'    => wp_strip_all_tags( $email ),
					'post_type'		=> 'mhm_crm',
					'post_status'   => 'publish'
				);

				$post_id = wp_insert_post( $my_post );

			}
			

			update_post_meta( $post_id, '_mhmcrm_first_name', $first_name );
			update_post_meta( $post_id, '_mhmcrm_last_name', $last_name );


				if ( $_mhm_crm_automate_woo_form ) {
					
					foreach ( $items as $item ) {

						$product_name = $item->get_name();
						$product_id = $item->get_product_id();

						if ( isset( $_mhm_crm_automate_woo_form[ $product_id ] ) ) {

							$form_id = $_mhm_crm_automate_woo_form[ $product_id ]['form_id'];
							$condition = $_mhm_crm_automate_woo_form[ $form_id ]['condition'];

							if ( isset( $_mhm_crm_automate_woo_form[ $form_id ]['status'] ) ) {

								$statuses = array_map( 'intval', $_mhm_crm_automate_woo_form[ $form_id ]['status'] );

								if ( $condition == 'add' ) {

									wp_add_object_terms( $post_id, $statuses, 'mhm_crm_status' );


								} elseif ( $condition == 'remove' ) {

									wp_remove_object_terms( $post_id, $statuses, 'mhm_crm_status' );

								}

							}

							if ( isset( $_mhm_crm_automate_woo_form[ $form_id ]['groups'] ) ) {

								$groups = array_map( 'intval', $_mhm_crm_automate_woo_form[ $form_id ]['groups'] );

								if ( $condition == 'add' ) {

									wp_add_object_terms( $post_id, $groups, 'mhm_crm_group' );

								} elseif ( $condition == 'remove' ) {

									wp_remove_object_terms( $post_id, $groups, 'mhm_crm_group' );

								}

							}

						}

					}

				}

		}

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Madhatmedia_Crm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Madhatmedia_Crm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/madhatmedia-crm-public.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), '4.0.6', 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Madhatmedia_Crm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Madhatmedia_Crm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/madhatmedia-crm-public.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array( 'jquery' ), '4.0.6', false );
	}

	public function display_crm_form() {

		ob_start();

		include_once PLUGIN_PATH . 'public/partials/form.php';

		$content = ob_get_contents();
		ob_end_clean();

		echo $content;

	}

	public function pre_get_posts( $query ) {

		global $pagenow;
		
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) ); 

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();
			$user_email = $user->user_email;

			$groups = get_terms( 'mhm_crm_group', array(
				'hide_empty' => false,
			) );

			$statuses = get_terms( 'mhm_crm_status', array(
				'hide_empty' => false,
			) );
			
			$group_term_ids = array();
			$status_term_ids = array();

			foreach ( $groups as $group ) {
		
				$admins_list = get_term_meta( $group->term_id, 'mhm_crm_assign_to_admins', true ) == '' ? array() : get_term_meta( $group->term_id, 'mhm_crm_assign_to_admins', true );

				$crm_admins_list = get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_admins', true ) == '' ? array() : get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_admins', true );

				$crm_staffs_list = get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_staffs', true ) == '' ? array() : get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_staffs', true );
				
				if ( in_array( 'crm_staff', $user->roles ) ) {

					if ( in_array( $user_email, $crm_staffs_list ) ) {
						$group_term_ids[] = $group->term_id;
					}
				
				}

				if ( in_array( 'administrator', $user->roles ) ) {

					if ( in_array( $user_email, $admins_list ) ) {
						$group_term_ids[] = $group->term_id;
					}

				}

				if ( in_array( 'crm_admin', $user->roles ) ) {

					if ( in_array( $user_email, $crm_admins_list ) ) {
						$group_term_ids[] = $group->term_id;
					}

				}
		
			}

			foreach ( $statuses as $status ) {
		
				$admins_list = get_term_meta( $status->term_id, 'mhm_crm_assign_to_admins', true ) == '' ? array() : get_term_meta( $status->term_id, 'mhm_crm_assign_to_admins', true );

				$crm_admins_list = get_term_meta( $status->term_id, 'mhm_crm_assign_to_crm_admins', true ) == '' ? array() : get_term_meta( $status->term_id, 'mhm_crm_assign_to_crm_admins', true );

				$crm_staffs_list = get_term_meta( $status->term_id, 'mhm_crm_assign_to_crm_staffs', true ) == '' ? array() : get_term_meta( $status->term_id, 'mhm_crm_assign_to_crm_staffs', true );
				
				if ( in_array( 'crm_staff', $user->roles ) ) {

					if ( in_array( $user_email, $crm_staffs_list ) ) {
						$status_term_ids[] = $status->term_id;
					}
				
				}

				if ( in_array( 'administrator', $user->roles ) ) {

					if ( in_array( $user_email, $admins_list ) ) {
						$status_term_ids[] = $status->term_id;
					}

				}

				if ( in_array( 'crm_admin', $user->roles ) ) {

					if ( in_array( $user_email, $crm_admins_list ) ) {
						$status_term_ids[] = $status->term_id;
					}

				}
		
			}

			if ( in_array( 'crm_staff', $user->roles ) ) {

				global $typenow;

				$meta_query_args = array(
					'post_type' => 'mhm_crm',
					'meta_query' => array(
						array(
							'key'		=> '_mhm_crm__assign_staff',
							'value' 	=> serialize( strval( $user_email ) ),
							'compare'	=> 'LIKE'
						)
					),
					'fields'          => 'ids'
				);

				$meta_query_post_ids = get_posts( $meta_query_args );

				$group_tax_query_args = array(
					'post_type' => 'mhm_crm',
					'tax_query' => array(
						array(
							'taxonomy' => 'mhm_crm_group',
							'field' => 'id',
							'terms' => $group_term_ids,
							'operator'=> 'IN'
						)
					),
					'fields'          => 'ids'
				);

				$group_tax_query_post_ids = get_posts( $group_tax_query_args );

				$status_tax_query_args = array(
					'post_type' => 'mhm_crm',
					'tax_query' => array(
						array(
							'taxonomy' => 'mhm_crm_status',
							'field' => 'id',
							'terms' => $status_term_ids,
							'operator'=> 'IN'
						)
					),
					'fields'          => 'ids'
				);

				$status_tax_query_post_ids = get_posts( $status_tax_query_args );
				
				if ( $_GET['post_type'] == 'mhm_crm' && $pagenow == 'edit.php') {
					
					$query->set( 'post__in', array_merge( $meta_query_post_ids, $group_tax_query_post_ids, $status_tax_query_post_ids ) );
					add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) ); 
				}

			} else if ( in_array( 'crm_admin', $user->roles ) ) {

				global $typenow;

				$meta_query_args = array(
					'post_type' => 'mhm_crm',
					'meta_query' => array(
						array(
							'key'		=> '_mhm_crm__assign_crm_admins',
							'value' 	=> serialize( strval( $user_email ) ),
							'compare'	=> 'LIKE'
						)
					),
					'fields'          => 'ids'
				);

				$meta_query_post_ids = get_posts( $meta_query_args );

				$group_tax_query_args = array(
					'post_type' => 'mhm_crm',
					'tax_query' => array(
						array(
							'taxonomy' => 'mhm_crm_group',
							'field' => 'id',
							'terms' => $group_term_ids,
							'operator'=> 'IN'
						)
					),
					'fields'          => 'ids'
				);

				$group_tax_query_post_ids = get_posts( $group_tax_query_args );

				$status_tax_query_args = array(
					'post_type' => 'mhm_crm',
					'tax_query' => array(
						array(
							'taxonomy' => 'mhm_crm_status',
							'field' => 'id',
							'terms' => $status_term_ids,
							'operator'=> 'IN'
						)
					),
					'fields'          => 'ids'
				);

				$status_tax_query_post_ids = get_posts( $status_tax_query_args );
				
				if ( $_GET['post_type'] == 'mhm_crm' && $pagenow == 'edit.php') {
					
					$query->set( 'post__in', array_merge( $meta_query_post_ids, $group_tax_query_post_ids, $status_tax_query_post_ids ) );
					add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) ); 

				}

			}

		}
		
	}

}

add_action( 'init123', function() {

	$user = wp_get_current_user();
	$user_email = $user->user_email;

	$groups = get_terms( 'mhm_crm_group', array(
		'hide_empty' => false,
	) );
	
	
	$meta_query_args = array(
		'post_type' => 'mhm_crm',
		'meta_query' => array(
			array(
				'key'		=> '_mhm_crm__assign_staff',
				'value' 	=> serialize( strval( $user_email ) ),
				'compare'	=> 'LIKE'
			)
		),
		'fields'          => 'ids'
	);

	$tax_query_args = array(
		'post_type' => 'mhm_crm',
		'tax_query' => array(
			array(
				'taxonomy' => 'mhm_crm_group',
				'field' => 'id',
				'terms' => array( 33 ),
				'operator'=> 'IN'
			)
		),
		'fields'          => 'ids'
	);

	echo '<pre>';
	 print_r( get_posts( $tax_query_args ) );
		print_r( get_posts( $meta_query_args ) );
		exit;

	foreach ( $groups as $group ) {

		$crm_staffs_list = get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_staffs', true ) == '' ? array() : get_term_meta( $group->term_id, 'mhm_crm_assign_to_crm_staffs', true );

		echo '<pre>';
			print_r( $crm_staffs_list );

	}

	exit;

});