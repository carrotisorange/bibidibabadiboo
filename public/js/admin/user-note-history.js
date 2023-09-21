var userNoteHistory = (function() {
    function addNote() {
        $.ajax({
            url: window.baseUrl + '/admin/users/add-note',
            type: 'post',
            data: {
                userId: $('#userId').val(),
                note: $('#note').val(),
                csrf: $('#csrf').val()
            },
            success: function() {
                window.close();
            }
        });
    }
    
    function switchSaveButton() {
        if ($.trim($(this).val()) == '') {
            $('#save').attr('disabled', 'disabled');
        } else {
            $('#save').removeAttr('disabled');
        }
    }
    return {
        switchSaveButton: switchSaveButton,
        addNote: addNote
    };
})();
window.userNoteHistory = userNoteHistory;
$(function() {
    $('#save').click(userNoteHistory.addNote);
    $('#note').keyup(userNoteHistory.switchSaveButton);
    $('#cancel').click(function(){window.close();});
});