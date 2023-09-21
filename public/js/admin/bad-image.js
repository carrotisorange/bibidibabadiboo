$(function() {
	$('#badImageResults tbody').addClass('clickable');
	$('#badImageResults tbody tr:not(.greyedOut)').on('click', function() {
		var reportId = $('.reportId', this).text();
		$.getJSON(window.baseUrl + "/admin/bad-image/get-user-interface-info?reportId=" + reportId,
			function(data) {
				if (data.isNewApp == 0) {
					alert('Please use Old App to view this report, '+ reportId);
				} else {
					window.reportEntry.openInputWindow(
						window.baseUrl + '/admin/bad-image/report-entry?reportId=' + reportId
					);
				}
				
			}
		);
		
	});
});
