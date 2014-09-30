<?php 
/**
 * CAFNR International Programs Template Tags
 *
 * @package   CAFNR International Programs
 */


/**
 * Produce the page code for the activity form, showing submitted info if available.
 *
 * Will take $_GET params for user (id) and activity_id
 * @return  html - generated code
 */
 

/* 
 * Describing CPT 'CAFNR Activity'
 *
 * custom field: value_type
 *
 * 	country_lead	: text
 * 	pi_radio		: Yes, No
 *	start_date		: date
 *	end_date		: date
 *	subject_textbox	: text
 * 	non_pi_role		: text
 *	is_pi			: Yes, No
 *	who_is_pi		: ID or text?
 *
 */
 
function cc_cafnr_activity_form_render( $post_id = null ){

	//if we've submitted!
	if( isset( $_POST['SubmitButton'] ) ){
		//Functionality to deal with $_POSTed form data
		echo "<div class='usr-msg'>Success in saving!</div>";
		
		if ( $_POST['new_activity'] == 'edit_activity' || $_POST['new_activity'] == 'new_activity' ) {
			//only logged-in users can submit this form
			if ( !is_user_logged_in() ) {
				wp_redirect( home_url() . NM_CUSTOM_REGISTER );
				exit;
			}
			
			
			if ( isset( $_POST['activity_id'] ) && ( $_POST['activity_id'] > 0 ) ){
				//update existing post
				$activity_id = $_POST['activity_id'];
				
				//update the post fields, if need be - just summary?
				$updating_post = array(
					'ID' => $activity_id,
					'post_content' => $_POST['activity_summary']
					);
				wp_update_post( $updating_post );
				
				$activity_name = get_the_title( $activity_id );
				
			} else if ( ( $_POST['cafnr_activity_name'] != '-1' ) && ( $_POST['cafnr_activity_name'] != 'add_new_activity' ) ){
					
				//new activity
				//get the activity title
				
				//we already have an id, so let's create a child post of same name, yes?
				$parent_activity_id = $_POST['cafnr_activity_name'];
				
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
				
			} else { //mel doesn't know what this is about..
				
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
			if ( $_POST['user_id'] > 0 ){
				$updating_post = array(
					'ID'	=>	$activity_id,
					'post_author'	=> $_POST['user_id']
					);
				wp_update_post( $updating_post );
				
				//Post author may not be activity owner (in the case of Ben filling out form for another faculty member) so we need post_meta field to capture true owner
				update_post_meta($activity_id, 'activity_owner', $_GET['user']);
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
					if ($_POST[$f] == '-1') { //defaults for country selects
						delete_post_meta($activity_id, $f);
					} else {
						update_post_meta( $activity_id, $f, $_POST[$f] );
					}
				}
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
			if ( isset ( $_POST['who_is_pi'] ) ){ //,'write_in_pi'
				if ( $_POST['who_is_pi'] == 'add_new_pi' ){
					update_post_meta( $activity_id, 'who_is_pi', "add_new_pi" );
					update_post_meta( $activity_id, 'write_in_pi', $_POST['write_in_pi'] );
				} else {
					update_post_meta( $activity_id, 'who_is_pi', $_POST['who_is_pi'] );
					delete_post_meta( $activity_id, 'write_in_pi' );
				}
			}
			//Activity type (the radio one)
			wp_set_object_terms( $activity_id, $_POST['activity_radio'], 'cafnr-activity-type' );
			
			//supplemental links - many inputs of same name
			if ( isset( $_POST['supplemental_links'] ) ) {
				//clean sweep on every save
				delete_post_meta( $activity_id, 'supplemental_links' );
				foreach( $_POST['supplemental_links'] as $link ) {
					if ( $link == "" ) continue;
					add_post_meta( $activity_id, 'supplemental_links', $link, false );  //false since not unique
				}
			}
			
			//collaborating people/institutions - many inputs of same name
			if ( isset( $_POST['collaborating'] ) ) {
				//clean sweep on every save
				delete_post_meta( $activity_id, 'collaborating' );
				foreach( $_POST['collaborating'] as $link ) {
					if ( $link == "" ) continue;
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
			
			if ( isset( $_POST['activity_file'] ) ) {
				$attachment = array(
					'post_title' => $activity_name . ' - ' . $activity_id . ' (Attachment)',
					'post_content' => '',
					'post_status' => 'publish',
					'post_mime_type' => $_POST['activity_file_type']
				);
				
				$attachment_id = wp_insert_attachment( $attachment, $_POST['activity_file'], $activity_id );	

			}
		
		//	
			//if successful, redirect to dashboard
			//wp_redirect( $location, $status );
			//exit;
		
		
		}
	}
	
	//TODO: this
	//get prior data if exists
	if ( !( is_null( $post_id ) ) ){
		//if we have a post_id, fill out the form
		
		//$post = get_post( $post_id );
		$action = 'edit_activity';
	} else if ( !( is_null( $_GET['activity_id'] ) ) ){
		echo $_GET['activity_id'];
		
		//Get post data, if we have ID in url
		$post_id = $_GET['activity_id'];
		
		/*$args = array(
			'p' => $post_id,
			'post_type'	=> 'cafnr-activity',
			'post_status' => 'publish'
			); */
			
		$this_activity = get_post($post_id);
		
		//get parent of post
		$this_activity_parent_holder = get_post_ancestors( $post_id ); //returns all parents
		$this_activity_parent = $this_activity_parent_holder[0]; //direct parent is [0] in returned array
		
		//get post meta and post taxonomy associated with this activity
		$this_activity_types = wp_get_post_terms( $this_activity->ID, 'cafnr-activity-type', array("fields" => "slugs") );
		$this_activity_fields = get_post_custom( $this_activity->ID );

		//fetch attachments of post
		$attach_args = array( 
			'post_type' => 'attachment', 
			'posts_per_page' => -1, 
			'post_status' =>'any', 
			'post_parent' => $this_activity->ID 
			);
			
		$this_activity_attachments = get_posts( $attach_args );
		
		//var_dump( ($this_activity_fields) );  //post_id int
		//var_dump( $this_activity_fields['activity_checkbox'] );  //post_id int
		
		
		$action = 'edit_activity';
	} else {
		$action = 'new_activity';
	}
	
	//get user from params (for now, since only admins will be able to access form..)
	if ( !( is_null( $_GET['user'] ) ) ){
		echo $_GET['user'];
		//$post_id = $_GET['activity_id'];
		$user = $_GET['user'];
	} else {
		$user = 0;
	}
	
	
	//get countries
	$countries = array();
	$countries = cc_cafnr_get_countries();
	
	//get all cafnr activities in db
	$activities = array();
	
	$args = array(
		'post_type'	=> 'cafnr-activity',
		'post_status' => 'publish',
		'posts_per_page' => '-1'
		);
	$activities = get_posts($args);
	
	$activities_array = array();
	
	//translate post objects into key=>value pairs (ID, name)
	foreach ( $activities as $post ){
		setup_postdata( $post ); 
		//remove posts with parents from list
		if( !empty( $post->post_parent ) ) continue; 
		$activities_array[$post->ID] = $post->post_title;
	
	}
	
	$group_members = cc_cafnr_get_member_array();
	?>
	
	<h3 class="gform_title">CAFNR International Programs</h3>
	
	<div class="gform_wrapper cafnr_activity">
		<form id="cafnr_activity_form" class="standard-form" method="post" action="">
			
			<input type="hidden" name="new_activity" value="<?php echo $action; ?>">
			<input type="hidden" name="activity_id" value="<?php echo $this_activity->ID; ?>">
			<input type="hidden" name="user_id" value="<?php echo $user; ?>">
			
			<li id="cafnr_master_type" class="gfield gfield_contains_required required">
			
				<label class="gfield_label">
					In the last 5 years, have you been in involved in ONE of the following activities outside of the United States? (please complete one form per activity)
					<span class="gfield_required">*</span>
				</label>
				<div class="ginput_container">
					<ul id="cafnr_activity_type_radio" class="gfield_radio">
						<li class="activity_radio">
							<input id="activity_radio_research" type="radio" onclick="" tabindex="1" value="funded-research-project" name="activity_radio" <?php if( in_array( 'funded-research-project', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_research">Funded Research Project</label>
						</li>
						<li class="activity_radio">
							<input id="activity_radio_training" type="radio" onclick="" tabindex="2" value="training-program" name="activity_radio" <?php if( in_array( 'training-program', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_training">Training Program</label>
						</li>
						<li class="activity_radio">
							<input id="activity_radio_visit" type="radio" onclick="" tabindex="3" value="professional-visit" name="activity_radio" <?php if( in_array( 'professional-visit', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_visit">Professional Visit</label>
						</li>
					</ul>
				</div>
			</li>
			
			<li id="cafnr_country" class="gfield gfield_contains_required required">
				<label class="gfield_label" for="input_22_8">
					Location
					<span class="gfield_required">*</span>
				</label>
				<div class="ginput_container ginput_list">
					<table class="gfield_list">
				<colgroup>
					<col id="gfield_list_8_col1" class="gfield_list_col_odd">
					<col id="gfield_list_8_col2" class="gfield_list_col_even">
				</colgroup>
				<thead>
					<tr>
						<th>Country</th>
						<th>City or Region</th>
						<th> </th>
					</tr>
				</thead>
				<tbody>
					<tr class="gfield_list_row_odd">
						<td class="gfield_list_cell gfield_list_8_cell1">
							<select tabindex="4" name="input_8[]">
								<?php
								foreach ( $countries as $key => $value ){ 
									$option_output = '<option value="';
									$option_output .= $key;
									$option_output .= '">';
									$option_output .= $value;
									$option_output .= '</option>';
									print $option_output;
									
								} ?>
								
							</select>
						</td>
						<td class="gfield_list_cell gfield_list_8_cell2">
							<input type="text" tabindex="5" value="" name="country[]">
						</td>
						<td class="gfield_list_icons">
							<img class="add_list_item " style="cursor:pointer; margin:0 3px;" onclick="gformAddListItem(this, 0)" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
							<img class="delete_list_item" onclick="gformDeleteListItem(this, 0)" style="cursor:pointer; visibility:hidden;" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
						</td>
					</tr>
				</tbody>
				</table>
				</div>
			</li>
			
			<li id="cafnr_activity_title" class="gfield gfield_contains_required required">
				<label class="gfield_label" for="cafnr_activity_name">
					Title of Activity
					<span class="gfield_required">*</span>
				</label>
				<div class="ginput_container">
					<select id="cafnr_activity_name" class="medium gfield_select" tabindex="6" onchange="" name="cafnr_activity_name">
						<option <?php if ( !isset( $post_id ) ) echo 'selected="selected"'; ?> value="-1">---Select---</option>
						<?php $count = 1;
						//echo $activities->found_posts;
						foreach ( $activities_array as $key => $value ){ 
							$option_output = '<option value="';
							$option_output .= $key;
							$option_output .= '"';
							if ( ( $key == $post_id ) || ( $key == $this_activity_parent) ) {
								$option_output .= 'selected="selected"';
							}
							$option_output .= '>';
							$option_output .= $value;
							$option_output .= '</option>';
							print $option_output;
							
						} ?>
						<option value="add_new_activity">ADD NEW ACTIVITY</option>
					</select>
				</div>
			</li>
			
			<li id="cafnr_add_activity_title" class="gfield no-title">
				<label class="gfield_label" for="input_22_10">Add Title of New Activity Here:</label>
				<div class="ginput_container">
					<input id="add_activity_title" class="medium" type="text" tabindex="7" value="" name="add_activity_title">
				</div>
			</li>
			
			<li id="cafnr_pi_radio" class="gfield gfield_contains_required required research-only" style="display: list-item;">
				<label class="gfield_label">
					Are you the PI/leader of this activity?
					<span class="gfield_required">*</span>
				</label>
				<div class="ginput_container">
					<ul id="input_22_24" class="gfield_radio">
						<li class="gchoice_24_0">
							<input id="pi_yes" type="radio" onclick="" tabindex="8" value="Yes" name="pi_radio" <?php checked( $this_activity_fields['pi_radio'][0], 'Yes' ); ?>>
							<label for="pi_yes">Yes</label>
						</li>
						<li class="gchoice_24_1">
							<input id="pi_no" type="radio" onclick="" tabindex="9" value="No" name="pi_radio" <?php checked( $this_activity_fields['pi_radio'][0], 'No' ); ?>>
							<label for="pi_no">No</label>
						</li>
					</ul>
				</div>
			</li>
			
			<li id="cafnr_who_is_pi" class="gfield non-pi-only research-only hidden-on-init" style="">
				<label class="gfield_label" for="cafnr_activity_pi">Who is the PI/leader of this activity?</label>
				<div class="ginput_container">
					<select id="who_is_pi" class="medium gfield_select" tabindex="10" name="who_is_pi">
						<option value="-1">---Select---</option>
						<option value="unknown">I DON'T KNOW</option>
						<?php foreach ( $group_members as $key => $value ) {
							$option_output = '<option value="';
							$option_output .= $key;
							$option_output .= '" ';
							$option_output .= selected( current( $this_activity_fields['who_is_pi'] ), $key );
							$option_output .= '>';
							$option_output .= $value;
							$option_output .= '</option>';
							print $option_output;
							
						} ?>
						<option value="add_new_pi">NOT IN THIS LIST (write-in)</option>
					</select>
				</div>
			</li>
			
			<li id="cafnr_add_pi" class="gfield no-title">
				<label class="gfield_label" for="input_22_10">PI Name</label>
				<div class="ginput_container">
					<input id="add_pi_name" class="medium" type="text" tabindex="7" value="" name="add_pi_name">
				</div>
			</li>
		
			<li id="cafnr_write_in_pi" class="gfield write-in-pi research-only hidden-on-init">
				<label class="gfield_label" for="input_22_34">Write in the name of the PI</label>
				<div class="ginput_container">
					<input id="write_in_pi" class="medium" type="text" tabindex="11" value="<?php echo current( $this_activity_fields['write_in_pi'] ); ?>" name="write_in_pi">
				</div>
			</li>
			
			<li id="cafnr_country_lead" class="gfield">
				<label class="gfield_label" for="input_22_34">Who is the in-country activity lead?</label>
				<div class="ginput_container">
					<input id="country_lead" class="medium" type="text" tabindex="11" value="<?php echo current( $this_activity_fields['country_lead'] ); ?>" name="country_lead">
				</div>
			</li>
		
			<li id="cafnr_activity_type_checkbox" class="gfield" style="">
				<label class="gfield_label">Type of Activity</label>
				<div class="ginput_container">
					<ul id="activity_type_checkbox" class="gfield_checkbox">
						<li class="gchoice_11_1">
							<input id="activity_checkbox_research" type="checkbox" tabindex="12" value="Research" onclick="" name="activity_checkbox[]" <?php if( !is_null( unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) echo ( in_array( 'Research', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_research">Research</label>
						</li>
						<li class="gchoice_11_2">
							<input id="activity_checkbox_training" type="checkbox" tabindex="13" value="Training" onclick="" name="activity_checkbox[]" <?php if( !is_null( unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) echo ( in_array( 'Training', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_training">Training</label>
						</li>
						<li class="gchoice_11_3">
							<input id="activity_checkbox_extension" type="checkbox" tabindex="14" value="Extension" onclick="" name="activity_checkbox[]" <?php if( !is_null( unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) echo ( in_array( 'Extension', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_extension">Extension</label>
						</li>
						<li class="gchoice_11_4">
							<input id="activity_checkbox_visit" type="checkbox" tabindex="15" value="Visit" onclick="" name="activity_checkbox[]" <?php if( !is_null( unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) echo ( in_array( 'Visit', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_visit">Visit</label>
						</li>
						<li class="gchoice_11_5">
							<input id="activity_checkbox_other" type="checkbox" tabindex="16" value="Other" onclick="" name="activity_checkbox[]" <?php if( !is_null( unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) echo ( in_array( 'Other', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_other">Other</label>
						</li>
					</ul>
				</div>
			</li>
			
			<li id="cafnr_subject_textbox" class="gfield">
				<label class="gfield_label" for="input_22_35">Academic Field, Research Focus, or Subject of Activity</label>
				<div class="ginput_container">
					<input id="subject_textbox" class="medium" type="text" tabindex="18" value="<?php echo current( $this_activity_fields['subject_textbox'] ); ?>" name="subject_textbox">
				</div>
				<div class="gfield_description">Example: Ag Econ, Climate Change, Biofuels, Ag Policy, etc.</div>
			</li>
			
			<li id="cafnr_start_date" class="gfield pi-only hidden-on-init" >
				<label class="gfield_label" for="start_date">Activity Start Date (approx.)</label>
				<div class="ginput_container">
					<input type="text" id="start_date" name="start_date" class="datepicker_with_icon datepicker" value="<?php if( !empty( $this_activity_fields['start_date'][0] ) ) { echo ( date( 'm/d/Y', strtotime( $this_activity_fields['start_date'][0] ) ) ); } ?>">
				</div>
			</li>
			
			<li id="cafnr_end_date" class="gfield pi-only hidden-on-init">
				<label class="gfield_label" for="end_date">Activity End Date (approx.)</label>
				<div class="ginput_container">
					<input type="text" id="end_date" name="end_date" class="datepicker_with_icon datepicker" value="<?php if( !empty( $this_activity_fields['end_date'][0] ) ) { echo ( date( 'm/d/Y', strtotime( $this_activity_fields['end_date'][0] ) ) ); } ?>">
				</div>
			</li>
			
			<li id="cafnr_collaborating" class="gfield">
				<label class="gfield_label" for="input_22_18">Can you identify collaborating partners & institutions?</label>
				<div class="ginput_container ginput_list">
					<table class="gfield_list">
						<colgroup>
							<col id="gfield_list_18_col1" class="gfield_list_col_odd">
						</colgroup>
						<tbody>
							<?php 
							$count = 1; //make sure the first one doesn't have a delete button
							if ( $this_activity_fields['collaborating'] ) { 
								foreach(  $this_activity_fields['collaborating'] as $link ) { ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell list_cell">
											<input type="text" tabindex="26" value="<?php echo $link; ?>" name="collaborating[]">
										</td>
										<td class="gfield_list_icons">
											<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
											<?php } ?>
										</td>
									</tr>
								<?php $count++; }
							} ?>
							<?php //make sure we have one empty input field ?>
							<tr class="gfield_list_row_odd">
								<td class="gfield_list_cell list_cell">
									<input type="text" tabindex="26" value="" name="collaborating[]">
								</td>
								<td class="gfield_list_icons">
									<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
									<?php } ?>
								</td>
							</tr>
							
						</tbody>
					</table>
				</div>
			</li>
		
			<li id="cafnr_activity_summary" class="gfield pi-only hidden-on-init">
				<label class="gfield_label" for="input_22_17">Please provide a brief summary of this activity.</label>
				<div class="ginput_container">
					<textarea id="activity_summary" class="textarea medium" cols="50" rows="10" tabindex="23" name="activity_summary" value=""><?php echo $this_activity->post_content; ?></textarea>
				</div>
			</li>
			
			<li id="cafnr_non_pi_role" class="gfield non-pi-only">
				<label class="gfield_label" for="input_22_26">What was your role in this activity?</label>
				<div class="ginput_container">
					<textarea id="non_pi_role" class="textarea medium" cols="50" rows="10" tabindex="24" name="non_pi_role" value=""><?php echo current( $this_activity_fields['non_pi_role'] ); ?></textarea>
				</div>
			</li>
		
			<li id="cafnr_funding_source" class="gfield pi-only hidden-on-init">
				<label class="gfield_label" for="input_22_38">What is the source of funding for this activity?</label>
				<div class="ginput_container">
					<input id="funding_source" class="medium" type="text" tabindex="25" name="funding_source" value="<?php echo current( $this_activity_fields['funding_source'] ); ?>">
				</div>
			</li>
		
			<li id="cafnr_supplemental_links" class="gfield">
				<label class="gfield_label" for="input_22_39">Do you have any LINKS to supplemental material you would like to provide?</label>
				<div class="ginput_container ginput_list">
					<table class="gfield_list">
						<colgroup>
							<col id="gfield_list_39_col1" class="gfield_list_col_odd">
						</colgroup>
						<tbody>
							<?php 
							$count = 1; //make sure the first one doesn't have a delete button
							if ( $this_activity_fields['supplemental_links'] ) { 
								foreach(  $this_activity_fields['supplemental_links'] as $link ) { ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell list_cell">
											<input type="text" tabindex="26" value="<?php echo $link; ?>" name="supplemental_links[]">
										</td>
										<td class="gfield_list_icons">
											<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
											<?php } ?>
										</td>
									</tr>
								<?php $count++; }
							} ?>
							<?php //make sure we have one empty input field ?>
							<tr class="gfield_list_row_odd">
								<td class="gfield_list_cell list_cell">
									<input type="text" tabindex="26" value="" name="supplemental_links[]">
								</td>
								<td class="gfield_list_icons">
									<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="gfield_description">This may include PPTs, Word Docs and PDFs, links to videos, and photos. </div>
			</li>
			<li id="cafnr_activity_upload" class="gfield">
				<label class="gfield_label" for="input_22_39">Do you have any supplemental material you would like to UPLOAD?</label>
			
					<p><span id="plupload-browse-button"><input type="button" value="Select files to upload..." /></span></p>
					<div id="plupload-upload-ui">
					<?php //echo get_the_post_thumbnail( $p->ID ) //get attachemtns here??>
					</div>

					<?php
					if ( $this_activity_attachments ) {
						foreach ( $this_activity_attachments as $attachment ) {
							echo apply_filters( 'the_title' , $attachment->post_title );
							the_attachment_link( $attachment->ID , false );
						}
					}
					?>
			</li>
			
		
		<input type="submit" name="SubmitButton" value="SUBMIT ACTIVITY" />
		
		</form>
	</div>
	
	
	
	
	<?php
}

function cc_cafnr_get_countries() {
	return array(
		'-1'=>'---Select---',
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
    );
}

/*
 * Returns array of members of CAFNR Group
 *
 * @params int Group_ID
 * @return array Array of Member ID => name
 */
function cc_cafnr_get_member_array( $group_id ){ 

	$group_id = cc_cafnr_get_group_id();
	global $bp;
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	if ( bp_group_has_members( array( 'group_id' => $group_id ) ) ) {
	
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			$group_members[bp_get_group_member_id()] = bp_get_group_member_name();
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	return $group_members;
	
}

function cc_cafnr_render_add_member_form(){
	
	$group_members = cc_cafnr_get_member_array( cc_cafnr_get_group_id() );
	global $uid;
	if( isset( $_POST['SubmitFaculty'] ) ){
		//echo 'Faculty Found!'; //mel's checks
		//echo "facultyid=" . $_POST['faculty_select'];
		
		$activities = cc_cafnr_get_faculty_activity_url_list( $_POST['faculty_select'] );
		//var_dump($activities);		
		cc_cafnr_render_faculty_activity_table( $activities );
		
		//If user selects --Select-- show nothing
		if ( $_POST['faculty_select'] == "-1" ) {
?>
			<script type="text/javascript">
				jQuery( document ).ready(function($) {
					$("#userinfo").hide();
					$("#newfacultydiv").hide();
				});
			</script>
<?php		
		} else {
			//if user selects adds new faculty, show newfacultydiv and hide other divs
			if ( $_POST['faculty_select'] == "add_new_faculty" ) {
?>
				<script type="text/javascript">
					jQuery( document ).ready(function($) {
						$("#activities").hide();
						$("#userinfo").hide();
						$("#newfacultydiv").show();
					});
				</script>
<?php	
		} else {
			//If user selects a faculty name, show userinfo form
			$user_info = get_userdata( $_POST['faculty_select'] );
			$uid = $_POST['faculty_select'];
			
			$all_meta_for_user = get_user_meta( $uid );
?>
				<script type="text/javascript">
					jQuery( document ).ready(function($) {
						$("#userID").val("<?php echo $_POST['faculty_select']; ?>");
						$("#activities").show();
						$("#userinfo").show();
						$("#newfacultydiv").hide();
						$("#cafnr_faculty_form").hide();
						$("#nameactivity").html("<?php echo $user_info->display_name; ?>'s Activities&nbsp;&nbsp;(<a href='/cafnr-intl-dashboard/'>change</a>)");
						
					});
				</script>
<?php
			}
		}
	} else {
?>
			<script type="text/javascript">
				jQuery( document ).ready(function($) {
					$("#userinfo").hide();
					$("#newfacultydiv").hide();
				});
			</script>
<?php	
	
	}

	
	$all_meta_for_user = get_user_meta( $uid );

	
	if (isset( $_POST['submitshortform'] )) {				
				if( isset( $_POST['userID'] ) ){
				
					$uid=$_POST['userID'];
					if ( isset ( $_POST['CVmethod'] ) ){
						update_user_meta( $uid, 'CVmethod', $_POST['CVmethod'] );
					}					
					if ( isset ( $_POST['CVlink'] ) ){
						update_user_meta( $uid, 'CVlink', $_POST['CVlink'] );
					}
					if ( isset ( $_POST['beyond5'] ) ){
						update_user_meta( $uid, 'beyond5', $_POST['beyond5'] );
					}
					if ( isset ( $_POST['futureactivity'] ) ){
						update_user_meta( $uid, 'futureactivity', $_POST['futureactivity'] );
					}
					if ( isset ( $_POST['leadassist'] ) ){
						update_user_meta( $uid, 'leadassist', $_POST['leadassist'] );
					}
					if ( isset ( $_POST['futurecontact'] ) ){
						update_user_meta( $uid, 'futurecontact', $_POST['futurecontact'] );
					}
					echo "Short Form Submitted!<br /><br />";

				}
	} else {
		//echo "nope";
	}
?>
	<form id="cafnr_faculty_form" class="standard-form" method="post" action="">
		<strong>Select a Faculty Member:</strong><br /><br />
		<select id="faculty_select" name="faculty_select" style="font-size:12pt;width:450px;">
			<option value="-1" selected="selected">---Select---</option>
			<option value="add_new_faculty">ADD NEW FACULTY</option>
			<?php foreach ( $group_members as $key => $value ) {
				$option_output = '<option value="';
				$option_output .= $key;
				$option_output .= '">';
				$option_output .= $value;
				$option_output .= '</option>';
				print $option_output;
				
			} ?>
		</select>
		

		<input type="submit" id="SubmitFaculty" name="SubmitFaculty" value="Go" style="font-size:12pt;" />
		
		<div id="newfacultydiv" style="margin-top:20px;"><strong>Add new Faculty Member:</strong><br /><br />
			<input type="text" id="newfaculty" size="50" />&nbsp;&nbsp;<input type="button" id="submitnewfaculty" value="Add New Faculty" />
		</div>
	</form>
	<div id="userinfo">
		<form id="cafnr_facultyadd_form" class="standard-form" method="post" action="">
			<br /><br />
			<input type="hidden" id="userID" name="userID" />
			<strong>Would you like to LINK to or UPLOAD your CV?</strong><br/>
			<input type="radio" id="CVmethod1" name="CVmethod" value="link" <?php if( $all_meta_for_user['CVmethod'][0] == "link") echo 'checked="checked"'; ?> />&nbsp;Link to my CV<br />
			<input type="radio" id="CVmethod2" name="CVmethod" value="upload" <?php if( $all_meta_for_user['CVmethod'][0] == "upload") echo 'checked="checked"'; ?> />&nbsp;Upload my CV
			
			<div id="linkDiv" style="display:none;">
				<br /><br />
				<strong>Add link to CV here:</strong><br/>	
				<input type="text" id="CVlink" name="CVlink" size="85" value="<?php echo $all_meta_for_user['CVlink'][0]; ?>" />
			</div>
			<div id="uploadDiv" style="display:none;">
				<br /><br />
				<strong>Upload CV here:</strong><br/>			
			</div>		
			<br /><br />
			<strong>Beyond the last five years, have you been involved in any international activities?</strong><br/>
			<input type="text" id="beyond5" name="beyond5" size="100" value="<?php echo $all_meta_for_user['beyond5'][0]; ?>" />
			<br /><br />
			<strong>Are you planning on engaging in any international activity in the future?</strong><br/>
			<input type="text" id="futureactivity" name="futureactivity" size="100" value="<?php echo $all_meta_for_user['futureactivity'][0]; ?>" />
			<br /><br />
			<strong>Would you be interested in leading or assisting with a project in your academic field or research focus?</strong><br/>
			<input type="text" id="leadassist" name="leadassist" size="100" value="<?php echo $all_meta_for_user['leadassist'][0]; ?>" />
			<br /><br />	
			<strong>In the future, would you prefer an online form or in-person interview?</strong><br/>
			<input type="radio" id="futurecontact1" name="futurecontact" value="online" <?php if( $all_meta_for_user['futurecontact'][0] == "online") echo 'checked="checked"'; ?> />&nbsp;Online form<br />
			<input type="radio" id="futurecontact2" name="futurecontact" value="interview" <?php if( $all_meta_for_user['futurecontact'][0] == "interview") echo 'checked="checked"'; ?> />&nbsp;Interview
			<br /><br />		
			<input type="submit" value="Submit" name="submitshortform" />
		</form>
	</div>	
<?php
	if ($all_meta_for_user['CVmethod'][0] == "link") {
?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				$("#linkDiv").show();
			});			
		</script>
<?php	
	} else if ($all_meta_for_user['CVmethod'][0] == "upload") {
?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				$("#uploadDiv").show();
			});			
		</script>
<?php
	}

}

/*
 * Returns array of activity names and links (to url form)
 *
 */
//TODO: expand this array to include ids and live links!
function cc_cafnr_get_faculty_activity_url_list( $user_id ){

	//this is where the faculty prior forms and bio stuff will render
	// $intl_args = array(
		// 'post_type' => 'cafnr-activity',
		// 'post_status' => 'publish',
		// 'author' => $user_id
		// );
	
	
	//TODO: talk to Mike about how we want to do this, searching by author or by meta, same diff..
	$intl_args = array(
		'post_type' => 'cafnr-activity',
		'post_status' => 'publish',	
		'meta_key' => 'activity_owner',
		'meta_value' => $user_id
	);
	$user_activity_posts = get_posts( $intl_args );
	//var_dump($user_activity_posts);
	$activity_list = array();
	$count = 1;
	foreach ( $user_activity_posts as $post ){
		setup_postdata( $post ); 
		
		//CAFNR_ACTIVITY_FORM_URL
		$url = get_site_url() . CAFNR_ACTIVITY_FORM_URL . '?activity_id=' . $post->ID;
		$activity_list[$count]['id'] = $post->ID;
		$activity_list[$count]['title'] = $post->post_title;
		$activity_list[$count]['form_url'] = $url;
		$activity_list[$count]['url'] = get_site_url() . '/' . $post->post_name;
		
		$count++;
	}

	//var_dump ($activity_list);
	return $activity_list;
}

/* 
 * Renders a table of activities already added for a faculty member
 *
 * @params array Associative array of names => links to forms
 *
 */
//TODO: expand this table after input array is expanded
function cc_cafnr_render_faculty_activity_table( $activities ) {
?>

	<div id="activities">
		
		<table id="box-table-a">
			<thead>
				<tr>
					<th scope="col" colspan="1"><span id="nameactivity"></span></th>	
					<th scope="col" colspan="3" style="text-align:right;"><input id="btnAddNewActivity" type="button" value="+ Add New Activity" /></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $activities as $key => $value ){ //TODO: add VIEW
					
					$id = $value["id"];
					$title = $value["title"];
					$url = $value["url"];
					$form_url = $value["form_url"];
					
				
				
					echo '<tr><td style="width:70%;">' . $title . '</td>';
					echo '<td style="width:10%;"><a href="' . $url . '" class="button">View</a></td>';
					echo '<td style="width:10%;"><a href="' . $form_url . '" class="button">Edit</a></td>';
					echo '<td style="width:10%;"><a href="#" class="button">Delete</a></td>';
					echo '</tr>';
				
				} ?>
			</tbody>
		</table>
	</div>
	
<?php
}

function cc_cafnr_add_member_save( $email, $group_id ){

	$group_id = cc_cafnr_get_group_id();
	$user_id = username_exists( $user_name );
	
	if ( !$user_id and email_exists($user_email) == false ) {
		$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
		$user_id = wp_create_user( $user_name, $random_password, $user_email );
	} else {
		$random_password = __('User already exists.  Password inherited.');
	}
	
	if( !is_numeric( $user_id ) || ( $user_id == 0 ) ){
	
		
	}
	/*When successful - this function returns the user ID of the created user. In case of failure 
	(username or email already exists) the function returns an error object, with these possible values and messages;

    empty_user_login, Cannot create a user with an empty login name.
    existing_user_login, This username is already registered.
    existing_user_email, This email address is already registered. */
	
	return $user_id;

}

?>