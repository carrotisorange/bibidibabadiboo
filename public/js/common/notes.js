(function() {
	common.noteWindow = (function() {
		var noteWindow;

		function isReady() {
			return (typeof(noteWindow) !== 'undefined' && !noteWindow.closed);
		}

		function closeWindow() {
			if (!isReady()) return;
			noteWindow.close();
		}

		function displayNotesIfExist() {
			if ($('#formIframe').contents().find('#hasNotes').val() > 0 && !isReady()) {
				reportNotes();
			}
		}

		function openWindow(location) {
			var view = {
				width: (screen.availWidth) ? screen.availWidth : screen.width,
				height: (screen.availHeight) ? screen.availHeight : screen.height
			};
			var left = (view.width - 500) / 2;
			var top = (view.height - 350) / 2;

			noteWindow = window.open(
				location,
				'reportNotes',
				'left=' + left + 'px,top=' + top + 'px,width=500,height=350'
			);
		}

		function reportNotes() {
			openWindow(window.baseUrl + '/data/report-entry/notes');
		}

		function reportNotesViewOnly() {
			openWindow(window.baseUrl + '/data/report-entry/notes?viewOnly=yes');
		}

		return {
			closeWindow: closeWindow,
			openWindow: openWindow,
			displayNotesIfExist: displayNotesIfExist,
			reportNotes: reportNotes,
			reportNotesViewOnly: reportNotesViewOnly,
			isReady: isReady
		}
	})();
})()