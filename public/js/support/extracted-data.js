$(function(){
	$('h2').click(function(){
		var table = $(this).attr('data-table');
		$('div#' + table).toggle(400);
	});
	$('table tr.empty').hover(function(){
		$(this).addClass('hover');
	}, function(){
		$(this).removeClass('hover');
	});
	$('table tr').hover(function(){
		$(this).addClass('hover');
	}, function(){
		$(this).removeClass('hover');
	});
});