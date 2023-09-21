/**
 * @author Cognizant
 * @version 1.0
 * Revised Date: 2010/06/24
 * @copyright (c) 2010 by LexisNexis Asset Company. All rights reserved.
 * @package Javascript
 * @access Public
 * LexisNexis setfocus
 */

/*
sfFocus = function() {
	var sfEls = document.getElementsByTagName("INPUT");
	for (var i=0; i<sfEls.length; i++) {
		sfEls[i].onfocus=function() {
			this.className+=" sffocus";
		}
		sfEls[i].onblur=function() {
			this.className=this.className.replace(new RegExp(" sffocus\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", sfFocus);
*/
function strstr (haystack, needle, bool) {
	// Finds first occurrence of a string within another
	//
	// version: 909.322
	// discuss at: http://phpjs.org/functions/strstr    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   bugfixed by: Onno Marsman
	// +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// *     example 1: strstr('Kevin van Zonneveld', 'van');
	// *     returns 1: 'van Zonneveld'    // *     example 2: strstr('Kevin van Zonneveld', 'van', true);
	// *     returns 2: 'Kevin '
	// *     example 3: strstr('name@example.com', '@');
	// *     returns 3: '@example.com'
	// *     example 4: strstr('name@example.com', '@', true);    // *     returns 4: 'name'
	var pos = 0;

	haystack += '';
	pos = haystack.indexOf( needle );
	if (pos == -1) {
		return false;
	}else{
		if (bool){
			return haystack.substr( 0, pos );
		} else{
			return haystack.slice( pos );
		}
	}
}


var child, latestnote, historicnotes, webform, usersession;


var divIndex = 1;

function SubmitAddUser(siteurl){
	document.getElementById('submitted').value="1";
}
c=0;

//Reset Password
function ResetPasswordDisplay(siteurl){


	val1=document.getElementById('userId').value;

	var left = (screen.width/2)-(700/2);
	var top = (screen.height/2)-(180/2);
	window.open(siteurl +'/admin/users/resetpassword/userId/'+val1,'ResetPassword','width=700, height=180,top='+top+',left='+left+'')
}
function OpenEditUser(siteurl){
	window.location.href=siteurl
}

function EnableAssign(){
	var val=document.getElementById('formId').value;

	if (val == '' ){
		document.getElementById('Assign').disabled=true;

	}else{
		document.getElementById('Assign').disabled=false;
	}

}


/**
 *  To enable Save button, if both label and value is present
 */
function EnableSaveOverflow()
{
	var formLabel = trim(document.getElementById('txtOverFlowLabel').value);
	var formValue = trim(document.getElementById('txtOverFlowValue').value);

	if( (formLabel != '' && formLabel != null) && (formValue != '' && formValue != null) ) {
		document.getElementById('btnSave').disabled = false;
	}
	else {
		document.getElementById('btnSave').disabled = true;
	}
}

function trim(str){
	if(!str || typeof str != 'string')
		return null;
	return str.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
}

function ClearListbox(siteurl){
	document.getElementById('stateId').value=''
	EnableListbox(siteurl);
}

function SelectAgencyMetrics(siteurl){
	if(document.getElementById('stateId').value)
	{

		var val = document.getElementById('stateId').value

		url =  siteurl+"/remote";
		vars = 'w=selectagencymetrics&stateId=' + val;
		callback = function(usage)
		{

			document.getElementById('agencyIdDec').disabled = false;
			document.getElementById('agencyIdDec').innerHTML = usage;

		}
		ajax(url, vars, callback);
	}
	else if(document.getElementById('stateId').value==''){
		document.getElementById('agencyIdDec').disabled = true;

	}
}

function closeWindow()
{
	alert('inside the window..');
	window.child.close();
	window.close();
}


//Bad Image Queue Loading

function SubmitBadImage( siteurl ) {
	window.opener.flagUnLoad();
	//    if(document.getElementById('noteId').value && window.opener.document.getElementById('policeReportId').value)
	if(document.getElementById('notes').value)
	{
		val1 = document.getElementById('notes').value;
		val2 = document.getElementById('policeReportId').value;
		url =  siteurl + '/data/dataentry/save/note/' + val1 + '/policeReportId/' + val2;

		window.opener.document.getElementById('entryform').submit();

		vars = val1 +' '+ val2;

		callback = function(usage)
		{
			//            alert(usage);
			// window.opener.location.href  =  siteurl + '/data/dataentry/index';
			window.close();


		}
		ajax(url, vars, callback);
	}
}


