(function() {
	common.vinValidation = (function() {
		var progressDialog = {};
		var statusInvalid = 'E';
		var statusValid = 'V';
		var validationHistory = {};
		var vehicle = {};

		function addToHistory(vehicle) {
			if (typeof(vehicle.originalVIN) != 'undefined' &&
				typeof(validationHistory[vehicle.originalVIN]) == 'undefined') {
				validationHistory[vehicle.originalVIN] = vehicle;
			}
		}

		function pullFromHistory(vin) {
			if (typeof(vin) != 'undefined' && typeof(validationHistory[vin]) != 'undefined') {
				return validationHistory[vin];
			}
		}

		function showInvalidVinPopup() {
			var vinValidationObj = this;
			$('<div id="vinValidationDialog">' +
					'This VIN could not be decoded successfully<br/>' +
					'<div style="padding-left:20px;padding-top: 10px;">' +
						'Click <b>Yes</b> to accept the invalid VIN.<br/>' +
						'Click <b>No</b> to re-enter VIN.<br/>' +
						
					'</div>' +
				'</div>').dialog({
					modal: true,
					title: 'Invalid VIN',
					width: 450,
					zIndex: 0,
					buttons: [
						//change status to E, focus on year field
						{text: "Yes", click: this.invalidVinFunc},
						//mark vin as invalid, left focus to the vin field, previously entered fields are present
						{text: "No", click: function(){vinValidationObj.reEnterVinFunc(statusInvalid, 'InvalidVin_No');}},
						
					],
					close: this.skipVinFunc
				});
		}

		function showExactMatchPopup(vehicle) {
			var vinValidationObj = this;
			$('<div id="vinValidationDialog">' +
						'<b>VIN:</b> ' + vehicle.VIN + '<br/>' +
						'<b>Year:</b> ' + vehicle.Year + '<br/>' +
						'<b>Make:</b> ' + vehicle.Make + '<br/>' +
						'<b>Model:</b> ' + vehicle.Model + '<br/><br/>' +
						'Click <b>Ok</b> to accept VIN.<br/>Click <b>Cancel</b> to re-enter VIN.' +
				'</div>').dialog({
					modal: true,
					title: 'Accept VIN',
					width: 450,
					zIndex: 0,
					buttons: [
						// accepting VIN as valid, filling out Year, Make, Model and VIN fields
						{text: "Ok", click: this.okFunc},
						// mark vin as valid, left focus on the vin field, previously entered fields are present
						{text: "Cancel", click: function(){vinValidationObj.reEnterVinFunc(statusValid);}}
					],
					close: function(){vinValidationObj.reEnterVinFunc(statusValid);}
				});
		}

		function showPotentialMatchPopup(vehicles) {
			var vinValidationObj = this;
			var potentialVinMatchHtml =
					'<div id="vinValidationDialog"> ' +
						'<table class="hor-zebra">' +
							'<thead>' +
								'<tr>' +
									'<td></td><td>VIN</td><td>Year</td><td>Make</td><td>Model</td>' +
								'</tr>' +
							'</thead>' +
							'<tbody>';

				$.each(vehicles, function(i, vehicle) {
					potentialVinMatchHtml +=
						'<tr>' +
							'<td>' +
								'<input type="radio" name="potentialVinMatchGroup" value="' + i + '"' +
									((i == 0) ? ' checked="checked" ' : '') +
								'/>' +
							'</td>' +
							'<td>' + vehicle.VIN + '</td>' +
							'<td>' + vehicle.Year + '</td>' +
							'<td>' + vehicle.Make + '</td>' +
							'<td>' + vehicle.Model + '</td>' +
						'</tr>';
				});

				potentialVinMatchHtml +=
							'</tbody>' +
						'</table>' +
						'<div style="display:none;color:red;text-align:center;" class="selectOneErrorMsg">' +
							'Please select at least one.' +
						'</div>' +
						'Click <b>Add</b> to accept VIN.<br/>' +
						'Click <b>Cancel</b> to re-enter VIN.' +
					'</div>';

				$(potentialVinMatchHtml).dialog({
					modal: true,
					title: 'Potential VIN matches',
					width: 600,
					buttons: [
						// accepting VIN as valid, filling out Year, Make, Model and VIN fields
						{text: "Add", click: this.acceptFunc},
						//mark vin as invalid, left focus to the vin field, previously entered fields are present
						{text: "Cancel", click: function(){vinValidationObj.reEnterVinFunc(statusInvalid, 'PotentialMatch_Cancel');}}
					],
					close: function(){vinValidationObj.reEnterVinFunc(statusInvalid, 'PotentialMatch_Cancel');}
				});
		}

		function ajaxErrorHandler (xhr, ajaxOptions, thrownError) {
			console.log("JSON Failure - ", xhr.status, ajaxOptions, thrownError);
			progressDialog.hide(true);
			callback(getResponse('E'));
		}

		function asyncVinValidation(data, url, message) {
			var vehicleFromHistory = this.pullFromHistory(data.vin);
			if (vehicleFromHistory) {
				this.jsonResultHandler(vehicleFromHistory);
			} else {
				if (typeof(message) == 'undefined') {
					var message = 'Please wait...';
				}
				this.progressDialog = window.progressDialogManager.create().show(message);
				vinValidationObj = this;
				$.ajax({
					type: 'GET',
					url: window.baseUrl + url,
					dataType: 'json',
					// doing this way (instead of just "success: this.jsonResultHandler") because
					// we need to have vinValidationObj context inside jsonResultHandler function
					success: function(result) {
						vinValidationObj.jsonResultHandler(result)
					},
					error: ajaxErrorHandler,
					data: data
				});
			}
		}

		function jsonResultHandler(json) {
			
			if (hasCsrfError(json)) {
				// TODO: Need business clarification
			} else {
				this.addToHistory(json);
				var vin = json.originalVIN;
				console.log("JSON Success - ", json);
				this.progressDialog.hide(true);
				if (json.count == 0) {
					if (typeof(json.errorMessage) != 'undefined') {
						//@TODO: handle this situation
						return;
					}
					this.showInvalidVinPopup();
				}
				else if (json.count == 1 && json.vehicles[0].VIN === vin) {
					this.vehicle = json.vehicles[0];
					this.showExactMatchPopup(this.vehicle);
				}
				// >= 1 VIN match
				else if (json.count >= 1) {
					this.vehicle = json.vehicles;
					console.log("more than 1 match or VIN mismatch", this.vehicle);
					this.showPotentialMatchPopup(this.vehicle);
				}
			}
		}

		return {
			showInvalidVinPopup: showInvalidVinPopup,
			showExactMatchPopup: showExactMatchPopup,
			showPotentialMatchPopup: showPotentialMatchPopup,
			ajaxErrorHandler: ajaxErrorHandler,
			asyncVinValidation: asyncVinValidation,
			progressDialog: progressDialog,
			statusInvalid: statusInvalid,
			statusValid: statusValid,
			jsonResultHandler: jsonResultHandler,
			vehicle: vehicle,
			pullFromHistory: pullFromHistory,
			addToHistory: addToHistory
		}
	})()
})();
