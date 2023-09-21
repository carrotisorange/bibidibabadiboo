$(document).ready(function() {
    $('#autoExtractionAccuracyResults tbody tr a').bind('click', function() {
        var width = window.screen.availWidth;
        var height = window.screen.availHeight;
        var reportID = $(this).data('report-id');

        window.open(
            window.baseUrl + '/admin/auto-extraction-metrics/auto-extraction-accuracy-overview?reportId=' + reportID,
            'reportChangeHistory',
            "resizable=1,scrollbars=1,width=" + width + ",height=" + height
        );
    });

    window.keyingAccuracy = function() {
        function openReportImage(reportId) {
            if ( typeof(reportId) != 'undefined' ) {
                window.reportEntry.openInputWindow(
                    window.baseUrl + '/admin/metrics/image-viewer-pdf?reportId=' + reportId,
                    null, true
                );
                // @TODO: The below segment blocks the image window so commented right now
                /*$(window).bind('unload', function(){
                    window.reportEntry.closeInputWindow();
                });*/
            }
        }
        return {openReportImage: openReportImage};
    }();
});
