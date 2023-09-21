(function(){
	common.badImageManager = function(formObj) {
		this.send = function() {
			formObj.requireNoteEntry({
				success: function() {
					formObj.windowClose.isAllowed(true);
					formObj.redirectFormImageAction('bad');
				}
			});
		},
		this.rekey = function() {
			formObj.requireNoteEntry({
				success: function() {
					formObj.windowClose.isAllowed(true);
					formObj.redirectFormImageAction('rekey');
				}
			});
		},
		this.discard = function() {
			formObj.requireNoteEntry({
				success: function() {
					formObj.windowClose.isAllowed(true);
					formObj.redirectFormImageAction('discard');
				}
			});
		},
		this.reorder = function() {
			formObj.requireNoteEntry({
				afterRender: function() {
					$('#reorder-yes, #reorder-no').bind('click', function() {
						$this = $(this);
						if ($this.attr('id') == 'reorder-yes') {
							$('#reorder-date-container').show();
						} else {
							$('#reorder-date-container').hide();
						}
					});
					$('#reorder-date').val($.datepicker.formatDate('yy-mm-dd', new Date())).datepicker({
						dateFormat: 'yy-mm-dd',
						beforeShow: function(input, inst) {
							inst.dpDiv.css({marginTop: -input.offsetHeight + 'px', marginLeft: input.offsetWidth + 'px'});
						}
					});
				},
				beforeSave: function() {
					var reorder = $('#reorder-yes, #reorder-no').filter(':checked');
					if (reorder.length == 0) {
						alert('You must select either Yes or No to continue.');
						return false;
					}

					return true;
				},
				success: function() {
					$('<input type="hidden" name="reorder" value="" />')
						.val($('#reorder-yes, #reorder-no').filter(':checked').val())
						.appendTo('#formContainer');
					$('<input type="hidden" name="reorder-date" value="" />')
						.val($('#reorder-date').val())
						.appendTo('#formContainer');

					formObj.submitFormImageAction('reorder', true);
				},
				appendContent:
					'Reorder: ' +
					'<label><input type="radio" name="reorder" id="reorder-yes" value="1" /> Yes</label>' +
					'<label><input type="radio" name="reorder" id="reorder-no" checked="checked" value="0" /> No</label>' +
					'<div id="reorder-date-container" class="ui-helper-hidden"><br />' +
						'<label>Date: <input type="text" name="reorder-date" id="reorder-date" /></label>' +
					'</div>'
			});
		}
	}
})();