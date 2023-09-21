/**
 *
 * @copyright (c) 2010 by LexisNexis Asset Company. All rights reserved.
 *
 * eCrash v3.0
 */
$(function(){
    
    if (typeof console == 'undefined') {
        console = {log:function(){}};
    }
    
    var ade = (function(){
        
        function clearFormAttrTable() {
            formAttrTable.fnClearTable(true);
            $('#formAttrDiv').hide();
        }
        
        function clearSelect(selectControl) {
            $(selectControl)
                .find('option').remove().end()
                .append(
                    $('<option>', {value: ''}).text('Select one')
                );
        }
        
        function loadSelect(selectControl, map) {
            $.each(map, function(key, value) {
                $(selectControl)
                    .append(
                        $('<option>', {value: key}).text(value)
                    );
            });
        }
        
        function loadAgencies(stateId) {
            
            ade.clearFormAttrTable();
            clearSelect($('#agencySelect'));

            if (stateId == null || stateId.length == 0)
                return;
            
            var successFunc = function(agencies) {  
                if (!hasCsrfError(agencies)) {
                    loadForms(stateId, ''); //loading State level forms
                    loadSelect($('#agencySelect'), agencies);
                }
            };
            
            $.ajax({
                type: 'GET',
                url: window.baseUrl + '/admin/assign-data-elements/fetch-agencies-json',
                dataType: 'json',
                success: successFunc,
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('AJAX error - ', xhr.status, ajaxOptions, thrownError);
                },
                data: {
                    stateId: stateId,
                    csrfToken: $('#csrfToken').text()
                }
            });
        }
        
        function loadForms(stateId, agencyId) {
            
            ade.clearFormAttrTable();
            clearSelect($('#formSelect'));

            if (stateId == null || stateId.length == 0) {
                return;
            }
            
            var successFunc = function(forms) {
                if (!hasCsrfError(forms)) {
                    loadSelect($('#formSelect'), forms);
                }
            };
            
            $.ajax({
                type: 'GET',
                url: window.baseUrl + '/admin/assign-data-elements/fetch-forms-json',
                dataType: 'json',
                success: successFunc,
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('AJAX error - ', xhr.status, ajaxOptions, thrownError);
                },
                data: {
                    stateId: stateId,
                    agencyId: agencyId,
                    csrfToken: $('#csrfToken').text()
                }
            });
        }
        
        function loadFormAttrs(formId) {
            
            ade.clearFormAttrTable();
            
            if (formId == null || formId.length == 0)
                return;
            
            var successFunc = function(formInfo) {

                if (!hasCsrfError(formInfo)) {
                    $('#formAttrDiv').show();
                    $('#workTypeAssignment').text(formInfo['workTypeAssigned']);
                    $.each(formInfo['attributes'], function(i, attr) {
                        formAttrTable.fnAddData([
                            attr['label'], attr['available'], attr['required'], 
                            attr['skipped'], attr['available']], 
                            false
                        );
                    });     

                    formAttrTable.fnDraw(true);
                }
            };
            
            $.ajax({
                type: 'GET',
                url: window.baseUrl + '/admin/assign-data-elements/fetch-form-attrs-json',
                dataType: 'json',
                success: successFunc,
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log('AJAX error - ', xhr.status, ajaxOptions, thrownError);
                },
                data: {
                    formId: formId,
                    csrfToken: $('#csrfToken').text()
                }
            });         
        }
        
        function enableRadioButtons(checkbox, row) {
            if ($(checkbox).is(":checked")) {
                $('input[name="element[' + row + '][selection]"]')
                    .removeAttr('disabled');
            }
            else {
                $('input[name="element[' + row + '][selection]"]')
                    .attr('disabled', 'disabled')
                    .prop("checked", false);
            }
        }
        
        function checkOption(checkbox, row) {
            $('input[name="element[' + row + '][selection]"]')
                .not($(checkbox))
                .prop("checked", false);
        }
        
        function openWindow(url) {
            var newWindow = window.open(url, 'AssignDataElements', 'width=700,height=400,resizable=yes');
            $(window).on("unload", function(e) {
                if (!newWindow.closed) {
                    newWindow.close(); //forced close   
                }
            });
        }
        
        //Init on load with 'Select one' option
        clearSelect($('#agencySelect'));
        clearSelect($('#formSelect'));      
        
        return {
            loadAgencies: loadAgencies,
            loadForms: loadForms,
            loadFormAttrs: loadFormAttrs,
            enableRadioButtons: enableRadioButtons,
            checkOption: checkOption,
            openWindow: openWindow,
            clearFormAttrTable: clearFormAttrTable
        };

    })();
    
    window.ade = ade;
        
    $('#stateSelect').change(function() {
        var stateId = $(this).val();
        ade.loadAgencies(stateId);
    });
    
    $('#agencySelect').change(function(){
        var stateId = $('#stateSelect').val();
        var agencyId = $(this).val();
        ade.loadForms(stateId, agencyId);
    });
    
    $('#formSelect').change(function(){
        var formId = $(this).val();
        $('#formId').val(formId);
        ade.loadFormAttrs(formId);
    });
    
    $('#btnNotes').click(function(){
        var formId = $('#formSelect').val();
        ade.openWindow(window.baseUrl + '/admin/assign-data-elements/show-notes/formId/' + formId + '');
    });
    
    $('#btnSave').click(function(){     
        var saveFunc = function() {
            $('#note').val($('#txtaNote').val());
            $('#frmDataElements').submit();
            $(this).dialog('destroy');
        };
        
        $('<textarea id="txtaNote" rows="10" cols="40"/>').dialog({
            modal: true, title: 'Notes', 
            buttons: [{text: "Save",  click: saveFunc}]
        }).show();
        
        $('#txtaNote').focus();
    });
    
    $('#btnCancel').click(function(){
        var formId = $('#formSelect').val();
        ade.loadFormAttrs(formId);
    }); 
    
    var formAttrTable = $('#formAttrTable').dataTable({
        bPaginate: false,
        sScrollY: "400px",
        bInfo: false,
        bAutoWidth: false,
        bJQueryUI: true,
        "ordering": false,
        aoColumns: [
            {
                bSortable: false,
                sWidth: '80%',
                mRender: function(data, type, row, meta ) { 

                    var attributeLabel = row[0];
                    var idAndName = 'element[' + meta.row + '][attributeLabel]';
                    return attributeLabel + '<input type="hidden" ' + 
                        ' name="' + idAndName + '" id="' + idAndName + '" ' + 
                        ' value="' + attributeLabel + '"/>';
                }
            },
            {
                bSortable: false, 
                mRender: function(data, type, row, meta ) {
                    var isChecked = row[1];
                    var idAndName = 'element[' + meta.row + '][available]';
                    return '<input type="checkbox" ' + 
                        (isChecked ? ' checked="checked" ' : '') +
                        ' name="' + idAndName + '" id="' + idAndName + '" ' + 
                        ' value="AVAILABLE" ' + 
                        ' onclick="ade.enableRadioButtons(this,' + meta.row + ');"/>';
                }
            },
            {
                bSortable: false, 
                mRender: function(data, type, row, meta ) {
                    var isAvailable = row[4];
                    var isChecked = row[2];
                    var name = 'element[' + meta.row + '][selection]';
                    var id = 'element[' + meta.row + '][required]';
                    return '<input type="checkbox" ' + 
                        (isChecked ? ' checked="checked" ' : '') +
                        (!isAvailable ? ' disabled="disabled" ' : '') +
                        ' name="' + name + '" id="' + id + '" ' + 
                        ' value="REQUIRED" ' + 
                        ' onclick="ade.checkOption(this,' + meta.row + ');"/>';
                }
            },
            {
                bSortable: false, 
                mRender: function(data, type, row, meta ) {
                    var isAvailable = row[4];
                    var isChecked = row[3];
                    var name = 'element[' + meta.row + '][selection]';
                    var id = 'element[' + meta.row + '][required]';
                    return '<input type="checkbox" ' + 
                        (isChecked ? ' checked="checked" ' : '') +
                        (!isAvailable ? ' disabled="disabled" ' : '') +
                        ' name="' + name + '" id="' + id + '" ' + 
                        ' value="SKIPPED" ' + 
                        ' onclick="ade.checkOption(this,' + meta.row + ');"/>';
                }
            },
            {
                //hidden column to preserve raw isAvailable flag - hack :(
                bVisible: false
            }
        ],
        aaSorting: [[1, 'asc']]
    });
    
});