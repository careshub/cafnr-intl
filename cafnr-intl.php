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
				'groupID' => cc_cafnr_get_group_id(),
				'surveyDashboard' => cc_cafnr_get_home_permalink()
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

