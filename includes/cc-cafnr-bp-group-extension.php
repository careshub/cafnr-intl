<?php 
if ( class_exists( 'BP_Group_Extension' ) ) : // Recommended, to prevent problems during upgrade or when Groups are disabled

class CC_CAFNR_Intl_Extension extends BP_Group_Extension {

    function __construct() {
        $args = array(
            'slug' => cc_aha_get_slug(),
            'name' => 'Survey Dashboard',
            'visibility' => 'private',
            'enable_nav_item'   => $this->cafnr_tab_is_enabled(),
            // 'access' => 'members',
            // 'show_tab' => 'members',
            'nav_item_position' => 15,
            // 'nav_item_name' => ccgn_get_tab_label(),
            'screens' => array(
                'edit' => array(
                  'enabled' => false,
                ),
                'create' => array(
                    'enabled' => false,
                    // 'position' => 100,
                ),
                'admin' => array(
                    'enabled' => false,
                ),


            ),
        );
        parent::init( $args );
    }
 
    public function display() {

        cc_aha_render_tab_subnav();

        if ( cc_aha_on_main_screen() ) {

            cc_aha_print_introductory_text();

        } else if ( cc_aha_on_survey_screen() ) {

                if ( ! cc_aha_user_can_do_assessment() ) {
                    echo '<div class="message info">Sorry, you do not have permission to view this page.</div>';
                } else {
                    // We'll store the "active" metro id in a cookie for persistence.
                    cc_aha_print_metro_select_container_markup();                    
                    // Get the right page of the form to display. bp_action_variable(1) is the page number
                    cc_aha_render_form( bp_action_variable(1) );
                }

        } else if ( cc_aha_on_analysis_screen() ) {
            // The revenue section is only available to users who can do the assessment or have been added to the secret access list.
            if ( cc_aha_on_analysis_screen( 'revenue' ) && ! ( cc_aha_user_can_do_assessment() || cc_aha_user_has_super_secret_clearance() ) ) {
                echo '<div class="message info">Sorry, you do not have permission to view this page.</div>';
            } else {
                // We'll store the selected metro id in a cookie for persistence.
                cc_aha_print_metro_select_container_markup();
                // Get the right summary page to display.
                cc_aha_render_summary_page();
            }
            
        } else if ( cc_aha_on_survey_quick_summary_screen() ) {
            cc_aha_render_all_questions_and_answers();
        }
    }

    public function cafnr_tab_is_enabled(){

    	if ( cc_aha_is_aha_group() ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
bp_register_group_extension( 'CC_CAFNR_Intl_Extension' );
 
endif;