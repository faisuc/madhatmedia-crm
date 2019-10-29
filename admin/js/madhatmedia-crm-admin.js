jQuery(document).ready(function($) {
	$('#emailLogsMHMCRM').DataTable();

	$(document).on("click", ".madhatmedia-crm-remove-metabox", function(e) {
		$(this).parent().parent().remove();
	});

	$('#menu-posts-mhm_crm ul').prepend('<li>' + $('#menu-posts-mhm_crm ul li:eq(5)').html() + '</li>');

	$('#menu-posts-mhm_crm ul li:eq(6)').remove();

	$('#menu-posts-mhm_crm a.menu-top').attr('href', '/wp-admin/edit.php?post_type=mhm_crm&page=mhm_crm_dashboard');

	$('.mhm_crm_assign_staff').select2();

});