var reportAutoVsManual = (function() {
    var reportDataTable;
    
    function submitAutoVsManualReport() {
        
        var resultColumns = [
            { "data": "state_abbr", "name": "state_abbr"},
            { "data": "report_id", "name": "report_id"},
            { "data": "work_type", "name": "work_type"},
            { "data": "creation_date", "name": "creation_date"},
            { "data": "report_status", "name": "report_status"},
            { "data": "auto_extraction", "name": "auto_extraction"},
            { "data": "manually_keyed", "name": "manually_keyed"},
            { "data": "auto_extraction_date", "name": "auto_extraction_date"},
            { "data": "pass1_username", "name": "pass1_username"},
            { "data": "pass1_start_date", "name": "pass1_start_date"},
            { "data": "pass1_end_date", "name": "pass1_end_date"},
            { "data": "pass2_username", "name": "pass2_username"},
            { "data": "pass2_start_date", "name": "pass2_start_date"},
            { "data": "pass2_end_date", "name": "pass2_end_date"},
            { "data": "pass1_duration", "name": "pass1_duration"},
            { "data": "pass2_duration", "name": "pass2_duration"},
            { "data": "total_duration", "name": "total_duration"}
        ];
        
        if ($('#keyingVendorId').length && $('#keyingVendorId').is('select')) {
            resultColumns.push({ "data": "vendor_name", "name": "vendor_name"});
        }
        
        this.reportDataTable = $('#auto-extraction-dataTable').DataTable({
            destroy: true,
            autoWidth: false,
            searching: false,
            lengthChange: false,
            ordering: false,
            dom: '<"col-xs-12 col-sm-5 col-md-6 text-start"l><"col-xs-12 col-sm-6 col-md-5 text-end" f><"col-xs-12 col-sm-1 col-md-1 text-start padding-left5" B>rt<"col-xs-12 col-sm-5 col-md-6 text-start pull-left"i><"col-xs-12 col-sm-7 col-md-6 text-end"p>',
            processing: false,
            serverSide: true,
            ajax: {
                url: window.baseUrl + '/admin/auto-extraction-metrics/auto-extraction-report',
                type: 'POST',
                cache: false,
                async: true,
                data: {
                    fromDate: $("#fromDate").val(),
                    toDate: $("#toDate").val(),
                    state: $("#state").val(),
                    csrf: $("#csrfToken").text(),
                    keyingVendorId: $("#keyingVendorId").val()
                },
                error: function (data, textStatus, errorThrown) {
                    dataTableError(data, textStatus, errorThrown);
                }
            },
            initComplete: function(settings, json) {
                if (reportAutoVsManual.reportDataTable.data().count()) {
                    $('.btn-export-excel').show();
                }
            },
            fnDrawCallback: function(oSettings) {
                refreshCSRF(oSettings.oAjaxData.csrf);
            },
            columns: resultColumns,
            pageLength: 10,
            language: {
                'emptyTable': 'No Matches found'
            },
        });
    }
    
    function hideDataTable() {
        $('#auto-extraction-dataTable-container').hide();
    }
    
    function showDataTable() {
        $('#auto-extraction-dataTable-container').show();
    }
    
    return {
        hideDataTable: hideDataTable,
        showDataTable: showDataTable,
        submitAutoVsManualReport: submitAutoVsManualReport
    };
})();

window.reportAutoVsManual = reportAutoVsManual;

$(document).ready(function() {
    if ($('form#getAutoVsManualReport').hasClass('isValidated')) {
        reportAutoVsManual.submitAutoVsManualReport();
        reportAutoVsManual.showDataTable();
    }

    $('.btn-export-excel').hide();
});