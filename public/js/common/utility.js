(function(){
	common.utility = {
		'fullEventStop': function(e) {
			e.stopImmediatePropagation();
			e.stopPropagation();
			e.preventDefault();
		},
		'isEventStopped': function(e) {
			return (e.isDefaultPrevented()
			|| e.isImmediatePropagationStopped()
			|| e.isPropagationStopped());
		}
	}
})()