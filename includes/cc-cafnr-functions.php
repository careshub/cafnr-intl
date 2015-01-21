<?php 
/**
 * CC CAFNR International Functions
 *
 * @package   CC CAFNR International Extras
 * @author    CARES staff
 * @license   GPL-2.0+
 * @copyright 2014 CommmunityCommons.org
 */

 
 
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

/**
 * Get various slugs
 * These are gathered here so when, inevitably, we have to change them, it'll be simple
 *
 * @since   1.0.0
 * @return  string
 */
function cc_cafnr_get_slug(){
    return 'intl-activities';
}
function cc_cafnr_get_all_activities_slug(){
    return 'all';
}
function cc_cafnr_get_activity_slug(){
    return 'cafnr-add-activity';
}
function cc_cafnr_get_one_activity_slug(){
	return 'view-activity';
}

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
            $group_id = 622;  //596
            break;
        case 'http://dev.communitycommons.org':
            $group_id = 596;
            break;
        case 'http://www.communitycommons.org':
            $group_id = 622;
            break;
        default:
            $group_id = 622;
            break;
    }
	
    return $group_id;
}


/**
 * Get URIs for the various pieces of this tab
 * 
 * @return string URL
 */
function cc_cafnr_get_home_permalink( $group_id = false ) {
    $group_id = ( $group_id ) ? $group_id : bp_get_current_group_id() ;
    $permalink = bp_get_group_permalink( groups_get_group( array( 'group_id' => $group_id ) ) ) .  cc_cafnr_get_slug() . '/';
    return apply_filters( "cc_cafnr_get_home_permalink", $permalink, $group_id);
}

function cc_cafnr_get_activity_permalink( $group_id = false ) {
    $permalink = cc_cafnr_get_home_permalink( $group_id ) . cc_cafnr_get_activity_slug() . '/';
    return apply_filters( "cc_cafnr_get_activity_permalink", $permalink, $group_id);
}

function cc_cafnr_save_activity_permalink( $group_id = false ) {
    $permalink = cc_cafnr_get_home_permalink( $group_id ) . cc_cafnr_get_activity_slug() . '/update-activity';
    return apply_filters( "cc_cafnr_save_activity_permalink", $permalink, $group_id);
}

function cc_cafnr_get_all_activities_permalink( $group_id = false ) {
    $permalink = cc_cafnr_get_home_permalink( $group_id ) . cc_cafnr_get_all_activities_slug() . '/';
    return apply_filters( "cc_cafnr_get_all_activities_permalink", $permalink, $group_id);
}

function cc_cafnr_get_activity_view_permalink( $activity_id ){
    $permalink = cc_cafnr_get_home_permalink( $group_id ) . cc_cafnr_get_one_activity_slug() . '/';
    return apply_filters( "cc_cafnr_get_activity_view_permalink", $permalink, $group_id);
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
function cc_cafnr_on_all_activities_screen(){
    if ( cc_cafnr_is_component() && bp_is_action_variable( cc_cafnr_get_all_activities_slug(), 0 ) ){
        return true;
    } else {
        return false;
    }
}


function cafnr_intl_scripts() {
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'plupload' );
	
	//dirname( __FILE__ )
	
	wp_enqueue_script( 'cc-cafnr', plugins_url( '/cc-cafnr.js', __FILE__), array(), '1.0.0', true );
	wp_enqueue_style( 'datepicker-style', plugins_url( '/css/datepicker.css', __FILE__) );
	wp_enqueue_style( 'gf-style',  plugins_url( '/css/g_forms_styles.css', __FILE__) );
	wp_enqueue_style( 'cafnr-style', plugins_url( '/css/cafnr-intl.css', __FILE__) );	

	//so we can use vars in js functions
	wp_localize_script(
		'cc-cafnr',
		'cafnr_ajax',
			array(
			'adminAjax' => admin_url( 'admin-ajax.php' ),
			'homeURL' => get_site_url(),
			'groupID' => cc_cafnr_get_group_id(),
			'surveyDashboard' => cc_cafnr_get_home_permalink()
			)
	);
}

add_action( 'wp_enqueue_scripts', 'enqueue_go', 7 );
function enqueue_go(){
	if( cc_cafnr_is_component() ){
		add_action( 'wp_enqueue_scripts', 'cafnr_intl_scripts' );
	} 
}
 
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

/**
 * Ajax functionality for plupload on the activity form
 *
 *
 * @return json
 */
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

/**
 * Ajax functionality for plupload on the user form
 *
 *
 * @return json
 */
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

