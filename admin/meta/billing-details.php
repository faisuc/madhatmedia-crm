<div class="bootstrap-iso">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <p class="form-field _billing_first_name_field ">
		            <label for="_billing_first_name">First Name</label><input type="text" class="short form-control" style="" name="_billing_first_name" id="_billing_first_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_first_name', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_last_name_field ">
		            <label for="_billing_last_name">Last Name</label><input type="text" class="short form-control" style="" name="_billing_last_name" id="_billing_last_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_last_name', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_company_field ">
		            <label for="_billing_company">Company</label><input type="text" class="short form-control" style="" name="_billing_company" id="_billing_company" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_company', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_address_1_field ">
		            <label for="_billing_address_1">Address 1</label><input type="text" class="short form-control" style="" name="_billing_address_1" id="_billing_address_1" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_address_1', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_address_2_field ">
		            <label for="_billing_address_2">Address 2</label><input type="text" class="short form-control" style="" name="_billing_address_2" id="_billing_address_2" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_address_2', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_city_field ">
		            <label for="_billing_city">City</label><input type="text" class="short form-control" style="" name="_billing_city" id="_billing_city" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_city', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_postcode_field ">
		            <label for="_billing_postcode">Postcode</label><input type="text" class="short form-control" style="" name="_billing_postcode" id="_billing_postcode" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_postcode', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class=" form-field _billing_country_field">
                    <label for="_billing_country">Country</label>
                    <select style="" id="_billing_country" name="_billing_country"  tabindex="-1" aria-hidden="true" class="form-control">

                            <option value="">--- Country ---</option>

                            <?php
                                foreach ( Madhatmedia_Crm_Admin::countries() as $key => $value ) :

                                    if ( get_post_meta( $post_id, '_mhmcrm__billing_country', true ) == $key ) {
                                        $selected = "selected";
                                    } else {
                                        $selected = "";
                                    }
                            ?>

                                    <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>

                            <?php
                                endforeach;
                            ?>

                    </select>
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_state_field ">
		            <label for="_billing_state">State/County</label><input type="text" class="js_field-state form-control" name="_billing_state" id="_billing_state" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_state', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_email_field ">
		            <label for="_billing_email">Email</label><input type="text" class="short form-control" style="" name="_billing_email" id="_billing_email" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_email', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _billing_phone_field ">
		            <label for="_billing_phone">Phone</label><input type="text" class="short form-control" style="" name="_billing_phone" id="_billing_phone" value="<?php echo get_post_meta( $post_id, '_mhmcrm__billing_phone', true ); ?>" placeholder="">
                </p>
            </div>
        </div>
    </div>
</div>