//Discard a BadImage
function SubmitDiscardImage( siteurl) {

	//if(document.getElementById('noteId').value && window.opener.document.getElementById('policeReportId').value)
	if(document.getElementById('notes').value)
	{
		val1 = document.getElementById('notes').value;
		val2 = document.getElementById('policeReportId').value;
		url =  siteurl + '/admin/badimage/save/note/' + val1 + '/policeReportId/' + val2;
		vars = val1 + ' ' + val2;
		status = window.opener.opener.document.getElementById('status').value;
		passgroup = window.opener.opener.document.getElementById('passGroup').value;
		firstname = window.opener.opener.document.getElementById('firstname').value;
		lastname = window.opener.opener.document.getElementById('lastname').value;
		window.opener.opener.document.getElementById('badimageflag').value = 1;
		badimageflag = window.opener.opener.document.getElementById('badimageflag').value;


		callback = function(usage)
		{
			window.opener.opener.location.href =  siteurl + '/admin/badimage/index/badimageflag/'+badimageflag+'/status/'+status+'/passGroup/'+passgroup+'/firstName/'+firstname+'/lastName/'+lastname;
			window.opener.opener.document.getElementById('badimagequeue').submit();
			window.close();
		}
		ajax(url, vars, callback);
	}
}


//submit badimage search page

function submitIndex(siteurl)
{
	status = window.document.getElementById('status').value;
	passgroup = window.document.getElementById('passGroup').value;
	firstname = window.document.getElementById('firstname').value;
	lastname = window.document.getElementById('lastname').value;

	//alert(siteurl + //'/admin/badimage/index/badimageflag/'+0+'/status/'+status+'/passGroup/'+passgroup+'/firstName/'+firstname+'/lastName/'+lastname);
	//window.location.href =  siteurl + //'/admin/badimage/index/badimageflag/'+0+'/status/'+status+'/passGroup/'+passgroup+'/firstName/'+firstname+'/lastName/'+lastname;
	document.getElementById('badimageflag').value = 0;
	window.document.getElementById('badimagequeue').submit();


}

function saveClose()
{

	window.opener.location.reload();

	window.child.close();
	window.close();

}

//Make the BADIMAGE availble for keying
function SubmitRekeyImage( siteurl) {

	//if(document.getElementById('noteId').value && window.opener.document.getElementById('policeReportId').value)
	if(document.getElementById('notes').value)
	{
		val1 = document.getElementById('notes').value;
		val2 = document.getElementById('policeReportId').value;

		url =  siteurl + '/admin/badimage/saverekey/note/' + val1 + '/policeReportId/' + val2;
		vars = val1 + ' ' + val2;

		status = window.opener.opener.document.getElementById('status').value;
		passgroup = window.opener.opener.document.getElementById('passGroup').value;
		firstname = window.opener.opener.document.getElementById('firstname').value;
		lastname = window.opener.opener.document.getElementById('lastname').value;
		window.opener.opener.document.getElementById('badimageflag').value = 1;
		badimageflag = window.opener.opener.document.getElementById('badimageflag').value;

		callback = function(usage)
		{
			window.opener.opener.location.href =  siteurl + '/admin/badimage/index/badimageflag/'+badimageflag+'/status'+status+'/passGroup/'+passgroup+'/firstName/'+firstname+'/lastName/'+lastname;
			window.opener.opener.document.getElementById('badimagequeue').submit();
			window.close();
		}
		ajax(url, vars, callback);
	}
}
var time;
function dateField()
{
	var reorderVal;
	var reorderDate;
	for (var i=0;i < document.entryform.reorder.length;i++)
	{
		if(document.entryform.reorder[i].checked){
			reorderVal = document.entryform.reorder[i].value;
		}

	}

	if(reorderVal == 0)
	{
		document.entryform.reorderDate.value = '';
		document.entryform.reorderDate.disabled = true;

	}
	else
	{
		document.entryform.reorderDate.disabled = false;
		time = new Date();
		monthVal = time.getMonth()+1;
		dateVal = time.getDate();
		yearVal = time.getFullYear();
		if(monthVal < 10)
			monthVal = '0'+monthVal;
		if(dateVal < 10 )
			dateVal = '0'+dateVal;

		document.entryform.reorderDate.value =  monthVal+'/'+dateVal+'/'+ yearVal;

	//document.entryform.reorderDate.value = (time.getMonth()+1)+'/'+time.getDate()+'/'+time.getFullYear();
	}
}


