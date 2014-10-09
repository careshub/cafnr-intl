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
	$group_id = cc_cafnr_get_group_id();
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

//ajax for plupload on the activity form
function cc_cafnr_user_upload() {
	
	$new_file = wp_handle_upload( $_FILES['user_uploads'], array( 'test_form' => false ) );
	
	if ( $new_file ) {
		$new_file['fileBaseName'] = basename( $new_file['file'] );
		echo json_encode( $new_file );
	} else {
		echo "There seems to be an error.";
	}
		die();
	}
add_action( 'wp_ajax_user_upload', 'cc_cafnr_user_upload' );

//ajax for plupload on the activity form
function cc_cafnr_activity_upload_delete() {
	
	$current_user = wp_get_current_user(); 
	
	//make sure user is author or admin
	$user_id = $_POST['user_id'];
	$attach_id = $_POST['attachment_id'];
	$parent_id = get_post_field( 'post_parent', $attach_id );
	
	$post_author = get_post_field( 'post_author', $parent_id );
	
	//if !author or ( bp_group_is_admin() || bp_group_is_mod() ), don't allow deletion!
	if ( ( $current_user->ID != $post_author ) && !( bp_group_is_admin() || bp_group_is_mod() ) ) {
		$data['error'] = $post_author . 'you do not have permission to delete this file';
		echo json_encode( $data );
		die();
	} else if ( $attach_id <= 0 ) {
		$data['error'] = 'Hmm, that is not a real file, now is it?';
		echo json_encode( $data );
		die();
	}
	
	$data['success'] = wp_delete_attachment( $attach_id );
	
	echo json_encode( $data );
	die();
	
}

add_action( 'wp_ajax_activity_upload_delete', 'cc_cafnr_activity_upload_delete' );


/**
 * Is this the CAFNR group?
 *
 * @since    1.0.0
 * @return   boolean
 */
function cc_cafnr_is_cafnr_group(){
    return ( bp_get_current_group_id() == cc_cafnr_get_group_id() );
}

/**
 * Get the group id based on the context
 *
 * @since   1.0.0
 * @return  integer
 */
function cc_cafnr_get_group_id(){
    switch ( get_home_url() ) {
        case 'http://commonsdev.local':
            $group_id = 596;
            break;
		case 'http://localhost/cc_local':
            $group_id = 596;
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 596;
            break;
        default:
            $group_id = 595;
            break;
    }
	
    return $group_id;
}

/**
 * Get various slugs
 * These are gathered here so when, inevitably, we have to change them, it'll be simple
 *
 * @since   1.0.0
 * @return  string
 */
function cc_cafnr_get_slug(){
    return 'survey-dashboard';
}
function cc_cafnr_get_activity_slug(){
    return 'cafnr-add-activity';
}

/**
 * Get URIs for the various pieces of this tab
 * 
 * @return string URL
 */
function cc_cafnr_get_home_permalink( $group_id = false ) {
    $group_id = ( $group_id ) ? $group_id : bp_get_current_group_id() ;
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) ) .  cc_cafnr_get_slug() . '/';
    return apply_filters( "cc_cafnr_home_permalink", $permalink, $group_id);
}
function cc_cafnr_get_activity_permalink( $group_id = false ) {
    $permalink = cc_cafnr_get_home_permalink( $group_id ) . cc_cafnr_get_activity_slug() . '/';
    return apply_filters( "cc_cafnr_activity_permalink", $permalink, $group_id);
}

/**
 * Where are we?
 * Checks for the various screens
 *
 * @since   1.0.0
 * @return  string
 */
function cc_cafnr_on_survey_dashboard_screen(){
    // There should be no action variables if on the main tab
    if ( cc_cafnr_is_component() && ! ( bp_action_variables( cc_cafnr_get_slug(), 0 ) ) ){
        return true;
    } else {
        return false;
    }
}
function cc_cafnr_on_activity_screen(){
    if ( cc_cafnr_is_component() && bp_is_action_variable( cc_cafnr_get_activity_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}

/**
 * Are we on the CAFNR survey tab?
 *
 * @since   1.0.0
 * @return  boolean
 */
function cc_cafnr_is_component() {
    if ( bp_is_groups_component() && bp_is_current_action( cc_cafnr_get_slug() ) )
        return true;

    return false;
}



/*
 * Add page templates for form, dashboards
 *
 */

add_filter( 'page_template', 'cafnr_intl_dashboard_page_template' );
function cafnr_intl_dashboard_page_template( $page_template )
{
    if ( is_page( 'cafnr-intl-dashboard' ) ) {
        $page_template = dirname( __FILE__ ) . '/page-templates/cafnr_intl_dashboard.php';
    }
    return $page_template;
}

add_filter( 'page_template', 'cafnr_activity_page_template' );
function cafnr_activity_page_template( $page_template )
{
    if ( is_page( 'cafnr-add-activity' ) ) {
        $page_template = dirname( __FILE__ ) . '/page-templates/cafnr_intl_activity_form.php';
    }
    return $page_template;
}

add_action( 'wp_ajax_del_cafnr_activity', 'del_cafnr_activity' );
function del_cafnr_activity() {
	global $wpdb; // this is how you get access to the database

	$activityid = intval( $_POST['activityid'] );
	wp_delete_post( $activityid );

	die(); // this is required to terminate immediately and return a proper response
}

//no_priv = not authenticated.
//add_action('wp_ajax_nopriv_add_cafnr_faculty', 'add_cafnr_faculty');
add_action( 'wp_ajax_add_cafnr_faculty', 'add_cafnr_faculty' );
function add_cafnr_faculty() {

	//TODO: add current_usercan and bp_is_admin, is_mod check to this!!
	//add user to Wordpress using an e-mail address
	$group_id = $_POST['groupid'];
	$email_address = $_POST['useremail'];
	$dname = $_POST['displayname'];
	$fname = $_POST['firstname'];
	$lname = $_POST['lastname'];

		if( null == username_exists( $email_address ) ) {

			// Generate the password and create the user
			$password = wp_generate_password( 12, false );
			$user_id = wp_create_user( $email_address, $password, $email_address );

			// Set the nickname
			wp_update_user(
				array(
					'ID'          => $user_id,
					'nickname'    => $email_address,
					'display_name' =>	$dname,
					'first_name' => $fname,
					'last_name' => $lname
				)
			);

			// Set the role
			$user = new WP_User( $user_id );
			$user->set_role( 'contributor' );

			// Email the user: TODO: are we doing this?
			//wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );
			//cc_cafnr_automatic_group_membership( $user_id );
			
			//add user to this group
			$group_id = cc_cafnr_get_group_id();
			groups_accept_invite( $user_id, $group_id );
			
			echo $user_id;
		} // end if

	die();
}

/*
//This adds all incoming to group, because of filter
function cc_cafnr_automatic_group_membership( $user_id ) {
	//adds new user to BuddyPress group
	if( !$user_id ) return false; 
		$group_id = cc_cafnr_get_group_id();
		groups_accept_invite( $user_id, $group_id );
	}
add_action( 'bp_core_activated_user', 'cc_cafnr_automatic_group_membership' ); */


