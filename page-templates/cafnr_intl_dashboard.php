<?php
/*
Template Name: CAFNR International Dashboard
*/

get_header(); 


?>

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php 
			cc_cafnr_intl_dashboard();
			
			while ( have_posts() ) : the_post(); ?>
				
				
			<?php endwhile; // end of the loop. ?>

		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_footer(); ?>

<?php
function cc_cafnr_intl_dashboard() {
?>
	<img src="http://dev.communitycommons.org/wp-content/uploads/2014/09/logo.jpg" width="400px" /><br /><br />
	<p><span style="font-weight:bold;font-size:18pt;margin:15px 0px 30px 0px;">Dashboard</span></p>
	
	<?php cc_cafnr_render_add_member_form(); ?>
	
	<br /><br />
	
	<div id="userinfo">
		<form>
			<br /><br />
			<strong>First Name:</strong><br/>
			<input type="text" id="firstname" name="firstname" size="45" />
			<br /><br />
			<strong>Last Name:</strong><br/>
			<input type="text" id="lastname" name="lastname" size="45" />
			<br /><br />		
			<strong>Would you like to LINK to or UPLOAD your CV?</strong><br/>
			<input type="radio" id="CVmethod1" name="CVmethod" value="link" />&nbsp;Link to my CV<br />
			<input type="radio" id="CVmethod2" name="CVmethod" value="upload" />&nbsp;Upload my CV
			<br /><br />
			<div id="linkDiv" style="display:none;">
				<strong>Add link to CV here:</strong><br/>	
				<input type="text" id="CVlink" name="CVlink" size="85" />
			</div>
			<div id="uploadDiv" style="display:none;">
				<strong>Upload CV here:</strong><br/>			
			</div>		
			<br /><br />
			<strong>Beyond the last five years, have you been involved in any international activities?</strong><br/>
			<input type="text" id="beyond5" name="beyond5" size="100" />
			<br /><br />
			<strong>Are you planning on engaging in any international activity in the future?</strong><br/>
			<input type="text" id="futureactivity" name="future" size="100" />
			<br /><br />
			<strong>Would you be interested in leading or assisting with a project in your academic field or research focus?</strong><br/>
			<input type="text" id="leadassist" name="leadassist" size="100" />
			<br /><br />	
			<strong>In the future, would you prefer an online form or in-person interview?</strong><br/>
			<input type="radio" id="futurecontact1" name="futurecontact" value="online" />&nbsp;Online form<br />
			<input type="radio" id="futurecontact2" name="futurecontact" value="interview" />&nbsp;Interview
			<br /><br />		
			<input type="submit" value="Submit" />
		</form>
	</div>
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
			$("#activities").hide();
			$("#userinfo").hide();
			$("#newfacultydiv").hide();
			
			$('#CVmethod1').click(function () {
				$("#linkDiv").show();
				$("#uploadDiv").hide();
			});
			$('#CVmethod2').click(function () {
				$("#linkDiv").hide();
				$("#uploadDiv").show();
			});
			
			$( "#faculty" ).change(function() {
				$("#nameactivity").html($("#faculty option:selected").text() + "'s Activities");				
				$("#activities").show();
				$("#userinfo").show();
			});
			$( "#btnAddNewActivity" ).click(function() {
				window.location = "http://dev.communitycommons.org/cafnr-intl/?email=" + $( "#faculty" ).val();
			});			
			$( "#faculty" ).click(function() {
				if ($( "#faculty" ).val() == "ADD") {
					$("#newfacultydiv").show();
					$("#activities").hide();
					$("#userinfo").hide();
				} else {
					$("#newfacultydiv").hide();
					$("#activities").show();
					$("#userinfo").show();					
				}
			});
			$("#submitnewfaculty").click(function() {
					$("#newfacultydiv").hide();
					$("#activities").show();
					$("#userinfo").show();					
			});
		});	
	</script>
	
<?php	
	
}