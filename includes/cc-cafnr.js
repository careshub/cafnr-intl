
var pluploadVars = {
	dirty: true,
	lastSave: 0,
	ajaxBusy: false,
	infobarDefault: "somthing defaulty",
	imageUpload: null,
	activityUploads: new Array()
};

var userPluploadVars = {
	dirty: true,
	lastSave: 0,
	ajaxBusy: false,
	infobarDefault: "somthing defaulty",
	imageUpload: null,
	userUploads: new Array()
};


// .research-only; hide if #activity_radio_research is !checked
// pi-only; hide if pi_yes is !checked

function clickListen(){

	//Detect changes to activity radio buttons
	jQuery('input[name=activity_radio]').click(function() {
		if( jQuery(this).val() != "funded-research-project") {
			jQuery('.research-only').hide();
		} else {
			jQuery('.research-only').show();
			if ( jQuery('input[name=pi_radio]').val() == "Yes" ) {
				jQuery('.write-in-pi').hide();
				jQuery('.pi-only').show();
				jQuery('.non-pi-only').hide();
			} else {
				jQuery('.pi-only').hide();
				jQuery('.non-pi-only').show();
			
			}
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
	
	//add country listener to existing .add_country in DOM
	jQuery('.add_country').on("click", addCountry );
	
	//remove country listener
	jQuery('.delete_country').on("click", deleteCountry );
	
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
	
	jQuery('.remove-activity-upload').on("click", function(){
		var uploadHTML = jQuery(this);
		var whichUpload = jQuery(this).data("deletefile");
		var userId = jQuery("input[name='userID']").val();
		deleteActivityUploads( uploadHTML, whichUpload, userId );
	});
	
	jQuery('.remove-user-file').on("click", function(){
		var uploadHTML = jQuery(this);
		var whichUpload = jQuery(this).data("deleteupload");
		var userId = jQuery("input[name='user_id']").val();
		deleteUserFile( uploadHTML, whichUpload, userId );
	});
	
	jQuery('#CVmethod1').click(function () {
		jQuery("#linkDiv").show();
		jQuery("#uploadDiv").hide();
	});
	jQuery('#CVmethod2').click(function () {
		jQuery("#linkDiv").hide();
		jQuery("#uploadDiv").show();
	});	
}

//add countries as repeater
function addCountry() {
	var whereToAppend = jQuery(this).parents('tbody');
	var count = 1; //need to get number of last country in list
	var newcount; //placeholder
	
	//get all jQuery('.countrylist')
	jQuery('.countrylist').each( function() {
		newcount = jQuery(this).data("countrycount");
		if ( newcount > count ){
			count = newcount; //update count to the highest count number in form
		}
	});
	//add one more to count, since new tr here
	count++;
	
	var whatToAppend = '<tr class="gfield_list_row_even"><td class="gfield_list_cell list_cell">';
	whatToAppend += '<select tabindex="4" name="countrylist-' + count + '" class="countrylist countrylist-' + count + '"></select></td>';
	whatToAppend += '<td class="gfield_list_cell"><input type="text" tabindex="4" value="" name="region-' + count + '"></td><td class="gfield_list_icons">';
	whatToAppend += '<img class="add_list_item add_country" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">';
	whatToAppend += '<img class="delete_list_item delete_country" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">';
	whatToAppend += '</td></tr>';
		
	//add a new row
	whereToAppend.append(whatToAppend);
	
	//now populate added row with countries //TODO: Mel, make this efficient, plz
	var countryCodes = getCountries();
	//set up options for select
	var options = '';
	
	for (var i = 0; i < countryCodes.length; i++) {
		options += '<option value="' + countryCodes[i].code + '"';

		options += '>';
		options += countryCodes[i].name + '</option>';
	}
	
	jQuery( 'select.countrylist-' + count ).html(options);
	
	
	//turn off click listeners (so no double-listening on existing divs)
	jQuery('.add_country').off("click", addCountry );
	jQuery('.delete_country').off("click", deleteCountry );
	
	//turn them back on so new rows get listened to, too
	jQuery('.add_country').on("click", addCountry );
	jQuery('.delete_country').on("click", deleteCountry );
}

function deleteCountry() {
	var whatToDelete = jQuery(this).parents('tr');
	
	whatToDelete.remove();
}

//add collborating trs for saving goodness
function addCollaborating() {
	var whereToAppend = jQuery(this).parents('tbody');
	
	var whatToAppend = '<tr class="gfield_list_row_even"><td class="gfield_list_cell list_cell">';
	whatToAppend = whatToAppend + '<input type="text" tabindex="26" value="" name="collaborating[]"></td><td class="gfield_list_icons">';
	whatToAppend = whatToAppend + '<img class="add_list_item add_collaborating" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">';
	whatToAppend = whatToAppend + '<img class="delete_list_item delete_collaborating" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">';
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
	whatToAppend = whatToAppend + '<img class="add_list_item add_supplemental_link" style="cursor:pointer; margin:0 3px;" onclick="" alt="Add a row" title="Add another row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/add.png">';
	whatToAppend = whatToAppend + '<img class="delete_list_item delete_supplemental_link" onclick="" alt="Remove this row" title="Remove this row" src="http://www.communitycommons.org/wp-content/plugins/gravityforms/images/remove.png">';
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

//on activity form load, make sure when post info is loaded into form, appropriate fields show automagically
//	also, load ajax function (triggered on form submit)
function activityFormLoad(){

	//on form load, let's make sure right fields are displaying
	
	//if( jQuery('#pi_yes').is(':selected') ){
	if( jQuery('#pi_yes').is(":checked") ){
	
		jQuery('.pi-only').removeClass('hidden-on-init');
		jQuery('.non-pi-only').addClass('hidden-on-init');
		jQuery('#cafnr_write_in_pi').addClass('hidden-on-init');
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
	
	//activity form and plupload init stuff
	if (jQuery('#cafnr_activity_form').length) {
		
		//cafnr_countries(); getCountries(); //TODO: this
		populateCountryDropdown();		
		
		//init plupolader 
		activityUploader('plupload-browse-button', 'plupload-upload-ui');
		
	} else {
		//plupload needs to destroy itself
		activityFormUnload();
	}
	
	jQuery('.remove-activity-file').on("click", function() {
		removeActivityFile();
	});
	
	//make sure we're saving the form
	activityFormSave();
	
	
}

//a function to make sure when post info is loaded into user form, appropriate fields show automagically
function userFormLoad(){

	//on form load, let's make sure right fields are displaying
	
	//populate country dropdowns here
	populateCountryDropdown();
	//activity form and plupload init stuff
	if (jQuery('#cafnr_facultyadd_form').length) {
		
		//init plupolader 
		userUploader('user-plupload-browse-button', 'user-plupload-upload-ui');
		
	} else {
		userFormUnload();
	}
	
	
}

//save activity form via ajax
function activityFormSave() {
	jQuery('#cafnr_activity_form').on('submit', function(e){
		
		e.preventDefault();
		var querystring = jQuery(this).serialize();
		
		//objects for sql json ajax
		var activity_inputs = jQuery("#cafnr_activity_form :input");
		var o = {};
		var activity_obj = jQuery.map(activity_inputs, function(n, i)
		{
			o[n.name] = jQuery(n).val();
			//return o;
		});
		
		o['activity-name'] = jQuery("#cafnr_activity_name :selected").text();
		
		activity_obj=o;
		
		//different way to get an object from the form
		var activity_array = jQuery("#cafnr_activity_form").serializeArray();
		var activity_json_obj = {};
		jQuery.each( activity_array, function( i, v){
			activity_json_obj[v.name] = v.value;
		});
		
		
		//TODO: add nonce on both sides
		//querystring += "&cafnr_ajax_data_nonce=" + nm_ajax.cafnr_ajax_data_nonce;
		querystring += "&action=" + 'cafnr_intl_edit_activity';
		
		//first, post to wordpress
		jQuery.ajax({
			type: 'post',
			url: cafnr_ajax.adminAjax,
			data: querystring,
			success: function(data, textStatus, jqXHR){
				jQuery('#infobar').show().html("<p>Your changes have been saved.</p>")
				window.setTimeout(
					function(){ jQuery("#infobar").fadeOut(); },
					5000
				);
				jQuery('#PageLoader').fadeOut();
				return false;
			},
			beforeSend: function(jqXHR, settings){
				jQuery('#PageLoader').fadeIn();
			},
			complete: function(jqXHR, textStatus){
			
				// once complete, post to external url for SQL and map things..
				
				var sql_url = "http://staging.maps.communitycommons.org/apiservice/getdata.svc/cafnr";
				
				var req = { "id": 111, "program": "test"};
				jQuery.ajax({
					type: "POST",
					url: 'http://staging.maps.communitycommons.org/apiservice/getdata.svc/cafnr',
					dataType: 'json',
					contentType: "application/json",
					crossDomain: true,
					data: JSON.stringify(activity_json_obj),
					success: function (response) {
						console.log('success', response);
					},
					error: function (response) {
						console.log('error', response);
					}
				});

		
				/*jQuery.ajax({
					type: "POST",
					url: sql_url,
					crossDomain: true,
					//data: JSON.stringify(querystring),
					contentType:'application/json; charset=utf-8',
					data: querystring_test,
					dataType: 'json',
					success: function(data, textStatus, jqXHR){
					
						jQuery('#PageLoader').fadeOut();
						return false;
					},
					beforeSend: function(jqXHR, settings){
						jQuery('#PageLoader').fadeIn();
					},
					complete: function(jqXHR, textStatus){
						//redirect to the dashboard
						//TODO: check for user param, activity param for message
						//window.location = cafnr_ajax.surveyDashboard
						
					},
					error: function (xhr, ajaxOptions, thrownError) {
						alert(xhr.status);
						alert(thrownError);
						e.preventDefault();
					}
				});*/
				
				
				//redirect to the dashboard
				//TODO: check for user param, activity param for message
				//window.location = cafnr_ajax.surveyDashboard
				
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(xhr.status);
				alert(thrownError);
				e.preventDefault();
			}
		});

		
		e.preventDefault();
		return false;

	});
	
}


//functions in support of plupload for activity form
var activityFormUnload = function() {
	//plupload: uploader.destroy();
	for (var i = 0; i < pluploadVars.activityUploads.length; i++) {
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
			
			//add count to inputs for multiple file inputs
			var count = 0; //need to get number of last file in list
			var newcount; //placeholder
			
			//get all jQuery('.countrylist')
			jQuery('.activity_file_count').each( function() {
				newcount = jQuery(this).data("filecount");
				if ( newcount > count ){
					count = newcount; //update count to the highest count number in form
				}
			});
			//add one more to count, since new tr here
			count++;
			
			
			//To do: add display html here for the types..
			var activityFileHtml = "<span><p>File uploaded: " + activityFile.fileBaseName + "&nbsp;&nbsp;<input class='remove-activity-file' type='button' value='Remove this sample' data-deletefile='" + activityFile.file + "' >" + 
				"&nbsp;&nbsp;&nbsp;&nbsp;Change file name: <input type='text' name='activity_attachment_name-" + count + "' value=''></input>  (default name: 'Attachment " + count + ")'</p>" +
				"<input type='hidden' name='activity_file-" + count + "' value='" + activityFile.file + "' />" +
				"<input type='hidden' name='activity_file_url-" + count + "' value='" + activityFile.url + "' />" +
				"<input type='hidden' name='activity_file_type-" + count + "' value='" + activityFile.type + "' /></span>" +
				"<input type='hidden' class='activity_file_count' data-filecount='" + count + "' name='activity_file_count-" + count + "' value='" + activityFile.count + "' /></span>";
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

//ajax function for deleting uploads
function deleteActivityUploads( uploadHTML, whichUpload, userId ){

	//set up our ajax and data
	jQuery.ajax({
		type: 'post',
		url: cafnr_ajax.adminAjax,
		dataType: 'json',
		data: {
			action: 'activity_upload_delete',
			user_id: userId,
			attachment_id: whichUpload
		},
		success: function(data, textStatus, jqXHR){
			if ( data.error != undefined ) {
				alert( data.error );
			} else if ( data.success != undefined ) {
				uploadHTML.parent('li').hide();
			}
			
			return false;
		},
		beforeSend: function(jqXHR, settings){
			//jQuery('#PageLoader').fadeIn();
		},
		complete: function(jqXHR, textStatus){
			//fadeout spinny, scroll to top, message?
			//jQuery('#PageLoader').fadeOut();
			//window.scrollTo(0,0);
		},
		error: function (xhr, ajaxOptions, thrownError) {
			alert("I'm sorry, there was an error. Please try again.");
		}

	});
}



//user form plupload functions
var userFormUnload = function() {
	//plupload: uploader.destroy();
	for (var i = 0; i < userPluploadVars.userUploads.length; i++) {
		userPluploadVars.userUploads[i].destroy();
	}
	
}

function userUploader( browseButton, uiContainer ){
	var userPluploader = new plupload.Uploader({
		runtimes:'html5,silverlight,flash,html4',
		/*flash_swf_url: cafnr_ajax.pluploadSWF,
		silverlight_xap_url: cafnr_ajax.pluploadXAP, */
		browse_button: browseButton,
		file_data_name: 'user_uploads',
		max_file_size: '200kb',
		url: cafnr_ajax.adminAjax,
		filters: [{title:'User Upload', extensions:'pdf,mp3,jpg,jpeg,gif,png'}],  //TODO: add more extensions
		multipart_params: { action: 'user_upload' }
	});

	//store reference to this object for later removal
	userPluploadVars.userUploads.push( userPluploader );

	userPluploader.init();

	userPluploader.bind('BeforeUpload', function(up_user, files){
		if( jQuery('.user-file-span').length > 0 ) {
			jQuery('.user-file-span').remove();
		}
	});
	
	userPluploader.bind('FilesAdded', function(up_user, files){
		up_user.start();
	});

	userPluploader.bind('UploadProgress', function(up, file) {
		jQuery('#' + uiContainer).html('<p class="ie9hide">' + file.percent + '% complete...</p><p class="red">Please wait to save your progress until your file has finished uploading</p>');
	});

	userPluploader.bind('Error', function(up, err) {
		if (err.code == -600) { //file size
			jQuery('#' + uiContainer).html("<p class='red'>I'm sorry, this file is too large.  200kb or less, please.</p>");
		} else if (err.code == -601) { //file type
			jQuery('#' + uiContainer).html("<p class='red'>I'm sorry, we accept only PDFs or MP3s.</p>");
		} else {
			jQuery('#' + uiContainer).html("<p class='red'>Sorry, there was an error. Please try again.</p>");
		}
	});

	userPluploader.bind('FileUploaded', function(up, file, response){
		if (response.response) {
			//response.response = file, url, type, fileBaseName
			var userFile = eval('(' + response.response + ')');
			
			//To do: add display html here for the types..
			var userFileHtml = "<span class='user-file-span'><p>File uploaded: " + userFile.fileBaseName + "&nbsp;&nbsp;<input class='remove-user-file' type='button' value='Remove this CV' data-deletefile='" + userFile.file + "' >" + 
				"</p>" +
				"<input type='hidden' name='user_file' value='" + userFile.file + "' />" +
				"<input type='hidden' name='user_file_url' value='" + userFile.url + "' />" +
				"<input type='hidden' name='user_file_basename' value='" + userFile.fileBaseName + "' />" +
				"<input type='hidden' name='user_file_type' value='" + userFile.type + "' /></span>";
				
			jQuery('#' + uiContainer).after(userFileHtml).show('slow', function(){
				jQuery('#user-plupload-upload-ui .ie9hide').hide();
				jQuery('#user-plupload-upload-ui .red').hide();
				
			});
			
			//add remove listeners to this 
			jQuery('.remove-user-file').off("click", removeUserFile);
			jQuery('.remove-user-file').on("click", {
				uploader: userPluploader,
				file: file
				}, removeUserFile );
			
			jQuery('#' + browseButton).html('<input type="button" value="Select a different file to upload..." />');
			
		} else {
			jQuery('#' + uiContainer).html('<p>Sorry, there was an error. Please try again.</p>');
		}
	});
}

function removeUserFile( uploaderInput ){
	//var fileurl = jQuery(this).data('deletefile');
	console.log('hey...');
	
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


function getCountries(){

	countryCodes = [
		{	code: "",	name: "---Select Country---"
		},
		{	code: "AF",	name: "Afghanistan"},
		{	code: "AX",name: "Åland Islands"},					
		{	code: "AL",	name: "Albania"},
		{	code: "DZ",	name: "Algeria"},
		{	code: "AS",	name: "American Samoa"},
		{	code: "AD",	name: "Andorra"},
		{	code: "AO",	name: "Angola"},
		{	code: "AI",	name: "Anguilla"},
		{	code: "AQ",	name: "Antarctica"},
		{	code: "AG",	name: "Antigua and Barbuda"},
		{	code: "AR",	name: "Argentina"},
		{	code: "AM",	name: "Armenia"},
		{	code: "AW",	name: "Aruba"},
		{	code: "AU",	name: "Australia"},
		{	code: "AT",	name: "Austria"},
		{	code: "AZ",	name: "Azerbaijan"},
		{	code: "BS",	name: "Bahamas"},
		{	code: "BH",	name: "Bahrain"},
		{	code: "BD",	name: "Bangladesh"},
		{	code: "BB",	name: "Barbados"},
		{	code: "BY",	name: "Belarus"},
		{	code: "BE",	name: "Belgium"},
		{	code: "BZ",	name: "Belize"},
		{	code: "BJ",	name: "Benin"},
		{	code: "BM",	name: "Bermuda"},
		{	code: "BT",	name: "Bhutan"},
		{	code: "BO",	name: "Bolivia, Plurinational State Of"},
		{	code: "BQ",	name: "Bonaire, Sint Eustatius and Saba"},
		{	code: "BA",	name: "Bosnia and Herzegovina"},
		{	code: "BW",	name: "Botswana"},
		{	code: "BV",	name: "Bouvet Island"},
		{	code: "BR",	name: "Brazil"},
		{	code: "IO",	name: "British Indian Ocean Territory"},
		{	code: "BN",	name: "Brunei Darussalam"},
		{	code: "BG",	name: "Bulgaria"},
		{	code: "BF",	name: "Burkina Faso"},
		{	code: "BI",	name: "Burundi"},
		{	code: "KH",	name: "Cambodia"},
		{	code: "CM",	name: "Cameroon"},
		{	code: "CA",	name: "Canada"},
		{	code: "CV",	name: "Cape Verde"},
		{	code: "KY",	name: "Cayman Islands"},
		{	code: "CF",	name: "Central African Republic"},
		{	code: "TD",	name: "Chad"},
		{	code: "CL",	name: "Chile"},
		{	code: "CN",	name: "China"},
		{	code: "CX",	name: "Christmas Island"},
		{	code: "CC",	name: "Cocos (Keeling) Islands"},
		{	code: "CO",	name: "Colombia"},
		{	code: "KM",	name: "Comoros"},
		{	code: "CG",	name: "Congo"},
		{	code: "CD",	name: "Congo The Democratic Republic Of The"},
		{	code: "CK",	name: "Cook Islands"},
		{	code: "CR",	name: "Costa Rica"},
		{	code: "HR",	name: "Croatia"},
		{	code: "CU",	name: "Cuba"},
		{	code: "CW",	name: "Curaçao"},
		{	code: "CY",	name: "Cyprus"},
		{	code: "CZ",	name: "Czech Republic"},
		{	code: "CI",	name: "Côte D\'Ivoire"},
		{	code: "DK",	name: "Denmark"},
		{	code: "DJ",	name: "Djibouti"},
		{	code: "DM",	name: "Dominica"},
		{	code: "DO",	name: "Dominican Republic"
		},
		{	code: "EC",	name: "Ecuador"},
		{	code: "EG",	name: "Egypt"},
		{	code: "SV",	name: "El Salvador"},
		{	code: "GQ",	name: "Equatorial Guinea"},
		{	code: "ER",	name: "Eritrea"},
		{	code: "EE",	name: "Estonia"},
		{	code: "ET",	name: "Ethiopia"},
		{	code: "FK",	name: "Falkland Islands  (Malvinas)"},
		{	code: "FO",	name: "Faroe Islands"},
		{	code: "FJ",	name: "Fiji"},
		{	code: "FI",	name: "Finland"},
		{	code: "FR",	name: "France"},
		{	code: "GF",	name: "French Guiana"},
		{	code: "PF",	name: "French Polynesia"},
		{	code: "TF",	name: "French Southern Territories"},
		{	code: "GA",	name: "Gabon"},
		{	code: "GM",	name: "Gambia"},
		{	code: "GE",	name: "Georgia"},
		{	code: "DE",	name: "Germany"},
		{	code: "GH",	name: "Ghana"},
		{	code: "GI",	name: "Gibraltar"},
		{	code: "GR",	name: "Greece"},
		{	code: "GL",	name: "Greenland"},
		{	code: "GD",	name: "Grenada"},
		{	code: "GP",	name: "Guadeloupe"},
		{	code: "GU",	name: "Guam"
		},
		{
			code: "GT",
			name: "Guatemala"
		},
		{
			code: "GG",
			name: "Guernsey"
		},
		{
			code: "GN",
			name: "Guinea"
		},
		{
			code: "GW",
			name: "Guinea-Bissau"
		},
		{
			code: "GY",
			name: "Guyana"
		},
		{
			code: "HT",
			name: "Haiti"
		},
		{
			code: "HM",
			name: "Heard Island and McDonald Islands"
		},
		{
			code: "VA",
			name: "Holy See (Vatican City State)"
		},
		{
			code: "HN",
			name: "Honduras"
		},
		{
			code: "HK",
			name: "Hong Kong"
		},
		{
			code: "HU",
			name: "Hungary"
		},
		{
			code: "IS",
			name: "Iceland"
		},
		{
			code: "IN",
			name: "India"
		},
		{
			code: "ID",
			name: "Indonesia"
		},
		{
			code: "IR",
			name: "Iran, Islamic Republic Of"
		},
		{
			code: "IQ",
			name: "Iraq"
		},
		{
			code: "IE",
			name: "Ireland"
		},
		{
			code: "IM",
			name: "Isle of Man"
		},
		{
			code: "IL",
			name: "Israel"
		},
		{
			code: "IT",
			name: "Italy"
		},
		{
			code: "JM",
			name: "Jamaica"
		},
		{
			code: "JP",
			name: "Japan"
		},
		{
			code: "JE",
			name: "Jersey"
		},
		{
			code: "JO",
			name: "Jordan"
		},
		{
			code: "KZ",
			name: "Kazakhstan"
		},
		{
			code: "KE",
			name: "Kenya"
		},
		{
			code: "KI",
			name: "Kiribati"
		},
		{
			code: "KP",
			name: "Korea, Democratic People\'s Republic Of"
		},
		{
			code: "KR",
			name: "Korea, Republic of"
		},
		{
			code: "KW",
			name: "Kuwait"
		},
		{
			code: "KG",
			name: "Kyrgyzstan"
		},
		{
			code: "LA",
			name: "Lao People\'s Democratic Republic"
		},
		{
			code: "LV",
			name: "Latvia"
		},
		{
			code: "LB",
			name: "Lebanon"
		},
		{
			code: "LS",
			name: "Lesotho"
		},
		{
			code: "LR",
			name: "Liberia"
		},
		{
			code: "LY",
			name: "Libya"
		},
		{
			code: "LI",
			name: "Liechtenstein"
		},
		{
			code: "LT",
			name: "Lithuania"
		},
		{
			code: "LU",
			name: "Luxembourg"
		},
		{
			code: "MO",
			name: "Macao"
		},
		{
			code: "MK",
			name: "Macedonia, the Former Yugoslav Republic Of"
		},
		{
			code: "MG",
			name: "Madagascar"
		},
		{
			code: "MW",
			name: "Malawi"
		},
		{
			code: "MY",
			name: "Malaysia"
		},
		{
			code: "MV",
			name: "Maldives"
		},
		{
			code: "ML",
			name: "Mali"
		},
		{
			code: "MT",
			name: "Malta"
		},
		{
			code: "MH",
			name: "Marshall Islands"
		},
		{
			code: "MQ",
			name: "Martinique"
		},
		{
			code: "MR",
			name: "Mauritania"
		},
		{
			code: "MU",
			name: "Mauritius"
		},
		{
			code: "YT",
			name: "Mayotte"
		},
		{
			code: "MX",
			name: "Mexico"
		},
		{
			code: "FM",
			name: "Micronesia, Federated States Of"
		},
		{
			code: "MD",
			name: "Moldova, Republic of"
		},
		{
			code: "MC",
			name: "Monaco"
		},
		{
			code: "MN",
			name: "Mongolia"
		},
		{
			code: "ME",
			name: "Montenegro"
		},
		{
			code: "MS",
			name: "Montserrat"
		},
		{
			code: "MA",
			name: "Morocco"
		},
		{
			code: "MZ",
			name: "Mozambique"
		},
		{
			code: "MM",
			name: "Myanmar"
		},
		{
			code: "NA",
			name: "Namibia"
		},
		{
			code: "NR",
			name: "Nauru"
		},
		{
			code: "NP",
			name: "Nepal"
		},
		{
			code: "NL",
			name: "Netherlands"
		},
		{
			code: "NC",
			name: "New Caledonia"
		},
		{
			code: "NZ",
			name: "New Zealand"
		},
		{
			code: "NI",
			name: "Nicaragua"
		},
		{
			code: "NE",
			name: "Niger"
		},
		{
			code: "NG",
			name: "Nigeria"
		},
		{
			code: "NU",
			name: "Niue"
		},
		{
			code: "NF",
			name: "Norfolk Island"
		},
		{
			code: "MP",
			name: "Northern Mariana Islands"
		},
		{
			code: "NO",
			name: "Norway"
		},
		{
			code: "OM",
			name: "Oman"
		},
		{
			code: "PK",
			name: "Pakistan"
		},
		{
			code: "PW",
			name: "Palau"
		},
		{
			code: "PS",
			name: "Palestinian Territory, Occupied"
		},
		{
			code: "PA",
			name: "Panama"
		},
		{
			code: "PG",
			name: "Papua New Guinea"
		},
		{
			code: "PY",
			name: "Paraguay"
		},
		{
			code: "PE",
			name: "Peru"
		},
		{
			code: "PH",
			name: "Philippines"
		},
		{
			code: "PN",
			name: "Pitcairn"
		},
		{
			code: "PL",
			name: "Poland"
		},
		{
			code: "PT",
			name: "Portugal"
		},
		{
			code: "PR",
			name: "Puerto Rico"
		},
		{
			code: "QA",
			name: "Qatar"
		},
		{
			code: "RO",
			name: "Romania"
		},
		{
			code: "RU",
			name: "Russian Federation"
		},
		{
			code: "RW",
			name: "Rwanda"
		},
		{
			code: "RE",
			name: "Réunion"
		},
		{
			code: "BL",
			name: "Saint Barthélemy"
		},
		{
			code: "SH",
			name: "Saint Helena, Ascension and Tristan Da Cunha"
		},
		{
			code: "KN",
			name: "Saint Kitts And Nevis"
		},
		{
			code: "LC",
			name: "Saint Lucia"
		},
		{
			code: "MF",
			name: "Saint Martin (French Part)"
		},
		{
			code: "PM",
			name: "Saint Pierre And Miquelon"
		},
		{
			code: "VC",
			name: "Saint Vincent And The Grenadines"
		},
		{
			code: "WS",
			name: "Samoa"
		},
		{
			code: "SM",
			name: "San Marino"
		},
		{
			code: "ST",
			name: "Sao Tome and Principe"
		},
		{
			code: "SA",
			name: "Saudi Arabia"
		},
		{
			code: "SN",
			name: "Senegal"
		},
		{
			code: "RS",
			name: "Serbia"
		},
		{
			code: "SC",
			name: "Seychelles"
		},
		{
			code: "SL",
			name: "Sierra Leone"
		},
		{
			code: "SG",
			name: "Singapore"
		},
		{
			code: "SX",
			name: "Sint Maarten (Dutch part)"
		},
		{
			code: "SK",
			name: "Slovakia"
		},
		{
			code: "SI",
			name: "Slovenia"
		},
		{
			code: "SB",
			name: "Solomon Islands"
		},
		{
			code: "SO",
			name: "Somalia"
		},
		{
			code: "ZA",
			name: "South Africa"
		},
		{
			code: "GS",
			name: "South Georgia and the South Sandwich Islands"
		},
		{
			code: "SS",
			name: "South Sudan"
		},
		{
			code: "ES",
			name: "Spain"
		},
		{
			code: "LK",
			name: "Sri Lanka"
		},
		{
			code: "SD",
			name: "Sudan"
		},
		{
			code: "SR",
			name: "Suriname"
		},
		{
			code: "SJ",
			name: "Svalbard And Jan Mayen"
		},
		{
			code: "SZ",
			name: "Swaziland"
		},
		{
			code: "SE",
			name: "Sweden"
		},
		{
			code: "CH",
			name: "Switzerland"
		},
		{
			code: "SY",
			name: "Syrian Arab Republic"
		},
		{
			code: "TW",
			name: "Taiwan, Province Of China"
		},
		{
			code: "TJ",
			name: "Tajikistan"
		},
		{
			code: "TZ",
			name: "Tanzania, United Republic of"
		},
		{
			code: "TH",
			name: "Thailand"
		},
		{
			code: "TL",
			name: "Timor-Leste"
		},
		{
			code: "TG",
			name: "Togo"
		},
		{
			code: "TK",
			name: "Tokelau"
		},
		{	code: "TO",	name: "Tonga"},
		{	code: "TT",	name: "Trinidad and Tobago"},
		{	code: "TN",	name: "Tunisia"},
		{	code: "TR",	name: "Turkey"},
		{	code: "TM",	name: "Turkmenistan"},
		{	code: "TC",	name: "Turks and Caicos Islands"},
		{
			code: "TV",
			name: "Tuvalu"
		},
		{
			code: "UG",
			name: "Uganda"
		},
		{
			code: "UA",
			name: "Ukraine"
		},
		{
			code: "AE",
			name: "United Arab Emirates"
		},
		{
			code: "GB",
			name: "United Kingdom"
		},
		{
			code: "US",
			name: "United States"
		},
		{
			code: "UM",
			name: "United States Minor Outlying Islands"
		},
		{
			code: "UY",
			name: "Uruguay"
		},
		{
			code: "UZ",
			name: "Uzbekistan"
		},
		{
			code: "VU",
			name: "Vanuatu"
		},
		{
			code: "VE",
			name: "Venezuela, Bolivarian Republic of"
		},
		{
			code: "VN",
			name: "Viet Nam"
		},
		{
			code: "VG",
			name: "Virgin Islands, British"
		},
		{
			code: "VI",
			name: "Virgin Islands, U.S."
		},
		{
			code: "WF",
			name: "Wallis and Futuna"
		},
		{
			code: "EH",
			name: "Western Sahara"
		},
		{
			code: "YE",
			name: "Yemen"
		},
		{
			code: "ZM",
			name: "Zambia"
		},
		{
			code: "ZW",
			name: "Zimbabwe"
		},
	];

	return countryCodes;
}

function populateCountryDropdown(){

	var countryCodes = getCountries();
	var countrySelected;
	
	jQuery('.countrylist').each( function(){
		//set up options for select
		var options = '';
		
		//there's got to be a more efficient way to select the selected..
		countrySelected = jQuery(this).data("countryvalue");
		
		for (var i = 0; i < countryCodes.length; i++) {
			options += '<option value="' + countryCodes[i].code + '"';
			if ( countrySelected == countryCodes[i].code ) {
				options += ' selected';
			}
			options += '>';
			options += countryCodes[i].name + '</option>';
		}
		jQuery(this).html(options);
	});
}


jQuery(document).ready(function($){
	//TODO: datepicker;
	//TODO: autocomplete for form..
	clickListen();
	
	//load initial activity form state, save ajax function
	activityFormLoad();
	
	//load user form change/click listens
	userFormLoad();
	
	//instantiate datepicker
	jQuery( ".datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true
	});
	
	
	
},(jQuery))

