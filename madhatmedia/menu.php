<?php

if ( ! function_exists( 'mhm_register_madhatmedia_crm_menu_page' ) ) {

    function mhm_register_madhatmedia_crm_menu_page() {

        add_submenu_page( 
            'mhm-madhatmedia-plugin-management', 
            'MadHatMedia CRM', 
            'MadHatMedia CRM',
            'manage_options', 
            'mhm-madhatmedia-madhatmedia-crm', 
            'mhm_madhatmedia_crm_menu_page'
        );

    }

}

if ( ! function_exists( 'mhm_madhatmedia_crm_menu_page' ) ) {

    function mhm_madhatmedia_crm_menu_page() {

        $email = get_option( 'madmatmedia_license_email-madhatmedia-crm' );
        $license_key = get_option( 'madmatmedia_license_key-madhatmedia-crm' );
        
        $product_id = 'madhatmedia-crm';

        $data = file_get_contents( MADHATMEDIA_WEBSITE_URL . '/woocommerce/?wc-api=software-api&request=check&email=' . $email . '&license_key=' . $license_key . '&product_id=' . $product_id );

        $data = json_decode( $data );

        if ( $data->success && ( isset( $data->activations ) && count( $data->activations ) > 0 ) ) {
            $activated = 1;
        } else if ( $data->success && ( isset( $data->activations ) && count( $data->activations ) == 0 ) ) {
            $activated = 2;
        } else {
            $activated = 3;
        }

        if ( $activated == 1 ) {
        
        ?>
            <div class="updated">
                <p>License is activated</p>
            </div>
        <?php

        } else if ( $activated == 2 ) {
        
        ?>

            <div class="updated">
                <p>License is deactivated</p>
            </div>

        <?php

        } else {

        ?>

            <div class="updated">
                <p>License is not valid</p>
            </div>

        <?php

        }

        ?>

            <div class="wrap">
                <h1 class="wp-heading-inline">License</h1>
                <form action="" method="post">
                    <table class="form-table">
                        <tbody>
                            <tr class="form-field form-required">
                                <th scope="row"><label for="email">Email <span class="description">(required)</span></label></th>
                                <td><input name="email" type="email" id="email" required value="<?php echo sanitize_email( $email ); ?>" autocomplete="off"></td>
                            </tr>
                            <tr class="form-field form-required">
                                <th scope="row"><label for="license_key">License Key <span class="description">(required)</span></label></th>
                                <td><input name="license_key" type="text" id="license_key" required value="<?php echo sanitize_text_field( $license_key ); ?>" autocomplete="off"></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                    <?php 

                        if ( $activated == 1 ) {

                    ?>
                            <input type="submit" name="madhatmedia_deactivate_license" class="button button-primary" value="DEACTIVATE">
                    <?php

                        } else if ( $activated == 2 ) {

                    ?>

                            <input type="submit" name="madhatmedia_activate_license" class="button button-primary" value="ACTIVATE">

                    <?php

                        } else {
                    
                    ?>
                            <input type="submit" name="madhatmedia_activate_license" class="button button-primary" value="ACTIVATE">
                    <?php

                        }

                    ?>
                    </p>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                </form>
            </div>

        <?php

    }

}


add_action( 'admin_menu', 'mhm_register_madhatmedia_crm_menu_page', 20 );