function getReorderVal(id , siteurl){
	var reorderVal = '';
	var reorderDate;

	for (var i=0;i < document.entryform.reorder.length;i++)
	{
		if(document.entryform.reorder[i].checked){
			reorderVal = document.entryform.reorder[i].value;
		}

	}

	reorderDate = document.entryform.reorderDate.value;
	reorderDate = reorderDate.replace(new RegExp('/','g' ),'-');
	if(reorderDate == '' && reorderVal == 1)
	{
		alert('Please enter the Re-orderDate');
		return false;
	}
	if(reorderVal == '')
	{
		alert('please select Re-order(Y/N) to continue');
		return false;
	}
	url =  siteurl + '/admin/badimage/reorder/reorderVal/' + reorderVal + '/reorderDate/' + reorderDate+'/policeReportId/'+id;
	vars = reorderVal+' '+reorderDate+' '+id;
	status = window.opener.document.getElementById('status').value;
	passgroup = window.opener.document.getElementById('passGroup').value;
	firstname = window.opener.document.getElementById('firstname').value;
	lastname = window.opener.document.getElementById('lastname').value;
	window.opener.document.getElementById('badimageflag').value = 1;
	badimageflag = window.opener.document.getElementById('badimageflag').value;

	callback = function(usage)
	{
		window.opener.location.href =  siteurl + '/admin/badimage/index/badimageflag/'+badimageflag+'/status/'+status+'/passGroup/'+passgroup+'/firstName/'+firstname+'/lastName/'+lastname;
		window.opener.document.getElementById('badimagequeue').submit();

	}
	ajax(url, vars, callback);
}

function SaveInvalidVIN(siteurl){

	if(document.getElementById('page').value)
		page = document.getElementById('page').value;
	else
		page = "1";

	processingStartTime = document.getElementById('processingStartTime').value;
	processingEndTime = document.getElementById('processingEndTime').value;
	invalidVINQueueID = document.getElementById('invalidVINQueueID').value;

	url =  siteurl+"/remote";
	vars = 'w=updateinvalidvinstatus&invalidVINQueueID=' + invalidVINQueueID ;

	callback = function(usage)
	{
		//alert(usage);
		window.opener.location.href = siteurl+"/admin/invalidvin/index/page/" + page +"/processingStartTime/"+ processingStartTime +"/processingEndTime/" + processingEndTime;
		if(usage != ''){
			window.location.href = siteurl+"/admin/invalidvin/edit/invalidVINQueueID/"+ usage +"/page/" + page +"/processingStartTime/"+ processingStartTime +"/processingEndTime/" + processingEndTime + "/actionstatus/invalid";
		}else
			window.close();
	}
	ajax(url, vars, callback);

}

function SaveValidVIN(siteurl){

	if(document.getElementById('proposedVin').value != ''){
		page = document.getElementById('page').value
		processingStartTime = document.getElementById('processingStartTime').value
		processingEndTime = document.getElementById('processingEndTime').value
		invalidVINQueueID = document.getElementById('invalidVINQueueID').value
		proposedVin = document.getElementById('proposedVin').value

		url =  siteurl+"/remote";
		vars = 'w=updatevalidvinstatus&invalidVINQueueID=' + invalidVINQueueID + '&proposedVin=' + proposedVin;

		callback = function(usage)
		{
			//alert(usage);
			window.opener.location.href = siteurl+"/admin/invalidvin/index/page/" + page +"/processingStartTime/"+ processingStartTime +"/processingEndTime/" + processingEndTime;
			if(usage != ''){
				window.location.href = siteurl+"/admin/invalidvin/edit/invalidVINQueueID/"+ usage +"/page/" + page +"/processingStartTime/"+ processingStartTime +"/processingEndTime/" + processingEndTime;
			}else
				window.close();
			}
		ajax(url, vars, callback);
	}else{
		alert("Please enter a propesed VIN");
	}
}

function SetPotentialVIN(potentialvin)
{
	document.getElementById('proposedVin').value = potentialvin;
}

/*
 *  Author Gauthaman
 *  Data Elements Assignments
 */

function EnableSaveADE() {
	if(document.getElementById('noteId').value!='' ){
		document.getElementById('save').disabled=false;
	}else{
		document.getElementById('save').disabled=true;
	}
}

