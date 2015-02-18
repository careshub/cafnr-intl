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

	//render the subnav..
	cc_cafnr_render_tab_subnav();
		
	
	//get prior activity data if exists
	if ( !( is_null( $post_id ) ) ){ //if the function has the post_id set
		//if we have a post_id, fill out the form
		
		$action = 'edit_activity';
	} else if ( !( is_null( $_GET['activity_id'] ) ) ){ //if we're editing an existing activity
		//echo $_GET['activity_id'];
		
		//Get post data, if we have ID in url
		$post_id = $_GET['activity_id'];
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

		//Get Activity checkbox vars
		$this_activity_checkbox = array();
		if ( !empty ( $this_activity_fields['activity_checkbox'] ) ) {
			$this_activity_checkbox = maybe_unserialize( $this_activity_fields['activity_checkbox'] );
		}
		
		//var_dump ($this_activity_types);
		
		//var_dump( ($this_activity_fields) );  //post_id int
		//var_dump( $this_activity_fields['activity_checkbox'] );  //post_id int
		
		
		$action = 'edit_activity';
	} else {
		$action = 'new_activity';
	}
	
	//get current user info for id and access
	$current_user = wp_get_current_user();  //$current_user->ID
	
	//get user from params IF current user has permissions
	if ( !( is_null( $_GET['user'] ) ) && ( bp_group_is_admin() || bp_group_is_mod() ) ){ 
		//echo $_GET['user'];
		//$post_id = $_GET['activity_id'];
		$user = $_GET['user'];
		
	} else { //get user from current user ID
		$user = $current_user->ID;
	}
	
	$user_info = get_userdata( $user );
    $username = $user_info->user_login;
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;
		
	//get all cafnr activities in db
	$activities = array();
	
	$args = array(
		'post_type'	=> 'cafnr-activity',
		'post_status' => 'publish',
		'posts_per_page' => '-1',
		'orderby'=> 'title', 
		'order' => 'ASC'
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
	
	//if we're admin and have no user info, render user selection drop down and make it required
	$user_selection_required = false;
	if ( is_null( $_GET['user'] ) && ( bp_group_is_admin() || bp_group_is_mod() ) ){
	
		$user_selection_required = true; 
	
	}
	?>
	
	<h4 class="user-msg"></h4>
	
	<h3 class="gform_title">CAFNR International Programs Engagement Survey</h3>
	
	<?php 
	if( $user_selection_required ){
	?>
		<strong class="required">Select a Faculty Member&nbsp;</strong><span class="gfield_required">*</span><br /><br />
		<select id="faculty_select_activity_form" name="faculty_select" style="font-size:12pt;width:450px;">
			<option value="-1" selected="selected">---Select---</option>
			<!--<option value="add_new_faculty">ADD NEW FACULTY</option>-->
			<?php foreach ( $group_members as $key => $value ) {
				$option_output = '<option value="';
				$option_output .= $key;
				$option_output .= '">';
				$option_output .= $value;
				$option_output .= '</option>';
				print $option_output;
				
			} ?>
		</select>

	<?php	
	} 
	?>
	
	<div class="gform_wrapper cafnr_activity">
		<form id="cafnr_activity_form" class="standard-form" method="post" action="">
			
			<input type="hidden" name="new_activity" value="<?php echo $action; ?>">
			<input type="hidden" name="activity_id" value="<?php echo $this_activity->ID; ?>">
			<input type="hidden" name="user_id" value="<?php echo $user; ?>">
			<input type="hidden" name="user_name" value="<?php echo $username; ?>">
			<input type="hidden" name="first_name" value="<?php echo $first_name; ?>">
			<input type="hidden" name="last_name" value="<?php echo $last_name; ?>">
			
			<li id="cafnr_master_type" class="gfield gfield_contains_required required">
			
				<label class="gfield_label">
					In the last 5 years, have you been in involved in ONE of the following activities outside of the United States?<br />&nbsp;(please complete one form per activity)
					<span class="gfield_required">*</span>
				</label>
				<div class="ginput_container">
					<ul id="cafnr_activity_type_radio" class="gfield_radio">
						<li class="activity_radio">
							<input id="activity_radio_research" type="radio" onclick="" tabindex="1" value="funded-research-project" name="activity_radio" <?php if( !empty( $this_activity_types) ) if ( in_array( 'funded-research-project', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_research">Funded Research Project</label>
						</li>
						<li class="activity_radio">
							<input id="activity_radio_training" type="radio" onclick="" tabindex="2" value="training-program" name="activity_radio" <?php if( !empty( $this_activity_types) ) if ( in_array( 'training-program', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_training">Training Program</label>
						</li>
						<li class="activity_radio">
							<input id="activity_radio_visit" type="radio" onclick="" tabindex="3" value="professional-visit" name="activity_radio" <?php if( !empty( $this_activity_types) ) if ( in_array( 'professional-visit', $this_activity_types ) ) echo 'checked="checked"'; ?>>
							<label for="activity_radio_visit">Professional Visit</label>
						</li>
					</ul>
				</div>
			</li>
			
			<?php //populated via javascript function (for repeater goodness) ?>
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
							<?php $count = 1;
							if ( $this_activity_fields['country'] ) {  //make sure the first one doesn't have a delete button
								foreach( $this_activity_fields['country'] as $country ) { 
									$country = maybe_unserialize( $country ); 
									//echo $country[0] ; ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell">
											<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countryvalue="<?php echo $country[0]; ?>" data-countrycount="<?php echo $count; ?>">
											</select>
										</td>
										<td class="gfield_list_cell">
											<input type="text" tabindex="4" value="<?php echo $country[1]; ?>" name="region-<?php echo $count; ?>">
										</td>
										
										<td class="gfield_list_icons">
											<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
											<?php } ?>
										</td>
									</tr>
								<?php $count++; }
							} ?>
							<?php //make sure we have one empty input field ?>
							<tr class="gfield_list_row_odd">
								<td class="gfield_list_cell">
									<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countrycount="<?php echo $count; ?>">
									</select>
								</td>
								<td class="gfield_list_cell">
									<input type="text" tabindex="4" value="" name="region-<?php echo $count; ?>">
								</td>
								<td class="gfield_list_icons">
									<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
									<?php } ?>
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
						<option value="add_new_activity">ADD NEW ENGAGEMENT</option>
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
					</select>
				</div>
			</li>
			
			<li id="cafnr_add_activity_title" class="gfield no-title">
				<label class="gfield_label" for="input_22_10">Add Title of New Engagement Here:</label>
				<div class="ginput_container">
					<input id="add_activity_title" class="medium" type="text" tabindex="7" value="" name="add_activity_title">
				</div>
			</li>
			
			<li id="cafnr_pi_radio" class="gfield gfield_contains_required required research-only">
				<label class="gfield_label">
					Are you the PI/leader of this engagement?
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
				<label class="gfield_label" for="cafnr_activity_pi">Who is the PI/leader of this engagement?</label>
				<div class="ginput_container">
					<select id="who_is_pi" class="medium gfield_select" tabindex="10" name="who_is_pi">
						<option value="-1">---Select---</option>
						<option value="unknown">I DON'T KNOW</option>
						<?php foreach ( $group_members as $key => $value ) {
							$option_output = '<option value="';
							$option_output .= $key;
							$option_output .= '">';
							$option_output .= $value;
							$option_output .= '</option>';
							print $option_output;
							
						} ?>
						<option value="add_new_pi">NOT IN THIS LIST (write-in)</option>
					</select>
				</div>
			</li>
			
			<!-- Mel isn't sure this is relevant anymore - 12/29/14
			<li id="cafnr_add_pi" class="gfield non-pi-only research-only hidden-on-init">
				<label class="gfield_label" for="input_22_10">PI Name</label>
				<div class="ginput_container">
					<input id="add_pi_name" class="medium" type="text" tabindex="11" value="" name="add_pi_name">
				</div>
			</li>
			-->
		
			<li id="cafnr_write_in_pi" class="gfield write-in-pi hidden-on-init research-only">
				<label class="gfield_label" for="input_22_34">Write in the name of the PI</label>
				<div class="ginput_container">
					<input id="write_in_pi" class="medium" type="text" tabindex="12" value="" name="write_in_pi">
				</div>
			</li>
			
			<li id="cafnr_country_lead" class="gfield">
				<label class="gfield_label" for="input_22_34">Who is the in-country activity lead?</label>
				<div class="ginput_container">
					<input id="country_lead" class="medium" type="text" tabindex="13" value="<?php if( !empty( $this_activity_fields['country_lead'] )) echo current( $this_activity_fields['country_lead'] ); ?>" name="country_lead">
				</div>
			</li>
		
			<li id="cafnr_activity_type_checkbox" class="gfield" style="">
				<label class="gfield_label">Type of Activity</label>
				<div class="ginput_container">
					<ul id="activity_type_checkbox" class="gfield_checkbox">
						<li class="gchoice_11_1">
							<input id="activity_checkbox_research" type="checkbox" tabindex="14" value="Research" onclick="" name="activity_checkbox[]" <?php if( !empty( $this_activity_checkbox ) ) echo ( in_array( 'Research', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_research">Research</label>
						</li>
						<li class="gchoice_11_2">
							<input id="activity_checkbox_training" type="checkbox" tabindex="15" value="Training" onclick="" name="activity_checkbox[]" <?php if( !empty( $this_activity_checkbox ) ) echo ( in_array( 'Training', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_training">Training</label>
						</li>
						<li class="gchoice_11_3">
							<input id="activity_checkbox_extension" type="checkbox" tabindex="16" value="Extension" onclick="" name="activity_checkbox[]" <?php if( !empty( $this_activity_checkbox ) ) echo ( in_array( 'Extension', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_extension">Extension</label>
						</li>
						<li class="gchoice_11_4">
							<input id="activity_checkbox_visit" type="checkbox" tabindex="17" value="Visit" onclick="" name="activity_checkbox[]" <?php if( !empty( $this_activity_checkbox ) ) echo ( in_array( 'Visit', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_visit">Visit</label>
						</li>
						<li class="gchoice_11_5">
							<input id="activity_checkbox_other" type="checkbox" tabindex="18" value="Other" onclick="" name="activity_checkbox[]" <?php if( !empty( $this_activity_checkbox ) ) echo ( in_array( 'Other', unserialize( current( $this_activity_fields['activity_checkbox'] ) ) ) ) ? 'checked="checked"' : ''; ?>>
							<label for="activity_checkbox_other">Other</label>
						</li>
					</ul>
				</div>
			</li>
			
			<li id="cafnr_subject_textbox" class="gfield">
				<label class="gfield_label" for="input_22_35">Academic Field, Research Focus, or Subject of Activity</label>
				<div class="ginput_container">
					<input id="subject_textbox" class="medium" type="text" tabindex="19" value="<?php if( !empty( $this_activity_fields['subject_textbox'] )) echo current( $this_activity_fields['subject_textbox'] ); ?>" name="subject_textbox">
				</div>
				<div class="gfield_description">Example: Ag Econ, Climate Change, Biofuels, Ag Policy, etc.</div>
			</li>
			
			<li id="cafnr_start_date" class="gfield pi-only hidden-on-init" >
				<label class="gfield_label" for="start_date">Activity Start Date (approx.)</label>
				<div class="ginput_container">
					<input type="text" id="start_date" name="start_date" tabindex="20" class="datepicker_with_icon datepicker" value="<?php if( !empty( $this_activity_fields['start_date'][0] ) ) { echo ( date( 'm/d/Y', strtotime( $this_activity_fields['start_date'][0] ) ) ); } ?>">
				</div>
			</li>
			
			<li id="cafnr_end_date" class="gfield pi-only hidden-on-init">
				<label class="gfield_label" for="end_date">Activity End Date (approx.)</label>
				<div class="ginput_container">
					<input type="text" id="end_date" name="end_date" tabindex="21" class="datepicker_with_icon datepicker" value="<?php if( !empty( $this_activity_fields['end_date'][0] ) ) { echo ( date( 'm/d/Y', strtotime( $this_activity_fields['end_date'][0] ) ) ); } ?>">
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
							<?php $count = 1;
							if ( $this_activity_fields['collaborating'] ) {  //make sure the first one doesn't have a delete button
								foreach(  $this_activity_fields['collaborating'] as $link ) { ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell list_cell">
											<input type="text" tabindex="22" value="<?php echo $link; ?>" name="collaborating[]">
										</td>
										<td class="gfield_list_icons">
											<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
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
									<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
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
					<textarea id="non_pi_role" class="textarea medium" cols="50" rows="10" tabindex="24" name="non_pi_role" value=""><?php if( !empty( $this_activity_fields['non_pi_role'] )) echo current( $this_activity_fields['non_pi_role'] ); ?></textarea>
				</div>
			</li>
		
			<li id="cafnr_funding_source" class="gfield pi-only hidden-on-init">
				<label class="gfield_label" for="input_22_38">What is the source of funding for this activity?</label>
				<div class="ginput_container">
					<input id="funding_source" class="medium" type="text" tabindex="25" name="funding_source" value="<?php if( !empty( $this_activity_fields['funding_source'] )) echo current( $this_activity_fields['funding_source'] ); ?>">
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
							<?php $count = 1;
							if ( $this_activity_fields['supplemental_links'] ) {  //make sure the first one doesn't have a delete button
								foreach(  $this_activity_fields['supplemental_links'] as $link ) { ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell list_cell">
											<input type="text" tabindex="26" value="<?php echo $link; ?>" name="supplemental_links[]">
										</td>
										<td class="gfield_list_icons">
											<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
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
									<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
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
			
					<p><a id="plupload-browse-button"><input type="button" value="Select a file to upload..."></a></p>
					<div id="plupload-upload-ui">
					<?php //echo get_the_post_thumbnail( $p->ID ) //get attachemtns here??>
					</div>

					<?php
					if ( $this_activity_attachments ) {
						$count = 1;
						echo '<div id="cafnr_upload_list"><h2>Files already uploaded: </h2><ul>';
						foreach ( $this_activity_attachments as $attachment ) {
							echo '<li>';
							
							$attachment_link = wp_get_attachment_url( $attachment->ID );
							echo "<a href='" . $attachment_link . "' target='_blank'>" . apply_filters( 'the_title' , $attachment->post_title ) . "</a>";
							echo "&nbsp;&nbsp;<input class='remove-activity-upload' name='remove-activity-upload' type='button' value='Remove this Upload' data-deleteupload='" . $attachment->ID . "' >";
							echo "<input type='hidden' class='activity_file_count' data-filecount='" . $count . "' name='activity_file_count-" . $count . "' value='" . $count . "' />";
							echo '</li>';
							$count++;
						}
						echo '</ul></div>';
					}
					?>
			</li>
			
		<?php
		//TODO: reroute after or have confirmation message (w/activity id in url)
		?>
		<input type="submit" id="activity-submit" name="SubmitButton" value="SUBMIT ENGAGEMENT" />
		
		</form>
	</div>
	
	<?php
}


function cc_cafnr_render_mod_admin_page(){
	
	
	//render the subnav..
	cc_cafnr_render_tab_subnav();
	
	$group_members = cc_cafnr_get_member_array();
	//get array of ALL activity objects
	$activity_args = array(
		'post_type' => 'cafnr-activity',
		'post_status' => 'publish'
	);
	$activities_query = new WP_Query( $activity_args );
	
	global $uid;
	if( isset( $_POST['SubmitFaculty'] ) ){
		//echo 'Faculty Found!'; //mel's checks
		//echo "facultyid=" . $_POST['faculty_select'];
		
		$activities = cc_cafnr_get_faculty_activity_url_list( $_POST['faculty_select'] );
		//var_dump($activities);		
		cc_cafnr_render_faculty_activity_table( $activities, $_POST['faculty_select'] );
		
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
		} else if ( $_POST['faculty_select'] == "add_new_faculty" ) {
			//if user selects adds new faculty, show newfacultydiv and hide other divs
			
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
						$("#nameactivity").html("<?php echo $user_info->display_name; ?>'s Engagements&nbsp;&nbsp;(<a class='reload-page'>select different faculty</a>)");
						
					});
				</script>
<?php
			
		}
	} else if (	!empty( $_GET['user'] )) {	
	
		cc_cafnr_render_tab_subnav();
	
		$activities = cc_cafnr_get_faculty_activity_url_list( $_GET['user'] );			
		
		cc_cafnr_render_faculty_activity_table( $activities, $_GET['user'] );			
		$user_info = get_userdata( $_GET['user'] );
		$uid = $_GET['user'];
		
		$all_meta_for_user = get_user_meta( $uid );
?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				$("#userID").val("<?php echo $_GET['user']; ?>");
				$("#activities").show();
				$("#userinfo").show();
				$("#newfacultydiv").hide();
				$("#cafnr_faculty_form").hide();
				$("#nameactivity").html("<?php echo $user_info->display_name; ?>'s Engagements&nbsp;&nbsp;(<a href='" + cafnr_ajax.surveyDashboard + "'>select different faculty</a>)");
				
			});
		</script>
<?php

	
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
		
			$uid = $_POST['userID'];
			if ( isset ( $_POST['CVmethod'] ) ){
				update_user_meta( $uid, 'CVmethod', $_POST['CVmethod'] );
			}					
			if ( isset ( $_POST['CVlink'] ) ){
				update_user_meta( $uid, 'CVlink', $_POST['CVlink'] );
				//if they linked to a cv, delete the uploaded one.  TODO: make sure this is cool w/ folks
				if ( isset( $_POST['old-CVfile'] ) ){
					//remove existing file
					$delete_success = wp_delete_attachment( $_POST['old-CVfile'] );
				}
			}
			if ( isset ( $_POST['beyond5'] ) ){
				update_user_meta( $uid, 'beyond5', $_POST['beyond5'] );
			}
			if ( isset ( $_POST['futureactivity'] ) ){
				update_user_meta( $uid, 'futureactivity', $_POST['futureactivity'] );
			}
			
			//"If so, wcan you identify the country or countries?"
			$i = 1;
			
			//first, remove all prior countries in database
			delete_post_meta( $uid, 'country' );
			
			while ( isset($_POST['countrylist-'.$i] ) ) {
				
				//create array to hold country meta
				$country_meta_array = array();
				
				if ( $_POST['countrylist-'.$i] != "" ) {
					$country_meta_array[] = $_POST['countrylist-'.$i];
					if ( isset( $_POST['region-'.$i] ) ) {
						$country_meta_array[] = $_POST['region-'.$i];
					}
					$success = add_user_meta( $uid, 'country', $country_meta_array );
					//echo $success;
				}
				//unset array
				unset( $country_meta_array );
				$i++;
			}
			
			if ( isset ( $_POST['leadassist'] ) ){
				update_user_meta( $uid, 'leadassist', $_POST['leadassist'] );
			}
			if ( isset ( $_POST['futurecontact'] ) ){
				update_user_meta( $uid, 'futurecontact', $_POST['futurecontact'] );
			}
			//we're going to store the cv file as an attachment, so we can delete it through WP on change
			if ( isset( $_POST['user_file_url'] ) ) {
				//if we have an attachment already, delete it
				if ( isset( $_POST['old-CVfile'] ) ){
					//remove existing file
					$delete_success = wp_delete_attachment( $_POST['old-CVfile'] );
					delete_user_meta( $uid, 'CVfile' );
				}
				
				//insert attachement (no parent) and update user meta
				$attachment = array(
					'guid'           => $_POST['user_file_url'], 
					'post_mime_type' => $_POST['user_file_type'],
					'post_title'     => $_POST['user_file_basename'],
					'post_content'   => '',
					'post_status'    => 'publish'
				);

				$attach_id = wp_insert_attachment( $attachment, $_POST['user_file_url'] );
				update_user_meta( $uid, 'CVfile', $attach_id );
			}
			echo "User info updated!<br /><br />";

		}
	} else {
		//echo "nope";
	}
?>

	<form id="cafnr_faculty_form" class="standard-form" method="post" action="">
		<strong>Select a Faculty Member to view/edit their Engagements:</strong><br /><br />
		<select id="faculty_select" class="activity_page" name="faculty_select" style="font-size:12pt;width:450px;">
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
		
		<div id="newfacultydiv" style="margin-top:35px;border-top:1px #888888 solid;">
			<br /><br />
			<strong>Add new Faculty Member E-Mail Address (REQUIRED):</strong><br /><br />
			<input type="text" id="newfacultyemail" size="50" />
			<br /><br />
			<strong>Add Display Name (optional):</strong><br /><br />
			<input type="text" id="displayname" size="50" />
			<br /><br />
			<strong>Add First Name (optional):</strong><br /><br />
			<input type="text" id="firstname" size="50" />
			<br /><br />
			<strong>Add Last Name (optional):</strong><br /><br />
			<input type="text" id="lastname" size="50" />
			<br /><br /><br />
			<input type="button" id="submitnewfaculty" value="Add New Faculty" />
			<br /><br />			
		</div>
	</form>
	
	<div id="userinfo">
		<form id="cafnr_facultyadd_form" class="standard-form" method="post" action="">
			<br /><br />
			<input type="hidden" id="userID" name="userID" />
			<strong>LINK to or UPLOAD <?php echo $user_info->display_name; ?>'s CV?</strong><br/>
			<input type="radio" id="CVmethod1" name="CVmethod" value="link" <?php if( $all_meta_for_user['CVmethod'][0] == "link") echo 'checked="checked"'; ?> />&nbsp;Link to my CV<br />
			<input type="radio" id="CVmethod2" name="CVmethod" value="upload" <?php if( $all_meta_for_user['CVmethod'][0] == "upload") echo 'checked="checked"'; ?> />&nbsp;Upload my CV
			
			<div id="linkDiv" style="display:none;">
				<br /><br />
				<strong>Add link to CV here:</strong><br/>	
				<input type="text" id="CVlink" name="CVlink" size="85" value="<?php echo $all_meta_for_user['CVlink'][0]; ?>" />
			</div>
			<div id="uploadDiv" style="display:none;">
				<br /><br />
				<?php if ( $all_meta_for_user['CVfile'][0] != "" ){
					echo '<strong>Uploaded CV:</strong><br/>';
					echo '<a href="' . wp_get_attachment_url( $all_meta_for_user["CVfile"][0] ) . '" target="_blank">' . "Link to CV" . '</a>';
					echo '<p><a id="user-plupload-browse-button"><input type="button" value="Select a different file to upload..."></a></p>';
					echo '<input type="hidden" name="old-CVfile" value="' . $all_meta_for_user['CVfile'][0] . '" />';
					echo '<div id="user-plupload-upload-ui"></div>';
				} else { ?>
					<strong>Upload CV here:</strong><br/>
					<p><a id="user-plupload-browse-button"><input type="button" value="Select a file to upload..."></a></p>
					<div id="user-plupload-upload-ui"></div>
				<?php } ?>
				
			</div>		
			<br /><br />
			<strong>Beyond the last five years, has <?php echo $user_info->display_name; ?> been involved in any international activities?</strong><br/>
			<input type="text" id="beyond5" name="beyond5" size="100" value="<?php echo $all_meta_for_user['beyond5'][0]; ?>" />
			<br /><br />
			
			<strong>Is <?php echo $user_info->display_name;?> planning on engaging in any international activity in the future? If so, can you identify the country or countries?</strong><br/>
			<input type="text" id="futureactivity" name="futureactivity" size="100" value="<?php echo $all_meta_for_user['futureactivity'][0]; ?>" />
			<br /><br />
			<?php //populated via javascript function (for repeater goodness) ?>
			<div id="cafnr_country" class="gfield gfield_contains_required required">
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
							<?php $count = 1;
							if ( $all_meta_for_user['country'] ) {  //make sure the first one doesn't have a delete button
								foreach( $all_meta_for_user['country'] as $country ) { 
									$country = maybe_unserialize( $country ); 
									//echo $country[0] ; ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell">
											<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countryvalue="<?php echo $country[0]; ?>" data-countrycount="<?php echo $count; ?>">
											</select>
										</td>
										<td class="gfield_list_cell">
											<input type="text" tabindex="4" value="<?php echo $country[1]; ?>" name="region-<?php echo $count; ?>">
										</td>
										
										<td class="gfield_list_icons">
											<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
											<?php } ?>
										</td>
									</tr>
								<?php $count++; }
							} ?>
							<?php //make sure we have one empty input field ?>
							<tr class="gfield_list_row_odd">
								<td class="gfield_list_cell">
									<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countrycount="<?php echo $count; ?>">
									</select>
								</td>
								<td class="gfield_list_cell">
									<input type="text" tabindex="4" value="" name="region-<?php echo $count; ?>">
								</td>
								<td class="gfield_list_icons">
									<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<br />
			<strong>Would <?php echo $user_info->display_name;?> be interested in leading or assisting with a project in their academic field or research focus?</strong><br/>
			<input type="text" id="leadassist" name="leadassist" size="100" value="<?php echo $all_meta_for_user['leadassist'][0]; ?>" />
			<br /><br />	
			<strong>In the future, would <?php echo $user_info->display_name; ?> prefer an online form or in-person interview?</strong><br/>
			<input type="radio" id="futurecontact1" name="futurecontact" value="online" <?php if( $all_meta_for_user['futurecontact'][0] == "online") echo 'checked="checked"'; ?> />&nbsp;Online form<br />
			<input type="radio" id="futurecontact2" name="futurecontact" value="interview" <?php if( $all_meta_for_user['futurecontact'][0] == "interview") echo 'checked="checked"'; ?> />&nbsp;Interview
			<br /><br />		
			<input type="submit" value="Submit" name="submitshortform" />
		</form>
	</div>	
	<div class="modal"></div>	
	
	
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

function cc_cafnr_render_member_page(){
		
	//get info for current user
	$current_user = wp_get_current_user();
	
	/*
	echo 'Username: ' . $current_user->user_login . '<br />';
    echo 'User email: ' . $current_user->user_email . '<br />';
    echo 'User first name: ' . $current_user->user_firstname . '<br />';
    echo 'User last name: ' . $current_user->user_lastname . '<br />';
    echo 'User display name: ' . $current_user->display_name . '<br />';
    echo 'User ID: ' . $current_user->ID . '<br />';
	*/
	
	//render the subnav
	cc_cafnr_render_tab_subnav();
	
	$activities = cc_cafnr_get_faculty_activity_url_list( $current_user->ID );
	
	//render the activities box
	cc_cafnr_render_faculty_activity_table( $activities, $current_user->ID );	
	
	$all_meta_for_user = get_user_meta( $current_user->ID );
?>
		<script type="text/javascript">
			jQuery( document ).ready(function($) {
				$("#userID").val("<?php echo $current_user->ID; ?>");
				$("#activities").show();
				$("#userinfo").show();
				$("#newfacultydiv").hide();
				$("#cafnr_faculty_form").hide();
				$("#nameactivity").html("<?php echo $current_user->display_name; ?>'s Engagements");
				
			});
		</script>
<?php
	
	
	if (isset( $_POST['submitshortform'] )) {				
		if( isset( $_POST['userID'] ) ){
		
			$uid=$_POST['userID'];
			if ( isset ( $_POST['CVmethod'] ) ){
				update_user_meta( $uid, 'CVmethod', $_POST['CVmethod'] );
			}					
			if ( isset ( $_POST['CVlink'] ) ){
				update_user_meta( $uid, 'CVlink', $_POST['CVlink'] );
				//if they linked to a cv, delete the uploaded one.  TODO: make sure this is cool w/ folks
				if ( isset( $_POST['old-CVfile'] ) ){
					//remove existing file
					$delete_success = wp_delete_attachment( $_POST['old-CVfile'] );
				}
			}
			if ( isset ( $_POST['beyond5'] ) ){
				update_user_meta( $uid, 'beyond5', $_POST['beyond5'] );
			}
			if ( isset ( $_POST['futureactivity'] ) ){
				update_user_meta( $uid, 'futureactivity', $_POST['futureactivity'] );
			}
			
			//"If so, wcan you identify the country or countries?"
			$i = 1;
			
			//first, remove all prior countries in database
			delete_post_meta( $uid, 'country' );
			
			while ( isset($_POST['countrylist-'.$i] ) ) {
				
				//create array to hold country meta
				$country_meta_array = array();
				
				if ( $_POST['countrylist-'.$i] != "" ) {
					$country_meta_array[] = $_POST['countrylist-'.$i];
					if ( isset( $_POST['region-'.$i] ) ) {
						$country_meta_array[] = $_POST['region-'.$i];
					}
					$success = add_user_meta( $uid, 'country', $country_meta_array );
					//echo $success;
				}
				//unset array
				unset( $country_meta_array );
				$i++;
			}
			
			
			if ( isset ( $_POST['leadassist'] ) ){
				update_user_meta( $uid, 'leadassist', $_POST['leadassist'] );
			}
			if ( isset ( $_POST['futurecontact'] ) ){
				update_user_meta( $uid, 'futurecontact', $_POST['futurecontact'] );
			}
			//we're going to store the cv file as an attachment, so we can delete it through WP on change
			if ( isset( $_POST['user_file_url'] ) ) {
				//if we have an attachment already, delete it
				if ( isset( $_POST['old-CVfile'] ) ){
					//remove existing file
					$delete_success = wp_delete_attachment( $_POST['old-CVfile'] );
					delete_user_meta( $uid, 'CVfile' );
				}
				
				//insert attachement (no parent) and update user meta
				$attachment = array(
					'guid'           => $_POST['user_file_url'], 
					'post_mime_type' => $_POST['user_file_type'],
					'post_title'     => $_POST['user_file_basename'],
					'post_content'   => '',
					'post_status'    => 'publish'
				);

				$attach_id = wp_insert_attachment( $attachment, $_POST['user_file_url'] );
				update_user_meta( $uid, 'CVfile', $attach_id );
			}
			echo "Short Form Submitted!<br /><br />";

		}
	} else {
		//echo "nope";
	}
?>
	
	<div id="userinfo">
		<h3>User Information</h3>
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
				
				<?php if ( $all_meta_for_user['CVfile'][0] != "" ){
					echo '<strong>Uploaded CV:</strong><br/>';
					echo '<a href="' . wp_get_attachment_url( $all_meta_for_user["CVfile"][0] ) . '" target="_blank">' . "Link to CV" . '</a>';
					echo '<p><a id="user-plupload-browse-button"><input type="button" value="Select a different file to upload..."></a></p>';
					echo '<input type="hidden" name="old-CVfile" value="' . $all_meta_for_user['CVfile'][0] . '" />';
					echo '<div id="user-plupload-upload-ui"></div>';
				} else { ?>
					<strong>Upload CV here:</strong><br/>
					<p><a id="user-plupload-browse-button"><input type="button" value="Select a file to upload..."></a></p>
					<div id="user-plupload-upload-ui"></div>
				<?php } ?>
				
			</div>		
			<br /><br />
			<strong>Beyond the last five years, have you been involved in any international activities?</strong><br/>
			<input type="text" id="beyond5" name="beyond5" size="100" value="<?php echo $all_meta_for_user['beyond5'][0]; ?>" />
			<br /><br />
			<strong>Are you planning on engaging in any international activity in the future? If so, can you identify the country or countries?</strong><br/>
			<input type="text" id="futureactivity" name="futureactivity" size="100" value="<?php echo $all_meta_for_user['futureactivity'][0]; ?>" />
			<br /><br />
			<?php //populated via javascript function (for repeater goodness) ?>
			<div id="cafnr_country" class="gfield gfield_contains_required required">
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
							<?php $count = 1;
							if ( $all_meta_for_user['country'] ) {  //make sure the first one doesn't have a delete button
								foreach( $all_meta_for_user['country'] as $country ) { 
									$country = maybe_unserialize( $country ); 
									//echo $country[0] ; ?>
									<tr class="gfield_list_row_odd">
										<td class="gfield_list_cell">
											<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countryvalue="<?php echo $country[0]; ?>" data-countrycount="<?php echo $count; ?>">
											</select>
										</td>
										<td class="gfield_list_cell">
											<input type="text" tabindex="4" value="<?php echo $country[1]; ?>" name="region-<?php echo $count; ?>">
										</td>
										
										<td class="gfield_list_icons">
											<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
											<?php if( $count!= 1 ) { ?>
												<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
											<?php } ?>
										</td>
									</tr>
								<?php $count++; }
							} ?>
							<?php //make sure we have one empty input field ?>
							<tr class="gfield_list_row_odd">
								<td class="gfield_list_cell">
									<select tabindex="4" name="countrylist-<?php echo $count; ?>" class="countrylist" data-countrycount="<?php echo $count; ?>">
									</select>
								</td>
								<td class="gfield_list_cell">
									<input type="text" tabindex="4" value="" name="region-<?php echo $count; ?>">
								</td>
								<td class="gfield_list_icons">
									<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">
									<?php if( $count!= 1 ) { ?>
										<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
			<br />
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
 * Renders a table of activities already added for a faculty member
 *
 * @params array Associative array of names => links to forms
 *
 */
//TODO: expand this table after input array is expanded
function cc_cafnr_render_faculty_activity_table( $activities, $which_user ) {
?>

	<div id="activities">
		
		<table class="mu-table">
			<thead>
				<tr>
					<th scope="col" colspan="3"><span id="nameactivity"></span></th>	
					<?php if ( bp_group_is_admin() || bp_group_is_mod() ) { ?>
						<th class="add-activity-button" scope="col" colspan="3"><a href="<?php echo cc_cafnr_get_activity_permalink() . "?user=" . $which_user; ?>" class="button">+ Add New Engagement</a></th>
					<?php } else { ?>
						<th class="add-activity-button" scope="col" colspan="3"><a href="<?php echo cc_cafnr_get_activity_permalink(); ?>" class="button">+ Add New Engagement</a></th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach ( $activities as $key => $value ){ //TODO: add VIEW
					
					//var_dump( $activities );
					$id = $value["id"];
					$title = $value["title"];
					$url = $value["url"];
					$form_url = $value["form_url"];
					//$activity_owner = $value["activity_owner"];				
					$author = $value["author"];		
					$postmeta = $value["postmeta"];

					//Information to show in quick view
					//TODO: account for NULL vals to get rid of warnings..
					if( $postmeta["subject_textbox"] != NULL ) {
						$subject = current( $postmeta["subject_textbox"] );
					} else {
						$subject = "";
					}
					if( $postmeta["start_date"] != NULL ) {
						$start_timestamp = strtotime (current( $postmeta["start_date"] ) );
					} else {
						$start_timestamp = 0;
					}
					if( $postmeta["start_date"] != NULL ) {
						$end_timestamp = strtotime (current( $postmeta["end_date"] ) );
					} else {
						$end_timestamp = 0;
					}
						
					
					echo '<tr><td colspan="3">' . $title . '</td>';
					//echo '<td style="width:10%;"><a href="' . $url . '" class="button">View</a></td>';
					echo '<td class="edit-activity-button"><a href="#" class="button" onclick="delActivity(' . $id . ', ' . $author . ')">Delete</a></td>';
					echo '<td class="edit-activity-button"><a href="' . $form_url . '" class="button">Edit</a></td>';
					echo '<td class="edit-activity-button"><a class="button quick-view-activity" data-activityid="' . $id . '">Quick View</a></td>';
					echo '</tr>';
					?>
					<tr class="hidden quick-view-tr" colspan="6" data-activityid="<?php echo $id; ?>">
						<td>Academic Field, Research Focus, or Subject of Activity: <?php if( $subject == "" ) { echo '<em>None provided</em>'; } else { echo '<strong>' . $subject . '</strong>'; } ?></td>
					</tr>
					<tr class="hidden quick-view-tr" data-activityid="<?php echo $id; ?>">
						<td>Start Date: <?php if( $start_timestamp == 0) { echo '<em>No start date provided</em>'; } else { echo '<strong>' . date('m/d/Y', $start_timestamp) . '</strong>'; } ?> </td>
					</tr>
					<tr class="hidden quick-view-tr" data-activityid="<?php echo $id; ?>">
						<td>End Date: <?php if( $end_timestamp == 0) { echo '<em>No end date provided</em>'; } else { echo '<strong>' . date('m/d/Y', $end_timestamp) . '</strong>'; } ?></td>
					</tr>
					<?php 
					echo '<div class="quick-view-info">';
					echo '<tr class="hidden quick-view-info" data-activityid="' . $id . '">';
					echo '<td>stuff</td>';
					echo '</tr>';
					echo '</div>';
				
				} ?>
			</tbody>
		</table>
	</div>
<?php
}


/* 
 * Renders a table of all activities added to system
 *
 * @params array Associative array of names => links to post
 *
 */
//TODO: add search filters to here?
//TODO: maybe show adminks edit link, too
function cc_cafnr_render_all_activity_table( $activities ) {
?>

	<div id="activities">
		
		<table class="mu-table">
			<thead>
				<tr>
					<th scope="col" colspan="6"><span id="nameactivity">All Engagements</span></th>	
					
				</tr>
			</thead>
			<tbody>
				<?php 
				foreach ( $activities as $key => $value ){ //TODO: add VIEW
					
					$id = $value["id"];
					$title = $value["title"];
					$url = $value["url"];
					$form_url = $value["form_url"];
					//$activity_owner = $value["activity_owner"];				
					$author = $value["author"];		
					$postmeta = $value["postmeta"];
					
					//Information to show in quick view
					//TODO: account for NULL vals to get rid of warnings..
					if( $postmeta["subject_textbox"] != NULL ) {
						$subject = current( $postmeta["subject_textbox"] );
					} else {
						$subject = "";
					}
					if( $postmeta["start_date"] != NULL ) {
						$start_timestamp = strtotime (current( $postmeta["start_date"] ) );
					} else {
						$start_timestamp = 0;
					}
					if( $postmeta["start_date"] != NULL ) {
						$end_timestamp = strtotime (current( $postmeta["end_date"] ) );
					} else {
						$end_timestamp = 0;
					}
					
					//var_dump( $start_timestamp );
					//is this the pi's writeup?
					$is_pi = false;
					if( $postmeta["pi_radio"] != NULL ) {
						if( current( $postmeta["pi_radio"]) == "Yes" ){ 
							$is_pi = true;
						}
					}
					//var_dump( $postmeta);
					
					?>
					
					<tr>
						<td colspan="3" class="<?php if( $is_pi ){ }?>"><?php echo $title; ?></td>
						
						<td><?php echo cc_cafnr_get_readable( "activity-type", current( $postmeta["activity_radio"] ) ); ?></td>
						
						<?php 
						if( bp_group_is_admin() || bp_group_is_mod() ) { ?>
							<td class="edit-activity-button"><a href="#" class="button" onclick="delActivity( <?php echo $id . ', ' . $author; ?>)">Delete</a></td>
						
							<td class="edit-activity-button"><a href="<?php echo $form_url; ?>" class="button">Edit</a></td>
						<?php 
						} ?>
						<td class="edit-activity-button"><a class="button quick-view-activity" data-activityid="<?php echo $id; ?>" >Quick View</a></td>
					</tr>
					
					<tr class="hidden quick-view-tr" colspan="6" data-activityid="<?php echo $id; ?>">
						<td>Academic Field, Research Focus, or Subject of Activity: <?php if( $subject == "" ) { echo '<em>None provided</em>'; } else { echo '<strong>' . $subject . '</strong>'; } ?></td>
					</tr>
					<tr class="hidden quick-view-tr" data-activityid="<?php echo $id; ?>">
						<td>Start Date: <?php if( $start_timestamp == 0) { echo '<em>No start date provided</em>'; } else { echo '<strong>' . date('m/d/Y', $start_timestamp) . '</strong>'; } ?> </td>
					</tr>
					<tr class="hidden quick-view-tr" data-activityid="<?php echo $id; ?>">
						<td>End Date: <?php if( $end_timestamp == 0) { echo '<em>No end date provided</em>'; } else { echo '<strong>' . date('m/d/Y', $end_timestamp) . '</strong>'; } ?></td>
					</tr>
					<?php
				} ?>
			</tbody>
		</table>
	</div>
<?php
}

//Search activities
function cc_cafnr_render_activity_search(){

	$countries = get_countries_for_all_activities();
	$countries = array_unique( $countries );
	sort($countries);
?>

	<table id="activity-search" class="mu-table">
		<thead>
			<tr>
				<th scope="col" colspan="6"><span id="nameactivity">Search Engagements</span></th>	
				
			</tr>
		</thead>
		<tbody>
			<tr class="search-row">
				<td colspan="2">
					<input id="search-text" name="search-text" type="text" placeholder="search by name, title"></input>
				</td>
				<td>
					<select id="search-country">
						<option value="-1" class="greyed">-- Search by Country --</option>
						<?php foreach( $countries as $country ){ ?>
							<option value="<?php echo $country; ?>"><?php echo $country; ?></option>
						<?php } ?>
					</select>
				</td>
				
				<td></td>
				<td></td>
				
				<td>
					<a id="submit-activity-search" class="button alignright">Search</a>
				</td>
				
			</tr>
			<tr class="search-results-header hidden">
				<td class="user-msg">
				</td>
			</tr>
			
		</tbody>

	</table>
<?php
}


function cc_cafnr_all_activities_render(){
	
	//render the subnav..
	cc_cafnr_render_tab_subnav();

	//print search function..
	cc_cafnr_render_activity_search();
	
	$activities = cc_cafnr_get_activity_list();
	
	cc_cafnr_render_all_activity_table( $activities );
}

/*
 * Renders Add Faculty Form
 *
 *
 */
function cc_cafnr_add_faculty_render(){

	cc_cafnr_render_tab_subnav();
?>
	<div class="user-msg"></div>
	<form id="cafnr_faculty_form" class="standard-form" method="post" action="">
		
		<div id="newfacultydiv_page" style="">

			<strong>Add new Faculty Member E-Mail Address (REQUIRED):</strong><br /><br />
			<input type="text" id="newfacultyemail" size="50" />
			<br /><br />
			<strong>Add Display Name (optional):</strong><br /><br />
			<input type="text" id="displayname" size="50" />
			<br /><br />
			<strong>Add First Name (optional):</strong><br /><br />
			<input type="text" id="firstname" size="50" />
			<br /><br />
			<strong>Add Last Name (optional):</strong><br /><br />
			<input type="text" id="lastname" size="50" />
			<br /><br /><br />
			<input type="button" id="submitnewfaculty" class="no_reroute" value="Add New Faculty" />
			<br /><br />			
		</div>
	</form>


<?php
}



/*** UTILITY FUNCTIONS ****/

/*
 * Returns array of members of CAFNR Group
 *
 * @params int Group_ID
 * @return array Array of Member ID => name
 */
function cc_cafnr_get_member_array( ){

	global $bp;
	$group_id = cc_cafnr_get_group_id();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	//var_dump($group);
	
	//set up group member array for drop downs
	$group_members = array();
	if ( bp_group_has_members( array( 'group_id' => $group_id, 'per_page' => 9999 ) ) ) {
	
		//iterate through group members, creating array for form list (drop down)
		while ( bp_group_members() ) : bp_group_the_member(); 
			$group_members[bp_get_group_member_id()] = bp_get_group_member_name();
		endwhile; 
		
		//var_dump ($group_members);  //works!
	}
	
	return $group_members;
	
}


/*
 * Returns array of activity names and links, by user id (to url form)
 *
 */
//TODO: expand this array to include ids and live links!
function cc_cafnr_get_faculty_activity_url_list( $user_id ){

	//this is where the faculty prior forms and bio stuff will render
	
	$intl_args = array(
		'post_type' => 'cafnr-activity',
		'post_status' => 'publish',	
	//	'meta_key' => 'activity_owner',
		'posts_per_page' => -1,
		'author' => $user_id
	//	'meta_value' => $user_id
	);
	$user_activity_posts = get_posts( $intl_args );
	//var_dump($user_activity_posts);
	$activity_list = array();
	$count = 1;
	foreach ( $user_activity_posts as $post ){
		setup_postdata( $post ); 
		
		//CAFNR_ACTIVITY_FORM_URL
		if ( bp_group_is_admin() || bp_group_is_mod() ) {
			$url = cc_cafnr_get_activity_permalink() . '?activity_id=' . $post->ID . '&user=' . $post->post_author;
		} else {
			$url = cc_cafnr_get_activity_permalink() . '?activity_id=' . $post->ID;
		}
		
		$activity_list[$count]['id'] = $post->ID;
		$activity_list[$count]['title'] = $post->post_title;
		$activity_list[$count]['form_url'] = $url;
		$activity_list[$count]['url'] = get_site_url() . '/' . $post->post_name;
		//$activity_list[$count]['activity_owner'] = $post->activity_owner;
		$activity_list[$count]['author'] = $post->post_author;
		$activity_list[$count]['postmeta'] = get_post_meta( $post->ID );
		
		$count++;
	}

	//var_dump ($activity_list);
	return $activity_list;
}


/*
 * Returns array of all activity names and links (to activity view - TODO)
 *
 */
//TODO: filters through this function
function cc_cafnr_get_activity_list( $user_activity_posts = null ){

	//this is where the faculty prior forms and bio stuff will render
	if ( $user_activity_posts == null ){
		$intl_args = array(
			'post_type' => 'cafnr-activity',
			'post_status' => 'publish',	
			'posts_per_page' => -1
		);
		
		$user_activity_posts = get_posts( $intl_args );
	}
	
	//var_dump($user_activity_posts);
	$activity_list = array();
	$count = 1;
	foreach ( $user_activity_posts as $post ){
		setup_postdata( $post ); 
		
		//View post
		$url = the_permalink();
		
		//get basic info about the activity
		$activity_list[$count]['id'] = $post->ID;
		$activity_list[$count]['title'] = $post->post_title;
		$activity_list[$count]['form_url'] = $url;
		$activity_list[$count]['url'] = get_site_url() . '/' . $post->post_name;
		//$activity_list[$count]['activity_owner'] = $post->activity_owner;
		$activity_list[$count]['author'] = $post->post_author;
		
		$activity_list[$count]['postmeta'] = get_post_meta( $post->ID );
		
		
		
		$count++;
	}

	//var_dump ($activity_list);
	return $activity_list;
}

/* 
 * Returns array of post objects, filtered by a country 
 *
 * @param array, string Array of post objects, country code
 * @return array Array of post objects
 */
function filter_posts_by_country( $post_objects, $search_country_code ){
	
	$filtered_posts = array();
	$country_meta;
	$country_codes;
	
	//get postmeta for countries on post objects
	foreach( $post_objects as $post ){
		setup_postdata( $post ); 
		
		$country_meta = get_post_meta( $post->ID, 'country' );
		$country_codes = maybe_unserialize( $country_meta ); //array of arrays of strings
		foreach( $country_codes as $country ){ //correct
			if( $country[0] == $search_country_code ){
				array_push( $filtered_posts, $post );
				
			}
			//var_dump( $country[0] );
		}
		//var_dump( $country_codes );
	
	}
	return $filtered_posts;
}

/* 
 * Returns array of country codes of only countries with activities
 *
 * @return array Array of Country codes
 */
function get_countries_for_all_activities() {
	
	$activities = cc_cafnr_get_activity_list();
	$countries = array(); //array of project arrays of countries
	
	//so much nesting..seriously!
	foreach( $activities as $key => $value ){
		foreach( $value as $k => $v ){
			if( $k == 'postmeta' ) {
				foreach( $v as $postmeta_key => $postmeta_value ){
					if( $postmeta_key == "country" ){
						foreach( $postmeta_value as $meta){
							array_push( $countries, current( maybe_unserialize( $meta ) ) );
							//var_dump( current( maybe_unserialize( $meta ) ) );
						}
					}
				}
				//var_dump( $v );
			}
			//var_dump( $k );
		}
		//var_dump( $key );
	}
	//var_dump( $countries );
	return( $countries );

}


/**
 * Builds the subnav of the CAFNR survey group tab
 *
 * @since   1.0.0
 * @return  HTML
 */
function cc_cafnr_render_tab_subnav(){

	if ( bp_group_is_admin() || bp_group_is_mod() ) { 
	?>
		<div id="subnav" class="item-list-tabs no-ajax">
			<ul class="nav-tabs">
				<li <?php if ( cc_cafnr_on_survey_dashboard_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_cafnr_get_home_permalink(); ?>">Engagements by Faculty</a>
				</li>
				<li <?php if ( cc_cafnr_on_activity_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_cafnr_get_activity_permalink(); ?>">Add Engagement</a>
				</li>
				<li <?php if ( cc_cafnr_on_all_activities_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_cafnr_get_all_activities_permalink(); ?>">All Engagements</a>
				</li>
				<li <?php if ( cc_cafnr_add_faculty_screen() ) { echo 'class="current"'; } ?>>
					<a href="<?php echo cc_cafnr_add_faculty_permalink(); ?>">Add Faculty</a>
				</li>
					
				
			</ul>
		</div>
	
	
	<?php
	} else { 
	?>
	<div id="subnav" class="item-list-tabs no-ajax">
		<ul class="nav-tabs">
			<li <?php if ( cc_cafnr_on_survey_dashboard_screen() ) { echo 'class="current"'; } ?>>
				<a href="<?php echo cc_cafnr_get_home_permalink(); ?>">My Engagements</a>
			</li>
			<li <?php if ( cc_cafnr_on_activity_screen() ) { echo 'class="current"'; } ?>>
				<a href="<?php echo cc_cafnr_get_activity_permalink(); ?>">Add Engagement</a>
			</li>
			<li <?php if ( cc_cafnr_on_all_activities_screen() ) { echo 'class="current"'; } ?>>
				<a href="<?php echo cc_cafnr_get_all_activities_permalink(); ?>">All Engagements</a>
			</li>
				
			
		</ul>
	</div>
	<?php 
	}
}

?>