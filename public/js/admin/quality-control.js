var qcManagement = ( function() {
    let formFilterType = 'weekly';

    function toggleElement() {
        let elDateRangeReportFilter = $("#dateRangeReportFilter");
        let elWeeklyReportFilter = $("#weeklyReportFilter");

        if (formFilterType == 'weekly') {
            formFilterType = 'date-range';
            elWeeklyReportFilter.hide();
            elDateRangeReportFilter.show();
            $("#toggleFilter").html('Weekly Filter');
        } else {
            formFilterType = 'weekly';
            elDateRangeReportFilter.hide();
            elWeeklyReportFilter.show();
            $("#toggleFilter").html('Date Range Filter');
        }
        
        $('#filterType').val(formFilterType);
    } 

    function toggleSummaryFilter() {
        $("#advanceFilter").click(function() {
            $("#modalFilter").toggle();
        });
    }

    function initDataTable() {
        var groupColumn = 2;

        $("#reportRemarksTable").DataTable({
            columnDefs: [
                { "visible": false, "targets": groupColumn }
            ],
            order: [[ groupColumn, 'asc' ]],
            drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td colspan="5">'+group+'</td></tr>'
                    );
    
                    last = group;
                }
            } );
        }
        });
    }

    return {
        toggleSummaryFilter :toggleSummaryFilter,
        initDataTable: initDataTable,
        toggleElement: toggleElement
    }
})();


window.qcManagement = qcManagement;

$( document ).ready( function() 
{
    qcManagement.toggleSummaryFilter();
    qcManagement.initDataTable();

    $("#toggleFilter").click( function(){
        qcManagement.toggleElement();
    });
});