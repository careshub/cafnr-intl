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
		
		wp_enqueue_script( 'cc-cafnr', plugins_url( 'cc-cafnr-intl2/includes/cc-cafnr.js', ''), array(), '1.0.0', true );
		wp_enqueue_style( 'datepicker-style', plugins_url( 'cc-cafnr-intl2/includes/css/datepicker.css') );
		wp_enqueue_style( 'gf-style',  plugins_url( 'cc-cafnr-intl2/includes/css/g_forms_styles.css') );
		wp_enqueue_style( 'cafnr-style', plugins_url( 'cc-cafnr-intl2/includes/css/cafnr-intl.css') );	
		
		//so we can use vars in js functions
		wp_localize_script(
			'cc-cafnr',
			'cafnr_ajax',
				array(
				'adminAjax' => admin_url( 'admin-ajax.php' ),
				'homeURL' => get_site_url()
				)
		);
	}

	add_action( 'wp_enqueue_scripts', 'cafnr_intl_scripts' );

	
//	add_action( 'bp_include', array( 'CC_AHA_Extras', 'get_instance' ), 21 );

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




add_filter("gform_field_value_facultyemail", "cafnr_intl_populate_email");
function cafnr_intl_populate_email($value){
	$useremail;
	if (!empty($_GET['email'])) {
		$useremail = $_GET['email'];
	} else {
	  $current_user = wp_get_current_user();
	  $useremail = $current_user->user_email;
	}
    return $useremail;
}


