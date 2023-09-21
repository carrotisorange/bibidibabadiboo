let qcManagement = ( function() {
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
    return {
        toggleElement: toggleElement
    };
})();


window.qcManagement = qcManagement;

$( document ).ready( function() 
{
    //initialize has calendar
    $( ".hasCalendar" ).datepicker(); 
    $("#toggleFilter").click( function(){
        qcManagement.toggleElement();
    } );
});
