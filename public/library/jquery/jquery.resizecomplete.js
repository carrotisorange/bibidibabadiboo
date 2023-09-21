/**
 * Atempts to reduce the number of resize events fired by certain browsers by
 * waiting a certain amount of time and then checking if resizing has stopped
 * (that the height/width has stayed the same) and then executing the callback.
 * Also works against objects instead of just the window (as default jquery does)
 *
 * Options:
 * * delay: The amount of time to wait is set by the 'delay' option or 200 default.
 *		- If you set delay too small it will reduce the effectiveness (multiple events
 *			may be fired because in short spans of time 'staying still' is a lot easier).
 *		- If you set it too large you will see a delay from when you resize until the
 *			callback is called.
 *
 * Useage:
 * $('div.jqueryobj').resizeComplete(
 *		function() { $(this).append('yay'); },
 *		{delay:200}
 * );
 */
$(function(){
	$.fn.resizeComplete = function(callback, options){
		var settings = {
			delay : 200
		}
		var	$config = $.extend({}, settings, options);

		return this.each(function(){
			var $element = $(this);
			var triggered = false;
			var lastWidth;
			var lastHeight;

			$(this).bind('resizeDone', function(event){
				callback.apply(this);
			});

			$(window).resize(function() {
				if (triggered) return;
				triggered = true;
				triggerCheck();
			});

			// Start checking to see if it has stopped resizing
			function triggerCheck()
			{
				lastWidth = $(this).width();
				lastHeight = $(this).height();
				window.setTimeout(check, $config.delay);
			}

			// Check until we have stopped and then fire the callback.
			function check()
			{
				if (!triggered) return;

				if ($(this).width() == lastWidth
					&& $(this).height() == lastHeight)
				{
					triggered = false;
					$element.trigger('resizeDone');
				}
				else
				{
					triggerCheck();
				}
			}
		});
	}
});