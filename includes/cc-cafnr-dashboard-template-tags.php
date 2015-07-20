<?php

function cc_cafnr_intl_dashboard() {
//TODO: change this logo location!
?>


	<?php
	//if current user is group admin or moderator, show the add member form with drop down
	if ( bp_group_is_admin() || bp_group_is_mod() ) {
		cc_cafnr_render_mod_admin_page();
	} else {

		cc_cafnr_render_member_page();
	} ?>

	<br /><br />

<?php

}
?>
