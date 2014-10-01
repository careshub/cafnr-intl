
var pluploadVars = {
	dirty: true,
	lastSave: 0,
	ajaxBusy: false,
	infobarDefault: "somthing defaulty",
	imageUpload: null,
	activityUploads: new Array()
};
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
	
	jQuery('.reload-page').click(function() {
		//location.reload( true ); //true = reload from server, not from cache
		window.location = window.location.href; //to avoid POST warning.. for now, until we make GET page.
	});
	
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
	if( jQuery('#pi_yes').is(":checked") ){
	
		jQuery('.pi-only').removeClass('hidden-on-init');
		jQuery('.non-pi-only').addClass('hidden-on-init');
		//jQuery('#cafnr_end_date').show();
	} else {
		//otherwise, do the opposite
		jQuery('.pi-only').addClass('hidden-on-init');
		jQuery('.non-pi-only').removeClass('hidden-on-init');
	}

	//if we have a write-in pi, set the who_is_pi to the 'Write in PI' value to display the write_in_pi field
	if ( jQuery('#write_in_pi').val() != "" ) {
		//change select value
		jQuery('#who_is_pi').val("add_new_pi");
		//make sure write_in_pi field is visible
		jQuery('#cafnr_write_in_pi').removeClass('hidden-on-init');
	}
	
	//if we're not doing a research program, hide .research-only
	if( !jQuery('#activity_radio_research').is(":checked") ){
		jQuery('.research-only').addClass('hidden-on-init');
	}
	
	//plupload init stuff
	if (jQuery('#cafnr_activity_form').length) {
		
		//cafnr_countries();  //TODO: this
		
		//init plupolader 
		activityUploader('plupload-browse-button', 'plupload-upload-ui');
		
	} else {
		activityFormUnload();
	}
	
	jQuery('.remove-activity-file').on("click", function() {
		removeActivityFile();
	});
	
	
}

var activityFormUnload = function() {
	//plupload: uploader.destroy();
	for (var i = 0; i < pluploadVars.activityUploadVars.length; i++) {
		pluploadVars.activityUploads[i].destroy();
	}
	
}

function activityUploader( browseButton, uiContainer ){
	var uploader = new plupload.Uploader({
		runtimes:'html5,silverlight,flash,html4',
		/*flash_swf_url: cafnr_ajax.pluploadSWF,
		silverlight_xap_url: cafnr_ajax.pluploadXAP, */
		browse_button: browseButton,
		multi_selection: true,
		file_data_name: 'activity_uploads',
		max_file_size: '2mb',
		url: cafnr_ajax.adminAjax,
		filters: [{title:'Activity Uploads', extensions:'pdf,mp3,jpg,jpeg,gif,png'}],  //TODO: add more extensions
		multipart_params: { action: 'activity_upload' }
	});

	//store reference to this object for later removal
	pluploadVars.activityUploads.push(uploader);

	uploader.init();

	uploader.bind('FilesAdded', function(up, files){
		up.start();
		
	});

	uploader.bind('UploadProgress', function(up, file) {
		jQuery('#' + uiContainer).html('<p class="ie9hide">' + file.percent + '% complete...</p><p class="red">Please wait to save your progress until your file has finished uploading</p>');
	});

	uploader.bind('Error', function(up, err) {
		if (err.code == -600) { //file size
			jQuery('#' + uiContainer).html("<p class='red'>I'm sorry, this file is too large.  2MB or less, please.</p>");
		} else if (err.code == -601) { //file type
			jQuery('#' + uiContainer).html("<p class='red'>I'm sorry, we accept only PDFs or MP3s.</p>");
		} else {
			jQuery('#' + uiContainer).html("<p class='red'>Sorry, there was an error. Please try again.</p>");
		}
	});

	uploader.bind('FileUploaded', function(up, file, response){
		if (response.response) {
			//response.response = file, url, type, fileBaseName
			var activityFile = eval('(' + response.response + ')');
			
			//To do: add display html here for the types..
			var activityFileHtml = "<span><p>File uploaded: " + activityFile.fileBaseName + "<input class='remove-activity-file' type='button' value='Remove this sample' data-deletefile='" + activityFile.file + "' ></p>" + 
				"File name: <input type='text' name='' value=''>" + activityFile.fileBaseName + "</input>" +
				"<input type='hidden' name='activity_file' value='" + activityFile.file + "' />" +
				"<input type='hidden' name='activity_file_type' value='" + activityFile.type + "' /></span>";
			jQuery('#' + uiContainer).after(activityFileHtml).show('slow', function(){
				jQuery('#plupload-upload-ui .ie9hide').hide();
				jQuery('#plupload-upload-ui .red').hide();
				
			});
			
			//add remove listeners to this 
			jQuery('.remove-activity-file').off("click", removeActivityFile);
			jQuery('.remove-activity-file').on("click", {
				uploader: uploader,
				file: file
				}, removeActivityFile );
			
			jQuery('#' + browseButton).html('<input type="button" value="Select another file to upload..." />');
			//jQuery( '<input class="remove-activity-file" type="button" value="Remove this sample" data-deletefile="' + activityFile.file + '" >' ).insertAfter( '#' + browseButton );
			
		} else {
			jQuery('#' + uiContainer).html('<p>Sorry, there was an error. Please try again.</p>');
		}
	});
}

function removeActivityFile( uploaderInput ){
	//var fileurl = jQuery(this).data('deletefile');
	
	
	//jQuery(this).parents('span').remove();
	//remove file from queue (doesn't seem to be removing it from uploads folder, hmm)
	var errormaybe = uploaderInput.data.uploader.removeFile( uploaderInput.data.file );
	var totalFileSpan = jQuery(this).parents('span');
	
	jQuery(this).parents('span').append(errormaybe);
	totalFileSpan.fadeOut(500, function() { 
		totalFileSpan.remove(); 
	});
	
	// jQuery(this).parents('span').siblings()
	console.log('file allegedly removed now');
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