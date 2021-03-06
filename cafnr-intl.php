<?php
/*
 * Plugin Name: CAFNR International Programs
 * Version: Current Version
 * Author: CARES
 * Description: This plugin creates a searchable activities table and adds faculty and engagements to the db.
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