function SelectAgencyAssignDataElements ( siteurl ) {
	if(document.getElementById('stateId').value)
	{
		var val = document.getElementById('stateId').value

		url =  siteurl + "/remote";
		vars = 'w=selectagencyassigndataelements&stateId=' + val;
		callback = function(usage)
		{
			document.getElementById('agencyIdDec').disabled = false;
			document.getElementById('agencyIdDec').innerHTML = usage;
		}
		ajax(url, vars, callback);
	}
	else if(document.getElementById('stateId').value==''){
		document.getElementById('agencyId').disabled = true;
	}
}

function SelectFormListFromAgencyAssignDataElements ( siteurl ) {
	if(document.getElementById( 'stateId' ).value)
	{
		val1 = document.getElementById( 'stateId' ).value;
		val2 = document.getElementById( 'agencyId' ).value;

		url =  siteurl + "/remote";
		vars = 'w=selectformfromagencyassigndataelements&stateId=' + val1 + '&agencyId=' + val2;
		callback = function( usage )
		{
			document.getElementById( 'formIdDec' ).disabled = false;
			document.getElementById( 'formIdDec' ).innerHTML = usage;
		}
		ajax( url, vars, callback );
	}
}

function SelectFormListFromStateAssignDataElements ( siteurl ) {
	if(document.getElementById( 'stateId' ).value)
	{
		val1 = document.getElementById( 'stateId' ).value

		url =  siteurl + "/remote";
		vars = 'w=selectformfromstateassigndataelements&stateId=' + val1;

		callback = function( usage )
		{
			document.getElementById( 'formIdDec' ).disabled = false;
			document.getElementById( 'formIdDec' ).innerHTML = usage;
		}
		ajax( url, vars, callback );
	}
}

function SubmitAssignDataElementsChangeSaveNotes ( siteurl ) {
	if( document.getElementById('noteId').value && window.opener.document.getElementById('formIdhidden').value )
	{
		val1 = document.getElementById('noteId').value;
		val2 = window.opener.document.getElementById('formIdhidden').value;
		val3 = 'addsavenotes';
		val4 = window.opener.document.getElementById('agencyIdhidden').value;

		url =  siteurl + '/admin/assigndataelements/save';
		vars = 'note=' + encodeURIComponent(val1) + '&formid=' + val2 + '&path=' + val3 + '&agencyid=' + val4;

		window.opener.document.getElementById('frmDataElements').submit();
		callback = function(usage)
		{
			window.close();
		}
		ajax(url, vars, callback);
	}
}

function SubmitAssignDataElementsChanges ( siteurl ) {

	if( window.opener.document.getElementById('formIdhidden') == null ) {
		if( document.getElementById('noteId').value && window.opener.document.getElementById('formId').value )
		{
			val1 = document.getElementById('noteId').value;
			val2 = window.opener.document.getElementById('formId').value;
			//val3 = 'addnotes';
			val4 = window.opener.document.getElementById('agencyId').value;

			url =  siteurl + '/admin/assigndataelements/save';
			vars = 'note=' + encodeURIComponent(val1) + '&formid=' + val2 + '&path=' + val3 + '&agencyid=' + val4;

			callback = function(usage)
			{
				//window.opener.location.reload(true);
				//document.getElementById('frm').submit();
				window.close();
			}
			ajax(url, vars, callback);
		}
	} else {
		if( document.getElementById('noteId').value && window.opener.document.getElementById('formIdhidden').value )
		{
			val1 = document.getElementById('noteId').value;
			val2 = window.opener.document.getElementById('formIdhidden').value;
			val3 = 'addnotes';
			val4 = window.opener.document.getElementById('agencyIdhidden').value;

			url =  siteurl + '/admin/assigndataelements/save/note';
			vars = 'note=' + encodeURIComponent(val1) + '&formid=' + val2 + '&path=' + val3 + '&agencyid=' + val4;

			callback = function(usage)
			{
				//window.opener.location.reload();
				window.close();
			}
			ajax(url, vars, callback);
		}
	}
}

function ListFormDataElements ( siteurl ) {
	if ( document.getElementById( 'formId' ).value ) {
		val1 = document.getElementById( 'formId' ).value;
		window.open( siteurl + '/admin/assigndataelements/index/formId/' + val1, "Notes", "width=700, height=400" );
	}
}

