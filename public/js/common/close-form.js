(function() {
	common.windowClose = (function(mainObj) {
		var isAllowed = false;
		var checkFormInput = true;

		function initialize() {
			// we have to do unbind here because otherwise when iframe gets reloaded
			// and this function gets called - it will mess up binding

			// Temporarily Commented while fixing the confirmClose and hasDataChanged Function
			//parent.$(parent.window).unbind('beforeunload').bind('beforeunload', confirmClose);
			
			parent.$(parent.window).unbind('beforeunload').bind('beforeunload', doCloseActions);
		}

		function confirmClose(e) {
			// we need isClosedFromChildWindow() to determine if closure triggered from parent window,
			// in that case we dont need to show confirmation because its already showed when user tried to click
			// on some link while keying form was opened
			if (isAllowed || !isClosedFromChildWindow()) {
				return;
			}
			if (checkFormInput && mainObj.windowClose.hasDataChanged()) {
				return 'There are unsaved data in your window. Do you want to close it?';
			}
		}

		function isClosedFromChildWindow() {
			// When user opens form, in parent window we are setting flag allowUnload = false, when we are closing form window from
			// parent window we are setting allowUnload = true, so if flag currently = false - form window closed from itself
			// (exit button clicked or window was manualy closed, etc)
			return typeof(window.parent.opener.reportEntry) != 'undefined' &&
				!window.parent.opener.reportEntry.getAllowUnload() &&
				typeof(window.parent.opener.logoutClicked) == 'undefined';
		}

		function doCloseActions() {
			// determine if closure of child window was triggered from itself
			// or from parent window (while keying form was opened user clicked on some link in the parent window).
			var closedFromChildWindow = isClosedFromChildWindow();
			// when unload event triggered from child window we need to set flag allowUnload = true so
			// user will be able to click on links in parent window without confirmation
			if (closedFromChildWindow){
				window.parent.opener.reportEntry.setAllowUnload(true);
			}

			if (isAllowed) {
				return;
			}

			switch ($('#entryFlow').val()) {

				case 'entry':
					if (closedFromChildWindow){
						window.parent.opener.location.href = window.location.href.replace(window.location.pathname, window.baseUrl + '/data/report-entry/exit');
					}
					else{
						$.ajax({async: false, url: window.baseUrl + '/data/report-entry/exit'});
					}
					break;

				case 'bad image':
				case 'discard':
					$.ajax({async: false, url: window.baseUrl + '/data/report-entry/cleanup'});
					if (closedFromChildWindow){
						window.parent.opener.location.reload();
					}
					break;

				case 'view':
					$.ajax({async: false, url: window.baseUrl + '/data/report-entry/cleanup'});
					break;
			}
			// if user clicked logout link - performing logout in the end, otherwise
			// ajax requests or redirections called after logout may not work properly
			// because user will be already logged out
			if (typeof(window.parent.opener.logoutClicked) != 'undefined'){
				window.parent.opener.location.href = window.opener.$('#logout').attr('href');
			}
			window.parent.imageWindow.closeWindow();
			window.parent.common.noteWindow.closeWindow();
		}

		return {
			initialize: initialize,
			isAllowed: function(v) {isAllowed = v;},
			checkFormInput: function(v) {checkFormInput = v;}
		};
	});
})();