add_filter("gform_column_input_22_8_1", "cafnr_intl_set_column1", 10, 5);
add_filter("gform_column_input_23_5_1", "cafnr_intl_set_column1", 10, 5);
function cafnr_intl_set_column1($input_info, $field, $column, $value, $form_id){
    return array("type" => "select", "choices" => 
				array(
				''=>'---Select---',
				'AF'=>'Afghanistan',
				'AL'=>'Albania',
				'DZ'=>'Algeria',
				'AS'=>'American Samoa',
				'AD'=>'Andorra',
				'AO'=>'Angola',
				'AI'=>'Anguilla',
				'AQ'=>'Antarctica',
				'AG'=>'Antigua And Barbuda',
				'AR'=>'Argentina',
				'AM'=>'Armenia',
				'AW'=>'Aruba',
				'AU'=>'Australia',
				'AT'=>'Austria',
				'AZ'=>'Azerbaijan',
				'BS'=>'Bahamas',
				'BH'=>'Bahrain',
				'BD'=>'Bangladesh',
				'BB'=>'Barbados',
				'BY'=>'Belarus',
				'BE'=>'Belgium',
				'BZ'=>'Belize',
				'BJ'=>'Benin',
				'BM'=>'Bermuda',
				'BT'=>'Bhutan',
				'BO'=>'Bolivia',
				'BA'=>'Bosnia And Herzegovina',
				'BW'=>'Botswana',
				'BV'=>'Bouvet Island',
				'BR'=>'Brazil',
				'IO'=>'British Indian Ocean Territory',
				'BN'=>'Brunei',
				'BG'=>'Bulgaria',
				'BF'=>'Burkina Faso',
				'BI'=>'Burundi',
				'KH'=>'Cambodia',
				'CM'=>'Cameroon',
				'CA'=>'Canada',
				'CV'=>'Cape Verde',
				'KY'=>'Cayman Islands',
				'CF'=>'Central African Republic',
				'TD'=>'Chad',
				'CL'=>'Chile',
				'CN'=>'China',
				'CX'=>'Christmas Island',
				'CC'=>'Cocos (Keeling) Islands',
				'CO'=>'Columbia',
				'KM'=>'Comoros',
				'CG'=>'Congo',
				'CK'=>'Cook Islands',
				'CR'=>'Costa Rica',
				'CI'=>'Cote D\'Ivorie (Ivory Coast)',
				'HR'=>'Croatia (Hrvatska)',
				'CU'=>'Cuba',
				'CY'=>'Cyprus',
				'CZ'=>'Czech Republic',
				'CD'=>'Democratic Republic Of Congo (Zaire)',
				'DK'=>'Denmark',
				'DJ'=>'Djibouti',
				'DM'=>'Dominica',
				'DO'=>'Dominican Republic',
				'TP'=>'East Timor',
				'EC'=>'Ecuador',
				'EG'=>'Egypt',
				'SV'=>'El Salvador',
				'GQ'=>'Equatorial Guinea',
				'ER'=>'Eritrea',
				'EE'=>'Estonia',
				'ET'=>'Ethiopia',
				'FK'=>'Falkland Islands (Malvinas)',
				'FO'=>'Faroe Islands',
				'FJ'=>'Fiji',
				'FI'=>'Finland',
				'FR'=>'France',
				'FX'=>'France, Metropolitan',
				'GF'=>'French Guinea',
				'PF'=>'French Polynesia',
				'TF'=>'French Southern Territories',
				'GA'=>'Gabon',
				'GM'=>'Gambia',
				'GE'=>'Georgia',
				'DE'=>'Germany',
				'GH'=>'Ghana',
				'GI'=>'Gibraltar',
				'GR'=>'Greece',
				'GL'=>'Greenland',
				'GD'=>'Grenada',
				'GP'=>'Guadeloupe',
				'GU'=>'Guam',
				'GT'=>'Guatemala',
				'GN'=>'Guinea',
				'GW'=>'Guinea-Bissau',
				'GY'=>'Guyana',
				'HT'=>'Haiti',
				'HM'=>'Heard And McDonald Islands',
				'HN'=>'Honduras',
				'HK'=>'Hong Kong',
				'HU'=>'Hungary',
				'IS'=>'Iceland',
				'IN'=>'India',
				'ID'=>'Indonesia',
				'IR'=>'Iran',
				'IQ'=>'Iraq',
				'IE'=>'Ireland',
				'IL'=>'Israel',
				'IT'=>'Italy',
				'JM'=>'Jamaica',
				'JP'=>'Japan',
				'JO'=>'Jordan',
				'KZ'=>'Kazakhstan',
				'KE'=>'Kenya',
				'KI'=>'Kiribati',
				'KW'=>'Kuwait',
				'KG'=>'Kyrgyzstan',
				'LA'=>'Laos',
				'LV'=>'Latvia',
				'LB'=>'Lebanon',
				'LS'=>'Lesotho',
				'LR'=>'Liberia',
				'LY'=>'Libya',
				'LI'=>'Liechtenstein',
				'LT'=>'Lithuania',
				'LU'=>'Luxembourg',
				'MO'=>'Macau',
				'MK'=>'Macedonia',
				'MG'=>'Madagascar',
				'MW'=>'Malawi',
				'MY'=>'Malaysia',
				'MV'=>'Maldives',
				'ML'=>'Mali',
				'MT'=>'Malta',
				'MH'=>'Marshall Islands',
				'MQ'=>'Martinique',
				'MR'=>'Mauritania',
				'MU'=>'Mauritius',
				'YT'=>'Mayotte',
				'MX'=>'Mexico',
				'FM'=>'Micronesia',
				'MD'=>'Moldova',
				'MC'=>'Monaco',
				'MN'=>'Mongolia',
				'MS'=>'Montserrat',
				'MA'=>'Morocco',
				'MZ'=>'Mozambique',
				'MM'=>'Myanmar (Burma)',
				'NA'=>'Namibia',
				'NR'=>'Nauru',
				'NP'=>'Nepal',
				'NL'=>'Netherlands',
				'AN'=>'Netherlands Antilles',
				'NC'=>'New Caledonia',
				'NZ'=>'New Zealand',
				'NI'=>'Nicaragua',
				'NE'=>'Niger',
				'NG'=>'Nigeria',
				'NU'=>'Niue',
				'NF'=>'Norfolk Island',
				'KP'=>'North Korea',
				'MP'=>'Northern Mariana Islands',
				'NO'=>'Norway',
				'OM'=>'Oman',
				'PK'=>'Pakistan',
				'PW'=>'Palau',
				'PA'=>'Panama',
				'PG'=>'Papua New Guinea',
				'PY'=>'Paraguay',
				'PE'=>'Peru',
				'PH'=>'Philippines',
				'PN'=>'Pitcairn',
				'PL'=>'Poland',
				'PT'=>'Portugal',
				'PR'=>'Puerto Rico',
				'QA'=>'Qatar',
				'RE'=>'Reunion',
				'RO'=>'Romania',
				'RU'=>'Russia',
				'RW'=>'Rwanda',
				'SH'=>'Saint Helena',
				'KN'=>'Saint Kitts And Nevis',
				'LC'=>'Saint Lucia',
				'PM'=>'Saint Pierre And Miquelon',
				'VC'=>'Saint Vincent And The Grenadines',
				'SM'=>'San Marino',
				'ST'=>'Sao Tome And Principe',
				'SA'=>'Saudi Arabia',
				'SN'=>'Senegal',
				'SC'=>'Seychelles',
				'SL'=>'Sierra Leone',
				'SG'=>'Singapore',
				'SK'=>'Slovak Republic',
				'SI'=>'Slovenia',
				'SB'=>'Solomon Islands',
				'SO'=>'Somalia',
				'ZA'=>'South Africa',
				'GS'=>'South Georgia And South Sandwich Islands',
				'KR'=>'South Korea',
				'ES'=>'Spain',
				'LK'=>'Sri Lanka',
				'SD'=>'Sudan',
				'SR'=>'Suriname',
				'SJ'=>'Svalbard And Jan Mayen',
				'SZ'=>'Swaziland',
				'SE'=>'Sweden',
				'CH'=>'Switzerland',
				'SY'=>'Syria',
				'TW'=>'Taiwan',
				'TJ'=>'Tajikistan',
				'TZ'=>'Tanzania',
				'TH'=>'Thailand',
				'TG'=>'Togo',
				'TK'=>'Tokelau',
				'TO'=>'Tonga',
				'TT'=>'Trinidad And Tobago',
				'TN'=>'Tunisia',
				'TR'=>'Turkey',
				'TM'=>'Turkmenistan',
				'TC'=>'Turks And Caicos Islands',
				'TV'=>'Tuvalu',
				'UG'=>'Uganda',
				'UA'=>'Ukraine',
				'AE'=>'United Arab Emirates',
				'UK'=>'United Kingdom',				
				'UM'=>'United States Minor Outlying Islands',
				'UY'=>'Uruguay',
				'UZ'=>'Uzbekistan',
				'VU'=>'Vanuatu',
				'VA'=>'Vatican City (Holy See)',
				'VE'=>'Venezuela',
				'VN'=>'Vietnam',
				'VG'=>'Virgin Islands (British)',
				'VI'=>'Virgin Islands (US)',
				'WF'=>'Wallis And Futuna Islands',
				'EH'=>'Western Sahara',
				'WS'=>'Western Samoa',
				'YE'=>'Yemen',
				'YU'=>'Yugoslavia',
				'ZM'=>'Zambia',
				'ZW'=>'Zimbabwe'
				)
    );
	
}

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

