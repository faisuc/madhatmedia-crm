<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://madhatmedia.net/
 * @since      1.0.0
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Madhatmedia_Crm
 * @subpackage Madhatmedia_Crm/admin
 * @author     Neil Carlo Sucuangco <necafasu@gmail.com>
 */
class Madhatmedia_Crm_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */

	private $_token;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->_token = 'mhm_crm';

		$this->define_constants();

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'wp_loaded', array( $this, 'wp_loaded') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'manage_mhm_crm_posts_columns', array( $this, 'manage_columns' ) );
		add_action( 'manage_mhm_crm_posts_custom_column' , array( $this, 'custom_mhm_crm_posts_column' ), 10, 2 );

		add_action( 'manage_edit-mhm_crm_group_columns', array( $this, 'my_custom_taxonomy_columns' ) );
		add_action( 'manage_edit-mhm_crm_status_columns', array( $this, 'my_custom_taxonomy_columns' ) );

		add_filter( 'manage_mhm_crm_group_custom_column', array( $this, 'my_custom_groups_columns_content' ), 10, 3 );
		add_filter( 'manage_mhm_crm_status_custom_column', array( $this, 'my_custom_status_columns_content' ), 10, 3 );

		add_action( 'wpcf7_before_send_mail', array( $this, 'wpcf7_before_send_mail' ), 10, 1 );

		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( $this, 'filter_groups_and_status' ), 10, 2 );

	}

	public function admin_head() {

		//echo '<script src="http://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>';

	}

	public function manage_columns( $post_columns ) {

		unset( $post_columns['date'] );
		$post_columns[ 'title' ] = 'Primary';
		$post_columns[ 'firstname' ] = 'First Name';
		$post_columns[ 'lastname' ] = 'Last Name';
		$post_columns[ 'groups' ] = 'Groups';
		$post_columns[ 'status' ] = 'Status';
		$post_columns[ 'date' ] = 'Date';


		return $post_columns;

	}

	public function custom_mhm_crm_posts_column( $column, $post_id ) {

		switch ( $column ) {

			case 'firstname':
				echo get_post_meta( $post_id, '_mhmcrm_first_name', true );
				break;
			case 'lastname':
				echo get_post_meta( $post_id, '_mhmcrm_last_name', true );
				break;
			case 'groups':

				$groups = get_the_terms( $post_id, 'mhm_crm_group' );
				$cat = array();
				$color = '';

				if ( ! empty( $groups ) ) {

					foreach ( $groups as $group ) {

						$color = get_term_meta( $group->term_id, 'mhm_crm_group_color', true );
						$cat[] = '<span style="color:' . $color  . '">' . $group->name . '</span>';

					}

				}

				echo implode( ',', $cat );

				break;

			case 'status':

				$status = get_the_terms( $post_id, 'mhm_crm_status' );
				$cat = array();
				$color = '';

				if ( ! empty( $status ) ) {

					foreach ( $status as $stat ) {

						$color = get_term_meta( $stat->term_id, 'mhm_crm_status_color', true );
						$cat[] = '<span style="color:' . $color  . '">' . $stat->name . '</span>';

					}

				}

				echo implode( ',', $cat );

				break;

		}

	}

	public function admin_notices() {

		if ( isset( $_SESSION['MHM_CRM_MAILCHIMP_MSGS'] ) && $_SESSION['MHM_CRM_MAILCHIMP_MSGS'] != "" ) {

			?>
			<div class="notice notice-success is-dismissible">
				<p><?php _e( $_SESSION['MHM_CRM_MAILCHIMP_MSGS'], 'sample-text-domain' ); ?></p>
			</div>
			<?php
			unset( $_SESSION['MHM_CRM_MAILCHIMP_MSGS'] );
		}

	}

	public function admin_init() {

		if (
			isset( $_POST['mhm_crm_webmail_fields'] )
			&& wp_verify_nonce( $_POST['mhm_crm_webmail_fields'], 'mhm_crm_webmail_action' )
		) {


			$totalCount = count( $_POST['email'] );

			$_mhm_crm_webmail = "";

			for ( $i = 0; $i < $totalCount; $i++ ) {

				$email = $_POST['email'][$i];
				$password = $_POST['password'][$i];
				$domain = $_POST['domain'][$i];

				if ($email != "" && $password != "" && $domain != "") {
					$_mhm_crm_webmail .= $email . ' ' . $password . ' ' . $domain . PHP_EOL;
				}

			}


			if ( get_option( '_mhm_crm_webmail_accounts' ) !== false ) {

				update_option( '_mhm_crm_webmail_accounts', $_mhm_crm_webmail );

			} else {

				add_option( '_mhm_crm_webmail_accounts', $_mhm_crm_webmail );
			}

			$_mhm_crm_webmail = get_option( '_mhm_crm_webmail_accounts' );

			$_mhm_crm_webmails = explode( PHP_EOL, $_mhm_crm_webmail );

			foreach ( $_mhm_crm_webmails as $accounts ) {

				$accounts = array_filter( preg_split( '/\s+/', $accounts ) );

				foreach ( $accounts as $acc ) {

					$email = $acc[0];
					$password = $acc[1];
					$domain = $acc[2];

				}

			}

		}

		if ( isset( $_POST['mhm_crm_mailchimp_api_key'] ) ) {

			if ( get_option( '_mhm_crm_mailchimp_api_key' ) !== false ) {

				update_option( '_mhm_crm_mailchimp_api_key', $_POST['mhm_crm_mailchimp_api_key'] );

			} else {

				add_option( '_mhm_crm_mailchimp_api_key', $_POST['mhm_crm_mailchimp_api_key'] );
			}

		}

		if (
			isset( $_POST['mhm_crm_metaboxes_fields'] )
			&& wp_verify_nonce( $_POST['mhm_crm_metaboxes_fields'], 'mhm_crm_metaboxes_action' )
		) {

			$__mhm_crm_record_title = array_values( $_POST['__mhm_crm_record_title'] );
			$__mhm_crm_record_section_item_field = array_values( $_POST['__mhm_crm_record_section_item_field'] );
			$__mhm_crm_record_section_item_field_cat = array_values( $_POST['__mhm_crm_record_section_item_field_cat'] );

			$record_field_posts = array();

	
			for ( $i = 0; $i < count( $__mhm_crm_record_title ); $i++ ) {

				$record_field_posts[$i]['record_title'] = $__mhm_crm_record_title[$i];

				for ( $x = 0; $x < count( $__mhm_crm_record_section_item_field[$i] ); $x++ ) {

					$record_field_posts[$i]['record_section_item_field'][] = $__mhm_crm_record_section_item_field[$i][$x];
					$record_field_posts[$i]['record_section_item_field_cat'][] = $__mhm_crm_record_section_item_field_cat[$i][$x];

				}

			}

			if ( get_option('_mhm_crm_record_sections') === false ) {
				add_option( '_mhm_crm_record_sections', $record_field_posts );
			} else {
				update_option( '_mhm_crm_record_sections', $record_field_posts );
			}

		}

		if ( isset( $_POST['_mhmcrm_cf7_form'] ) ) {

			$form_id = $_POST['cf7forms'];
			$fields = array();

			$enabled = 'no';
			if ( isset( $_POST['enable'] ) ) {
				$enabled = 'yes';
			} else {
				$enabled = 'no';
			}

			$enabledArray = array( 'enabled' => $enabled );

			if( ! get_option( '_mhmcrm_cf7_form_options' ) ) {

				$fields[ $form_id ] = array_merge( $_POST['_mhmcrm_cf7_form'], $enabledArray );

				add_option( '_mhmcrm_cf7_form_options', $fields );

			} else {

				$fields = get_option( '_mhmcrm_cf7_form_options' );
				$fields[ $form_id ] = array_merge( $_POST['_mhmcrm_cf7_form'], $enabledArray );

				update_option( '_mhmcrm_cf7_form_options', $fields );

			}

		}

		if ( isset( $_POST['enableMailchimpList'] ) ) {

			$lists = isset( $_POST['list'] ) ? $_POST['list'] : array();

			if ( ! empty( $lists ) ) {

				if ( get_option('_mhm_crm_mailchimp_activated_lists') === false ) {
					add_option( '_mhm_crm_mailchimp_activated_lists', $lists );
				} else {
					update_option( '_mhm_crm_mailchimp_activated_lists', $lists );
				}

			}

		}

		if ( isset( $_POST['saveMapMailchimpListsToProduct'] ) ) {

			$productID = isset( $_POST['productID'] ) ? $_POST['productID'] : '';
			$mailchimp = isset( $_POST['list'] ) ? $_POST['list'] : array();
			$mailchimpProdLists = get_option( '_mhm_crm_mapMailchimpListsToProduct' );

			$lists = array();

			if ( $productID != '' ) {

				if ( $mailchimpProdLists == false ) {

					$lists[ $productID ] = $mailchimp;

					add_option( '_mhm_crm_mapMailchimpListsToProduct', $lists );

				} else {

					$mailchimpProdLists[ $productID ] = $mailchimp;

					update_option( '_mhm_crm_mapMailchimpListsToProduct', $mailchimpProdLists );

				}

			}

		}

		if ( isset( $_POST['_mhm_crm_automate_cf7_form'] ) ) {

			$form_ids = isset( $_POST['_mhm_crm_automate_cf7_form_id'] ) ? $_POST['_mhm_crm_automate_cf7_form_id'] : array();
			$conditions = isset( $_POST['_mhm_crm_automate_cf7_condition'] ) ? $_POST['_mhm_crm_automate_cf7_condition'] : array();
			$statuses = isset( $_POST['_mhm_crm_automate_cf7_status'] ) ? $_POST['_mhm_crm_automate_cf7_status'] : array();
			$groups = isset( $_POST['_mhm_crm_automate_cf7_group'] ) ? $_POST['_mhm_crm_automate_cf7_group'] : array(); 

			$form_ids = array_values( $form_ids );
			$conditions = array_values( $conditions );
			$statuses = array_values( $statuses );
			$groups = array_values( $groups );

			$mhm_crm_automate_cf7 = array();
			$mhm_crm_automate_cf7_sub_array = array();
			
			for ( $i = 0; $i < count( $form_ids ); $i++ ) {

				$form_id = $form_ids[$i];
				$condition = $conditions[$i];
				

				$mhm_crm_automate_cf7[ $form_id ]['form_id'] = $form_id;
				$mhm_crm_automate_cf7[ $form_id ]['condition'] = $condition;
				
				if ( isset( $statuses[$i] ) ) {
					$status = $statuses[$i];
				
					$mhm_crm_automate_cf7[ $form_id ]['status'] = $status;
				}

				if ( isset( $groups[ $i ] ) ) {
					$group = $groups[$i];
					$mhm_crm_automate_cf7[ $form_id ]['groups'] = $group;
				}

			}

			
			if ( get_option( '_mhm_crm_automate_cf7_form' ) ) {
				update_option( '_mhm_crm_automate_cf7_form', $mhm_crm_automate_cf7 );
			} else {
				add_option( '_mhm_crm_automate_cf7_form', $mhm_crm_automate_cf7 );
			}

		}


		if ( isset( $_POST['_mhm_crm_automate_woo_form'] ) ) {

			$form_ids = isset( $_POST['_mhm_crm_automate_woo_form_id'] ) ? $_POST['_mhm_crm_automate_woo_form_id'] : array();
			$conditions = isset( $_POST['_mhm_crm_automate_woo_condition'] ) ? $_POST['_mhm_crm_automate_woo_condition'] : array();
			$statuses = isset( $_POST['_mhm_crm_automate_woo_status'] ) ? $_POST['_mhm_crm_automate_woo_status'] : array();
			$groups = isset( $_POST['_mhm_crm_automate_woo_group'] ) ? $_POST['_mhm_crm_automate_woo_group'] : array(); 

			$form_ids = array_values( $form_ids );
			$conditions = array_values( $conditions );
			$statuses = array_values( $statuses );
			$groups = array_values( $groups );

			$mhm_crm_automate_woo = array();
			$mhm_crm_automate_woo_sub_array = array();
			
			for ( $i = 0; $i < count( $form_ids ); $i++ ) {

				$form_id = $form_ids[$i];
				$condition = $conditions[$i];
				

				$mhm_crm_automate_woo[ $form_id ]['form_id'] = $form_id;
				$mhm_crm_automate_woo[ $form_id ]['condition'] = $condition;
				
				if ( isset( $statuses[$i] ) ) {
					$status = $statuses[$i];
				
					$mhm_crm_automate_woo[ $form_id ]['status'] = $status;
				}

				if ( isset( $groups[ $i ] ) ) {
					$group = $groups[$i];
					$mhm_crm_automate_woo[ $form_id ]['groups'] = $group;
				}

				if ( isset( $_POST['_mhm_crm_automate_woo_mailchimp'][$i] ) ) {
					if ( ! add_post_meta( $form_id, '_mhm_crm_automate_woo_mailchimp', $_POST['_mhm_crm_automate_woo_mailchimp'][$i], true ) ) { 
						update_post_meta( $form_id, '_mhm_crm_automate_woo_mailchimp', $_POST['_mhm_crm_automate_woo_mailchimp'][$i] );
					}
				} else {
					if ( ! add_post_meta( $form_id, '_mhm_crm_automate_woo_mailchimp', array(), true ) ) { 
						update_post_meta( $form_id, '_mhm_crm_automate_woo_mailchimp', array() );
					}
				}

			}

			

			
			if ( get_option( '_mhm_crm_automate_woo_form' ) ) {
				update_option( '_mhm_crm_automate_woo_form', $mhm_crm_automate_woo );
			} else {
				add_option( '_mhm_crm_automate_woo_form', $mhm_crm_automate_woo );
			}

		}

		if ( isset( $_POST['premade_sections_submit'] ) ) {
			$sections = $_POST['mhmcrm'];
			$premade_sections_option = ( array ) get_option( 'mhm_crm_premade_sections' );
			$premade_sections = array();

			if ( count( $sections ) > 0 ) {
				foreach ( $sections as $key => $section ) {
					$premade_sections[ $key ] = $section;
				}
			}
		
			update_option( 'mhm_crm_premade_sections', $premade_sections );

		}

		if ( isset( $_POST['mhmcrm_settings_submit'] ) ) {
			$mhmcrm = $_POST['mhmcrm'];

			if ( get_option( '_mhm_crm_settings' ) ) {
				update_option( '_mhm_crm_settings', $mhmcrm );
			} else {
				add_option( '_mhm_crm_settings', $mhmcrm );
			}
		}

	}

	public function wp_loaded() {

		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'mhm_crms' ) {

			$_mhm_crm_webmail = get_option( '_mhm_crm_webmail_accounts' );

			$_mhm_crm_webmails = explode( PHP_EOL, $_mhm_crm_webmail );

			foreach ( $_mhm_crm_webmails as $accounts ) {

				$acc = array_filter( preg_split( '/\s+/', $accounts ) );

				if ( ! isset( $acc[0] ) ) {
					continue;
				}

					$emailAddress = $acc[0];
					$emailPassword = $acc[1];
					$domainURL = $acc[2];
					$useHTTPS = true;

					$inbox = imap_open('{'.$domainURL.':143/notls}INBOX',$emailAddress,$emailPassword) or die('Cannot connect to domain:' . imap_last_error());

					// Grabs any e-mail that is not read
					$emails = imap_search($inbox,'UNDELETED');

					if($emails) {

						foreach($emails as $email_number) {
							$message = imap_fetchbody($inbox,$email_number,1.1);
							if ($message == "") { // no attachments is the usual cause of this
								$message = imap_fetchbody($inbox, $email_number, 1);
							}

							$header = imap_headerinfo ( $inbox, $email_number);

							$emailAdd = $header->from[0]->mailbox . '@' . $header->from[0]->host;
							$toAdd = $header->to[0]->mailbox . $header->to[0]->host;
							$toEmailAdd = $header->to[0]->mailbox . '@' . $header->to[0]->host;
							$date = $header->date;

							$args = array(
								'post_type'  => 'mhm_crm',
								'post_status'	=> 'trash',
								'meta_query' => array(
									array(
										'key'   => '_mhmcrm_user_email',
										'value' => $toEmailAdd,
									)
								)
							);
							$deletedlists = get_posts( $args );

							if ( empty( $deletedlists ) ) {

								$args = array(
									'post_type'  => 'mhm_crm',
									'post_status'	=> 'publish',
									'meta_query' => array(
										array(
											'key'   => '_mhmcrm_user_email',
											'value' => $toEmailAdd,
										)
									)
								);
								$postslist = get_posts( $args );


								if ( empty( $postslist ) ) {

									$my_post = array(
										'post_title'    => $toEmailAdd,
										'post_status'   => 'publish',
										'post_author'   => get_current_user_id(),
										'post_type'		=> 'mhm_crm'
									);


									$post_id = wp_insert_post( $my_post );

									add_post_meta( $post_id, '_mhmcrm_user_email', $toEmailAdd );

								}

							}

							/*
							if ( ! email_exists( $toAdd ) ) {

								$my_post = array(
									'post_title'    => $toAdd,
									'post_status'   => 'publish',
									'post_author'   => get_current_user_id(),
									'post_type'		=> 'mhm_crm'
								);


								$post_id = wp_insert_post( $my_post );

								add_post_meta( $post_id, '_mhmcrm_user_email', $toAdd );

							}
							*/

						}// end foreach loop

					}



			}

		}

	}

	public function admin_menu() {

		add_submenu_page(
			'edit.php?post_type=mhm_crm',
			__('Dashboard', 'mhm_crm'),
			__('Dashboard', 'mhm_crm'),
			'manage_options',
			'mhm_crm_dashboard',
			array($this, 'dashboard')
		);

		if ( current_user_can( 'administrator' ) ) {

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Webmail', 'mhm_crm'),
				__('Webmail', 'mhm_crm'),
				'manage_options',
				'mhm_crm_webmail',
				array($this, 'webmail')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('MailChimp', 'mhm_crm'),
				__('MailChimp', 'mhm_crm'),
				'manage_options',
				'mhm_crm_mailchimp',
				array($this, 'mailChimp')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Premade Sections', 'mhm_crm'),
				__('Premade Sections', 'mhm_crm'),
				'manage_options',
				'mhm_crm_premade_sections',
				array($this, 'premade_sections')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Records Sections', 'mhm_crm'),
				__('Records Sections', 'mhm_crm'),
				'manage_options',
				'mhm_crm_metabox',
				array($this, 'metabox')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Form Mapping', 'mhm_crm'),
				__('Form Mapping', 'mhm_crm'),
				'manage_options',
				'mhm_crm_cf7',
				array($this, 'contact_form_7')
			);

			add_submenu_page(
				'',
				__('WooCommerce', 'mhm_crm'),
				__('WooCommerce', 'mhm_crm'),
				'manage_options',
				'mhm_crm_woocommerce',
				array($this, 'woocommerce')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Automate', 'mhm_crm'),
				__('Automate', 'mhm_crm'),
				'manage_options',
				'mhm_crm_automate',
				array($this, 'automate')
			);

			add_submenu_page(
				'edit.php?post_type=mhm_crm',
				__('Settings', 'mhm_crm'),
				__('Settings', 'mhm_crm'),
				'manage_options',
				'mhm_crm_settings',
				array($this, 'settings')
			);

		}

	}

	public function mailChimp() {

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1
		);

		$products = get_posts( $args );

		?>

		<div class="bootstrap-iso">
			<div class="container-fluid">

				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#home">Mailchimp</a></li>
					<!--<li><a data-toggle="tab" href="#menu1">Map Mailchimp Lists to Products</a></li>-->
				</ul>

				<div class="tab-content">
					<div id="home" class="tab-pane fade in active">
						<form action="" method="post">
							API Key: <input type="text" name="mhm_crm_mailchimp_api_key" value="<?php echo get_option( '_mhm_crm_mailchimp_api_key' ); ?>" />
							<input type="submit" value="SAVE">
						</form>
						<?php

						$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

						$mailchimpLists = json_decode( $mailchimp->getLists() );
						$activatedLists = get_option('_mhm_crm_mailchimp_activated_lists');

						if ( $activatedLists === false ) {
							$activatedLists = array();
						}

						echo '<form action="" method="post">';
						echo '<h3>Enable Mailchimp List</h3>';
						echo '<ul>';
							foreach ( $mailchimpLists->lists as $list ) {
								if ( in_array( $list->id, $activatedLists ) ) {
									$selected = 'checked';
								} else {
									$selected = '';
								}
								echo '<li><input ' . $selected . ' type="checkbox" name="list[]" value="' . $list->id . '"> ' . $list->name . '</li>';
							}
						echo '</ul>';
						echo '<input type="hidden" name="enableMailchimpList">';
						echo '<input type="submit" value="SUBMIT" class="btn btn-default">';
						echo '</form>';
				?>
					</div>
					<!--
					<div id="menu1" class="tab-pane fade">
						<h3>Map Mailchimp List to Products</h3>
						<form action="" method="post">
							<div class="form-group">
								<label for="sel1">Select product:</label>
								<select class="form-control" name="productID">
									<option value="">--- Select Product ---</option>
									<?php if ( ! empty( $products ) ) : ?>
										<?php foreach ( $products as $product ): ?>
											<?php
												$selected = '';
												if ( isset( $_GET['productID'] ) && $_GET['productID'] == $product->ID ) {
													$selected = 'selected';
												}
											?>
											<option <?php echo $selected; ?> value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="form-group">
								<?php

									$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

									$mailchimpLists = json_decode( $mailchimp->getLists() );

									$mailchimpProdLists = get_option( '_mhm_crm_mapMailchimpListsToProduct' );

									echo '<ul>';

									foreach ( $mailchimpLists->lists as $list ) {

										$selected = '';

										if ( isset( $_GET['productID'] ) ) {
											if ( isset( $mailchimpProdLists[ $_GET['productID'] ] ) ) {
												$mailchimpLists = $mailchimpProdLists[ $_GET['productID'] ];
												if ( ! empty( $mailchimpLists ) && in_array( $list->id, $mailchimpLists ) ) {
													$selected = 'checked';
												}
											}
										}

										echo '<li><input ' . $selected . ' type="checkbox" name="list[]" value="' . $list->id . '"> ' . $list->name . '</li>';
									}

									echo '</ul>';

								?>
							</div>
							<input type="hidden" name="saveMapMailchimpListsToProduct">
							<input type="submit" value="SUBMIT" class="btn btn-default">
						</form>
					</div>
					-->
				</div>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {

				var hash = window.location.hash;



				$(document).on('click', '.nav-tabs a', function() {
					window.location.hash = $(this).attr('href');
				});

				if (hash != '') {
					$('a[href="' + hash + '"]').trigger('click');

					$('.tab-content .tab-pane').removeClass('active');
					$('.tab-content ' + hash).removeClass('fade').addClass('active');

				}

				$(document).on('change', '.tab-content #menu1 select', function() {

					var prodId = $(this).val();

					window.location.href = '/wp-admin/edit.php?post_type=mhm_crm&page=mhm_crm_mailchimp&mapmailchimpliststoproduct=true&productID=' + prodId + '#menu1';

				});

			});
		</script>
		<?php

	}

	public function metabox() {

		$_mhm_crm_record_sections = get_option( '_mhm_crm_record_sections' );

		?>
			<h1>Records Section</h1>
			<div class="bootstrap-iso">
				<form action="" method="post">
					<div class="container-fluid newmetabox-parent-container">
						
						<?php

							if ( $_mhm_crm_record_sections != false ) {

								foreach ( $_mhm_crm_record_sections as $key => $section ) {
						?>
									<div class="row" id="row_section<?php echo $key; ?>"><br /><br /><div class="col-md-5">
										<div class="form-inline">
											<input class="form-control mb-2" name="__mhm_crm_record_title[<?php echo $key; ?>]" value="<?php echo $section['record_title']; ?>" placeholder="Record Title">
											<button type="submit" class="btn btn-primary mb-2 row_section_remove" name="__mhm_crm_record_section[<?php echo $key; ?>]" data-row-section="<?php echo $key; ?>">Remove Section</button>
											<button type="submit" class="btn btn-primary mb-2 row_section_item_add" data-row-section="<?php echo $key; ?>">Add Field</button>
										</div>
										<div class="record_boxes_item_container">
											<?php
												for ( $i = 0; $i < count( $section['record_section_item_field'] ); $i ++ ) {
											?>
													<div class="record_boxes_item form-inline">
														<div class="form-group mb-6">
															<input type="text" value="<?php echo $section['record_section_item_field'][$i]; ?>" name="__mhm_crm_record_section_item_field[<?php echo $key; ?>][]" placeholder="Title" class="form-control">
														</div>
														<div class="form-group mb-6">
															<select class="form-control" name="__mhm_crm_record_section_item_field_cat[<?php echo $key; ?>][]">
																<option value="textbox" <?php echo $section['record_section_item_field_cat'][$i] == 'textbox' ? 'selected' : ''; ?>>Textbox</option>
																<option value="textarea"  <?php echo $section['record_section_item_field_cat'][$i] == 'textarea' ? 'selected' : ''; ?>>Textarea</option>
															</select>
														</div>
														<button type="submit" class="btn btn-primary mb-2 row_section_item_remove" data-row-section="<?php echo $i; ?>">Remove</button>
													</div>
											<?php
												}
											?>
										</div>
									</div>
								</div>
						<?php

								}

							}

						?>

					</div>
					<div class="container-fluid newmetabox-container">
						<button type="button" class="btn btn-primary" id="madhatmedia-crm-add-metabox">Add Meta Box</button>
						<button type="submit" class="btn btn-primary">SAVE</button>
					</div>
					<?php wp_nonce_field( 'mhm_crm_metaboxes_action', 'mhm_crm_metaboxes_fields' ); ?>
				</form>
			</div>
			<script>
				jQuery(document).ready(function($) {
					
					$(document).on('click', '#madhatmedia-crm-add-metabox', function() {

						var form_creator_index = Math.random();

						var html = `<div class="row" id="row_section` + form_creator_index + `"><br /><br /><div class="col-md-5">
								<div class="form-inline">
									<input class="form-control mb-2" name="__mhm_crm_record_title[` + form_creator_index + `]" placeholder="Record Title">
									<button type="submit" class="btn btn-primary mb-2 row_section_remove" name="__mhm_crm_record_section[` + form_creator_index + `]" data-row-section="` + form_creator_index + `">Remove Section</button>
									<button type="submit" class="btn btn-primary mb-2 row_section_item_add" data-row-section="` + form_creator_index + `">Add Field</button>
								</div>
								<div class="record_boxes_item_container">
									<div class="record_boxes_item form-inline">
										<div class="form-group mb-6">
											<input type="text" name="__mhm_crm_record_section_item_field[` + form_creator_index + `][]" placeholder="Title" class="form-control">
										</div>
										<div class="form-group mb-6">
											<select class="form-control" name="__mhm_crm_record_section_item_field_cat[` + form_creator_index + `][]">
												<option value="textbox">Textbox</option>
												<option value="textarea">Textarea</option>
											</select>
										</div>
										<button type="submit" class="btn btn-primary mb-2 row_section_item_remove" data-row-section="` + form_creator_index + `">Remove</button>
									</div>
								</div>
							</div>
						</div>`;

						$('.newmetabox-parent-container').append(html);

					});

					$(document).on('click', '.row_section_remove', function(e) {

						e.preventDefault();

						var row_id = $(this).attr('data-row-section');

						$(this).parent().parent().parent().remove();						

					});

					$(document).on('click', '.row_section_item_remove', function(e) {

						e.preventDefault();

						var row_id = $(this).attr('data-row-section');

						$(this).parent().remove();

					});

					$(document).on('click', '.row_section_item_add', function(e) {
						
						e.preventDefault();

						var form_creator_index = $(this).attr('data-row-section');

						var html = `<div class="record_boxes_item form-inline">
										<div class="form-group mb-6">
											<input type="text" name="__mhm_crm_record_section_item_field[` + form_creator_index + `][]" placeholder="Title" class="form-control">
										</div>
										<div class="form-group mb-6">
											<select class="form-control" name="__mhm_crm_record_section_item_field_cat[` + form_creator_index + `][]">
												<option value="textbox">Textbox</option>
												<option value="textarea">Textarea</option>
											</select>
										</div>
										<button type="submit" class="btn btn-primary mb-2 row_section_item_remove" data-row-section="` + form_creator_index + `">Remove</button>
									</div>`;

						$(this).parent().parent().parent().find($('.record_boxes_item_container')).append(html);

					});

				});
			</script>
		<?php


	}

	public function webmail() {

		$webmails = get_option( '_mhm_crm_webmail_accounts' );

		$_mhm_crm_webmails = explode( PHP_EOL, $webmails );

		$accountsArr = array();

		foreach ( $_mhm_crm_webmails as $accounts ) {

			$acc = array_filter( preg_split( '/\s+/', $accounts ) );

				if ( ! isset( $acc[0] ) ) {
					continue;
				}

				$email = $acc[0];
				$password = $acc[1];
				$domain = $acc[2];

				$accountsArr[] = array(
					'email' => $email,
					'password' => $password,
					'domain' => $domain
				);



		}

		echo '<h1>Webmail Accounts</h1>';

		echo '<form action="" method="post">';

		wp_nonce_field( 'mhm_crm_webmail_action', 'mhm_crm_webmail_fields' );

		$i = 0;

		echo '<table>';
		echo '<thead>';
			echo '<tr>';
				echo '<th>Email</th>';
				echo '<th>Password</th>';
				echo '<th>Domain</th>';
			echo '</tr>';
		echo '</thead>';

		echo '<tbody>';

		for ( $i = 0; $i <= 0; $i++ ) {

			if (isset( $accountsArr[$i] ) ) {

				$email = $accountsArr[$i]['email'];
				$password = $accountsArr[$i]['password'];
				$domain = $accountsArr[$i]['domain'];

			} else {

				$email = "";
				$password = "";
				$domain = "";

			}

			echo '<tr>';
				echo '<td><input type="text" name="email[' . $i . ']" value="' . $email . '"></td>';
				echo '<td><input type="text" name="password[' . $i . ']" value="' . $password . '"></td>';
				echo '<td><input type="text" name="domain[' . $i . ']" value="' . $domain . '"></td>';
			echo '</tr>';

		}

		echo '</tbody>';

		echo '</table>';

		echo '<br /><small>Eg: email password domain</small>';

		echo '<br /><input type="submit" value="SAVE">';

		echo '</form>';

	}

	private function define_constants() {

		define( 'MHM_CRM_TOKEN', $this->_token );

	}

	public function customers_list() {

		echo 123;

	}

	public function new_customer() {

		echo 1234;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/madhatmedia-crm-admin.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.css', array(), $this->version, 'all' );

		wp_enqueue_style( 'dataTables', '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', array(), '1.10.19', 'all' );

		wp_enqueue_style( 'select2js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), '4.0.6', 'all' );

		wp_enqueue_style( 'fontawesome', 'https://use.fontawesome.com/releases/v5.6.0/css/all.css', array(), '4.6.3', 'all' );

		if ( get_post_type() == 'mhm_crm' ) {
			wp_enqueue_style( 'pbtt_css', plugin_dir_url( __FILE__ ) . 'css/pbtt.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
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

		$options = get_option( '_mhm_crm_settings' );

		wp_enqueue_script( 'bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), '3.3.7', true );

		wp_enqueue_script( 'google-map', 'https://maps.googleapis.com/maps/api/js?key=' . $options['google_maps_api_key'] );

		wp_enqueue_script( 'dataTables', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', array( 'jquery'), '1.10.16', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/madhatmedia-crm-admin.js', array( 'jquery' ), $this->version, true );

		wp_enqueue_script( 'select2js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array( 'jquery' ), '4.0.6', true );


		if ( get_post_type() == 'mhm_crm' ) {
			wp_enqueue_script( 'pbtt_js', plugin_dir_url( __FILE__ ) . 'js/pbtt.js', array( 'jquery', 'jquery-ui-sortable' ), $this->version, true );
		}

	}

	public function countries() {

		$countries =

			array(
			"AF" => "Afghanistan",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, the Democratic Republic of the",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Cote D'Ivoire",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands (Malvinas)",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GN" => "Guinea",
			"GW" => "Guinea-Bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and Mcdonald Islands",
			"VA" => "Holy See (Vatican City State)",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran, Islamic Republic of",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libyan Arab Jamahiriya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macao",
			"MK" => "Macedonia, the Former Yugoslav Republic of",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territory, Occupied",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and the Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"CS" => "Serbia and Montenegro",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and the South Sandwich Islands",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan, Province of China",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TL" => "Timor-Leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Viet Nam",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.s.",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe"
			);

		return $countries;

	}

	public function my_custom_taxonomy_columns( $columns ) {

		$columns['mhm_crm_color'] = 'Color';

		return $columns;

	}

	public function my_custom_groups_columns_content( $content, $column_name, $term_id ) {

		if ( 'mhm_crm_color' == $column_name ) {

			$color = get_term_meta( $term_id, 'mhm_crm_group_color', true );

			if ($color != '') {
				$content = "<div style='background-color: {$color}; padding: 10px;'></div>";
			} else {
				$content = '';
			}
		}

		return $content;

	}

	public function my_custom_status_columns_content( $content, $column_name, $term_id ) {

		if ( 'mhm_crm_color' == $column_name ) {

			$color = get_term_meta( $term_id, 'mhm_crm_status_color', true );

			if ($color != '') {
				$content = "<div style='background-color: {$color}; padding: 10px;'></div>";
			} else {
				$content = '';
			}

		}

		return $content;

	}

	public function contact_form_7() {

		$forms = get_posts(	array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'wpcf7_contact_form'
		) );

		$form_tags = array();

		if ( isset( $_GET['form_id'] ) ) {
			$form_tags = WPCF7_ContactForm::find(array( 'p' => $_GET['form_id'] ) )[0]->scan_form_tags();
		}

		if ( get_option( '_mhmcrm_cf7_form_options' ) ) {
			$options = get_option( '_mhmcrm_cf7_form_options' )[ $_GET['form_id'] ];
		} else {
			$options = array();
		}

		$prefix = '_mhmcrm_';

		$post_meta_fields = array(
			'General Details' => array(
				'first_name',
				'last_name',
				'customer_title',
				'user_email',
				'customer_department',
				'customer_mobile',
				'customer_fax',
				'customer_site',
				'date_of_birth',
				'customer_assistant',
				'customer_twitter'
			),
			'Billing Details' => array(
				'_billing_first_name',
				'_billing_last_name',
				'_billing_company',
				'_billing_address_1',
				'_billing_address_2',
				'_billing_city',
				'_billing_postcode',
				'_billing_country',
				'_billing_state',
				'_billing_email',
				'_billing_phone'
			),
			'Shipping Details' => array(
				'_shipping_first_name',
				'_shipping_last_name',
				'_shipping_company',
				'_shipping_address_1',
				'_shipping_address_2',
				'_shipping_city',
				'_shipping_postcode',
				'_shipping_country',
				'_shipping_state'
			),
			'Real Estate Zip Code' => array(
				'_real_estate_zip_code'
			)
		);

		$_mhm_crm_record_sections = get_option( '_mhm_crm_record_sections' );

		if ( ! empty( $forms ) ) {
		?>
			<div class="bootstrap-iso">
				<form action="" method="post">
					<div class="container">
						<div class="row">
							<div class="col-md-3">
								<label>Contact Form 7</label>
								<select name="cf7forms" class="form-control" onchange="this.options[this.selectedIndex].value && (window.location = 'edit.php?post_type=mhm_crm&page=mhm_crm_cf7&form_id=' + this.options[this.selectedIndex].value);">
									<option value=''>--- Select Form ---</option>
									<?php foreach ( $forms as $form ) : ?>
										<option <?php echo isset( $_GET['form_id'] ) && $_GET['form_id'] == $form->ID ? 'selected' : ''; ?> value="<?php echo $form->ID; ?>"><?php echo $form->post_title; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<table class="table">
									<tr>
										<td>Enable</td>
										<td><input type="checkbox" name="enable" <?php echo $options['enabled'] == 'yes' ? 'checked' : ''; ?> ></td>
									</tr>
									<?php foreach ( $post_meta_fields as $key => $fields ) : ?>
											<tr>
												<td colspan="2"><b><?php echo $key; ?></b></td>
											</tr>
											<?php foreach ( $fields as $field ) : ?>
											<?php

												$check_options = get_option( '_mhmcrm_cf7_form_options' );
												$track_map_forms = array();

												if ($check_options) {

													foreach ( $check_options as $key => $value ) {

														$title = get_the_title( $key );
														$form_field = '_mhmcrm_' . $field;

														if ( $value[ $form_field ] != '' ) {
															$track_map_forms[] = $title;
														}
													}

												}

											?>
												<tr>
													<td><?php echo $field; ?></td>
													<td>
														<select name="_mhmcrm_cf7_form[_mhmcrm_<?php echo $field; ?>]">
															<option value="">--- Select Form Tag ---</option>
															<?php foreach ( $form_tags as $tag ) :
																if ( $tag['name'] == '' ) continue;

																if ( $options['_mhmcrm_' . $field] == $tag['name'] ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
																<option <?php echo $selected; ?> value="<?php echo $tag['name']; ?>"><?php echo $tag['name']; ?></option>
															<?php endforeach; ?>
														</select>
													</td>
													<td>
														<?php echo implode( ', ', $track_map_forms ); ?>
													</td>
												</tr>
											<?php endforeach; ?>
									<?php endforeach; ?>

									<?php
										if ( $_mhm_crm_record_sections != false ) {

											for ( $i = 0; $i < count( $_mhm_crm_record_sections ); $i++ ) {

												$title = $_mhm_crm_record_sections[$i]['record_title'];
												$field = $_mhm_crm_record_sections[$i]['record_section_item_field'];

									?>
												<tr>
													<td colspan="2"><b><?php echo $title; ?></b></td>
												</tr>

												<?php
													for ( $x = 0; $x < count( $_mhm_crm_record_sections[$i]['record_section_item_field'] ); $x++ ) {

														$field = $_mhm_crm_record_sections[$i]['record_section_item_field'][$x];

														$field_name = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','-', trim($field))) . '-dynamicmetabox-' . $i;
												?>
												<?php

													$check_options = get_option( '_mhmcrm_cf7_form_options' );
													$track_map_forms = array();

													foreach ( $check_options as $key => $value ) {

														$title = get_the_title( $key );
														$form_field = $field_name;

														if ( $value[ $form_field ] != '' ) {
															$track_map_forms[] = $title;
														}
													}

												?>
														<tr>
															<td><?php echo $field; ?></td>
															<td>
																<select name="_mhmcrm_cf7_form[<?php echo $field_name; ?>]">
																<option value="">--- Select Form Tag ---</option>
																<?php foreach ( $form_tags as $tag ) :
																	if ( $tag['name'] == '' ) continue;

																	if ( $options[ $field_name ] == $tag['name'] ) {
																		$selected = 'selected';
																	} else {
																		$selected = '';
																	}
																?>
																	<option <?php echo $selected; ?> value="<?php echo $tag['name']; ?>"><?php echo $tag['name']; ?></option>
																<?php endforeach; ?>
															</select>
															</td>
															<td>
																<?php echo implode( ', ', $track_map_forms ); ?>
															</td>
														</tr>
												<?php } ?>
									<?php
												}
											
										}
									?>

								</table>
							</div>

						</div>
						<div class="row">
							<input type="submit" class="btn btn-primary" value="SAVE">
						</div>
					</div>
				</form>
			</div>
		<?php
		}
	}

	public function wpcf7_before_send_mail( $contact_form ) {

		global $wpdb;
		
		$submission = WPCF7_Submission::get_instance();
		$form_id = $contact_form->id();

		$options = get_option( '_mhmcrm_cf7_form_options' );

		if ( isset( $options[ $form_id ] ) && $options[ $form_id ]['enabled'] == 'yes' ) {

			$options = $options[ $form_id ];

			//Get current form
			$wpcf7      = $contact_form::get_current();

			// get current SUBMISSION instance
			$submission = WPCF7_Submission::get_instance();

			if ( $submission ) {

				$data = $submission->get_posted_data();

				if ( isset( $options['_mhmcrm_user_email'] ) ) {
					$email = $data[ $options['_mhmcrm_user_email'] ];
				} else {
					$email = '';
				}

				$my_post = array(
					'post_title'    => wp_strip_all_tags( $email ),
					'post_type'		=> 'mhm_crm',
					'post_status'   => 'publish'
				);

				$query = $wpdb->prepare(
					'SELECT ID FROM ' . $wpdb->posts . '
					WHERE post_title = %s
					AND post_type = \'mhm_crm\' AND post_status = \'publish\'',
					$email
				);

				$wpdb->query( $query );
				
				if ( $wpdb->num_rows > 0 ) {

					$post_id = $wpdb->get_var( $query );

				} else {

					$post_id = wp_insert_post( $my_post );
				
				}
				
				foreach ( $options as $key => $val ) {

					if ( isset( $data[ $val ] ) ) {

						if ( $key == '_mhmcrm_user_email' ) {
							$email = $data[ $val ];
						}

						if ( ! add_post_meta( $post_id, $key, $data[ $val ], true ) ) {

							update_post_meta( $post_id, $key, $data[ $val ] );

						}

					}

				}

				wp_update_post( array(
					'ID'			=> $post_id,
					'post_title'	=> $email
				) );


				$_mhm_crm_automate_cf7_form = get_option( '_mhm_crm_automate_cf7_form' );

				if ( $_mhm_crm_automate_cf7_form ) {
					
					if ( isset( $_mhm_crm_automate_cf7_form[ $form_id ] ) ) {

						$form_id = $_mhm_crm_automate_cf7_form[ $form_id ]['form_id'];
						$condition = $_mhm_crm_automate_cf7_form[ $form_id ]['condition'];

						if ( isset( $_mhm_crm_automate_cf7_form[ $form_id ]['status'] ) ) {

							$statuses = array_map( 'intval', $_mhm_crm_automate_cf7_form[ $form_id ]['status'] );

							if ( $condition == 'add' ) {

								wp_add_object_terms( $post_id, $statuses, 'mhm_crm_status' );


							} elseif ( $condition == 'remove' ) {

								wp_remove_object_terms( $post_id, $statuses, 'mhm_crm_status' );

							}

						}

						if ( isset( $_mhm_crm_automate_cf7_form[ $form_id ]['groups'] ) ) {

							$groups = array_map( 'intval', $_mhm_crm_automate_cf7_form[ $form_id ]['groups'] );

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

	public function row_actions( $actions, $post ) {

		global $wpdb;

		if ( $post->post_type == 'mhm_crm' ) {

			$post_id = $post->ID;
			$email = $post->post_title;

			$post = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='mhm_crm' AND post_status = 'publish'", $email ));

			$actions['mhm_crm_woocommerce_actions'] = '<a href="/wp-admin/edit.php?post_type=mhm_crm&page=mhm_crm_woocommerce&email=' . $email . '">View Orders</a>';
		}

		return $actions;

	}

	public function woocommerce() {

		global $wpdb;

		if ( isset( $_GET['email'] ) && $_GET['email'] != '' ) {

			$email = $_GET['email'];

			$user = get_user_by( 'email', $email );

			$args = array();

			if ( $user ) {
				$args['_customer_user'] = $user->data->ID;
			}

			$args['_billing_email'] = $email;
			
			if ( ! empty( $args ) ) {

			$orders = wc_get_orders( $args );

			if ( empty( $orders ) ) return;

		?>

				<div class="bootstrap-iso">
					<div class="container">
						<h1>Orders - <?php echo $email; ?></h1>
						<table class="table table-striped">
							<thead>
							<tr>
								<th>Order ID</th>
								<th>Date</th>
								<th>Status</th>
								<th>Total</th>
							</tr>
							</thead>
							<tbody>
								<?php foreach ( $orders as $order ): ?>
								<?php
									$order_data = $order->get_data();
								?>
									<tr>
										<td><a href="/wp-admin/post.php?post=<?php echo $order->get_id(); ?>&action=edit"><?php echo $order->get_id(); ?></a></td>
										<td><?php echo $order_data['date_created']->date('Y-m-d H:i:s'); ?></td>
										<td><?php echo $order_data['status']; ?></td>
										<td><?php echo $order->get_total(); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>

		<?php
			}

		}

	}

	public function automate() {

		$forms = get_posts(	array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'wpcf7_contact_form'
		) );

		$products = get_posts(	array(
			'posts_per_page'	=> -1,
			'post_type'			=> 'product'
		) );

		$status = get_terms( 'mhm_crm_status', array(
			'hide_empty' => false,
		) );

		$groups = get_terms( 'mhm_crm_group', array(
			'hide_empty' => false,
		) );
		
		$_mhm_crm_automate_cf7_form = get_option( '_mhm_crm_automate_cf7_form' );

		if ( $_mhm_crm_automate_cf7_form ) {
			$_mhm_crm_automate_cf7_form = array_values( $_mhm_crm_automate_cf7_form );
		} else {
			$_mhm_crm_automate_cf7_form = array();
		}

		$_mhm_crm_automate_woo_form = get_option( '_mhm_crm_automate_woo_form' );

		if ( $_mhm_crm_automate_woo_form ) {
			$_mhm_crm_automate_woo_form = array_values( $_mhm_crm_automate_woo_form );
		} else {
			$_mhm_crm_automate_woo_form = array();
		}

		?>

		<div class="form-id-container-creator" style="display: none;">
			
		</div>
		<div class="bootstrap-iso">
			<div class="container-fluid">
				<ul class="nav nav-tabs">
					<li class="active"><a data-toggle="tab" href="#cf7">Contact Form 7</a></li>
					<li><a data-toggle="tab" href="#woocommerce">WooCommerce</a></li>
				</ul>
				<div class="tab-content">
					<div id="cf7" class="tab-pane fade in active">
						<h3>Contact Form 7</h3>
						<form action="" method="post">
							<div class="cf7-form-lists">
								
								<?php $i = 0; if ( ! empty( $_mhm_crm_automate_cf7_form ) ): ?>
									<?php foreach ( $_mhm_crm_automate_cf7_form as $key => $value ): ?>
										<div class="cf7-form-creator">
											<div class="form-group">
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Form</label>
													<select class="form-control col-sm-10" name="_mhm_crm_automate_cf7_form_id[<?php echo $i; ?>]">
														<?php foreach ( $forms as $form ): ?>
															<?php
																if ( $value['form_id'] == $form->ID ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $form->ID; ?>"><?php echo $form->post_title; ?></option>
														<?php endforeach; ?>
													</select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Condition</label>
													<select class="form-control col-sm-10" required name="_mhm_crm_automate_cf7_condition[<?php echo $i; ?>]">
														<option value="">--- Condition ---</option>
														<option value="add" <?php echo $value['condition'] == 'add' ? 'selected' : ''; ?>>ADD</option>
														<option value="remove" <?php echo $value['condition'] == 'remove' ? 'selected' : ''; ?>>REMOVE</option>
													<select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Status</label>
													<select class="form-control col-sm-10" name="_mhm_crm_automate_cf7_status[<?php echo $i; ?>][]">
														<option value="">--- Status ---</option>
														<?php foreach ( $status as $s ): ?>
															<?php 
																if ( in_array( $s->term_id, $value['status'] ) ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
														<?php endforeach; ?>
													<select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Group</label>
													<select class="form-control col-sm-10 _mhm_crm_select2js" name="_mhm_crm_automate_cf7_group[<?php echo $i; ?>][]" multiple>
														<option value="">--- Group ---</option>
														<?php foreach ( $groups as $s ): ?>
															<?php 
																if ( in_array( $s->term_id, $value['groups'] ) ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
														<?php endforeach; ?>
													<select>
												</div>
												<button type="submit" class="btn btn-default remove-cf7-form-btn">REMOVE</button>
											</div>
										</div>
									<?php $i++; endforeach; ?>
								<?php endif; ?>

							</div>
							<br />
							<p>
								<input type="submit" class="btn btn-primary" name="_mhm_crm_automate_cf7_form" value="SAVE">
								<input type="button" class="add-cf7-form-btn btn btn-primary" value="ADD FORM">
							</p>
						</form>
					</div>
					<div id="woocommerce" class="tab-pane fade">
						<h3>WooCommerce</h3>
						<form action="" method="post">
							<div class="woo-form-lists">
								
								<?php $i = 0; if ( ! empty( $_mhm_crm_automate_woo_form ) ): ?>
									<?php foreach ( $_mhm_crm_automate_woo_form as $key => $value ): ?>
										<div class="woo-form-creator">
											<div class="form-group">
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Product</label>
													<select class="form-control col-sm-10" name="_mhm_crm_automate_woo_form_id[<?php echo $i; ?>]">
														<?php foreach ( $products as $product ): ?>
															<?php
																if ( $value['form_id'] == $product->ID ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
														<?php endforeach; ?>
													</select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Condition</label>
													<select class="form-control col-sm-10" required name="_mhm_crm_automate_woo_condition[<?php echo $i; ?>]">
														<option value="">--- Condition ---</option>
														<option value="add" <?php echo $value['condition'] == 'add' ? 'selected' : ''; ?>>ADD</option>
														<option value="remove" <?php echo $value['condition'] == 'remove' ? 'selected' : ''; ?>>REMOVE</option>
													<select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Status</label>
													<select class="form-control col-sm-10" name="_mhm_crm_automate_woo_status[<?php echo $i; ?>][]">
														<option value="">--- Status ---</option>
														<?php foreach ( $status as $s ): ?>
															<?php 
																if ( in_array( $s->term_id, $value['status'] ) ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
														<?php endforeach; ?>
													<select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">Group</label>
													<select class="form-control col-sm-10 _mhm_crm_select2js" name="_mhm_crm_automate_woo_group[<?php echo $i; ?>][]" multiple>
														<option value="">--- Group ---</option>
														<?php foreach ( $groups as $s ): ?>
															<?php 
																if ( in_array( $s->term_id, $value['groups'] ) ) {
																	$selected = 'selected';
																} else {
																	$selected = '';
																}
															?>
															<option <?php echo $selected; ?> value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
														<?php endforeach; ?>
													<select>
												</div>
												<div class="form-group row">
													<label class="col-sm-2 col-form-label">MailChimp</label>
													<select class="_mhm_crm_select2js col-sm-10" name="_mhm_crm_automate_woo_mailchimp[<?php echo $i; ?>][]" multiple>
													<?php

														$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

														$mailchimpLists = json_decode( $mailchimp->getLists() );

														$mailchimpProdLists = get_option( '_mhm_crm_mapMailchimpListsToProduct' );

														foreach ( $mailchimpLists->lists as $list ) {

															$selected = '';

															if ( in_array( $list->id, get_post_meta( $value['form_id'], '_mhm_crm_automate_woo_mailchimp', true ) ) ) {
																$selected = 'selected';
															}

															echo '<option ' . $selected . ' value="' . $list->id . '">' . $list->name . '</option>';

														}

													?>
													</select>
												</div>
												<button type="submit" class="btn btn-default remove-woo-form-btn">REMOVE</button>
											</div>
										</div>
									<?php $i++; endforeach; ?>
								<?php endif; ?>

							</div>
							<br />
							<p>
								<input type="submit" class="btn btn-primary" name="_mhm_crm_automate_woo_form" value="SAVE">
								<input type="button" class="add-woo-form-btn btn btn-primary" value="ADD FORM">
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>

		<script>
			jQuery(document).ready(function($) {

				$(document).on('click', '.add-cf7-form-btn', function(e) {
					e.preventDefault();

					var form = $('.form-id-container-creator').html();
					var form_creator_index = Math.random();

					$('.cf7-form-lists').append(`<div class="cf7-form-creator">
									<div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Form</label>
											<select class="form-control col-sm-10" name="_mhm_crm_automate_cf7_form_id[` + form_creator_index + `]">
												<?php foreach ( $forms as $form ): ?>
													<option value="<?php echo $form->ID; ?>"><?php echo $form->post_title; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Condition</label>
											<select class="form-control col-sm-10" required name="_mhm_crm_automate_cf7_condition[` + form_creator_index + `]">
												<option value="">--- Condition ---</option>
												<option value="add">ADD</option>
												<option value="remove">REMOVE</option>
											<select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Status</label>
											<select class="form-control col-sm-10" name="_mhm_crm_automate_cf7_status[` + form_creator_index + `][]">
												<option value="">--- Status ---</option>
												<?php foreach ( $status as $s ): ?>
													<option value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
												<?php endforeach; ?>
											<select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Group</label>
											<select class="form-control col-sm-10 _mhm_crm_select2js" name="_mhm_crm_automate_cf7_group[` + form_creator_index + `][]" multiple>
												<option value="">--- Group ---</option>
												<?php foreach ( $groups as $s ): ?>
													<option value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
												<?php endforeach; ?>
											<select>
										</div>
										<button type="submit" class="btn btn-default remove-cf7-form-btn">REMOVE</button>
									</div>
								</div>`);

								$('._mhm_crm_select2js').select2();

				});			

				$(document).on('click', '.remove-cf7-form-btn', function(e) {
					$(this).parent().parent().remove();
				});


				$(document).on('click', '.add-woo-form-btn', function(e) {
					e.preventDefault();

					var form = $('.form-id-container-creator').html();
					var form_creator_index = Math.random();

					$('.woo-form-lists').append(`<div class="woo-form-creator">
									<div class="">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Product</label>
											<select class="form-control col-sm-10" name="_mhm_crm_automate_woo_form_id[` + form_creator_index + `]">
												<?php foreach ( $products as $product ): ?>
													<option value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Condition</label>
											<select class="form-control col-sm-10" required name="_mhm_crm_automate_woo_condition[` + form_creator_index + `]">
												<option value="">--- Condition ---</option>
												<option value="add">ADD</option>
												<option value="remove">REMOVE</option>
											<select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Form</label>
											<select class="form-control col-sm-10" name="_mhm_crm_automate_woo_status[` + form_creator_index + `][]">
												<option value="">--- Status ---</option>
												<?php foreach ( $status as $s ): ?>
													<option value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
												<?php endforeach; ?>
											<select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Form</label>
											<select class="form-control col-sm-10 _mhm_crm_select2js" name="_mhm_crm_automate_woo_group[` + form_creator_index + `][]" multiple>
												<option value="">--- Group ---</option>
												<?php foreach ( $groups as $s ): ?>
													<option value="<?php echo $s->term_id; ?>"><?php echo $s->name; ?></option>
												<?php endforeach; ?>
											<select>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">MailChimp</label>
													<select class="_mhm_crm_select2js col-sm-10" name="_mhm_crm_automate_woo_mailchimp[` + form_creator_index + `][]" multiple>
													<?php

														$mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );

														$mailchimpLists = json_decode( $mailchimp->getLists() );

														$mailchimpProdLists = get_option( '_mhm_crm_mapMailchimpListsToProduct' );

														foreach ( $mailchimpLists->lists as $list ) {

															$selected = '';

															echo '<option value="' . $list->id . '">' . $list->name . '</option>';

														}

													?>
													</select>
												</div>
										<button type="submit" class="btn btn-default remove-woo-form-btn">REMOVE</button>
									</div>
								</div>`);
					
								$('._mhm_crm_select2js').select2();

				});			

				$(document).on('click', '.remove-woo-form-btn', function(e) {
					$(this).parent().parent().remove();
				});

				$('._mhm_crm_select2js').select2();

			});
		</script>
		<style>
			._mhm_crm_select2js {
				width: 400px !important;
			}
		</style>
		<?php

	}

	public function dashboard() {

		$customers = get_posts(
			array(
				'posts_per_page'	=> -1,
				'post_type'			=> 'mhm_crm'
			)
		);

		$total_customers = count( $customers );

		$statuses = get_terms( array(
			'taxonomy'		=> 'mhm_crm_status',
			'hide_empty'	=> false,
		) );

		$total_status = count( $statuses );

		$groups = get_terms( array(
			'taxonomy'		=> 'mhm_crm_group',
			'hide_empty'	=> false,
		) );

		$total_groups = count( $groups );

		$_mhm_crm_automate_cf7_form = get_option( '_mhm_crm_automate_cf7_form' );

		$total_mhm_crm_automate_cf7_form = count( $_mhm_crm_automate_cf7_form );

		$_mhm_crm_automate_woo_form = get_option( '_mhm_crm_automate_woo_form' );

		$total_mhm_crm_automate_woo_form = count( $_mhm_crm_automate_woo_form );
		
		$current_user = wp_get_current_user();

		$args = array(
			'post_type'		=> 'mhm_crm',
			'meta_query'	=> array(
				array(
					'key'	=> '_mhm_crm__assign_staff',
					'value'	=> serialize( strval( $current_user->user_email ) ),
					'compare' => 'LIKE'
				)
			)
		);

		$assigned_records = get_posts( $args );

		?>
		<br />
		<br />
		<br />
		<br />
		<div class="bootstrap-iso">
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-user-alt fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo $total_customers; ?></div>
										<div>Total Customers</div>
									</div>
								</div>
							</div>
							<a href="/wp-admin/edit.php?post_type=mhm_crm">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-green">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-tasks fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo $total_status; ?></div>
										<div>Types of Statuses</div>
									</div>
								</div>
							</div>
							<a href="/wp-admin/edit-tags.php?taxonomy=mhm_crm_status&post_type=mhm_crm">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-yellow">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-users fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo $total_groups; ?></div>
										<div>Types of Groups</div>
									</div>
								</div>
							</div>
							<a href="/wp-admin/edit-tags.php?taxonomy=mhm_crm_group&post_type=mhm_crm">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-red">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-user-astronaut fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo $total_mhm_crm_automate_cf7_form; ?></div>
										<div>Form Automation</div>
									</div>
								</div>
							</div>
							<a href="/wp-admin/edit.php?post_type=mhm_crm&page=mhm_crm_automate">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-green">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-robot fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo $total_mhm_crm_automate_woo_form; ?></div>
										<div>WooCommerce Automation</div>
									</div>
								</div>
							</div>
							<a href="/wp-admin/edit.php?post_type=mhm_crm&page=mhm_crm_automate">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-lg-3 col-md-6">
						<div class="panel panel-red">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-3">
										<i class="fa fa-robot fa-5x"></i>
									</div>
									<div class="col-xs-9 text-right">
										<div class="huge"><?php echo count( $assigned_records ); ?></div>
										<div>Assigned Records</div>
									</div>
								</div>
							</div>
							<a href="#user_assigned_records">
								<div class="panel-footer">
									<span class="pull-left">View Details</span>
									<span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
				</div>
			</div>
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-6">
						<div class="panel panel-default">
							<div class="panel-heading" style="background: green; color: white;">
								Status
							</div>
							<!-- /.panel-heading -->
							<div class="panel-body">
								<div class="table-responsive table-bordered">
									<table class="table">
										<thead>
											<tr>
												<th>Name</th>
												<th>Count</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $statuses as $status ): ?>
												<tr>
													<td><a href="/wp-admin/edit.php?mhm_crm_status=<?php echo $status->slug; ?>&post_type=mhm_crm"><?php echo $status->name; ?></a></td>
													<td><a href="/wp-admin/edit.php?mhm_crm_status=<?php echo $status->slug; ?>&post_type=mhm_crm"><?php echo $status->count; ?></a></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<!-- /.table-responsive -->
							</div>
							<!-- /.panel-body -->
						</div>
						<!-- /.panel -->
					</div>
					<!-- /.col-lg-6 -->
					<div class="col-lg-6">
						<div class="panel panel-default">
							<div class="panel-heading" style="background: #F0AD4E; color: white;">
								Groups
							</div>
							<!-- /.panel-heading -->
							<div class="panel-body">
								<div class="table-responsive table-bordered">
									<table class="table">
										<thead>
											<tr>
												<th>Name</th>
												<th>Count</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $groups as $group ): ?>
												<tr>
													<td><a href="/wp-admin/edit.php?mhm_crm_group=<?php echo $group->slug; ?>&post_type=mhm_crm"><?php echo $group->name; ?></a></td>
													<td><a href="/wp-admin/edit.php?mhm_crm_group=<?php echo $group->slug; ?>&post_type=mhm_crm"><?php echo $group->count; ?></a></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<!-- /.table-responsive -->
							</div>
							<!-- /.panel-body -->
						</div>
						<!-- /.panel -->
					</div>
					<!-- /.col-lg-6 -->
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="panel panel-default">
							<div class="panel-heading" style="background: green; color: white;">
								Assigned Records
							</div>
							<!-- /.panel-heading -->
							<div class="panel-body">
								<div class="table-responsive table-bordered" id="user_assigned_records">
									<table class="table">
										<thead>
											<tr>
												<th>First Name</th>
												<th>Last Name</th>
												<th>Email</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ( $assigned_records as $record ): ?>
											<?php
												$user_email = get_post_meta( $record->ID, '_mhmcrm_user_email', true );
												$first_name = get_post_meta( $record->ID, '_mhmcrm_first_name', true );
												$last_name = get_post_meta( $record->ID, '_mhmcrm_last_name', true );
											?>
												<tr>
													<td><a href="/wp-admin/post.php?post=<?php echo $record->ID; ?>&action=edit"><?php echo $first_name; ?></a></td>
													<td><a href="/wp-admin/post.php?post=<?php echo $record->ID; ?>&action=edit"><?php echo $last_name; ?></a></td>
													<td><a href="/wp-admin/post.php?post=<?php echo $record->ID; ?>&action=edit"><?php echo $user_email; ?></a></td>
												</tr>
											<?php endforeach; ?>
										</tbody>
									</table>
								</div>
								<!-- /.table-responsive -->
							</div>
							<!-- /.panel-body -->
						</div>
						<!-- /.panel -->
					</div>
				</div>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#user_assigned_records table').DataTable();
			});
		</script>
		<?php

	}

	public function filter_groups_and_status( $post_type, $which ) {

		if ( 'mhm_crm' !== $post_type )
		return;

		$taxonomies = array( 'mhm_crm_status', 'mhm_crm_group' );

		foreach ( $taxonomies as $taxonomy_slug ) {

			$taxonomy_obj = get_taxonomy( $taxonomy_slug );
			$taxonomy_name = $taxonomy_obj->labels->name;

			$terms = get_terms( $taxonomy_slug );

			echo "<select name='{$taxonomy_slug}' id='{$taxonomy_slug}' class='postform'>";
			echo '<option value="">' . sprintf( esc_html__( 'Show All %s', 'text_domain' ), $taxonomy_name ) . '</option>';
			foreach ( $terms as $term ) {
				printf(
					'<option value="%1$s" %2$s>%3$s (%4$s)</option>',
					$term->slug,
					( ( isset( $_GET[$taxonomy_slug] ) && ( $_GET[$taxonomy_slug] == $term->slug ) ) ? ' selected="selected"' : '' ),
					$term->name,
					$term->count
				);
			}
			echo '</select>';
		}

	}

	public static function assignee_trigger_email( $type, $meta_key, $users, $post_id = 0 ) {
		
		if ( $type == 'customer' ) {

			$assignees = get_post_meta( $post_id, $meta_key, true );

			$existing = array_intersect( $users, $assignees );

			$user_email = get_post_meta( $post_id, '_mhmcrm_user_email', true );

			if ( ! empty( $users ) ) {

				$to = $users;
				$subject = 'New Customer';
				$body = "New customer '{$user_email}' has been assigned to you.";

				$response = wp_mail( $to, $subject, $body );

			}

		} else if ( $type == 'status' ) {

			$assignees = get_term_meta( $post_id, $meta_key, true );
			
			$existing = array_intersect( $users, $assignees );

			$term = get_term_by( 'id', ( int ) $post_id, 'mhm_crm_status' );

			if ( ! empty( $users ) ) {

				$to = $users;
				$subject = 'New Status';
				$body = "New status '{$term->name}' has been assigned to you.";
				
				$response = wp_mail( $to, $subject, $body );

			}

		} else if ( $type == 'groups' ) {

			$assignees = get_term_meta( $post_id, $meta_key, true );
			
			$existing = array_intersect( $users, $assignees );

			$term = get_term_by( 'id', ( int ) $post_id, 'mhm_crm_group' );

			if ( ! empty( $users ) ) {

				$to = $users;
				$subject = 'New Group';
				$body = "New group '{$term->name}' has been assigned to you.";
				
				$response = wp_mail( $to, $subject, $body );

			}

		}

	}

	public function premade_sections() {

		$premade_sections = array(
			'General Details' => 'general_details',
			'Billing Details' => 'billing_details',
			'Shipping Details' => 'shipping_details',
			'Notes' => 'notes',
			'Phone' => 'phone',
			'Email Logs' => 'email_logs',
			'Logs' => 'logs',
			'Real Estate Zip Codes' => 'real_estate_zip_codes'
		);

		$premade_sections_option = ( array ) get_option( 'mhm_crm_premade_sections' );

		?>
			<div class="bootstrap-iso">
				<div class="container">
					<h2>Premade Sections</h2>
					<form action="" method="post">
						<?php foreach ( $premade_sections as $title => $section ) : ?>
							<div class="form-group">
								<label><?php echo $title; ?></label>
								<input type="checkbox" <?php echo isset( $premade_sections_option[ $section ] ) ? 'checked' : ''; ?> name="mhmcrm[<?php echo $section; ?>]">
							</div>
						<?php endforeach; ?>
						<input type="hidden" name="premade_sections_submit" value="true">
						<button type="submit" class="btn btn-default">Submit</button>
					</form>
				</div>
			</div>
		<?php

	}

	public function settings() {
		$options = get_option( '_mhm_crm_settings' );
		?>
		<div class="bootstrap-iso">
			<div class="container">
				<form action="" method="post">
					<div class="form-group">
						<label>Google Maps Api Key</label>
						<input type="text" style="width: 100%;" name="mhmcrm[google_maps_api_key]" class="form-control" placeholder="Google Maps API Key" value="<?php echo $options['google_maps_api_key']; ?>">
					</div>
					<input type="hidden" name="mhmcrm_settings_submit" value="true">
					<button type="submit" class="btn btn-default">Submit</button>
				</form>
			</div>
		</div>
		<?php
	}

}
