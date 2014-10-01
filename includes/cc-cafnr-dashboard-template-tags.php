
<?php

function cc_cafnr_intl_dashboard() {
?>
	<img src="http://dev.communitycommons.org/wp-content/uploads/2014/09/logo.jpg" width="400px" /><br /><br />
	<p><span style="font-weight:bold;font-size:18pt;margin:15px 0px 30px 0px;">Dashboard</span></p>
	
	<?php //if current user is group admin or moderator, show the add member form with drop down
	if ( bp_group_is_admin() || bp_group_is_mod() ) {
	
		cc_cafnr_render_mod_admin_form(); 
	} else {
	
		cc_cafnr_render_member_form();
	} ?>
	
	<br /><br />
	

		<style type="text/css">
		#box-table-a
		{
			font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
			font-size: 12px;
			//margin: 45px;
			width: 60%;
			text-align: left;
			border-collapse: collapse;
		}
		#box-table-a th
		{
			font-size: 13px;
			font-weight: normal;
			padding: 8px;
			background: #b9c9fe;
			border-top: 4px solid #aabcfe;
			border-bottom: 1px solid #fff;
			color: #039;
		}
		#box-table-a td
		{
			padding: 8px;
			background: #e8edff; 
			border-bottom: 1px solid #fff;
			color: #669;
			border-top: 1px solid transparent;
		}
		#box-table-a tr:hover td
		{
			background: #d0dafd;
			color: #339;
		}
		</style>	
	<script type="text/javascript">
		jQuery( document ).ready(function($) {

			
			$('#CVmethod1').click(function () {
				$("#linkDiv").show();
				$("#uploadDiv").hide();
			});
			$('#CVmethod2').click(function () {
				$("#linkDiv").hide();
				$("#uploadDiv").show();
			});		

			$( "#btnAddNewActivity" ).click(function() {
				window.location = "/wordpress/cafnr-add-activity/?user=" + $( "#userID" ).val();
			});			

		});	
	</script>
	
<?php	
	
}
?>