function OpenShowHistoryWindow ( siteurl ) {
	var left = (screen.width/2)-(500/2);
	var top  = (screen.height/2)-(400/2);

	if( document.getElementById('formIdhidden') == null ) {
		if ( document.getElementById( 'formId' ).value ) {
			val1 = document.getElementById( 'formId' ).value;
			window.open( siteurl + '/admin/assigndataelements/showhistoricnotes/formId/' + val1, 'Notes', 'width=550, height=385, top='+top+', left='+left+'' );
		}
	}else {
		if ( document.getElementById( 'formIdhidden' ).value || document.getElementById( 'agencyIdhidden' ).value ) {
			val1 = document.getElementById( 'formIdhidden' ).value;
			val2 = document.getElementById( 'agencyIdhidden' ).value;
			window.open( siteurl + '/admin/assigndataelements/showhistoricnotes/formId/' + val1 + '/agencyId/' + val2, 'Notes', 'width=550, height=385, top='+top+', left='+left+'' );
		}
	}
}

function OpenShowSaveWindow( siteurl ) {
	var left = (screen.width/2)-(500/2);
	var top  = (screen.height/2)-(225/2);
	window.open( siteurl + '/admin/assigndataelements/addsavenotes', 'Notes', 'width=500, height=225, top='+top+', left='+left+'' );
}
/*
 *  Author Gauthaman
 *  Data Elements Assignments
 */

function SelectAgencyViewKeyedImage() {
	if(document.getElementById('stateId').value)
	{
		val1 = document.getElementById('stateId').value
		val2 = document.getElementById('agencyId').value

		url =  window.baseUrl+"/remote";
		vars = 'w=selectagencyviewkeyedimage&stateId=' + val1 + "&agencyId=" + val2;
		callback = function(usage)
		{
			document.getElementById('agencyIdDec').disabled = false;
			document.getElementById('agencyIdDec').innerHTML = usage;
		}
		ajax(url, vars, callback);
	}
}

function ImageEntryNoteAdd(siteurl)
{
	if(document.getElementById('note').value)
	{
		val1 = document.getElementById('note').value;
		val2 = document.getElementById('policeReportId').value;

		url =  siteurl + '/data/dataentry/savenotes'
		vars = 'note=' + encodeURIComponent(val1) + '&reportId=' + val2;
		callback = function(usage)
		{
			window.close();
		}
		ajax(url, vars, callback);
	}
}

function SetPotentialVINPass3(vinid)
{

	var fp = document.getElementById("invalidvinmanagement").elements;
	var c = 0;
	var checkid = 0;

	for(i = 0; i < fp.length; i++)
	{
		if(fp[i].type == "radio" && fp[i].checked)
		{
			c++;
			checkid = i;;
		}
	}

	if(c > 0)
	{
		vinDetails = fp[checkid].value;
		vinDetailsvalue = vinDetails.split("$@$");

		potentialvin = vinDetailsvalue[0];
		year = vinDetailsvalue[1];
		make = vinDetailsvalue[2];
		model = vinDetailsvalue[3];

		window.opener.document.getElementById(vinid).value = potentialvin;

		vinStatusId = vinid.replace("VIN", "VIN_Status");
		window.opener.document.getElementById(vinStatusId).value = "V";
		vinYearId = vinid.replace("VIN", "Model_Year");
		window.opener.document.getElementById(vinYearId).value = year;
			window.opener.document.getElementById(vinYearId).readOnly = true;
		vinMakeId = vinid.replace("VIN", "Make");
		window.opener.document.getElementById(vinMakeId).value = make;
			window.opener.document.getElementById(vinMakeId).readOnly = true;
		vinModelId = vinid.replace("VIN", "Model");
		window.opener.document.getElementById(vinModelId).value = model;
			window.opener.document.getElementById(vinModelId).readOnly = true;

		window.close();
	}
	else
	{
		alert("Please select a potential VIN");
	}

}

function SetPotentialVINPass3Valid(potentialvin, vinid, year, make, model)
{
	window.opener.document.getElementById(vinid).value = potentialvin;

	vinStatusId = vinid.replace("VIN", "VIN_Status");
	window.opener.document.getElementById(vinStatusId).value = "V";
	vinYearId = vinid.replace("VIN", "Model_Year");
	window.opener.document.getElementById(vinYearId).value = year;
	window.opener.document.getElementById(vinYearId).readOnly = true;
	vinMakeId = vinid.replace("VIN", "Make");
	window.opener.document.getElementById(vinMakeId).value = make;
	window.opener.document.getElementById(vinMakeId).readOnly = true;
	vinModelId = vinid.replace("VIN", "Model");
	window.opener.document.getElementById(vinModelId).value = model;
	window.opener.document.getElementById(vinModelId).readOnly = true;

}

