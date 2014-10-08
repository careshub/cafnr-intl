<?php
/*
 * Plugin Name: CAFNR International Programs - Mel's Dev
 * Version: Current Version
 * Author: Michael Barbaro
 * Description: This plugin creates a small dashboard form and support for a larger Gravity Form.
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

 
function cc_cafnr_intl_class_init(){
	// Get the class fired up
	// Helper and utility functions
	
	require_once( dirname( __FILE__ ) . '/includes/cc-cafnr-functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/cc-cafnr-template-tags.php' );
	require_once( dirname( __FILE__ ) . '/includes/cc-cafnr-dashboard-template-tags.php' );
	//require_once( dirname( __FILE__ ) . '' );
	// 	jquery-ui-datepicker
	
	function cafnr_intl_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'plupload' );
		
		//dirname( __FILE__ )
		
		wp_enqueue_script( 'cc-cafnr', plugins_url( '/includes/cc-cafnr.js', __FILE__), array(), '1.0.0', true );
		wp_enqueue_style( 'datepicker-style', plugins_url( '/includes/css/datepicker.css', __FILE__) );
		wp_enqueue_style( 'gf-style',  plugins_url( '/includes/css/g_forms_styles.css', __FILE__) );
		wp_enqueue_style( 'cafnr-style', plugins_url( '/includes/css/cafnr-intl.css', __FILE__) );	

		//so we can use vars in js functions
		wp_localize_script(
			'cc-cafnr',
			'cafnr_ajax',
				array(
				'adminAjax' => admin_url( 'admin-ajax.php' ),
				'homeURL' => get_site_url(),
				'groupID' => cc_cafnr_get_group_id()
				)
		);
	}

	add_action( 'wp_enqueue_scripts', 'cafnr_intl_scripts' );

	
//	add_action( 'bp_include', array( 'CC_AHA_Extras', 'get_instance' ), 21 );

	//TODO: remove this in favor of active: cc_cafnr_get_activity_permalink()
	define( 'CAFNR_ACTIVITY_FORM_URL', '/cafnr-add-activity' );
	
	
}
add_action( 'bp_include', 'cc_cafnr_intl_class_init' );

/* Only load the component if BuddyPress is loaded and initialized. */
function startup_cafnr_extras_group_extension_class() {
	require( dirname( __FILE__ ) . '/includes/cc-cafnr-bp-group-extension.php' );
}
add_action( 'bp_include', 'startup_cafnr_extras_group_extension_class', 24 );

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

add_action('wp_ajax_nopriv_add_cafnr_faculty', 'add_cafnr_faculty');
add_action( 'wp_ajax_add_cafnr_faculty', 'add_cafnr_faculty' );
function add_cafnr_faculty() {
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

		  // Email the user
		  wp_mail( $email_address, 'Welcome!', 'Your Password: ' . $password );
		  cc_cafnr_automatic_group_membership( $user_id );
		  echo $user_id;
		} // end if

	die(); 
}

function cc_cafnr_automatic_group_membership( $user_id ) {
	//adds new user to BuddyPress group
	if( !$user_id ) return false; 
		$group_id = cc_cafnr_get_group_id();
		groups_accept_invite( $user_id, $group_id );
	}
add_action( 'bp_core_activated_user', 'cc_cafnr_automatic_group_membership' );


// add_filter("gform_field_value_facultyemail", "cafnr_intl_populate_email");
// function cafnr_intl_populate_email($value){
	// $useremail;
	// if (!empty($_GET['email'])) {
		// $useremail = $_GET['email'];
	// } else {
	  // $current_user = wp_get_current_user();
	  // $useremail = $current_user->user_email;
	// }
    // return $useremail;
// }



// add_filter("gform_column_input_22_36_1", "cafnr_intl_set_column2", 10, 5);
// add_filter("gform_column_input_22_37_1", "cafnr_intl_set_column2", 10, 5);
// function cafnr_intl_set_column2($input_info, $field, $column, $value, $form_id){
    // return array("type" => "select", "choices" => 
			// array(
				// "" => "---Select---",
				// "Bob Jones" => "Bob Jones",
				// "Anne Smith" => "Anne Smith",
				// "Harry Potter" => "Harry Potter"
				// )
			// );
// }

