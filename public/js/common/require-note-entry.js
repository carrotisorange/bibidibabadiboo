(function(){
	common.requireNoteEntry = function(options) {
		options = $.extend({
			afterRender: $.noop,
			success: $.noop,
			failure: $.noop,
			beforeSave: $.noop,
			title: 'Note Required',
			prefixContent: '',
			appendContent: ''
		}, options);
		var dialogContent = $(
			'<div>' +
				options.prefixContent +
				'<textarea id="report-entry-note"></textarea><br />' +
				options.appendContent +
			'</div>'
		);

		function saveNote() {
			var noteText = $.trim($('#report-entry-note').val());
			if (noteText == '') {
				alert('You must enter a note');
				return;
			}
			if (options.beforeSave() === false) {
				return;
			}

			var loader = progressDialogManager.create().show();
			$.post(
				window.baseUrl + '/data/report-entry/notes',
				{
					note: $('#report-entry-note').val(),
					csrf: $('#csrf').val()
				},
				function(data) {
					// Will log to console if it does, no other action to take though
					hasCsrfError(data);

					destroyDialog(true);
					loader.hide(true);
				}
			);
		}

		function destroyDialog(success) {
			dialogContent.dialog('hide');

			try {
				if (typeof(success) != 'undefined' && success === true) {
					options.success();
				} else {
					options.failure();
				}
			} catch (err) {
				console.error(err);
				alert('An error occurred while processing the response. Data may not be saved successfully.');
			}

			dialogContent.dialog('destroy');
			dialogContent.remove();
		}

		dialogContent.dialog({
			title: 'Note Required',
			width: 300,
			minWidth: 300,
			height: 'auto',
			modal: true,
			buttons: {
				'Save': saveNote,
				'Cancel': destroyDialog
			}
		});
		options.afterRender();
	}
})()