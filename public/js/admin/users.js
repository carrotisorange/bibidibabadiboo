var userManagement = (function() {
    function switchAssignButton() {
        if ($('#formId').val() == '' && $('#agencyFormId').val() == '') {
            $('#assign').attr('disabled', 'disabled');
        }
        else {
            $('#assign').removeAttr('disabled');
        }
    }
    
    function assignForm() {
        var formId = $('#formId').val() != '' ? $('#formId').val() : $('#agencyFormId').val();
        var allowReport = ($('#processingStartTime').val() != '' && $('#processingEndTime').val() != '');
        var allowToAssign = true;
        
        // JQM: jQuery selector compatibility issue fixed, changed the name selector of input(formIds)
        $('input[name="formIds[]"]').each(function() {
            if (formId == $(this).val()) {
                allowToAssign = false;
            }
        });
        if (!allowToAssign) {
            if(allowReport){
                assignReport(formId);
            }else{
                alert('The List is already added');
            }
            return;
        }
        
        $.ajax({
            url: window.baseUrl + '/admin/users/assign-form',
            type: 'POST',
            data: {
                formId: formId,
                agencyId: $('#agencyId').val(),
                stateId: $('#stateId').val(),
                userId: $('#userId').val(),
                csrf: $('#csrf').val()
            },
            success: function(formHtml) {
                if (!hasCsrfError(formHtml)) {
                    $('#formAssigned').append(formHtml);
                }
            }
        });
        if(allowReport)assignReport(formId);
    }

    /* Start: Assign Report based on Date Filtering */
    function assignReport(formId){
        $("#assign").text("Assigning..");
        $.ajax({
        url: window.baseUrl + '/admin/users/assign-report',
        data: {
            formId: formId,
            startDate: $('#processingStartTime').val(),
            endDate: $('#processingEndTime').val(),
            userId: $('#userId').val(),
            csrf: $('#csrf').val()
        },
        success: function(data) {
            if (!hasCsrfError(data)) {
                if (typeof data['html'] != "undefined") {
                    
                    if (data['html']=='') {
                        alert('No Reports Available!');
                    }
                    $("#assign").text("Assign");
                    if ($('.TR_FORM_'+formId).length) {                       
                       $('.TR_FORM_'+formId).remove();                      
                    }
                    $('#daterangereportList').show();                                        
                    $('.daterangereportTable tbody').append(data['html']); 
                    if ($(".daterangereportTable tbody tr").length > 7) {
                         $("#verticalscroller").addClass("reportTableScroller");                         
                    }
                    if ($(".daterangereportTable tbody tr").length==0) {
                        $("#daterangereportList").css("display","none");
                    } 
                }
            }
        },
        error: function(){
            alert('Something went wrong, Please try again later');
        }
        });
    }
    
    /* End: Assign Report based on Date Filtering */  

    /* Clear date */
    function clearDate(){
     $('#processingStartTime').val('');
     $('#processingEndTime').val('');
     return false;
    }
    
    /* End Clear date */

    /* set Report to user*/
    function setReport() {
        var reportId = $('#reportID').val();
        if (reportId == '') return false;
        if (isNaN($('#reportID').val())) {alert('Not a valid Number');return false;}
        var allowToAssign = true;
        
        // JQM: Jquery selectors updated.
        $('input[name="reportIdAssigned\[\]"], input[name="reportIdToAssign\[\]"], input[name="userReportAssigned\[\]"], input[name="availableReportIds\[\]"]').each(function() {
            if (reportId == $(this).val()) {
                allowToAssign = false;
            }
        });
        
        if (!allowToAssign) {
            alert('The Report is already added');
            return;
        } 
        $("#setreport").text("Assigning..");
        $.ajax({
            url: window.baseUrl + '/admin/users/set-report',
            data: {
                reportId: reportId,
                userId: $('#userId').val(),
                csrf: $('#csrf').val()
            },
            success: function(data) {
                if (!hasCsrfError(data)) {
                    if (typeof data['html'] != "undefined") {
                    if(data['html']!=''){
                    $('#reportList').show(); 
                    $('.reportList').append(data['html']);
                    $('#reportID').val("");                    
                    }
                    if(data['message']!='')
                    alert(data.message);
                    }
                    $("#setreport").text("Assign");
                }
            },
            error: function(){
                alert('Something went wrong, Please try again later');
            }
        });
    }
    /* End set Report to user */


    /* Allow Only Numbers For ReportID Field */
    $('#reportID').on('input', function (event) { 
       this.value = this.value.replace(/[^0-9]/g, '');
    });
    /* End Allow Only Numbers FOr ReportId Field */
    
    function populateAgencyList(stateId) {
        if (typeof(stateId) == 'undefined') return false;
        $.ajax({
            url: window.baseUrl + '/admin/users/get-agency-by-state-id',
            type: 'POST',
            data: {
                stateId: stateId,
                csrf: $('#csrf').val()
            },
            success: function(agencyList) {
                var html = '<option label="Select an Agency" value="">Select an Agency</option>';
                for (var agency in agencyList) {
                    html += '<option label="' + agencyList[agency] + '" value="' + agency + '">' + agencyList[agency] + '</option>';
                }
                $('#agencyId').html(html).removeAttr('disabled');
            }
        });
    }
    
    function populateFormList(stateId, agencyId, formElementId) {
        if (typeof(stateId) == 'undefined') return false;
        
        var data = {
            stateId: stateId,
            agencyId: '',
            csrf: $('#csrf').val()
        };
        if (typeof(agencyId) != 'undefined') {
            data.agencyId = agencyId;
        }
        if (typeof(formElementId) == 'undefined') {
            formElementId = 'formId';
        }
        $.ajax({
            url: window.baseUrl + '/admin/users/get-form',
            type: 'POST',
            data: data,
            success: function(formList) {
                var html = '<option label="Select a Form" value="">Select a Form</option>';
                for (var form in formList) {
                    html += '<option label="' + formList[form] + '" value="' + form + '">' + formList[form] + '</option>';
                }
                $('#' + formElementId).html(html).removeAttr('disabled');
                switchAssignButton();
            }
        });
    }
    
    function enableSearchByDropDown(stateId) {
        if($('#selectBy input:radio:checked').val() == 'eCrashAgency') {
            $('#formId').val('').attr('disabled', 'disabled');
            populateAgencyList(stateId);
        }
        else {
            $('#agencyId').val('').attr('disabled', 'disabled');
            $('#agencyFormId').val('').attr('disabled', 'disabled');
            populateFormList(stateId);
        }
    }
    
    function switchSaveButton() {
        var requiredFields = ['nameFirst', 'nameLast', 'username', 'email', 'peoplesoftEmployeeId', 'keyingVendorId'];
        var enableSave = true;
        for (var field in requiredFields) {
            if ($.trim($('#' + requiredFields[field]).val()) == '') {
                enableSave = false;
            }
        }
        if (enableSave) {
            $('#save').removeAttr('disabled');
        }
        else {
            $('#save').attr('disabled', 'disabled');
        }
    }
    
    function enterNote() {
        var view = {
            width: (screen.availWidth) ? screen.availWidth : screen.width,
            height: (screen.availHeight) ? screen.availHeight : screen.height
        };
        var left = (view.width - 500) / 2;
        var top = (view.height - 240) / 2;
        window.open(
            window.baseUrl + '/admin/users/show-add-note-form', 'Notes', 
            'width=500,height=245,top='+top+',left='+left+',minimize=no'
        );
    }
    
    function showNoteHistory() {
        var view = {
            width: (screen.availWidth) ? screen.availWidth : screen.width,
            height: (screen.availHeight) ? screen.availHeight : screen.height
        };
        var left = (view.width - 550) / 2;
        var top = (view.height - 400) / 2;
        var userId = $('#userId').val();
        
        window.open(
            window.baseUrl + '/admin/users/note-history?userId=' + userId, 
            'Notes',
            'width=550,height=400,top='+top+',left='+left
        );
    }
    
    function cancel() {
        $.ajax({
            url: window.baseUrl + '/admin/users/cancel',
            data: {
                csrf: $('#csrf').val(),
            },
            success: function(data) {
                if (!hasCsrfError(data)) {
                    //confirmation before cancel
                    if(!confirm('Closing the window will lose the changes. Close anyway?')) return false;

                    var userId = $('#userId').val();
                    // if userId is empty - we are adding user, otherwise editing
                    if (typeof(userId) == 'undefined' || userId == '') {
                        window.location = window.baseUrl + '/admin/users/add';
                    } else {
                        window.close();
                    }
                }
            }
        });
    }
    
    function resetPassword() {
        var view = {
            width: (screen.availWidth) ? screen.availWidth : screen.width,
            height: (screen.availHeight) ? screen.availHeight : screen.height
        };
        var left = (view.width - 700) / 2;
        var top = (view.height - 180) / 2;
        
        $.ajax({
            url: window.baseUrl +'/admin/users/reset-password',
            data: {
                csrf: $('#csrf').val(),
                userId: $('#userId').val()
            },
            success: function(data) {
                if (!hasCsrfError(data)) {
                    
                    var passwordSent = false;
                    if (typeof data['passwordSent'] != "undefined") {
                        passwordSent = data['passwordSent'];
                    }
                    
                    window.open(
                        window.baseUrl + '/admin/users/reset-password-popup?passwordSent=' + passwordSent,
                        'ResetPassword',
                        'width=700, height=181,top=' + top + ',left=' + left
                    );
                }
            }
        });
    }
    
    function deleteUser() {
        if (!confirm('Are you sure?')) return false;
                
        $.ajax({
            url: window.baseUrl + '/admin/users/delete-user',
            data: {
                csrf: $('#csrf').val(),
                userId: $('#userId').val()
            },
            success: function(data) {
                if (!hasCsrfError(data)) {
                    window.opener.location.reload(true);
                    window.close();
                }
            }
        });
    }
    return {
        resetPassword: resetPassword,
        cancel: cancel,
        showNoteHistory: showNoteHistory,
        enterNote: enterNote,
        switchSaveButton: switchSaveButton,
        enableSearchByDropDown: enableSearchByDropDown,
        populateFormList: populateFormList,
        populateAgencyList: populateAgencyList,
        assignForm: assignForm,
        clearDate: clearDate,        
        setReport:setReport,
        switchAssignButton: switchAssignButton,
        deleteUser: deleteUser
    };
})();

