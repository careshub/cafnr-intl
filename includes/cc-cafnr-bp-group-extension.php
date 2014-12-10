<?php 
if ( class_exists( 'BP_Group_Extension' ) ) : // Recommended, to prevent problems during upgrade or when Groups are disabled

class CC_CAFNR_Intl_Extension extends BP_Group_Extension {

    function __construct() {
        $args = array(
            'slug' => cc_cafnr_get_slug(),
            'name' => 'Survey Dashboard',
            'visibility' => 'public',
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
	
		//cc_aha_render_tab_subnav();

        if ( cc_cafnr_on_survey_dashboard_screen() ) {
		
            cc_cafnr_intl_dashboard();

        } else if ( cc_cafnr_on_activity_screen() ) {

			cc_cafnr_activity_form_render();
		
        }
		
    }

    public function cafnr_tab_is_enabled(){

    	if ( cc_cafnr_is_cafnr_group() ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
bp_register_group_extension( 'CC_CAFNR_Intl_Extension' );
 
endif;