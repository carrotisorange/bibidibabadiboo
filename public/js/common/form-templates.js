(function() {
    common.formTemplates = (function() {
        function showPopup(forms) {
            if (!hasCsrfError(forms)) {
                var html = '';
                if ($.isEmptyObject(forms)) {
                    html = 'There are no alternative form templates available.'
                } else {
                    var html = '<select style="margin-right: 10px" name="formTemplatesList" id="formTemplatesList">';
                    for (formId in forms) {
                        html += '<option value="' + formId + '">' + forms[formId] + '</option>';
                    }
                    html += '</select>';
                }
                $('<div id="formTemplatesDialog"></div>').html(html).dialog({
                    modal: true,
                    title: 'Alternative Form Templates',
                    width: 300,
                    buttons: [
                        {text: "Ok", click: function(){okButtonHandler(forms);}}
                    ]
                });
            }
        }

        function okButtonHandler(forms) {
            if ($.isEmptyObject(forms)) {
                $("#formTemplatesDialog").dialog('destroy').remove();
            } else {
                redirect('/data/report-entry/display?alternativeFormId=' + $('#formTemplatesList').val());
            }
        }

        function getFormsList() {
            $.getJSON(
                window.baseUrl + '/data/report-entry/get-alternative-forms-list',
                {
                    reportId : $('#reportId').val(),
                    alternativeFormId: $('#alternativeFormId').val(),
                    csrf: $('#csrf').val()
                },
                showPopup
            );
        }

        function bind() {
            $('#formTemplates').click(getFormsList);
        }

        return {
            bind: bind
        }
    })()
})()