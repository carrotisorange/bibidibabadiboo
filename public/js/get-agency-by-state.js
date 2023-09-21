$(function() {
	$('#state').change(modifyAgencyByState);

	function modifyAgencyByState()
	{
		var stateId = $('#state').val();

		if (stateId == "all") {
			stateId = '';
		}

		$.getJSON(window.baseUrl + "/admin/metrics/get-agencies-by-state?stateId=" + stateId,
			function(data) {
				$('#agency').html('<option value="all" label="All">All');
				$.each(data, function(key, value) {
					$('<option value="' + key + '" label="' + value + '">' + value + '</option>').appendTo('#agency');
				});
			}
		);
	}
});

