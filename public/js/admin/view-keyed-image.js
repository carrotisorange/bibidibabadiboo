/**
 * @copyright (c) 2011 LexisNexis. All rights reserved.
 */
var viewKeyedImages = (function() {
    //disable other fields on view keyed report search page if report id is entered
    function disableFields() {
        var fieldVal = null,
            idSearch = ['cruOrderId', 'reportId', 'returnAllPasses'],
            fuzzySearch = [
                'processingStartTime', 'processingEndTime', 'stateId', 'agencyId',
                'operatorLastName', 'operatorFirstName', 'licensePlate', 'vin',
                'partyFirstName', 'partyLastName', 'registrationState', 'caseIdentifier', 
                'crashDate', 'reportType'
            ];
        $.each(idSearch, function(key, value) {
            var $this = $('#' + value);
            if ($this.is(':checkbox')) {
                fieldVal = $this.is(':checked') ? $this.val() : null;
            } else {
                fieldVal = $this.val();
            }
            fieldVal = $.trim(fieldVal);
            return !(fieldVal.length > 0);
        });
    
        var disabled = (fieldVal.length > 0) ? 'disabled' : false;
        $.each(fuzzySearch, function(key, value) {
            $('#' + value).attr('disabled', disabled);
        });
        
        if ($.trim($('#cruOrderId').val()).length > 0) {
            $('#reportId').attr('disabled', 'disabled');
        } else if ($.trim($('#reportId').val()).length > 0) {
            $('#cruOrderId').attr('disabled', 'disabled');
        } else {
            $('#reportId').attr('disabled', false);
            $('#cruOrderId').attr('disabled', false);
        }
    }
    function isAllowedForSearch() {
        if ($('#returnAllPasses').is(':checked')
            && ($('#cruOrderId').val() == '' || $('#cruOrderId').val() == 0)
            && ($('#reportId').val() == '' || $('#reportId').val() == 0)) {
            alert('You have to specify CRU Order ID or Report ID');
            return false;
        }
        return true;
    }

    return {
        isAllowedForSearch: isAllowedForSearch,
        disableFields: disableFields
    };
})();
    
window.viewKeyedImages = viewKeyedImages;
$(function() {
    $('#submit').click(viewKeyedImages.isAllowedForSearch);
    $('#cruOrderId, #reportId, #returnAllPasses').bind('keyup click', viewKeyedImages.disableFields);
    $('#viewKeyedImagesResults tbody').addClass('clickable');
    $('#viewKeyedImagesResults tbody tr').bind('click', function() {
        var editedInCC = false;
        var isNewApp = 1;
        var reportId = trim($('.report_reportId', this).text());
        $.ajax({
            url: window.baseUrl + '/admin/view-keyed-image/check-command-center-flag',
            type: 'post',
            async: false,
            data: {
                reportId: reportId
            },
            success: function(data) {
                editedInCC = data.result;
                isNewApp = data.isNewApp;
            }
        });
        if(editedInCC === true) {
            alert("The report you are attempting to view has been edited within Command Center. Please access the report via Command Center to view or edit the report.");
        } else if (isNewApp == 0) {
            // Report was keyed in the old application
            //alert('Please use Old App to view this report');
            window.location.href = window.baseUrl + '/admin/view-keyed-image/export-old-keyed-report?reportId=' + reportId;
        } else if ($('#returnAllPasses:checked').length) {
            var width = window.screen.availWidth;
            var height = window.screen.availHeight;
            window.open(
                window.baseUrl + '/data/report-entry/pass-overview?reportId=' + reportId,
                'reportChangeHistory',
                "resizable=1,scrollbars=1,width=" + width + ",height=" + height
            );
        } else {
            window.reportEntry.openInputWindow(
                window.baseUrl + '/admin/view-keyed-image/report-entry?reportId=' + reportId
            );
        }
    });
    
    $('#viewKeyedImagesResults tbody tr').click(function(){$(this).addClass('highlightTr')});
});