// Action taken while clicking the
// 1. Cancel button of Invalid VIN Queue confirmation or
// 2. Cancel button of Potential Matches window
// 3. Cancel button of VIN succesfull validation
function SetPotentialVINPass3Close(vinid)
{
	//Setting the focus to the end
	ctrl = window.opener.document.getElementById(vinid);
	pos = ctrl.value.length;
	if(ctrl.setSelectionRange)
	{
		ctrl.focus();
		ctrl.setSelectionRange(pos,pos);
	}
	else if (ctrl.createTextRange) {
		var range = ctrl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', pos);
		range.moveStart('character', pos);
		range.select();
	}

	vinStatusId = vinid.replace("VIN", "VIN_Status");
	window.opener.document.getElementById(vinStatusId).value = "H";

	vinYearId = vinid.replace("VIN", "Model_Year");
	window.opener.document.getElementById(vinYearId).readOnly = false;
	vinMakeId = vinid.replace("VIN", "Make");
	window.opener.document.getElementById(vinMakeId).readOnly = false;
	vinModelId = vinid.replace("VIN", "Model");
	window.opener.document.getElementById(vinModelId).readOnly = false;

	window.close();
}

// Action taken while clicking the Yes button of Invalid VIN Queue confirmation
function SetPotentialVINPass3CloseYes(vinid)
{
	vinStatusId = vinid.replace("VIN", "VIN_Status");
	window.opener.document.getElementById(vinStatusId).value = "H";

	vinYearId = vinid.replace("VIN", "Model_Year");
	window.opener.document.getElementById(vinYearId).readOnly = false;
	vinMakeId = vinid.replace("VIN", "Make");
	window.opener.document.getElementById(vinMakeId).readOnly = false;
	vinModelId = vinid.replace("VIN", "Model");
	window.opener.document.getElementById(vinModelId).readOnly = false;

	window.close();
}

// Action taken while clicking the Cancel button of Invalid VIN Queue confirmation
function SetPotentialVINPass3CloseCancel(vinid)
{
	window.opener.document.getElementById(vinid).value = "";
	vinStatusId = vinid.replace("VIN", "VIN_Status");
	window.opener.document.getElementById(vinStatusId).value = "H";

	vinYearId = vinid.replace("VIN", "Model_Year");
	window.opener.document.getElementById(vinYearId).readOnly = false;
	window.opener.document.getElementById(vinYearId).value = "";
	vinMakeId = vinid.replace("VIN", "Make");
	window.opener.document.getElementById(vinMakeId).readOnly = false;
	window.opener.document.getElementById(vinMakeId).value = "";
	vinModelId = vinid.replace("VIN", "Model");
	window.opener.document.getElementById(vinModelId).readOnly = false;
	window.opener.document.getElementById(vinModelId).value = "";

	window.close();
}