function assignRekeyForm(formId, type){
    
    var value = $('#rekey'+formId).prop('checked') ? 1 : 0;
/*
    $.ajax({
            url: window.baseUrl + '/admin/users/assign-rekey-form',
            data: {
                formId: formId,
                userId: $('#userId').val(),
                value: value
            },
            success: function(html) {
                
            },
            error: function(){
                alert('Please try again later');
            }
        });
*/
    rekeyCheckBox(type);
}

function rekeyCheckBox(type){
    //0 is for paper keying
    if (type == 0) {
        var rekey = false;
        $('.rekey').each(function(index) {
            if ($(this).prop('checked') == true) {
                rekey = true;
                $('#entryStage-'+$('#rekeyId').val()).prop('checked', true);
            }
        });
        if (rekey == false) {
            $('#entryStage-'+$('#rekeyId').val()).prop('checked', false);
        }
    } else if (type == 1) {//1 is for electronic keying
        var rekey = false;
        $('.eRekey').each(function(index) {
            if ($(this).prop('checked') == true) {
                rekey = true;
                $('#entryStage-'+$('#eRekeyId').val()).prop('checked', true);
            }
        });
        if(rekey == false){
            $('#entryStage-'+$('#eRekeyId').val()).prop('checked', false);
        }
    }
}

