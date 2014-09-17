<?php 
/**
 * CC CAFNR International Functions
 *
 * @package   CC CAFNR International Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */

 
/*
 * Register CAFNR Activity
 *
 */
function cc_cafnr_activity_register() {
		$labels = array(
			'name' => 'CAFNR Activities',
			'singular_name' => 'CAFNR Activity',
			'add_new' => 'Add New',
			'add_new_item' => 'Add New CAFNR Activity',
			'edit_item' => 'Edit CAFNR Activity',
			'new_item' => 'New CAFNR Activity',
			'all_items' => 'All CAFNR Activities',
			'view_item' => 'View CAFNR Activity',
			'search_items' => 'Search CAFNR Activities',
			'not_found' => 'No CAFNR Activities found',
			'not_found_in_trash' => 'No CAFNR Activities found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'CAFNR Activities'
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'cafnr-activities', 'with_front' => false ),
			'capability_type' => 'post',
			'has_archive' => true,
			'hierarchical' => true,
			'menu_position' => 30,
			'taxonomies' => array( 'cafnr-activity-type' ),
			'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'post-formats' )
		);
	register_post_type( 'cafnr-activity', $args );
}
add_action( 'init', 'cc_cafnr_activity_register' );


function cc_cafnr_activity_taxonomy_register() {
	$labels = array(
	    'name'	=> _x( 'CAFNR Activity Types', 'taxonomy general name' ),
	    'singular_name'	=> _x( 'CAFNR Activity Type', 'taxonomy singular name' ),
	    'search_items'	=> __( 'Search CAFNR Activity Types' ),
	    'popular_items'	=> __( 'Popular CAFNR Activity Types' ),
	    'all_items'	=> __( 'All CAFNR Activity Types' ),
	    'parent_item' => null,
	    'parent_item_colon'	=> null,
	    'edit_item' => __( 'Edit CAFNR Activity Type' ), 
	    'update_item' => __( 'Update CAFNR Activity Type' ),
	    'add_new_item' => __( 'Add New CAFNR Activity Type' ),
	    'new_item_name' => __( 'New CAFNR Activity Type' ),
	    'separate_items_with_commas' => __( 'Separate CAFNR Activity types with commas' ),
	    'add_or_remove_items' => __( 'Add or remove CAFNR Activity types' ),
	    'choose_from_most_used' => __( 'Choose from the most used CAFNR Activity types' ),
	    'not_found' => __( 'No CAFNR Activity types found.' ),
	    'menu_name' => __( '-- Edit CAFNR Activity Types' )
	);
	
	$args = array(
		'hierarchical' => true,
	    'labels' => $labels,
	    'show_ui' => true,
	    'show_admin_column' => true,
	    'query_var' => true,
	    'rewrite' => array( 'slug' => 'cafnr-activity-type' )
	);
	
	register_taxonomy( 'cafnr-activity-type', 'cafnr-activity', $args );
}
add_action( 'init', 'cc_cafnr_activity_taxonomy_register' );


/* OLD GRAVITY FORMS GF STUFF */
/*
* Adds CAFNR International Group members to drop down
*
*
*
*/
add_filter('gform_pre_render_25_1', 'cc_cafnr_populate_group_members');

function cc_cafnr_populate_group_members(){

	$field['choices'] = array('text' => '1', 'value' => 'holder');
	return $field['choices'];

	global $bp;
	$group_id = 596;
	$group = groups_get_group( array( ‘group_id’ => $group_id ) );
	
	
	//return array($group_id);
	
	//if not a drop down of the class name cc-cafnr-populate-members, get out of here
//	if($field['type'] != 'select' || strpos($field['cssClass'], 'cc-cafnr-populate-members') === false)
	    //continue;

	$choices = array(array('text' => 'Select a Post', 'value' => ' '));
	
	if ( bp_group_has_members( '$group' ) ) {
	
	?>
	
			<?php while ( bp_group_members() ) : bp_group_the_member(); 
 
			$choices[] = array('text' => '1', 'value' => bp_group_member_link() );		
			?>
			
			<?php endwhile; ?>
			
			<?php $field['choices'] = $choices; 
			return $choices; ?>
	
	<?php } else {
		
		$field['choices'] = array('text' => '1', 'value' => 'holder');
		$choices = array('text' => '1', 'value' => 'holder');
		return $choices;

	}

}

//groups_is_user_admin( $user_id, $group_id )

//ajax for plupload on the activity form
function cc_cafnr_activity_upload() {
	
	$new_file = wp_handle_upload( $_FILES['activity_uploads'], array( 'test_form' => false ) );
	
	if ( $new_file ) {
		$new_file['fileBaseName'] = basename( $new_file['file'] );
		echo json_encode( $new_file );
	} else {
		echo "There seems to be an error.";
	}
		die();
	}
add_action( 'wp_ajax_activity_upload', 'cc_cafnr_activity_upload' );
