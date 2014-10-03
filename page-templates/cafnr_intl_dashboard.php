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

