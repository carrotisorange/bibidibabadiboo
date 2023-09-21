var userManagement = (function() {
    var userDataTable;
    
    function submitSearchUser() {
        var entryStageSelected = [];
        $('.entryStage:checked').each(function(i) {
            entryStageSelected[i] = $(this).val();
        });
          
        var entryStageWidth = "40%";
        var statusWidth = "10%";
        var keyingVendorId = null;
        if ($('#keyingVendorId').length) {
            keyingVendorId = $('#keyingVendorId').val();
            if($('#keyingVendorId').is('select')) {
                entryStageWidth = "35%";
                statusWidth = "5%";
            }
        }
        
        var resultColumns = [
            { "data": "nameLast", "name": "nameLast", "width": "10%"},
            { "data": "nameFirst", "name": "nameFirst", "width": "15%"},
            { "data": "username", "name": "username", "width": "15%"},
            { "data": "role_external", "name": "role_external", "width": "10%"},
            { "data": "entryStages", "name": "entryStages", "width": entryStageWidth},
            { "data": "status", "name": "status", "width": statusWidth}
        ];
        
        if ($('#keyingVendorId').length && $('#keyingVendorId').is('select')) {
            resultColumns.push({ "data": "vendorName", "name": "vendorName", "width": "5%"});
        }
        
        this.userDataTable = $('#user-dataTable').DataTable({
            destroy: true,
            autoWidth: false,
            searching: false,
            lengthChange: false,
            ordering: false,
            dom: '<"col-xs-12 col-sm-5 col-md-6 text-start"l><"col-xs-12 col-sm-6 col-md-5 text-end" f><"col-xs-12 col-sm-1 col-md-1 text-start padding-left5" B>rt<"col-xs-12 col-sm-5 col-md-6 text-start"i><"col-xs-12 col-sm-7 col-md-6 text-end"p>',
            processing: false,
            serverSide: true,
            ajax: {
                url: window.baseUrl + '/admin/users',
                type: 'POST',
                cache: false,
                async: true,
                data: {
                    nameLast: $("#nameLast").val(),
                    nameFirst: $("#nameFirst").val(),
                    userRoleId: $("#userRoleId").val(),
                    entryStage: entryStageSelected,
                    keyingVendorId: keyingVendorId,
                    csrf: $("#csrfToken").text()
                },
                error: function (data, textStatus, errorThrown) {
                    dataTableError(data, textStatus, errorThrown);
                }
            },
            initComplete: function(settings, json) {
                $('#' + settings.sTableId + ' tbody').addClass('clickable');
            },
            fnDrawCallback: function(oSettings) {
                refreshCSRF(oSettings.oAjaxData.csrf);
            },
            columns: resultColumns,
            pageLength: rowsPerPage,
            language: {
                'emptyTable': 'No Matches found'
            },
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:first-child').append('<div class="hide report_userId">' + data.userId + '</div>');
                $(row).find('td:first-child').append('<div class="hide report_page">1</div>');
            }
        });
    }
    
    function hideDataTable() {
        $('#user-dataTable-container').hide();
    }
    
    function showDataTable() {
        $('#user-dataTable-container').show();
    }
    
    return {
        hideDataTable: hideDataTable,
        showDataTable: showDataTable,
        submitSearchUser: submitSearchUser
    };
})();

$(function() {
    //$('#userResults tbody').addClass('clickable');
    
    $(document).on('click', '#user-dataTable tbody tr', function() {
        // we need to pass parameters with prefix search in order to distinguish them
        // from parameters on user edit page. Search parameters are using when operator
        // is being redirected from user edit page to users list
        var paramNames = {
            '.report_userId': 'userId',
            '.report_page': 'searchPage',
            '#nameFirst': 'searchNameFirst',
            '#nameLast': 'searchNameLast',
            '#userRoleId': 'searchUserRoleId'
        };
        var params = [];
        var tr = this;
        $.each(paramNames, function(selector, name){
            var val;
            if (selector.indexOf('.report_') === 0) {
                val = $(selector, tr).text();
            } else {
                val = $(selector).val();
            }
            params.push(name + '=' + $.trim(val));
        });
        
        window.open(window.baseUrl + '/admin/users/edit?' + params.join('&'));
    });
});

window.userManagement = userManagement;

$(document).ready(function() {
    if ($('form#searchUser').hasClass('isValidated')) {
        userManagement.submitSearchUser();
        userManagement.showDataTable();
    }
});