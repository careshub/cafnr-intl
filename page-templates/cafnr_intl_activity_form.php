<?php
/*
Template Name: CAFNR International Activity Form
*/
global $wpdb;

//echo 'yo';
get_header();

//$gf_style_url = plugins_url( 'cc-cafnr-intl/includes/css/g_forms_styles.css', '');
//$style_url = plugins_url( 'cc-cafnr-intl/includes/css/cafnr-intl.css', '');
//$datepicker_style_url = plugins_url( 'cc-cafnr-intl/includes/css/datepicker.css', '');
//$js_url = plugins_url( 'cc-cafnr-intl/includes/cc-cafnr.js', '');

?>

	<!--<link rel="stylesheet" type="text/css" href="<?php echo $style_url; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $gf_style_url; ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo $datepicker_style_url; ?>">
	<script type="text/javascript" src="<?php echo $js_url; ?>"></script>-->

	<div id="primary" class="site-content">
		<div id="content" role="main">

			<?php
			cc_cafnr_activity_form_render();

			?>

		</div><!-- #content -->
	</div><!-- #primary -->


<?php get_footer();



?>