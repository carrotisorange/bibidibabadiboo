$(function(){

    function setActivePageByElement(ele, preserveMessage) {
        //function below is defined in form-logic.js AND in universal-sectional.js
        eCrash.setActivePage($(ele).parents('.form-page'), null, preserveMessage);
    }

    function focusHashField() {

        var hash = parent.window.location.hash;

        var tranlationIdList = { '#Vehicle_VIN' : '#Vehicle_Vin' };
        
        if (hash.length > 1) {
            hash = hash.substr(1);

            var hashParams = {};
            $.each(hash.split('&'), function(key, value) {
                var parts = value.split('=');
                if (typeof parts[1] == 'undefined') {
                    parts[1] = null;
                } else {
                    parts[1] = decodeURIComponent(parts[1]);
                }

                hashParams[decodeURIComponent(parts[0])] = parts[1];
            });

            if (hashParams.focusField) {
                var field = '#' + hashParams.focusField;

                if(field.indexOf('#People') != -1){
                    field = field.replace("#People", "#Person");
                }
                else if(field.indexOf('#Vehicles') != -1) {
                    field = field.replace("#Vehicles", "#Vehicle");
                }

                //tranlationIdList
                var splitField = field.split('-');
                var explodedField = splitField[0];
                
                if(explodedField in tranlationIdList){
                    field = field.replace(explodedField, tranlationIdList[explodedField]);
                }
                
                setTimeout("$(window).focus();$('"+field+"').focus().val($('"+field+"').val());", 400);
                setActivePageByElement(field);
                if ($('input[name=' + hashParams.focusField + ']').attr('type') == 'radio') {
                    $('input[name=' + hashParams.focusField + ']:checked').focus();
                } else {
                    openTab($( "#formContainer" ).find( field ));
                    $(field).focus();
                }
            }

        }
    }

    $(function() {
        focusHashField();
    });
});