/**
 * Ajax functionality for deleting user's uploaded file on the user form
 *
 *
 * @return json
 */
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

//no_priv = not authenticated.
add_action( 'wp_ajax_cafnr_intl_edit_activity', 'cafnr_intl_edit_activity' );

function cafnr_intl_edit_activity(){

	//Functionality to deal with $_POSTed form data
	//echo "<div class='usr-msg'>Success in saving!</div>";
	
	//TODO: add is author. mod or admin permissions to this submit
	if ( $_POST['new_activity'] == 'edit_activity' || $_POST['new_activity'] == 'new_activity' ) {
		//only logged-in users can submit this form
		if ( !is_user_logged_in() ) {
			wp_redirect( home_url() ); //TODO: where to go?
			exit;
		}
		
		//update existing post
		if ( isset( $_POST['activity_id'] ) && ( $_POST['activity_id'] > 0 ) ){
			$activity_id = $_POST['activity_id'];
			
			//update the post fields, if need be - just summary?
			$updating_post = array(
				'ID' => $activity_id,
				'post_content' => $_POST['activity_summary']
				);
			wp_update_post( $updating_post );
			
			$activity_name = get_the_title( $activity_id );
			
		} else if ( ( $_POST['cafnr_activity_name'] != '-1' ) && ( $_POST['cafnr_activity_name'] != 'add_new_activity' ) ){
			
			//new activity with parent name
			
			//we already have an id, so let's create a child post of same name, yes?
			$parent_activity_id = $_POST['cafnr_activity_name'];
			
			//get the activity title
			$activity_name = get_the_title( $parent_activity_id );
			
			//from here, we'll create a child post of the parent w/same name, current user author
			$activity = array(
				'post_title' => $activity_name,
				'post_type' => 'cafnr-activity',
				'post_status' => 'publish',
				'post_content' => $_POST['activity_summary'],
				'post_parent' => $parent_activity_id
			);
			
			$activity_id = wp_insert_post( $activity );
			
		} else { //completely new activity with no parent
			
			$activity_name = $_POST['add_activity_title'];
			
			$activity = array(
				'post_title' => $activity_name,
				'post_type' => 'cafnr-activity',
				'post_status' => 'publish',
				'post_content' => $_POST['activity_summary']
			);
			
			$activity_id = wp_insert_post( $activity );
			
		}
		
		//set post author based on user id (TODO: change this methodology to be secure, once !admins/mods can access this form.)
		if ( $_POST['user_id'] > 0 ){  //if we have an activity_owner as a param
			$updating_post = array(
				'ID'	=>	$activity_id,
				'post_author'	=> $_POST['user_id']
				);
			wp_update_post( $updating_post );
			
			//TODO: remove this meta field, once post_author is set up correctly
			//Post author may not be activity owner (in the case of Ben filling out form for another faculty member) so we need post_meta field to capture true owner
			update_post_meta( $activity_id, 'activity_owner', $_GET['user'] );
		}
		
		//project-specific meta fields (the easy ones)
		$activity_fields = array(
				'activity_radio', //save to custom taxonomy instead?
				'country_lead',
				'pi_radio',
				'subject_textbox',
				'non_pi_role',
				'funding_source'
				
			);
		foreach ( $activity_fields as $f ) {
			if (isset($_POST[$f])) {
				if ( ( $_POST[$f] == '-1' ) || (  $_POST[$f] == "" ) ) { //defaults for selects
					delete_post_meta($activity_id, $f);
				} else {
					update_post_meta( $activity_id, $f, $_POST[$f] );
				}
			}
		}
		
		//What countries and regions are we in?
		$i = 1;
		
		//first, remove all prior countries in database
		delete_post_meta($activity_id, 'country');
		
		while ( isset($_POST['countrylist-'.$i] ) ) {
			
			//create array to hold country meta
			$country_meta_array = array();
			
			if ( $_POST['countrylist-'.$i] != "" ) {
				$country_meta_array[] = $_POST['countrylist-'.$i];
				if ( isset( $_POST['region-'.$i] ) ) {
					$country_meta_array[] = $_POST['region-'.$i];
				}
				$success = add_post_meta( $activity_id, 'country', $country_meta_array );
				//echo $success;
			}
			//unset array
			unset( $country_meta_array );
			$i++;
		}
		
		//Is this user the pi? 
		if( isset ( $_POST['pi_radio'] ) ){
			update_post_meta( $activity_id, 'is_pi', $_POST['pi_radio'] );
		}
		
		//dates!
		if ( !empty ( $_POST['start_date'] ) ){
			//because we're converting to date, we need to account for 0 (else it's 1970 and it's time to move on..)
			if ( ( $_POST['start_date'] == "") || $_POST['start_date'] == 0 ) {
				update_post_meta( $activity_id, 'start_date', "" );
			} else {
				$startDate = date( 'Y-m-d H:i:s', strtotime( $_POST['start_date'] ) );
				update_post_meta( $activity_id, 'start_date', $startDate );
			}
		}
		
		if ( isset ( $_POST['end_date'] ) ){
			if ( ( $_POST['end_date'] == "") || $_POST['end_date'] == 0 ) {
				update_post_meta( $activity_id, 'end_date', "" );
			} else {
				$endDate = date( 'Y-m-d H:i:s', strtotime( $_POST['end_date'] ) );
				update_post_meta( $activity_id, 'end_date', $endDate );
			}
		}
		
		//TODO: account for write-in PI in drop down! (get all meta of 'who_is_pi' for all cafnr-activity post types
		if ( isset ( $_POST['who_is_pi'] ) ){
			update_post_meta( $activity_id, 'who_is_pi', "" );
		}
		//Activity type (the radio one)
		wp_set_object_terms( $activity_id, $_POST['activity_radio'], 'cafnr-activity-type' );
		
		//supplemental links - many inputs of same name
		if ( isset( $_POST['supplemental_links'] ) ) {
			//clean sweep on every save
			delete_post_meta( $activity_id, 'supplemental_links' );
			foreach( $_POST['supplemental_links'] as $link ) {
				if ( $link != "" )
					add_post_meta( $activity_id, 'supplemental_links', $link, false );  //false since not unique
			}
		}
		
		//collaborating people/institutions - many inputs of same name
		if ( isset( $_POST['collaborating'] ) ) {
			//clean sweep on every save
			delete_post_meta( $activity_id, 'collaborating' );
			foreach( $_POST['collaborating'] as $link ) {
				if ( $link != "" )
					add_post_meta( $activity_id, 'collaborating', $link, false );  //false since not unique
			}
		}
		if( isset( $_POST['activity_checkbox'] ) ){
			$activity_checkbox = $_POST['activity_checkbox'];
			$old_activity_meta = get_post_meta( $activity_id, 'activity_checkbox', true );
			// Update post meta
			if( !empty( $old_activity_meta ) ){
				update_post_meta( $activity_id, 'activity_checkbox', $activity_checkbox );
			} else {
				add_post_meta( $activity_id, 'activity_checkbox', $activity_checkbox, true );
			}
		}
		
		//get all files uploaded
		$i = 1;
		while ( isset($_POST['activity_file_count-' . $i] ) ) {
			
			//if we have new data
			if ( isset( $_POST['activity_file-' . $i] ) && isset( $_POST['activity_file_type-' . $i] ) && isset( $_POST['activity_file_url-' . $i] ) ) {
				if ( $_POST['activity_attachment_name-' . $i] != "" ) {  //if user sets name here
					$attachment_title =  $_POST['activity_attachment_name-' . $i];
				} else {
					$attachment_title = $activity_name . ' - ' . $activity_id . ' (Attachment ' . $i . ' )';
				}
				
				$attachment = array(
					'post_title' => $attachment_title,
					'post_content' => '',
					'guid' => $_POST['activity_file_url-' . $i],
					'post_status' => 'publish',
					'post_mime_type' => $_POST['activity_file_type-' . $i]
				);
				
				$attachment_id = wp_insert_attachment( $attachment, $_POST['activity_file'], $activity_id );	
			}
			
			$i++;
		}
		
	//	
		//if successful, redirect to dashboard
		//TODO: make this redirect to tab, universally
		//$dashboard = cc_cafnr_get_home_permalink();
		
	
	}
	//this will NOT redirect if we've already POSTed data to this page ('headers already sent')
	echo $activity_id;
	//wp_redirect( $dashboard . '?user=' . $_POST['user_id'] . '&msg=1' );
	wp_redirect( $dashboard );
	exit;
	
	/** END POST SUBMIT / SAVING ***/
	




}

/*
 * Parses message codes from url
 *
 */
function cafnr_message_parser( $number = 0 ){
	
	$return_message;
	
	switch( $number ){
		case 1:
			$return_message = "Your activity has been added";
			break;
		case 2:
			$return_message = "Your activity has been updated";
			break;
		case 3:
		default:
			$return_message = "";
			break;
	}
	
	return $return_message;
	
}

