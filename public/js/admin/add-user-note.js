var addUserNote = (function() {
	function addNote() {
		var note = $('#note').val();
		$('#note', window.opener.document).val(note);
		$('#saveUser', window.opener.document).submit();
		window.close();
	}

	function switchSaveButton() {
		if ($.trim($(this).val()) == '') {
			$('#save').attr('disabled', 'disabled');
		} else {
			$('#save').removeAttr('disabled');
		}

	}
	return{
		switchSaveButton: switchSaveButton,
		addNote: addNote
	};
})();
window.addUserNote = addUserNote;
$(function() {
	$('#save').click(addUserNote.addNote);
	$('#note').keyup(addUserNote.switchSaveButton);
	$('#cancel').click(function(){
        window.close();
    });
});