function ajax(url, vars, callbackFunction)
{
	var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("MSXML2.XMLHTTP.3.0");
	request.open("POST", url, true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(vars);
	request.onreadystatechange = function()
	{
		if (request.readyState == 4 && request.status == 200) {
			if (callbackFunction != null) {
				callbackFunction(request.responseText);
			}
		}
	};
}

function ajaxSynchronous(url, vars, callbackFunction)
{
	var request = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("MSXML2.XMLHTTP.3.0");
	request.open("POST", url, false);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.onreadystatechange = function()
	{
		if (request.readyState == 4 && request.status == 200) {
			if (callbackFunction != null) {
				callbackFunction(request.responseText);
			}
		}
	};
	request.send(vars);
}

/**
 * Submitting image entry forms by clicking SAVE button
 */
function formSubmit() {
	$('#entryform').submit();
}

/**
 * To close image viewer, latest note and historic note
 */
function childClose()
{
	// Closing Image Viewer Popup
	if (child && child.open && !child.closed)
		child.close();

	// Closing Latest Note Popup
	if (latestnote && latestnote.open && !latestnote.closed)
		latestnote.close();

	// Closing Historic Note Popup
	if (historicnotes && historicnotes.open && !historicnotes.closed)
		historicnotes.close();
}

/**
 * To close web form, image viewer, latest note and historic note. Also to revert report status.
 */
function childCloseAndRevert()
{
	// Closing Image Web form
	if (webform && webform.open && !webform.closed){
		webform.childClose();
		if( webform.reverStatus )
			webform.reverStatus();
		webform.close();
	}
}

/**
 * To close latest note and historic note
 */
function noteClose()
{
	// Closing Image Viewer Popup
	if (child && child.open && !child.closed)
		child.close();

	// Closing Latest Note Popup
	if (latestnote && latestnote.open && !latestnote.closed)
		latestnote.close();

	// Closing Historic Note Popup
	if (historicnotes && historicnotes.open && !historicnotes.closed)
		historicnotes.close();
}

/*************** Base 64 decode *******************/

var END_OF_INPUT = -1;

var base64Chars = new Array(
	'A','B','C','D','E','F','G','H',
	'I','J','K','L','M','N','O','P',
	'Q','R','S','T','U','V','W','X',
	'Y','Z','a','b','c','d','e','f',
	'g','h','i','j','k','l','m','n',
	'o','p','q','r','s','t','u','v',
	'w','x','y','z','0','1','2','3',
	'4','5','6','7','8','9','+','/'
);

var reverseBase64Chars = new Array();
for (var i=0; i < base64Chars.length; i++){
	reverseBase64Chars[base64Chars[i]] = i;
}

var base64Str;
var base64Count;
function setBase64Str(str){
	base64Str = str;
	base64Count = 0;
}

function ntos(n){
	n=n.toString(16);
	if (n.length == 1) n="0"+n;
	n="%"+n;
	return unescape(n);
}

function readReverseBase64(){
	if (!base64Str) return END_OF_INPUT;
	while (true){
		if (base64Count >= base64Str.length) return END_OF_INPUT;
		var nextCharacter = base64Str.charAt(base64Count);
		base64Count++;
		if (reverseBase64Chars[nextCharacter]){
			return reverseBase64Chars[nextCharacter];
		}
		if (nextCharacter == 'A') return 0;
	}
	return END_OF_INPUT;
}

function decodeBase64(str){
	setBase64Str(str);
	var result = "";
	var inBuffer = new Array(4);
	var done = false;
	while (!done && (inBuffer[0] = readReverseBase64()) != END_OF_INPUT
		&& (inBuffer[1] = readReverseBase64()) != END_OF_INPUT){
		inBuffer[2] = readReverseBase64();
		inBuffer[3] = readReverseBase64();
		result += ntos((((inBuffer[0] << 2) & 0xff)| inBuffer[1] >> 4));
		if (inBuffer[2] != END_OF_INPUT){
			result +=  ntos((((inBuffer[1] << 4) & 0xff)| inBuffer[2] >> 2));
			if (inBuffer[3] != END_OF_INPUT){
				result +=  ntos((((inBuffer[2] << 6)  & 0xff) | inBuffer[3]));
			} else {
				done = true;
			}
		} else {
			done = true;
		}
	}
	return result;
}

/*************** Base 64 decode Ends *******************/

function openChild(imagePath, ImageServerStatus , msg)
{
	var screenWidth = screen.width;
	var screenHeight = screen.height;
	var popupHeight = (screen.availHeight)/2 - 30;
	var popupTop = popupHeight + 30;

	if (!child){
		child = window.open("","child","resizable=yes, scrollbars=yes, width="+screenWidth+" ,height="+popupHeight+" , top="+0 +" ,left="+0);
	}

	child.document.write("<title>LexisNexis</title>\n\t\t");
	child.document.write("<body leftmargin=0 topmargin=0 scroll=yes>")
	if( ImageServerStatus == 1)
		child.document.write("<embed id=\"myid\" width=100% height= 100%  resizable=yes  src="+imagePath+">");
	else
		child.document.write("<br><br><br><br><br><h3 align=center>"+ decodeBase64(msg) +"</h3>");
	child.document.write("</body>");
	child.document.close();
}

function deleteReport(siteurl, reportId)
{
	url = siteurl+"/remote";
	vars = 'w=deletereport&reportId='+reportId;

	callback = function(usage)
	{
		return;
	}
	ajax(url, vars, callback);
}


var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
	for (i = 0; i < s.length; i++){
		// Check that current character is number.
		var c = s.charAt(i);
		if (((c < "0") || (c > "9"))) return false;
	}
	// All characters are numbers.
	return true;
}

