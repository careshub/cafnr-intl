
<?php

function cc_cafnr_intl_dashboard() {

?>
	<a href="/wordpress/cafnr-intl-dashboard"><img src="http://dev.communitycommons.org/wp-content/uploads/2014/09/logo.jpg" width="400px" /></a><br /><br />
	<p><span style="font-weight:bold;font-size:18pt;margin:15px 0px 30px 0px;">Dashboard</span></p>
	
	<?php 
	//if current user is group admin or moderator, show the add member form with drop down
	if ( bp_group_is_admin() || bp_group_is_mod() ) {
		cc_cafnr_render_mod_admin_form(); 
	} else {
	
		cc_cafnr_render_member_form();
	} ?>
	
	<br /><br />
	
<?php	
	
}
?>