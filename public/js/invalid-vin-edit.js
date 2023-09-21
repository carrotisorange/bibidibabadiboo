/**
 * @copyright (c) 2011 LexisNexis. All rights reserved.
 */

$(function() {
	var vv = {
		vehicles: null,
		imageViewer: createImageWindow(),
		showInvalidVinPopupProp: true,
		progressDialog:
			$('<div style="width:220px;height:19px;text-align:center;">' +
				'<img src="' + window.baseUrl + '/images/ajax-loader_1.gif"/>' +
			  '</div>').dialog({
				modal: true, title: 'Please wait', autoOpen: false
			}),
		notifyUser: function(msg, append) {
			if (typeof(append) != 'undefined' && append){
				$('div#userNotificationMessage').append(msg).show();
			}
			else{
				$('div#userNotificationMessage').html(msg).show();
			}
		},
		refreshParentWindow: function() {
			window.opener.location.href =
				window.baseUrl + '/admin/invalid-vin/index' + $('#parentWindowRefreshParams').val();
		},
		resetValidationInfo: function() {
			vehicles = null;
			$('#btnSaveValidatedVin').attr('disabled', 'disabled');
			$('#potentialMatches tbody').html(''); //clear previous result
			$('#potentialMatchesWrapperDiv').hide();
		},
		validateVin : function() {
			$('div#userNotificationMessage').hide();
			vv.progressDialog.dialog('open');
			vv.resetValidationInfo();

			$.ajax({
				type: 'GET',
				url:  window.baseUrl + '/admin/invalid-vin/potential-match-json/',
				dataType: 'json',
				success: vv._validateVinJsonAjaxHandler,
				error:function (xhr, ajaxOptions, thrownError){
					console.log('JSON Failure - ', xhr.status, ajaxOptions, thrownError);
					vv.progressDialog.dialog('close');
					$('#proposedVin').focus();
				},
				data: {
					vin: $.trim($('#proposedVin').val()),
					vinLocation: 'invalidVinQueue',
					csrfToken: $('#csrf').val()
				}
			});
		},
		_validateVinJsonAjaxHandler: function(data) {
			console.log('JSON Success - ', data);
			vv.progressDialog.dialog('close');

			if ( data.count == 0 ) {
				if (vv.showInvalidVinPopupProp){
					$('<div>This VIN could not be decoded successfully.</div>').dialog({
						modal: true, title: 'Invalid VIN',
						buttons: {'Ok': function(){$(this).dialog('destroy');$('#proposedVin').focus();}}
					});
				}
				else {
					vv.notifyUser('<b>This VIN could not be decoded successfully.</b></div>', true);
					vv.showInvalidVinPopup(true);
				}
			}
			else if ( data.count >= 1 ) {
				vehicles = data.vehicles;
				var checked = '';
				headerText = 'Potential Matches';
				if (vehicles.length == 1){
					checked = 'checked="checked"';
					$('#btnSaveValidatedVin').removeAttr('disabled');
					$('#btnDeselectPotentialVins').removeAttr('disabled');
					if (vehicles[0].VIN.toUpperCase() == $('#proposedVin').val().toUpperCase()){
						headerText = 'Exact Match';
					}
				}
				$('#potentialMatchHeader').text(headerText + ':');
				$.each(vehicles, function(i, vehicle){
					$('#potentialMatchTable > tbody:last').append(
						'<tr class="' + ((i%2 == 1) ? 'odd' : 'even') + '">' +
							'<td>' +
								'<input type="radio" ' + checked + ' name="potentialVinMatchGroup" value="' + i + '"/>' +
							'</td>' +
							'<td>' + vehicle.VIN + '</td>' +
							'<td>' + vehicle.Year + '</td>' +
							'<td>' + vehicle.Make + '</td>' +
							'<td>' + vehicle.Model + '</td>' +
						'</tr>'
					);
				});
				$('#potentialMatchesWrapperDiv').show();
			}
		},
		markAsInvalid: function() {
			$('div#userNotificationMessage').hide();
			vv.progressDialog.dialog('open');

			$.ajax({
				type: 'POST',
				url:  window.baseUrl + '/admin/invalid-vin/mark-as-invalid-vin-json/',
				dataType: 'json',
				success: vv._markAsInvalidJsonAjaxHandler,
				error:function (xhr, ajaxOptions, thrownError){
					console.log('JSON Failure - ', xhr.status, ajaxOptions, thrownError);
					vv.progressDialog.dialog('close');
					$('#proposedVin').focus();
					alert('Mark as Invalid failed. Please retry.');
				},
				data: {
					vinInvalidQueueId: $('#vinInvalidQueueId').val(),
					csrfToken: $('#csrf').val(),
					processingStartTime: $('#processingStartTime').val(),
					processingEndTime: $('#processingEndTime').val()
				}
			});
		},
		_markAsInvalidJsonAjaxHandler: function(data) {
			vv._handleResponseData(data, 'Marked as invalid. Loaded next vehicle.');
		},
		saveValidatedVin : function() {
			$('div#userNotificationMessage').hide();
			vv.progressDialog.dialog('open');

			checkedVinRadio = $("input[name='potentialVinMatchGroup']:checked");
			if ( checkedVinRadio.length > 0 ) {
				vehicle = vehicles[checkedVinRadio.val()];
				console.log('Selected vehicle: ', vehicle);

				$.ajax({
					type: 'GET',
					url:  window.baseUrl + '/admin/invalid-vin/save-validated-vin-json/',
					dataType: 'json',
					success: vv._saveValidatedVinJsonAjaxHandler,
					error:function (xhr, ajaxOptions, thrownError){
						console.log('JSON Failure - ', xhr.status, ajaxOptions, thrownError);
						vv.progressDialog.dialog('close');
						$('#proposedVin').focus();
						alert('Save failed. Please retry.');
					},
					data: {
						vinInvalidQueueId: $('#vinInvalidQueueId').val(),
						csrfToken: $('#csrf').val(),
						proposedVin: vehicle.VIN,
						processingStartTime: $('#processingStartTime').val(),
						processingEndTime: $('#processingEndTime').val()
					}
				});
			}
			else {
				$('<div style="color:red;">Please select a potential match.</div>').dialog({
					modal: true, title: 'Warning',
					buttons: {'Ok':function(){$(this).dialog('destroy');}}
				});
			}
		},
		_saveValidatedVinJsonAjaxHandler: function(data) {
			vv._handleResponseData(data, 'Saved validated VIN. Loaded next vehicle.');
		},
		_handleResponseData: function(data, msg) {
			console.log('JSON Success - ', data);
			vv.progressDialog.dialog('close');

			if ( data && data.status ) {

				vv.refreshParentWindow();
				var nextInvVeh = data.nextInvalidVehicle;

				if ( nextInvVeh ) {
					vv.resetValidationInfo();
					$('#vinInvalidQueueId').val(nextInvVeh.vinInvalidQueueId);
					$('#reportId').val(nextInvVeh.reportId);
					$('#vin').val(nextInvVeh.invalidVin);
					$('#year').val(nextInvVeh.year);
					$('#make').val(nextInvVeh.make);
					$('#model').val(nextInvVeh.model);
					$('#proposedVin').val(nextInvVeh.invalidVin);

					validatingOnVinLoad();
					vv.notifyUser(msg + '<br /><br />');
					vv.imageViewer.openWindow('/admin/invalid-vin/image-viewer-alternatiff');
				}
				else {
					console.log('vv.closeWindows()');
					window.close();
				}
			}
			else {
				alert('Save failed. Reason: ' + ((data && data.reason) ? data.reason : 'unknown'));
			}
		},
		showInvalidVinPopup: function(showInvalidVinPopupProp){
			vv.showInvalidVinPopupProp = showInvalidVinPopupProp;
		}
	};

	function formExit(){
		window.close();
	}
	function deselectPotentialVin(){
		$("input[name='potentialVinMatchGroup']").attr('checked', false);
		$('#btnDeselectPotentialVins').attr('disabled', 'disabled');
		$('#btnSaveValidatedVin').attr('disabled', 'disabled');
	}
	function validatingOnVinLoad(){
		vv.showInvalidVinPopup(false);
		vv.validateVin();
	}
	window.vv = vv;

	// Debug Code Saver. This allows the application to continue working even if debug code is left in.
	if (typeof(console) == 'undefined') console = {log: function() {}, trace: function() {}};

	$("input[name='potentialVinMatchGroup']").live('click', function(){
		checkedVinRadio = $("input[name='potentialVinMatchGroup']:checked");
		index = checkedVinRadio.val();
		if ( typeof(index) != 'undefined' ) {
			$('#btnSaveValidatedVin').removeAttr('disabled');
		}
		$('#btnDeselectPotentialVins').removeAttr('disabled');
	});

	$('#btnMarkAsInvalid').click(vv.markAsInvalid);
	$('#btnValidateVin').click(vv.validateVin);
	$('#btnSaveValidatedVin').click(vv.saveValidatedVin);
	$('#btnExit').click(formExit);
	$('#btnDeselectPotentialVins').click(deselectPotentialVin);

	vv.imageViewer.openWindow('/admin/invalid-vin/image-viewer-alternatiff');

	$(window).bind('unload', vv.imageViewer.closeWindow);
	$(document).ready(function(){
		validatingOnVinLoad();
	});
	//Set focus on 'Proposed VIN' field upon page load
	$('#proposedVin').focus();
});
