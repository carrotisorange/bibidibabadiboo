
$(function() {
	$('.reportEntry a').click(function(e) {
		e.preventDefault();
	});
	$('.reportEntry').click(function() {
		var url = $('a', this).attr('href');
		window.reportEntry.openInputWindow(window.baseUrl + url);
	});
});