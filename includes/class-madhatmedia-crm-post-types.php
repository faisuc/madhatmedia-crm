<?php

if ( ! defined( 'ABSPATH' ) ) {

    exit;

}

if ( ! class_exists( 'MHM_CRM_Admin_Post_Types' ) ) :

    class MHM_CRM_Admin_Post_Types {

        public function __construct() {

            add_action( 'init', array( $this, 'register_post_types' ) );
            add_action( 'add_meta_boxes', array( $this, 'register_customer_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_postdata' ) );
            add_action( 'mhm_crm_status_add_form_fields', array( $this, 'mhm_crm_status_add_form_fields' ) );
            add_action( 'mhm_crm_status_edit_form_fields', array( $this, 'mhm_crm_status_edit_form_fields' ) );
            add_action( 'edited_mhm_crm_status', array( $this, 'mhm_crm_status_save_color' ) );
            add_action( 'created_mhm_crm_status', array( $this, 'mhm_crm_status_save_color' ) );

            add_action( 'mhm_crm_group_add_form_fields', array( $this, 'mhm_crm_group_add_form_fields' ) );
            add_action( 'mhm_crm_group_edit_form_fields', array( $this, 'mhm_crm_group_edit_form_fields' ) );
            add_action( 'edited_mhm_crm_group', array( $this, 'mhm_crm_group_save_color' ) );
            add_action( 'created_mhm_crm_group', array( $this, 'mhm_crm_group_save_color' ) );

        }

        public function register_post_types() {

            $args = array(
                'labels'	=>	array(
                    'all_items'           => 	'Customers',
                    'menu_name'	          =>	'CRM',
                    'singular_name'       =>	'Customer',
                    'edit_item'           =>	'Edit Customer',
                    'add_new_item'        =>	'Add New Customer',
                    'new_item'            =>	'New Customer',
                    'view_item'           =>	'View Customer',
                    'items_archive'       =>	'Customer Archive',
                    'search_items'        =>	'Search Customer',
                    'not_found'	          =>	'No customers found',
                    'not_found_in_trash'  =>	'No customers found in trash'
                ),
                'supports'		=>	array( '' ),
                'menu_position'	=>	5,
                'public'		=>	true,
                'label'         => 'MHM CRM'
              );

              register_post_type( 'mhm_crm', $args );


              $labels = array(
                'name'                       => _x( 'Groups', 'taxonomy general name', 'mhm_crm' ),
                'singular_name'              => _x( 'Group', 'taxonomy singular name', 'mhm_crm' ),
                'search_items'               => __( 'Search Groups', 'mhm_crm' ),
                'popular_items'              => __( 'Popular Groups', 'mhm_crm' ),
                'all_items'                  => __( 'All Groups', 'mhm_crm' ),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __( 'Edit Group', 'mhm_crm' ),
                'update_item'                => __( 'Update Group', 'mhm_crm' ),
                'add_new_item'               => __( 'Add New Group', 'mhm_crm' ),
                'new_item_name'              => __( 'New Group Name', 'mhm_crm' ),
                'separate_items_with_commas' => __( 'Separate Groups with commas', 'mhm_crm' ),
                'add_or_remove_items'        => __( 'Add or remove Groups', 'mhm_crm' ),
                'choose_from_most_used'      => __( 'Choose from the most used Groups', 'mhm_crm' ),
                'not_found'                  => __( 'No Groups found.', 'mhm_crm' ),
                'menu_name'                  => __( 'Groups', 'mhm_crm' ),
            );

            $args = array(
                'labels'                => $labels,
                'hierarchical'          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_admin_column'     => false,
                'show_in_nav_menus'     => true,
                'show_tagcloud'         => true,
                'meta_box_cb'           => false,
                'update_count_callback' => '_update_post_term_count',
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'mhm_crm_group' ),
            );

            register_taxonomy( 'mhm_crm_group', 'mhm_crm', $args );

            $labels = array(
                'name'                       => _x( 'Status', 'taxonomy general name', 'mhm_crm' ),
                'singular_name'              => _x( 'Status', 'taxonomy singular name', 'mhm_crm' ),
                'search_items'               => __( 'Search Status', 'mhm_crm' ),
                'popular_items'              => __( 'Popular Status', 'mhm_crm' ),
                'all_items'                  => __( 'All Status', 'mhm_crm' ),
                'parent_item'                => null,
                'parent_item_colon'          => null,
                'edit_item'                  => __( 'Edit Status', 'mhm_crm' ),
                'update_item'                => __( 'Update Status', 'mhm_crm' ),
                'add_new_item'               => __( 'Add New Status', 'mhm_crm' ),
                'new_item_name'              => __( 'New Status Name', 'mhm_crm' ),
                'separate_items_with_commas' => __( 'Separate Status with commas', 'mhm_crm' ),
                'add_or_remove_items'        => __( 'Add or remove Status', 'mhm_crm' ),
                'choose_from_most_used'      => __( 'Choose from the most used Status', 'mhm_crm' ),
                'not_found'                  => __( 'No Status found.', 'mhm_crm' ),
                'menu_name'                  => __( 'Status', 'mhm_crm' ),
            );

            $args = array(
                'labels'                => $labels,
                'hierarchical'          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_admin_column'     => false,
                'show_in_nav_menus'     => true,
                'show_tagcloud'         => true,
                'meta_box_cb'           => false,
                'update_count_callback' => '_update_post_term_count',
                'query_var'             => true,
                'rewrite'               => array( 'slug' => 'mhm_crm_status' ),
            );

            register_taxonomy( 'mhm_crm_status', 'mhm_crm', $args );

        }

        public function register_customer_meta_boxes() {

            $premade_sections_option = ( array ) get_option( 'mhm_crm_premade_sections' );

            if ( isset( $premade_sections_option['general_details'] ) ) {
                add_meta_box( 'mhm_crm-customer-meta-box', __( 'General Details', 'textdomain' ), array( $this, 'metabox_customer_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['billing_details'] ) ) {
                add_meta_box( 'mhm_crm-billing-details-meta-box', __( 'Billing Details', 'textdomain' ), array( $this, 'metabox_customer_billing_details_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['shipping_details'] ) ) {
                add_meta_box( 'mhm_crm-shipping-details-meta-box', __( 'Shipping Details', 'textdomain' ), array( $this, 'metabox_customer_shipping_details_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['real_estate_zip_codes'] ) ) {
                add_meta_box( 'mhm_crm-shipping-details-meta-box', __( 'Real Estate Zip Codes', 'textdomain' ), array( $this, 'metabox_real_estate_zipcodes_display_callback' ), 'mhm_crm' );
            }

            $user = wp_get_current_user();

            if ( current_user_can( 'administrator' ) || in_array( 'crm_staff', (array) $user->roles ) ) {
                
                add_meta_box( 'mhm_crm-groups-cat-meta-box', __( 'Groups', 'textdomain' ), array( $this, 'metabox_customer_groups_cat_display_callback' ), 'mhm_crm', 'side' );
            
            
                add_meta_box( 'mhm_crm-status-cat-meta-box', __( 'Status', 'textdomain' ), array( $this, 'metabox_customer_status_cat_display_callback' ), 'mhm_crm', 'side' );

            }

            if ( current_user_can( 'administrator' ) ) {

                add_meta_box( 'mhm_crm-assign-admins-meta-box', __( 'Assign Admin', 'textdomain' ), array( $this, 'metabox_customer_assign_admins_display_callback' ), 'mhm_crm', 'side' );

                add_meta_box( 'mhm_crm-assign-crm-admins-meta-box', __( 'Assign CRM Admin', 'textdomain' ), array( $this, 'metabox_customer_assign_crm_admins_display_callback' ), 'mhm_crm', 'side' );

                add_meta_box( 'mhm_crm-assign-crm-staffs-meta-box', __( 'Assign CRM Staff', 'textdomain' ), array( $this, 'metabox_customer_assign_staff_display_callback' ), 'mhm_crm', 'side' );

            }

            add_meta_box( 'mhm_crm-customer-mailchimp-box', __( 'MailChimp', 'textdomain' ), array( $this, 'metabox_customer_mailchimp_display_callback' ), 'mhm_crm', 'side' );

            if ( isset( $premade_sections_option['notes'] ) ) {
                add_meta_box( 'mhm_crm-customer-notes-box', __( 'Notes', 'textdomain' ), array( $this, 'metabox_customer_notes_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['phone'] ) ) {
                add_meta_box( 'mhm_crm-customer-phone-box', __( 'Phone', 'textdomain' ), array( $this, 'metabox_customer_phone_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['email_logs'] ) ) {
                add_meta_box( 'mhm_crm-customer-emaillogs-box', __( 'Email Logs', 'textdomain' ), array( $this, 'metabox_customer_emaillogs_display_callback' ), 'mhm_crm' );
            }

            if ( isset( $premade_sections_option['logs'] ) ) {
                add_meta_box( 'mhm_crm-customer-logs-box', __( 'Logs', 'textdomain' ), array( $this, 'metabox_customer_logs_display_callback' ), 'mhm_crm' );
            }

            $_mhm_crm_record_sections = get_option( '_mhm_crm_record_sections' );

            if ( $_mhm_crm_record_sections != false ) {

                for ( $i = 0; $i < count( $_mhm_crm_record_sections ); $i++ ) {

                    $section = $_mhm_crm_record_sections[$i];
                    $title = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','-', $section['record_title']));
                    
                    add_meta_box( 'mhm_crm-' . $title . '-' . time() . '-box', __( $section['record_title'], 'textdomain' ), array( $this, 'dynamic_metaboxes' ), 'mhm_crm', 'advanced', 'default', array( $i ) );


                }

            }

        }

        public function dynamic_metaboxes( $post, $callback_args ) {

            $mid = array_map( 'esc_html', $callback_args['args'] );
            $mid = $mid[0];
            $_mhm_crm_record_sections = get_option( '_mhm_crm_record_sections' );


            wp_nonce_field( 'mhm_crm_customer_action', 'mhm_crm_customer_fields' );

            ?>

                <div class="bootstrap-iso">
                    <div class="container-fluid">
                        <div class="row">
                            <?php

                                for ( $i = 0; $i < count( $_mhm_crm_record_sections[$mid]['record_section_item_field'] ); $i ++ ) {

                                    $primary_field_name = $_mhm_crm_record_sections[$mid]['record_section_item_field'][$i];
                                    $field_name = strtolower(preg_replace('/[^a-zA-Z0-9-_\.]/','-', trim($primary_field_name))) . '-dynamicmetabox-' . $mid[0];

                                    $field_cat = $_mhm_crm_record_sections[$mid]['record_section_item_field_cat'][$i];
                            ?>
                                    <div class="col-md-3">
                                        <p class="form-field first_name_field ">
                                            <label for="first_name"><?php echo $primary_field_name; ?></label>
                                            
                                            <?php if ( $field_cat == 'textbox'): ?>
                                                <input type="text" class="short form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>" value="<?php echo get_post_meta( $post->ID, '_mhmcrm_' . $field_name, true ); ?>">
                                            <?php elseif ( $field_cat == 'textarea' ): ?>
                                                <textarea class="short form-control" name="<?php echo $field_name; ?>" id="<?php echo $field_name; ?>"><?php echo get_post_meta( $post->ID, '_mhmcrm_' . $field_name, true ); ?></textarea>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                            <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>

            <?php

        }

        public function metabox_customer_mailchimp_display_callback( $post_id ) {

            global $post;

            $post_id = $post->ID;

            $email = get_post_meta( $post_id, '_mhmcrm_user_email', true );

            $mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );
            $lists = json_decode( $mailchimp->getLists() );

            if ( ! empty( $lists->lists ) ) {

                $activatedLists = get_option('_mhm_crm_mailchimp_activated_lists');

                if ( $activatedLists === false ) {
                    $activatedLists = array();
                }

                    foreach ( $lists->lists as $list ) {

                        $status = json_decode( $mailchimp->subscriptionStatus( $list->id, $email ) );

                        if ( $status->status == 'subscribed' ) {
                            $selected = "checked";
                        } else {
                            $selected = "";
                        }

                        if ( ! in_array( $list->id, $activatedLists ) ) {
                            continue;
                        }

                        echo '<input name="mhmcrm_mailchimp_lists[]" type="checkbox" ' . $selected . ' value="' . $list->id . '">' . $list->name . '<br />';
                    }


            }

        }

        public function metabox_customer_emaillogs_display_callback() {

            global $post;

            $post_id = $post->ID;

            $email = get_post_meta( $post_id, '_mhmcrm_user_email', true );

            $_mhm_crm_webmail = get_option( '_mhm_crm_webmail_accounts' );

			$_mhm_crm_webmails = explode( PHP_EOL, $_mhm_crm_webmail );

            ?>
            <div class="bootstrap-iso">
            <table id="emailLogsMHMCRM" class="display" style="width:100%">
        <thead>
            <tr>
                <th>From</th>
                <th>To</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </thead>
            <?php
            if ( is_array( $_mhm_crm_webmails ) && ! empty( $_mhm_crm_webmails ) ) {

                $emailMessages = get_post_meta( $post_id, '_mhm_crm_webmail_messages', true );

                if ( $emailMessages == null ) {
                    $emailMessages = array();
                }

                $looped = false;

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

                                if ( $toEmailAdd == $email ) {

                                    if ( ! in_array( $email_number, $emailMessages ) ) {
                                        $emailMessages[ $email_number ] = array(
                                            'from'  => $emailAdd,
                                            'to'    => $toEmailAdd,
                                            'date'  => $date,
                                            'message' => $message
                                        );

                                        $looped = true;
                                    }

                                    //imap_delete( $inbox, $email_number );

                                }

                            }// end foreach loop

                        }



                }

                if ( $looped ) {
                    update_post_meta( $post_id, '_mhm_crm_webmail_messages', $emailMessages );
                }

                $emailMessages = get_post_meta( $post_id, '_mhm_crm_webmail_messages', true );

                if ( ! empty( $emailMessages ) ) {

                                    ?>
<style>.modal-dialog{
    overflow-y: initial !important
}
.modal-body{
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}</style>
        <tbody>
                    <?php
                        foreach ( $emailMessages as $key => $mail ) {
                    ?>
                            <tr>
                                <td><?php echo $mail['from']; ?></td>
                                <td><?php echo $mail['to']; ?></td>
                                <td><?php
                                    if (strlen($mail['message']) > 10) {
                                        echo substr( $mail['message'], 0, 10 ) . '...';
                                        echo ' <a data-toggle="modal" data-target="#myModal' . $key . '">Show More</a>';
                                    } else {
                                        echo $mail['message'];
                                    }
                                ?></td>
                                <td><?php echo $mail['date']; ?></td>
                            </tr>
                            <div id="myModal<?php echo $key; ?>" class="modal fade" role="dialog">
                                <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title">Message</h4>
                                    </div>
                                    <div class="modal-body">
                                        <p><?php echo $mail['message']; ?></p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>

                                </div>
                            </div>
                    <?php
                        }
                    ?>
        </tbody>

                                    <?php
                }

            }
            ?>
            <tfoot>
            <tr>
            <th>From</th>
                <th>To</th>
                <th>Message</th>
                <th>Date</th>
            </tr>
        </tfoot>
    </table>
        </div>
            <?php

        }

        public function metabox_customer_display_callback() {

            global $post;

            $post_id = $post->ID;

            wp_nonce_field( 'mhm_crm_customer_action', 'mhm_crm_customer_fields' );

            include_once PLUGIN_PATH . 'admin/meta/customer.php';

        }

        public function metabox_customer_billing_details_display_callback() {

            global $post;

            $post_id = $post->ID;

            wp_nonce_field( 'mhm_crm_customer_action', 'mhm_crm_customer_fields' );

            include_once PLUGIN_PATH . 'admin/meta/billing-details.php';

        }

        public function metabox_customer_shipping_details_display_callback() {

            global $post;

            $post_id = $post->ID;

            wp_nonce_field( 'mhm_crm_customer_action', 'mhm_crm_customer_fields' );

            include_once PLUGIN_PATH . 'admin/meta/shipping-details.php';

        }

        public function metabox_real_estate_zipcodes_display_callback() {
            global $post;

            $post_id = $post->ID;

            ob_start();
            wp_nonce_field( 'mhm_crm_realestatezipcodes_action', 'mhm_crm_realestatezipcodes_fields' );

            include_once PLUGIN_PATH . 'admin/meta/real_estate_zipcodes.php';

            $content = ob_get_clean();

            echo $content;
        }

        public function metabox_customer_groups_cat_display_callback() {

            global $post;

            $post_id = $post->ID;

            $terms = get_terms( 'mhm_crm_group', array(
                'hide_empty' => false,
            ) );

            $postTerms = wp_get_post_terms( $post_id, 'mhm_crm_group', array('fields' => 'ids') );

            $groups = get_post_meta( $post_id, '_mhmcrm__customer_groups', true );

            ?>

                <p class="form-field">

                    <select name="_mhmcrm__customer_groups[]" multiple class="mhm_crm_assign_staff" style="width: 100%;">

                        <?php foreach ( $terms as $term ) : ?>

                            <?php

                                if ( in_array( $term->term_id, $postTerms ) ) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                            ?>

                            <option <?php echo $selected; ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>

                        <?php endforeach; ?>

                    </select>

                </p>

            <?php
        }

        public function metabox_customer_status_cat_display_callback() {

            $terms = get_terms( 'mhm_crm_status', array(
                'hide_empty' => false,
            ) );

            global $post;

            $post_id = $post->ID;

            $postTerms = wp_get_post_terms( $post_id, 'mhm_crm_status', array('fields' => 'ids') );

            ?>
                <p class="form-field">

                    <select name="_mhmcrm__customer_status">

                        <option value="">--- Status ---</option>

                        <?php foreach ( $terms as $term ) : ?>

                            <?php

                                $status = get_post_meta( $post_id, '_mhmcrm__customer_status', true );

                                if ( in_array( $term->term_id, $postTerms ) ) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                            ?>

                            <option <?php echo $selected; ?> value="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></option>

                        <?php endforeach; ?>

                    </select>

                </p>

            <?php

        }

        public function metabox_customer_assign_staff_display_callback() {

            global $post, $user;

            $post_id = $post->ID;

        $users = get_users( array(
                'role__in'     => array('crm_staff'),
            ) );

  
        $users_selected = get_post_meta( $post_id, '_mhm_crm__assign_staff', true );


            ?>
                <p class="form-field">

                    <select name="_mhmcrm__customer_assign_crm_staffs[]" multiple class="mhm_crm_assign_staff" style="width: 100%;">

                        <option value="">--- CRM Staffs ---</option>

                        <?php foreach ( $users as $term ) : ?>

                            <?php
                                if ( in_array( $term->data->user_email, $users_selected ) ) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                            ?>

                            <option <?php echo $selected; ?> value="<?php echo $term->data->user_email; ?>"><?php echo $term->data->user_email; ?></option>

                        <?php endforeach; ?>

                    </select>

                </p>

            <?php

        }

        public function metabox_customer_assign_crm_admins_display_callback() {

            global $post, $user;

            $post_id = $post->ID;

        $users = get_users( array(
                'role__in'     => array('crm_admin'),
            ) );

  
        $users_selected = get_post_meta( $post_id, '_mhm_crm__assign_crm_admins', true );


            ?>
                <p class="form-field">

                    <select name="_mhmcrm__customer_assign_crm_admins[]" multiple class="mhm_crm_assign_staff" style="width: 100%;">

                        <option value="">--- CRM Admins ---</option>

                        <?php foreach ( $users as $term ) : ?>

                            <?php
                                if ( in_array( $term->data->user_email, $users_selected ) ) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                            ?>

                            <option <?php echo $selected; ?> value="<?php echo $term->data->user_email; ?>"><?php echo $term->data->user_email; ?></option>

                        <?php endforeach; ?>

                    </select>

                </p>

            <?php

        }

        public function metabox_customer_assign_admins_display_callback() {

            global $post, $user;

            $post_id = $post->ID;

        $users = get_users( array(
                'role__in'     => array('administrator'),
            ) );

  
        $users_selected = get_post_meta( $post_id, '_mhm_crm__assign_admins', true );


            ?>
                <p class="form-field">

                    <select name="_mhmcrm__customer_assign_admins[]" multiple class="mhm_crm_assign_staff" style="width: 100%;">

                        <option value="">--- Administrators ---</option>

                        <?php foreach ( $users as $term ) : ?>

                            <?php
                                if ( in_array( $term->data->user_email, $users_selected ) ) {
                                    $selected = "selected";
                                } else {
                                    $selected = "";
                                }

                            ?>

                            <option <?php echo $selected; ?> value="<?php echo $term->data->user_email; ?>"><?php echo $term->data->user_email; ?></option>

                        <?php endforeach; ?>

                    </select>

                </p>

            <?php

        }

        public function metabox_customer_notes_display_callback() {

            global $post;

            $post_id = $post->ID;

            $notes = get_post_meta( $post_id, '_mhm_crm__notes', true );

            ?>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Note</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $i = 1;

                            if ( $notes != null ) {
                                foreach ( $notes as $note ) {
                        ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d h:i:s a', strtotime($note['time'])); ?><input type="datetime-local" style="display: none;" name="_mhm_crm__notes_time[<?php echo $i; ?>]" value="<?php echo $note['time']; ?>"></td>
                                        <td><?php echo $note['note']; ?><textarea style="width: 100%; display: none;" name="_mhm_crm__notes_text[<?php echo $i; ?>]"><?php echo $note['note']; ?></textarea></td>
                                    </tr>
                        <?php
                                    $i++;
                                }
                            }
                        ?>

                        <tr>
                            <td><input type="datetime-local" style="" name="_mhm_crm__notes_time[0]" class="_mhm_crm__notes_time" value="" placeholder=""></td>
                            <td>
                                <textarea style="width: 100%;" name="_mhm_crm__notes_text[0]"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php

        }

        public function metabox_customer_phone_display_callback() {

            global $post;

            $post_id = $post->ID;

            $notes = get_post_meta( $post_id, '_mhm_crm__phones', true );

            ?>
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Note</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $i = 1;

                            if ( $notes != null ) {
                                foreach ( $notes as $note ) {
                        ?>
                                    <tr>
                                        <td><?php echo date('Y-m-d h:i:s a', strtotime($note['time'])); ?><input type="datetime-local" style="display: none;" name="_mhm_crm__phone_time[<?php echo $i; ?>]" value="<?php echo $note['time']; ?>"></td>
                                        <td><?php echo $note['note']; ?><textarea style="width: 100%; display: none;" name="_mhm_crm__phone_text[<?php echo $i; ?>]"><?php echo $note['note']; ?></textarea></td>
                                    </tr>
                        <?php
                                    $i++;
                                }
                            }
                        ?>

                        <tr>
                            <td><input type="datetime-local" style="" name="_mhm_crm__phone_time[0]" class="_mhm_crm__phone_time" value="" placeholder=""></td>
                            <td>
                                <textarea style="width: 100%;" name="_mhm_crm__phone_text[0]"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php

        }

        public function save_postdata( $post_id ) {

            global $post;

            if (
                isset( $_POST['mhm_crm_customer_fields'] )
                && wp_verify_nonce( $_POST['mhm_crm_customer_fields'], 'mhm_crm_customer_action' )
            ) {

                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                    return;
                }

                if ( ! current_user_can( 'edit_post', $post_id ) ) {
                    return;
                }

                $user_id = get_current_user_id();

                $_SESSION['MHM_CRM_MAILCHIMP_MSGS'] = "";

                if ( isset( $_POST['mhmcrm_mailchimp_lists'] ) && ! empty( $_POST['mhmcrm_mailchimp_lists'] ) ) {

                    $mailchimp = new Madhatmedia_Crm_MailChimp( get_option( '_mhm_crm_mailchimp_api_key' ) );
                    $email = get_post_meta( $post_id, '_mhmcrm_user_email', true );
                    $fname = get_post_meta( $post_id, '_mhmcrm_first_name', true );
                    $lname = get_post_meta( $post_id, '_mhmcrm_last_name', true );

                    $mailchimpLists = json_decode( $mailchimp->getLists() );



                    if ( ! empty( $mailchimpLists->lists ) ) {

                        foreach ( $mailchimpLists->lists as $list ) {

                            if ( ! in_array( $list->id, $_POST['mhmcrm_mailchimp_lists'] ) ) {

                                $response = $mailchimp->unsubscribeToCampaign( $list->id, $email );

                            }

                        }

                    }



                    foreach ( $_POST['mhmcrm_mailchimp_lists'] as $listID ) {

                        $response = json_decode( $mailchimp->subscribeToCampaign( $listID, $email, $fname, $lname ) );

                        $details = json_decode( $mailchimp->getListDetails( $listID ) );
                        $listName = $details->name;

                        if ( $response->status == 400 ) {

                            $_SESSION['MHM_CRM_MAILCHIMP_MSGS'] .= "<p>" . $listName . " - " . $response->detail . "</p>";

                        }
                    }

                }

                foreach ( $_POST as $key => $value ) {

                    if ( $key == '_mhm_crm__notes_time' ) {
                        continue;
                    }

                    if ( $key == '_mhm_crm__notes_text' ) {
                        continue;
                    }

                    if ( $key == '_mhm_crm__phone_time' ) {
                        continue;
                    }

                    if ( $key == '_mhmcrm__customer_assign_staff' ) {
                        continue;
                    }

                    if ( $key == '_mhm_crm__phone_text' ) {
                        continue;
                    }

                    if ( ! add_post_meta( $post_id, '_mhmcrm_' . $key, $value, true ) ) {

                        update_post_meta( $post_id, '_mhmcrm_' . $key, $value );

                    }

                }

                if ( ( isset( $_POST['_mhm_crm__notes_time'] ) && ! empty( $_POST['_mhm_crm__notes_time'] ) ) && ( isset( $_POST['_mhm_crm__notes_text'] ) && ! empty( $_POST['_mhm_crm__notes_text'] ) ) ) {

                    $notes = get_post_meta( $post_id, '_mhm_crm__notes', true );

                    delete_post_meta( $post_id, '_mhm_crm__notes' );

                    $notes_data = array();

                    for ( $i = 0; $i < count( $_POST['_mhm_crm__notes_time'] ); $i++ ) {

                        $time = $_POST['_mhm_crm__notes_time'][$i];
                        $note = $_POST['_mhm_crm__notes_text'][$i];

                        $data = array(
                            'time'      => $time,
                            'note'      => $note,
                            'user_id'   => $user_id
                        );

                        $notes_data[] = $data;

                    }
                    //echo '<pre>'; print_r( $_POST['_mhm_crm__notes_time'] ); print_r( $notes_data ); exit;
                    if ( ! add_post_meta( $post_id, '_mhm_crm__notes', $notes_data, true ) ) {

                        update_post_meta( $post_id, '_mhm_crm__notes', $notes_data );

                    }

                }


                if ( ( isset( $_POST['_mhm_crm__phone_time'] ) && ! empty( $_POST['_mhm_crm__phone_time'] ) ) && ( isset( $_POST['_mhm_crm__phone_text'] ) && ! empty( $_POST['_mhm_crm__phone_text'] ) ) ) {

                    $notes = get_post_meta( $post_id, '_mhm_crm__phones', true );

                    delete_post_meta( $post_id, '_mhm_crm__phones' );

                    $notes_data = array();

                    for ( $i = 0; $i < count( $_POST['_mhm_crm__phone_time'] ); $i++ ) {

                        $time = $_POST['_mhm_crm__phone_time'][$i];
                        $note = $_POST['_mhm_crm__phone_text'][$i];

                        $data = array(
                            'time'      => $time,
                            'note'      => $note,
                            'user_id'   => $user_id
                        );

                        $notes_data[] = $data;

                    }
                    //echo '<pre>'; print_r( $_POST['_mhm_crm__notes_time'] ); print_r( $notes_data ); exit;
                    if ( ! add_post_meta( $post_id, '_mhm_crm__phones', $notes_data, true ) ) {

                        update_post_meta( $post_id, '_mhm_crm__phones', $notes_data );

                    }

                }

                if ( isset( $_POST['_mhmcrm__customer_groups'] ) && ! empty( $_POST['_mhmcrm__customer_groups'] ) ) {

                    //wp_set_post_categories( $post_id, $_POST['_mhmcrm__customer_groups'] );
                    $cat_ids = array_map( 'intval', $_POST['_mhmcrm__customer_groups'] );
                    $cat_ids = array_unique( $cat_ids );

                    wp_set_object_terms( $post_id, $cat_ids, 'mhm_crm_group' );

                }

                if ( isset( $_POST['_mhmcrm__customer_status'] ) && ! empty( $_POST['_mhmcrm__customer_status'] ) ) {

                    $cat_ids = array_map( 'intval', ( array ) $_POST['_mhmcrm__customer_status'] );
                    $cat_ids = array_unique( $cat_ids );

                    wp_set_object_terms( $post_id, $cat_ids, 'mhm_crm_status' );

                }

                if ( isset( $_POST['_mhmcrm__customer_assign_crm_staffs'] ) && ! empty( $_POST['_mhmcrm__customer_assign_crm_staffs'] ) ) {

                    $staff = array_map( 'strval', ( array ) $_POST['_mhmcrm__customer_assign_crm_staffs'] );
                    update_post_meta( $post_id, '_mhm_crm__assign_staff', $staff );

                    Madhatmedia_Crm_Admin::assignee_trigger_email( 'customer', '_mhm_crm__assign_staff', $staff, $post_id );

                } else {
                    update_post_meta( $post_id, '_mhm_crm__assign_staff', array() );
                }

                if ( isset( $_POST['_mhmcrm__customer_assign_crm_admins'] ) && ! empty( $_POST['_mhmcrm__customer_assign_crm_admins'] ) ) {

                    $staff = array_map( 'strval', ( array ) $_POST['_mhmcrm__customer_assign_crm_admins'] );
                    update_post_meta( $post_id, '_mhm_crm__assign_crm_admins', $staff );

                    Madhatmedia_Crm_Admin::assignee_trigger_email( 'customer', '_mhm_crm__assign_crm_admins', $staff, $post_id );

                } else {
                    update_post_meta( $post_id, '_mhm_crm__assign_crm_admins', array() );
                }

                if ( isset( $_POST['_mhmcrm__customer_assign_admins'] ) && ! empty( $_POST['_mhmcrm__customer_assign_admins'] ) ) {

                    $staff = array_map( 'strval', ( array ) $_POST['_mhmcrm__customer_assign_admins'] );

                    Madhatmedia_Crm_Admin::assignee_trigger_email( 'customer', '_mhm_crm__assign_admins', $staff, $post_id );

                    update_post_meta( $post_id, '_mhm_crm__assign_admins', $staff );

                } else {
                    update_post_meta( $post_id, '_mhm_crm__assign_admins', array() );
                }

                $log = date( 'Y-m-d H:i:s', time() ) . " - Profile Updated. ";

                $logs = get_post_meta( $post_id, '_mhm_crm__logs', true );

                if ( $logs == null ) {
                    $logs = array();
                }

                $logs[] = $log;


                if ( ! add_post_meta( $post_id, '_mhm_crm__logs', $logs, true ) ) {

                    update_post_meta( $post_id, '_mhm_crm__logs', $logs );

                }

                if ( $post->post_type == 'mhm_crm' ) {

                    remove_action( 'save_post', array( $this, 'save_postdata' ) );

                    $first_name = $_POST['first_name'];
                    $last_name = $_POST['last_name'];
                    $title = $first_name . ' ' . $last_name;

                    wp_update_post( array( 'ID' => $post_id, 'post_title' => $title ) );

                }


            }

        }

        public function metabox_customer_logs_display_callback() {

            global $post;

            $post_id = $post->ID;

            $user_id = get_current_user_id();

            $logs = get_post_meta( $post_id, '_mhm_crm__logs', true );

            if ( $logs != null ) {

                foreach ( $logs as $log ) {
                    echo $log . '<br />';
                }

            }

        }

        public function mhm_crm_status_add_form_fields( $term ) {

            $admins = get_users( array(
                'role__in'     => array( 'administrator' ),
            ) );

            $crm_admins = get_users( array(
                'role__in'     => array( 'crm_admin' ),
            ) );

            $crm_staffs = get_users( array(
                'role__in'     => array( 'crm_staff' ),
            ) );
            
            ?>
                <div class="form-field">
                    <label>Color</label>

                    <input type="color" name="mhm_crm_status_color" value="#ffffff">
                </div>

            <?php

                $this->render_assigned_to_html_div();

        }

        public function mhm_crm_status_edit_form_fields( $term ) {


            $t_id = $term->term_id;

            $term_color = get_term_meta( $t_id, 'mhm_crm_status_color', true );

            ?>
                <tr class="form-field">
                    <th>
                        <label>Color</label>
                    </th>

                    <td><input type="color" name="mhm_crm_status_color" value="<?php echo $term_color; ?>"></td>
                </tr>

            <?php

                $this->render_assigned_to_html_table( $t_id );

        }

        public function mhm_crm_status_save_color( $term_id ) {

            if ( isset( $_POST['mhm_crm_status_color'] ) ) {

                update_term_meta( $term_id, 'mhm_crm_status_color', $_POST['mhm_crm_status_color'] );

            }

            if ( isset( $_POST['mhm_crm_assign_to_admins'] ) ) {
                
                $_POST['mhm_crm_assign_to_admins'] == '' ? array() : $_POST['mhm_crm_assign_to_admins'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'status', 'mhm_crm_assign_to_admins', $_POST['mhm_crm_assign_to_admins'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_admins', $_POST['mhm_crm_assign_to_admins'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_admins', array() );
            }

            if ( isset( $_POST['mhm_crm_assign_to_crm_admins'] ) ) {

                $_POST['mhm_crm_assign_to_crm_admins'] == '' ? array() : $_POST['mhm_crm_assign_to_crm_admins'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'status', 'mhm_crm_assign_to_crm_admins', $_POST['mhm_crm_assign_to_crm_admins'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_admins', $_POST['mhm_crm_assign_to_crm_admins'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_admins', array() );
            }

            if ( isset( $_POST['mhm_crm_assign_to_crm_staffs'] ) ) {

                $_POST['mhm_crm_assign_to_crm_staffs'] == '' ? array() : $_POST['mhm_crm_assign_to_crm_staffs'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'status', 'mhm_crm_assign_to_crm_staffs', $_POST['mhm_crm_assign_to_crm_staffs'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_staffs', $_POST['mhm_crm_assign_to_crm_staffs'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_staffs', array() );
            }

        }

        public function mhm_crm_group_add_form_fields( $term ) {

            ?>
                <div class="form-field">
                    <label>Color</label>

                    <input type="color" name="mhm_crm_group_color" value="#ffffff">
                </div>
            <?php

            $this->render_assigned_to_html_div();

        }

        public function mhm_crm_group_edit_form_fields( $term ) {

            $t_id = $term->term_id;

            $term_color = get_term_meta( $t_id, 'mhm_crm_group_color', true );

            ?>
                <tr class="form-field">
                    <th>
                        <label>Color</label>
                    </th>

                    <td><input type="color" name="mhm_crm_group_color" value="<?php echo $term_color; ?>"></td>
                </tr>
            <?php

            $this->render_assigned_to_html_table( $t_id );

        }

        public function mhm_crm_group_save_color( $term_id ) {

            if ( isset( $_POST['mhm_crm_group_color'] ) ) {

                update_term_meta( $term_id, 'mhm_crm_group_color', $_POST['mhm_crm_group_color'] );

            }
            
            if ( isset( $_POST['mhm_crm_assign_to_admins'] ) ) {
                
                $_POST['mhm_crm_assign_to_admins'] == '' ? array() : $_POST['mhm_crm_assign_to_admins'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'groups', 'mhm_crm_assign_to_admins', $_POST['mhm_crm_assign_to_admins'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_admins', $_POST['mhm_crm_assign_to_admins'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_admins', array() );
            }

            if ( isset( $_POST['mhm_crm_assign_to_crm_admins'] ) ) {

                $_POST['mhm_crm_assign_to_crm_admins'] == '' ? array() : $_POST['mhm_crm_assign_to_crm_admins'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'groups', 'mhm_crm_assign_to_crm_admins', $_POST['mhm_crm_assign_to_crm_admins'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_admins', $_POST['mhm_crm_assign_to_crm_admins'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_admins', array() );
            }

            if ( isset( $_POST['mhm_crm_assign_to_crm_staffs'] ) ) {

                $_POST['mhm_crm_assign_to_crm_staffs'] == '' ? array() : $_POST['mhm_crm_assign_to_crm_staffs'];

                Madhatmedia_Crm_Admin::assignee_trigger_email( 'groups', 'mhm_crm_assign_to_crm_staffs', $_POST['mhm_crm_assign_to_crm_staffs'], $term_id );

                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_staffs', $_POST['mhm_crm_assign_to_crm_staffs'] );
            } else {
                update_term_meta( $term_id, 'mhm_crm_assign_to_crm_staffs', array() );
            }

        }

        public function render_assigned_to_html_div() {

            $admins = get_users( array(
                'role__in'     => array( 'administrator' ),
            ) );

            $crm_admins = get_users( array(
                'role__in'     => array( 'crm_admin' ),
            ) );

            $crm_staffs = get_users( array(
                'role__in'     => array( 'crm_staff' ),
            ) );

            ?>

                <div class="form-field">
                    <label>Assign to Admin</label>

                    <select class="select-status-groups" name="mhm_crm_assign_to_admins[]" style="width: 100%;" multiple>
                        <option value="all">All</option>
                        <?php foreach ( $admins as $admin ): ?>
                            <option value="<?php echo $admin->data->user_email; ?>"><?php echo $admin->data->user_email; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Assign to CRM Admin</label>

                    <select class="select-status-groups" name="mhm_crm_assign_to_crm_admins[]" style="width: 100%;" multiple>
                    <option value="all">All</option>
                        <?php foreach ( $crm_admins as $crm_admin ): ?>
                            <option value="<?php echo $crm_admin->data->user_email; ?>"><?php echo $crm_admin->data->user_email; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Assign to CRM Staff</label>

                    <select class="select-status-groups" name="mhm_crm_assign_to_crm_staffs[]" style="width: 100%;" multiple>
                        <option value="all">All</option>
                        <?php foreach ( $crm_staffs as $crm_staff ): ?>
                            <option value="<?php echo $crm_staff->data->user_email; ?>"><?php echo $crm_staff->data->user_email; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <script>
                    jQuery(document).ready(function($) {
                        $('.select-status-groups').select2();
                    });
                </script>

            <?php

        }

        public function render_assigned_to_html_table( $t_id ) {

            $admins = get_users( array(
                'role__in'     => array( 'administrator' ),
            ) );

            $crm_admins = get_users( array(
                'role__in'     => array( 'crm_admin' ),
            ) );

            $crm_staffs = get_users( array(
                'role__in'     => array( 'crm_staff' ),
            ) );

            

            $admins_list = get_term_meta( $t_id, 'mhm_crm_assign_to_admins', true ) == '' ? array() : get_term_meta( $t_id, 'mhm_crm_assign_to_admins', true );

            $crm_admins_list = get_term_meta( $t_id, 'mhm_crm_assign_to_crm_admins', true ) == '' ? array() : get_term_meta( $t_id, 'mhm_crm_assign_to_crm_admins', true );

            $crm_staffs_list = get_term_meta( $t_id, 'mhm_crm_assign_to_crm_staffs', true ) == '' ? array() : get_term_meta( $t_id, 'mhm_crm_assign_to_crm_staffs', true );
            
            ?>
                <tr class="form-field">
                    <th><label>Assign to Admin</label></th>

                    <td>
                        <select class="select-status-groups" name="mhm_crm_assign_to_admins[]" style="width: 100%;" multiple>
                            <option value="all" <?php echo in_array( 'all', $admins_list) ? 'selected' : ''; ?>>All</option>
                            <?php foreach ( $admins as $admin ): ?>
                                <option <?php echo in_array( $admin->data->user_email, $admins_list) ? 'selected' : ''; ?> value="<?php echo $admin->data->user_email; ?>"><?php echo $admin->data->user_email; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr class="form-field">
                    <th><label>Assign to CRM Admin</label></th>

                    <td>
                        <select class="select-status-groups" name="mhm_crm_assign_to_crm_admins[]" style="width: 100%;" multiple>
                            <option value="all" <?php echo in_array( 'all', $crm_admins_list) ? 'selected' : ''; ?>>All</option>
                            <?php foreach ( $crm_admins as $crm_admin ): ?>
                                <option <?php echo in_array( $crm_admin->data->user_email, $crm_admins_list) ? 'selected' : ''; ?> value="<?php echo $crm_admin->data->user_email; ?>"><?php echo $crm_admin->data->user_email; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr class="form-field">
                    <th><label>Assign to CRM Staff</label></th>

                    <td>
                        <select class="select-status-groups" name="mhm_crm_assign_to_crm_staffs[]" style="width: 100%;" multiple>
                            <option value="all" <?php echo in_array( 'all', $crm_staffs_list) ? 'selected' : ''; ?>>All</option>
                            <?php foreach ( $crm_staffs as $crm_staff ): ?>
                                <option <?php echo in_array( $crm_staff->data->user_email, $crm_staffs_list) ? 'selected' : ''; ?> value="<?php echo $crm_staff->data->user_email; ?>"><?php echo $crm_staff->data->user_email; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <script>
                    jQuery(document).ready(function($) {
                        $('.select-status-groups').select2();
                    });
                </script>

            <?php

        }

    }

    new MHM_CRM_Admin_Post_Types();

endif;