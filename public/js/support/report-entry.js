$(function(){

var lock;
$('button, submit, input[type="submit"]').button();
if (init())
{
	setReportEntryDataWidth();
	adjustDynamicSectionHeight($('#reportEntryData'), 7);
	adjustDynamicSectionHeight($('.scrollSection'), 11);
}







function init()
{
	if ($('#reportEntryData').length == 0)
	{
		return false;
	}

	// Set all report entries to be locked together initially
	lock = $('.scroll-locked');

	$('.reportEntry tbody').each(function() {
		var stripeCount = 0;

		$(this).children('tr').each(function() {
			//TODO: Forced assignment first?
			var className = (stripeCount++%2) ? 'even' : 'odd';
			$(this).addClass(className);
		});
	});

	return true;
}

	// Deal with locked scrolling
	$('.scrollSection').scroll(function() {
		if ($(this).hasClass('scroll-locked'))
		{
			// Need to check to see if this one is locked. If not ignore this.
			var scroll = $(this).scrollTop();
			lock.each(function(){
				$(this).scrollTop(scroll);
			});
		}
	});

/****** STUFF DEALING WITH DYNAMIC DIMENSIONS */
	$(window).resize(function(){
		adjustDynamicSectionHeight($('.scrollSection'), 10);
		adjustDynamicSectionHeight($('#reportEntryData'), 7);
	});
	function adjustDynamicSectionHeight($section, padding)
	{
		if ($section.length !== 0) {
			$section.height($(this).height()-$section.offset().top-padding);
		}
	}
	function setReportEntryDataWidth()
	{
		var $container = $('#reportEntryDataContainer');

		var $lastEle = $('#reportEntryDataContainer .reportEntry:last');
		var rightmost = $lastEle.position().left + $lastEle.width();
		$container.width(rightmost);
	}
/****** */

/****** SELECT SEARCH HILIGHT */
	$('td').click(function() {
		if ($(this).parent().hasClass('select-search'))
		{
			$('.select-search').removeClass('select-search');
		}
		else
		{
			$('.select-search').removeClass('select-search');
			$(this).parent().addClass('select-search');

			var fieldname;
			if ($(this).hasClass('field'))
			{
				fieldname = $(this).html();
			}
			else
			{
				fieldname = $(this).siblings('.field').html();
			}

			$('td.field').each(function() {
				if ($(this).html() == fieldname)
				{
					$(this).parent().addClass('select-search');
				}
			});
		}
	});
/****** */

/****** HIDE BLANK*/
	$('button.button-hide-blank').live('click', function() {
		$('tr.value-blank').hide();
		$('button.button-hide-blank').each(function(){
			$(this).children('.ui-button-text').html('Show Blank');
			$(this).removeClass('button-hide-blank').addClass('button-show-blank');
		});
	});
	$('button.button-show-blank').live('click', function() {
		$('tr.value-blank').show();
		$('button.button-show-blank').each(function(){
			$(this).children('.ui-button-text').html('Hide Blank');
			$(this).removeClass('button-show-blank').addClass('button-hide-blank');
		});
	});
/****** */

/****** COLLAPSING */
	$('.reportEntryInfo .collapse-button').click(function() {
		if ($(this).hasClass('ui-icon-triangle-1-n'))
		{
			//Triangle is pointing n(north), collapse the all of the divs.
			$('.reportEntryInfo .collapsible').slideUp( 300, function(){adjustDynamicSectionHeight($('.scrollSection'),10);});
			$('.reportEntryInfo .collapse-button').removeClass('ui-icon-triangle-1-n').addClass('ui-icon-triangle-1-s ui-state-active');

		}
		else if ($(this).hasClass('ui-icon-triangle-1-s'))
		{
			$('.reportEntryInfo .collapsible').slideDown( 300, function(){adjustDynamicSectionHeight($('.scrollSection'),10);});
			$('.reportEntryInfo .collapse-button').removeClass('ui-icon-triangle-1-s ui-state-active').addClass('ui-icon-triangle-1-n');
		}
	});
/****** */

/****** VIEW FORMATTED/RAW */
	$('button.button-view-raw').click(function(){
		$('button.button-view-raw').each(function(){
			var formattedDiv = $(this).closest('.reportEntryData-formatted');
			formattedDiv.hide();
			formattedDiv.siblings('.reportEntryData-raw').first().show();
		});
	});
	$('button.button-view-formatted').click(function(){
		$('button.button-view-formatted').each(function(){
			var formattedDiv = $(this).closest('.reportEntryData-raw');
			formattedDiv.hide();
			formattedDiv.siblings('.reportEntryData-formatted').first().show();
		});
	});
/****** */

/****** LOCKING & UNLOCKING */
	$('.reportEntry .ui-icon-locked').live('click', function(){
		$(this).removeClass('ui-icon-locked ui-state-active').addClass('ui-icon-unlocked ui-state-default');
		$(this).parent('.buttons').parent().children('.scroll-locked').removeClass('scroll-locked').addClass('scroll-unlocked');
		lock = $('.scroll-locked');
	});

	$('.reportEntry .ui-icon-unlocked').live('click', function(){
		$(this).removeClass('ui-icon-unlocked ui-state-default').addClass('ui-icon-locked ui-state-active');
		$(this).parent('.buttons').parent().children('.scroll-unlocked').removeClass('scroll-unlocked').addClass('scroll-locked');
		lock = $('.scroll-locked');
	});
/****** */

/*** HOVERSTATES **/
	$('.reportEntry td').hover(
		function(){ $(this).parent().addClass('tablehover'); },
		function(){ $(this).parent().removeClass('tablehover'); }
	);
	$('.ui-state-default').hover(
		function(){ $(this).addClass('ui-state-hover'); },
		function(){ $(this).removeClass('ui-state-hover'); }
	);
/****** */

				
	$('.button-select-raw').click(function() {
		var ele = $(this).closest('.reportEntrySection').find('.scrollSection');
		selectElementText(ele.get(0), window);
	});

	/**
	 * http://stackoverflow.com/questions/985272/jquery-selecting-text-in-an-element-akin-to-highlighting-with-your-mouse
	 */
	function selectElementText(el, win) {
		win = win || window;
		var doc = win.document, sel, range;
		if (win.getSelection && doc.createRange) {
			sel = win.getSelection();
			range = doc.createRange();
			range.selectNodeContents(el);
			sel.removeAllRanges();
			sel.addRange(range);
		} else if (doc.body.createTextRange) {
			range = doc.body.createTextRange();
			range.moveToElementText(el);
			range.select();
		}
	}
});

