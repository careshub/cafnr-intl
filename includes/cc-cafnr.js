

// .research-only; hide if #activity_radio_research is !checked
// pi-only; hide if pi_yes is !checked

function clickListen(){

	jQuery('input[name=activity_radio]').click(function() {
		if( jQuery(this).val() != "funded-research-project") {
			jQuery('.research-only').hide();
		} else {
			jQuery('.research-only').show();
		}
	});
	
	jQuery('input[name=pi_radio]').click(function() {
		if( jQuery(this).val() == "Yes") {
			jQuery('.pi-only').show();
			jQuery('.non-pi-only').hide();
		} else {
			jQuery('.pi-only').hide();
			jQuery('.non-pi-only').show();
		}
	});
	
	//show activity box if "ADD NEW ACTIVITY" is selected on form
	//var selected_option = jQuery('#cafnr_activity_name');
	
	jQuery('#cafnr_activity_name').change( function(){
		jQuery('#cafnr_activity_name option').each( function() {
			if( jQuery(this).is(':selected') && ( jQuery(this).val() == "add_new_activity") ){
				jQuery('.no-title').fadeIn();
			} else {
				jQuery('.no-title').fadeOut();
			}
		});
	});
	
	jQuery('#who_is_pi').change( function(){
		jQuery('#who_is_pi option').each( function() {
			if( jQuery(this).is(':selected') && ( jQuery(this).val() == "add_new_pi") ){
				jQuery('.write-in-pi').fadeIn();
			} else {
				jQuery('.write-in-pi').fadeOut();
			}
		});
	});
	
	//show new faculty box if 'add new faculty' is selected
	jQuery('#faculty_select').change( function(){
		jQuery('#faculty_select option').each( function() {
			if( jQuery(this).is(':selected') && ( jQuery(this).val() == "add_new_faculty") ){
				jQuery('#newfacultydiv').show();
				jQuery('#SubmitFaculty').hide();
			} else {
				jQuery('#newfacultydiv').hide();
				jQuery('#SubmitFaculty').show();
			}
		});
	});
	
	//add collaborating listener
	jQuery('.add_collaborating').on("click", addCollaborating );
	
	//remove collaborating listener
	jQuery('.delete_collaborating').on("click", deleteCollaborating );
	
	//add supplemental link listener
	jQuery('.add_supplemental_link').on("click", addSupplementalLink );
	
	//remove supplemental links listener
	jQuery('.delete_supplemental_link').on("click", deleteSupplementalLink );
}

//add collborating trs for saving goodness
function addCollaborating() {
	var whereToAppend = jQuery(this).parents('tbody');
	
	var whatToAppend = '<tr class="gfield_list_row_even"><td class="gfield_list_cell list_cell">';
	whatToAppend = whatToAppend + '<input type="text" tabindex="26" value="" name="collaborating[]"></td><td class="gfield_list_icons">';
	whatToAppend = whatToAppend + '<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">';
	whatToAppend = whatToAppend + '<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">';
	whatToAppend = whatToAppend + '</td></tr>';

	//add a new row
	whereToAppend.append(whatToAppend);
	
	//turn off click listeners (so no double-listening on existing divs)
	jQuery('.add_collaborating').off("click", addCollaborating );
	jQuery('.delete_collaborating').off("click", deleteCollaborating );
	
	//turn them back on so new rows get listened to, too
	jQuery('.add_collaborating').on("click", addCollaborating );
	jQuery('.delete_collaborating').on("click", deleteCollaborating );
}

function deleteCollaborating() {
	var whatToDelete = jQuery(this).parents('tr');
	
	whatToDelete.remove();
}

function addSupplementalLink() {
	var whereToAppend = jQuery(this).parents('tbody');
	//console.log('hello!');
	
	var whatToAppend = '<tr class="gfield_list_row_even"><td class="gfield_list_cell list_cell">';
	whatToAppend = whatToAppend + '<input type="text" tabindex="26" value="" name="supplemental_links[]"></td><td class="gfield_list_icons">';
	whatToAppend = whatToAppend + '<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">';
	whatToAppend = whatToAppend + '<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://dev.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">';
	whatToAppend = whatToAppend + '</td></tr>';

//add a new row
	whereToAppend.append(whatToAppend);
	
	//turn off click listeners (so no double-listening)
	jQuery('.add_supplemental_link').off("click", addSupplementalLink );
	jQuery('.delete_supplemental_link').off("click", deleteSupplementalLink );
	
	//turn them back on so new rows get listened to, too
	jQuery('.add_supplemental_link').on("click", addSupplementalLink );
	jQuery('.delete_supplemental_link').on("click", deleteSupplementalLink );
}

function deleteSupplementalLink() {
	var whatToDelete = jQuery(this).parents('tr');
	
	whatToDelete.remove();
}

//a function to make sure when post info is loaded into form, appropriate fields show automagically
function cafnrIntakeFormLoad(){

	//on form load, let's make sure right fields are displaying
	
	//if( jQuery('#pi_yes').is(':selected') ){
	if( jQuery('#pi_yes').val() == 'Yes' ){
	
		jQuery('.pi-only').removeClass('hidden-on-init');
		jQuery('.non-pi-only').addClass('hidden-on-init');
		//jQuery('#cafnr_end_date').show();
	}

	//if we're not doing a research program, hide .research-only
	if( !jQuery('#activity_radio_research').is(":checked") ){
		jQuery('.research-only').addClass('hidden-on-init');
	}
}










jQuery(document).ready(function($){
	//TODO: datepicker;
	//TODO: autocomplete for form..
	clickListen();
	
	cafnrIntakeFormLoad();
	
	jQuery( ".datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true
	});
	
	
},(jQuery))