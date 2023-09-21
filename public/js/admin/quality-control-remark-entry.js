var qcManagement = ( function() {
    let fieldNamesWithRemarks = [],
    openerAction = 'close',
    redirectURL = window.baseUrl + '/admin/quality-control', 
    imageWindow = createImageWindow();
    
    function criticalityDropdownFilter(elementChanged) {
        let typeFilter = elementChanged.val();
        $(".td-criticality-indicator").each( function(index , element) 
        {
            let dataType = $(element);

            if (typeFilter == 'all') {
                $('.td-criticality-indicator').show();
            } else if (typeFilter != dataType.data('type').toLowerCase()) {
                dataType.hide();
            } else {
                dataType.show();
            }
        });
    }

    function hoardRemarkFields() {
        $(".remarkValue").each(function(index, element) 
        {
            let htmlElement = $(element);
            let value = htmlElement.val();
            let fieldName = htmlElement.data('fieldname');
            let isInNamesWithRemarks = fieldNamesWithRemarks.includes(fieldName);

            if (value != '') {
                if (!isInNamesWithRemarks) {
                    fieldNamesWithRemarks.push(fieldName);
                }
            } else {
                if (isInNamesWithRemarks) {
                    removeA(fieldNamesWithRemarks , fieldName); 
                }
            }
            writeFieldWithRemarks(fieldNamesWithRemarks);
        });
    }

    function writeFieldWithRemarks(fieldWithRemarks) {
        let html = '';

        if (fieldWithRemarks.length > 0) {
            for(let i in fieldWithRemarks) {
                html += `<span>${fieldWithRemarks[i]}</span>`;        
            }
            $("#editedFieldSection").show();
        } else {
            html = '';
            $("#editedFieldSection").hide();
        }
        $("#editedFields").html(html);
    }

    function removeA(arr) {
        var what, a = arguments, L = a.length, ax;
        while (L > 1 && arr.length) {
            what = a[--L];
            while ((ax= arr.indexOf(what)) !== -1) {
                arr.splice(ax, 1);
            }
        }
        return arr;
    }

    function closeFormAndPDF() {
        window.parent.close();
        imageWindow.closeWindow();
    }

    function redirectOrCloseOpener() {
        if (openerAction == 'redirect') {
            window.parent.opener.location.href = 
            window.location.href.replace(window.location.pathname,redirectURL);
        } else {
            imageWindow.closeWindow();
            window.parent.opener.close();
        }
    }

    function submit() {
        let remarks = [], 
        submitChanges = true,
        general = {
            reportId : $("input[name='reportId']").val(),
            formId : $("input[name='formId']").val(),
            stateId : $("input[name='stateId']").val(),
            isEdit : $("input[name='isEdit']").val()
        }, 
        fetchParam = {
            workType: $("#keepWorkType").is(':checked'),
            state: $("#keepState").is(':checked'),
            reportType: $("#keepReportType").is(':checked'),
        };
        /**
         * preparing the data of fields with
         * values no values will not be included to post method
         */
        $("input[name$='[remark_value]']").map( function() 
        {
            let fieldValue = $(this).val().trim();
            let fieldName = $(this).data('fieldname');
            let fieldPassValue = $(this).data('passvalue');
            let fieldCriticality = $(this).data('criticality');
            if (fieldValue != '') {
                remarks.push({
                    key : fieldName,
                    value : fieldValue,
                    passValue : fieldPassValue,
                    criticality : fieldCriticality
                });
            }
        });

        if (remarks.length < 1) {
            alert("There are no changes found, to skip this report click skip button");
            return false;
        }
        
        if (submitChanges) {
            $.ajax({
                type: 'POST',
                method: 'POST',
                url:window.baseUrl + '/admin/quality-control/remark-entry',
                data: {
                    fields : remarks,
                    general : general,
                    fetchParam : fetchParam
                },
                beforeSend : function(){
                    $("#loadingMessage").html('loading...');
                },
                success : function(response) {
                    redirectURL =  window.baseUrl +  response.redirectURL;
                    if (response.action == 'create')
                        openerAction = 'redirect';
                    closeFormAndPDF();
                }
            });
        }
    }

    function noIssue() {
        let reportId = $("#reportId").val();
        let fetchParam = {
            workType: $("#keepWorkType").is(':checked'),
            state: $("#keepState").is(':checked'),
            reportType: $("#keepReportType").is(':checked'),
        };

        if (reportId != '') {
            $.ajax({
                type: 'POST',
                method: 'POST',
                url:window.baseUrl + '/admin/quality-control/noissue',
                data: {
                    reportId : reportId,
                    fetchParam : fetchParam
                },
                beforeSend : function(){
                    //show user that there is something happening in the background
                    $("#loadingMessage").html('loading...');
                },
                success : function(response)
                {
                    $("#loadingMessage").html('done...');
                    redirectURL =  window.baseUrl +  response.redirectURL;
                    openerAction = 'redirect';
                    closeFormAndPDF();
                }
            });
        }
    }

    function unloadUserReport()
    {
        $.ajax({
            type : 'post',
            method : 'post',
            url: window.baseUrl + '/admin/quality-control/unload-user-report',
            success : function(response) {
                openerAction = 'close';
                closeFormAndPDF();
            }
        });
    }

    return {
        hoardRemarkFields: hoardRemarkFields,
        writeFieldWithRemarks: writeFieldWithRemarks,
        removeA: removeA,
        submit: submit,
        imageWindow:imageWindow,
        criticalityDropdownFilter : criticalityDropdownFilter,
        noIssue : noIssue,
        openerAction : openerAction,
        redirectOrCloseOpener:redirectOrCloseOpener,
        closeFormAndPDF : closeFormAndPDF,
        unloadUserReport : unloadUserReport
    };
})();


window.qcManagement = qcManagement;

$( document ).ready(function()
{
    qcManagement.imageWindow.openWindow('/data/report-entry/image-viewer-pdf');
    //Initiate searchable table
    if ($('#passOverview')) {
        $('#passOverview').DataTable({
            autoWidth: false,
            lengthChange: false,
            ordering: false,
            paging:false,
            autoFill: true,
        });
    }
    $('.remarkValue').keyup(qcManagement.hoardRemarkFields);
    $("#criticalityType").change( function() {
        qcManagement.criticalityDropdownFilter($(this)); 
    });
    $("#btnCancel").click(function(e){
        qcManagement.unloadUserReport();
    });
    $("#fieldForm").on('submit',function(e){
        e.preventDefault();
        qcManagement.submit();
    });
    $("#btnNoIssue").click(qcManagement.noIssue);
        $(window.parent).on('beforeunload' , qcManagement.redirectOrCloseOpener);
});