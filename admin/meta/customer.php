<div class="bootstrap-iso">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <p class="form-field first_name_field ">
                    <label for="first_name">First Name</label><input type="text" class="short form-control" name="first_name" id="first_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm_first_name', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field last_name_field ">
		            <label for="last_name">Last Name</label><input type="text" class="short form-control" style="" name="last_name" id="last_name" value="<?php echo get_post_meta( $post_id, '_mhmcrm_last_name', true ); ?>" placeholder="">
                </p>	
            </div>
            <div class="col-md-3">
                <p class="form-field customer_title_field ">
		            <label for="customer_title">Title</label><input type="text" class="short form-control" style="" name="customer_title" id="customer_title" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_title', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field user_email_field ">
		            <label for="user_email">Email Address</label><input type="text" class="short form-control" style="" name="user_email" id="user_email" value="<?php echo get_post_meta( $post_id, '_mhmcrm_user_email', true ); ?>" value="" placeholder="" required="1">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_department_field ">
		            <label for="customer_department">Department</label><input type="text" class="short form-control" style="" name="customer_department" id="customer_department" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_department', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_mobile_field ">
		            <label for="customer_mobile">Mobile</label><input type="text" class="short form-control" style="" name="customer_mobile" id="customer_mobile" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_mobile', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_fax_field ">
		            <label for="customer_fax">Fax</label><input type="text" class="short form-control" style="" name="customer_fax" id="customer_fax" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_fax', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_site_field ">
		            <label for="customer_site">Website</label><input type="text" class="short form-control" style="" name="customer_site" id="customer_site" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_site', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field date_of_birth_field ">
		            <label for="date_of_birth">Date of Birth</label><input type="date" class="short hasDatepicker form-control" style="" name="date_of_birth" id="date_of_birth" value="<?php echo get_post_meta( $post_id, '_mhmcrm_date_of_birth', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_assistant_field ">
		            <label for="customer_assistant">Assistant</label><input type="text" class="short form-control" style="" name="customer_assistant" id="customer_assistant" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_assistant', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_skype_field ">
		            <label for="customer_skype">Skype</label><input type="text" class="short form-control" style="" name="customer_skype" id="customer_skype" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_skype', true ); ?>" placeholder="">
                </p>
            </div>
            <div class="col-md-3">
                <p class="form-field customer_twitter_field ">
		            <label for="customer_twitter">Twitter</label><input type="text" class="short form-control" style="" name="customer_twitter" id="customer_twitter" value="<?php echo get_post_meta( $post_id, '_mhmcrm_customer_twitter', true ); ?>" placeholder="">
                </p>
            </div>
        </div>
    </div>
</div>