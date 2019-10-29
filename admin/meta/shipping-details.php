<div class="bootstrap-iso">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <p class="form-field _shipping_first_name_field ">
		            <label for="_shipping_first_name">First Name</label><input type="text" class="short form-control" style="" name="_shipping_first_name" id="_shipping_first_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_first_name', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_last_name_field ">
		            <label for="_shipping_last_name">Last Name</label><input type="text" class="short form-control" style="" name="_shipping_last_name" id="_shipping_last_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_last_name', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_company_field ">
		            <label for="_shipping_company">Company</label><input type="text" class="short form-control" style="" name="_shipping_company" id="_shipping_company" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_company', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_address_1_field ">
		            <label for="_shipping_address_1">Address 1</label><input type="text" class="short form-control" style="" name="_shipping_address_1" id="_shipping_address_1" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_address_1', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_address_2_field ">
		            <label for="_shipping_address_2">Address 2</label><input type="text" class="short form-control" style="" name="_shipping_address_2" id="_shipping_address_2" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_address_2', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_city_field ">
		            <label for="_shipping_city">City</label><input type="text" class="short form-control" style="" name="_shipping_city" id="_shipping_city" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_city', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field _shipping_postcode_field ">
		            <label for="_shipping_postcode">Postcode</label><input type="text" class="short form-control" style="" name="_shipping_postcode" id="_shipping_postcode" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_postcode', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class=" form-field _shipping_country_field">
                    <label for="_shipping_country">Country</label>
                    <select style="" class="form-control" id="_shipping_country" name="_shipping_country"  tabindex="-1" aria-hidden="true">
                        
                        <option value="">--- Country ---</option>
                    <?php
                                    foreach ( Madhatmedia_Crm_Admin::countries() as $key => $value ) :

                                        if ( get_post_meta( $post_id, '_mhmcrm__shipping_country', true ) == $key ) {
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
                <p class="form-field _shipping_state_field ">
		            <label for="_shipping_state">State/County</label><input type="text" class="js_field-state form-control" name="_shipping_state" id="_shipping_state" value="<?php echo get_post_meta( $post_id, '_mhmcrm__shipping_state', true ); ?>" placeholder="">
                </p>
            </div>
        </div>
    </div>
</div>