$(function(){
    $('#entryStage-'+$('#eRekeyId').val()).parent().css('display', 'none');
    $('#entryStage-'+$('#rekeyId').val()).parent().css('display', 'none');
    rekeyCheckBox(0);
    rekeyCheckBox(1);
});
window.userManagement = userManagement;

$(function() {
    $('#delete').click(userManagement.deleteUser);
    $('#save').click(userManagement.enterNote);
    $('#cancel').click(userManagement.cancel);
    $('#resetPassword').click(userManagement.resetPassword);
    $('#noteHistory').click(userManagement.showNoteHistory);
    $('#nameFirst, #nameLast, #username, #email, #peoplesoftEmployeeId, #keyingVendorId').change(userManagement.switchSaveButton);
    $('#formId, #agencyFormId').change(userManagement.switchAssignButton);
    $('#assign').click(userManagement.assignForm);
    $('#clearbtn').click(userManagement.clearDate);
    $('#setreport').click(userManagement.setReport);
    $('#agencyId').change(function() {
        var stateId = $('#stateId').val();
        var agencyId = $('#agencyId').val();
        userManagement.populateFormList(stateId, agencyId, 'agencyFormId');
    });
    $('#selectBy input:radio').change(function() {
        var stateId = $('#stateId').val();
        if (stateId != '') {
            userManagement.enableSearchByDropDown(stateId);
            userManagement.switchAssignButton();
        }
    });
    $('#stateId').change(function() {
        var stateId = $('#stateId').val();
        if(stateId != '') {
            $('#selectBy, #selectBy input:radio').removeAttr('disabled');
            if (typeof($('input[name="selectBy"]:radio:checked').val()) == 'undefined') {
                $('#selectBy-eCrashAgency').attr('checked', 'checked');
            }
            userManagement.enableSearchByDropDown(stateId);
        }
        else {
            $('#selectBy input[type="radio"]').removeAttr('checked').attr('disabled', 'disabled');
            $('#agencyFormId').val('').attr('disabled', 'disabled');
            $('#agencyId').val('').attr('disabled', 'disabled');
            $('#formId').val('').attr('disabled', 'disabled');
            userManagement.switchAssignButton();
        }
    });
    
    if ($(".hasCalendar").length) {
        $(".hasCalendar").datepicker();
    };
});