function stripCharsInBag(s, bag){
	var i;
	var returnString = "";
	// Search through string's characters one by one.
	// If character is not in bag, append to returnString.
	for (i = 0; i < s.length; i++){
		var c = s.charAt(i);
		if (bag.indexOf(c) == -1) returnString += c;
	}
	return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
	// EXCEPT for centurial years which are not also divisible by 400.
	return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {
			this[i] = 30
		}
		if (i==2) {
			this[i] = 29
		}
	}
	return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("The date format should be : mm/dd/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Please enter a valid month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Please enter a valid day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Please enter a valid 4 digit year between "+minYear+" and "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Please enter a valid date")
		return false
	}
	return true
}

function ValidateForm(){
	if(document.getElementById('fromDate')!=null)
		$fromDate=document.getElementById('fromDate');
	if(document.getElementById('toDate')!=null)
		$toDate=document.getElementById('toDate');
	if($fromDate.value!="")
	{
		if (isDate($fromDate.value)==false){
			$fromDate.focus()
			return false
		}
	}
	if($toDate.value!="")
	{
		if (isDate($toDate.value)==false){
			$toDate.focus()
			return false
		}
	}
	return true
}

/*
 * Code to bring the focus to the first HTML input box by default
 */

function setCursorFocus() {
	document.forms[0].elements[0].focus();
}


//fetch reports from server to fill cache; happens after first report is viewed
function preFetchAllReports(siteurl)
{
	url =  siteurl+"/remote";
	vars = 'w=prefetchallreports';

	callback = function(usage)
	{
		return;
	}
	ajax(url, vars, callback);
}

//fetch next report from server; happens on click of save
function preFetchNextReport(siteurl)
{
	url =  siteurl+"/remote";
	vars = 'w=prefetchnextreport';

	callback = function(usage)
	{
		return;
	}
	ajax(url, vars, callback);
}


function checkSessionTimeout()
{
	var left = (screen.width/2)-(600/2);
	var top = (screen.height/2)-(190/2);
	var screenWidth = screen.width;
	var screenHeight = screen.height;

	url = window.baseUrl + "/remote";
	vars = 'w=sessionwarning&updateLastActivity=false&csrf=' + $('#csrfToken').text();
	callback = function(usage)
	{
		if (usage == "logout") {
			if (webform && webform.open && !webform.closed) {
				webform.childClose();
				if(webform.flagUnLoad)
					webform.flagUnLoad();
				window.allowUnLoad = true;
				webform.close();
			}

			//Closing the timeout window if it is open
			if (usersession && usersession.open && !usersession.closed) {
				usersession.close();
			}
			window.location.href = window.baseUrl + "/logout";
		} else if (usage == "showform") {
			usersession = window.open(
                window.baseUrl + '/admin/users/configure-timeout',
				'UserSession','width=595, height=220,top='+top+',left='+left+''
			)
		}
	}

	ajax(url, vars, callback);
}

function setSessionTimeout()
{
	url = window.baseUrl + "/remote";
	vars = 'w=resetsessiontime';
	callback = function(usage)
	{
		window.close();
	}
	ajax(url, vars, callback);
}

/**
 * Closing child windows on Logout
 */

function closeOpenWindows(){
	var screenWidth = screen.width;
	var screenHeight = screen.height;

	if (webform && webform.open && !webform.closed){
		webform.childClose();
		if(webform.flagUnLoad)
			webform.flagUnLoad();
		webform.close();
	}
}

function CloseEvent()
{
	window.close();
}

function PotentialMatchCheckClose()
{
	$('#basic-modal-content').modal();
	vinId = document.getElementById('vinIdtag').value;

	txt = "This VIN could not be decoded successfully\nClick Yes to send VIN to Invalid Queue\nClick No to re-enter VIN\nClick Cancel to skip";
	caption = "Invalid VIN";
	vbMsg(txt,caption);

	if(isChoice == 6){
		SetPotentialVINPass3CloseYes(vinId);
	}
	else if(isChoice == 7){
		SetPotentialVINPass3Close(vinId);
	}
	else{
		SetPotentialVINPass3CloseCancel(vinId);
	}

	$.modal.close();
}

function PotentialMatchCheckValid(outputmessage,vin,year,make,model)
{
	$('#basic-modal-content').modal();
	vinId = document.getElementById('vinIdtag').value;

	if(confirm(outputmessage))
	{
		SetPotentialVINPass3Valid(vin,vinId, year, make, model);
		window.close();
	}else{
		SetPotentialVINPass3Close(vinId);
	}

	$.modal.close();
}

