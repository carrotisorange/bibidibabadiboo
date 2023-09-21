/**
 * @todo Separate this into a form-control logic and a field-control logic file.
 */

/**
 * @todo Find a way to group criteria in the field definition and logic controllers.
 * @todo Ensure the valueList popup display outside of the label element, regardless of screen position.
 * @todo Fix valueList scrolling so the width accounts for the scroll bar.
 */
function focusonfield(){
    //image focus problem with proper coding standereds
    var match=/focusField/gi;
    var url = escape(parent.window.location);
    if(url.search(match)== -1){
        setTimeout("$(window).focus();$('#Incident_CaseIdentifier').focus().val($('#Incident_CaseIdentifier').val());", 2000);
    }
}

(function() {
    focusonfield();
    $(document).on('focusout', '#formPages input[type="text"], #formPages textarea', function () {
        $(this).val($(this).val().toUpperCase());
    });
    $(document).on('focusout', '#quickVinDialog input[type="text"]', function () {
        $(this).val($(this).val().toUpperCase());
    });
    
    var eCrash = {
        data: {
            dynamicVerification: {},
            lastVin: {} //Used by vin-validation
        },
        fields: {},
        globalFunction: []
    };
    window.eCrash = eCrash;
    
    eCrash.valueListsKeysUpperCase = {};
    var debugGlobal = true;

    function consoleReplacement(fn) {
        return function replaceConsoleWithRealConsole() {
            if (typeof(window.console) !== 'undefined') {
                console = window.console;
                if (typeof(console.group) == 'undefined') {
                    console.group = $.noop;
                    console.groupEnd = $.noop;
                }

                console[fn].apply(console, arguments);
            }
        }
    }

    // Debug Code Saver. This allows the application to continue working even if debug code is left in.
    if (debugGlobal && typeof(console) == 'undefined') var console = {
        log: consoleReplacement('log'),
        trace: consoleReplacement('trace'),
        exception: consoleReplacement('exception'),
        group: consoleReplacement('group'),
        groupEnd: consoleReplacement('groupEnd')
    };

    // /dev/null
    if (!debugGlobal) var console = {
        log: function(){},
        trace: function(){},
        exception: function(){},
        group: function(){},
        groupEnd: function(){}
    };

    /**
     * Validate 'Soft' Logic
     * Applies validation logic after the field is done.
     */
    eCrash.validateSoft = function(func) {
        return function(e) {
            console.log('validateSoft', func);
            if (isEventStopped(e)) return;

            var ele = $(e.target);

            if ($.trim(ele.val()).length > 0 && !func(ele.val())) {
                var fieldName = ele.parents('label').text();
                if (!fieldName) fieldName = 'Value';

                ele.addClass('invalid');
                eCrash.fieldPopup.destroy('error', ele);  //destroy previous
                eCrash.fieldPopup.show(
                    'error',
                    ele,
                    [eCrash.parseErrorMessage(func.errorMessage, fieldName, 'should')]
                );
            } else {
                ele.removeClass('invalid');
                eCrash.fieldPopup.destroy('error', ele);
            }
        }
    }

    eCrash.fieldValue = function(ele) {
        var retValue = null;

        if (!ele.is(':radio, :checkbox')) {
            retValue = ele.val();
        } else {
            retValue = ele.filter(':checked').val();
            if (typeof(retValue) == 'undefined') {
                retValue = '';
            }
        }

        return retValue;
    }

    /**
     * Validate 'Force' Logic
     * This will stop the form from being saved if it will break the validation rules.
     */
    eCrash.validateForce = function(func) {
        return function(e) {
            console.log('validateForce', func);
            if (isEventStopped(e)) return;

            var ele = $(e.target);
            var newValue = eCrash.fieldValue(ele);
            
            if ($.trim(newValue).length > 0 && !func(newValue, e)) {
                var fieldName = ele.parents('label').text();
                if (!fieldName) fieldName = 'Value';

                ele.addClass('invalid');
                eCrash.fieldPopup.destroy('error', ele);  //destroy previous
                eCrash.fieldPopup.show(
                    'error',
                    ele,
                    [eCrash.parseErrorMessage(func.errorMessage, fieldName, 'must')]
                );

                fullEventStop(e);
            } else {
                ele.removeClass('invalid');
                eCrash.fieldPopup.destroy('error', ele);
            }
        }
    }
    
    /**
     * Validate 'Force' Logic
     * This will immediately stop an addition if it will break the validation rules.
     */
    eCrash.validateForceImmediate = function(func) {
        return function(e) {
            console.log('validateForceImmediate', func);
            if (isEventStopped(e)) return;
            // This should allow the check to skip for special keys (tab, delete etc.)
            if (e.which == 0 || e.which == 8) return;

            var ele = $(e.target);
            var newValue = eCrash.fieldValue(ele);

            if (ele.is(':text, textarea') && e.which) {
                var selectionRange = getSelectionRange(ele[0]);
                
                if (selectionRange[0] != selectionRange[1] || selectionRange[1] != newValue.length) {
                    newValue = newValue.substr(0, selectionRange[0]) + String.fromCharCode(e.which) + newValue.substr(selectionRange[1]);
                } else {
                    newValue += String.fromCharCode(e.which);
                }
            }

            if ($.trim(newValue).length > 0 && !func(newValue)) {
                if (e.type == 'save') {
                    var fieldName = ele.parents('label').text();
                    if (!fieldName) fieldName = 'Value';

                    ele.addClass('invalid');
                    eCrash.fieldPopup.destroy('error', ele); //destroy previous
                    eCrash.fieldPopup.show(
                        'error',
                        ele,
                        [eCrash.parseErrorMessage(func.errorMessage, fieldName, 'must')]
                    );
                }

                fullEventStop(e);
            }
            else {
                if (ele.hasClass('invalid')) {
                    ele.removeClass('invalid');
                    eCrash.fieldPopup.destroy('error', ele);
                }
            }
        }
    }

    /**
     * ValueList Logic
     * This will list values according to a provided map.
     */
    eCrash.valueList = (function() {
        function calculateHighlightRow(activeValuesLength, highlightRow, whichKey) {
            if (activeValuesLength == 0) {
                return null;
            }

            if (highlightRow + 1 > activeValuesLength || highlightRow === null) {
                highlightRow = -1;
            }

            switch (whichKey) {
                case 38:highlightRow--;break; // Up
                case 33:highlightRow -= 5;break; // Page Up
                case 34:highlightRow += 5;break; // Page Down
                case 40:highlightRow++;break; // Down
            }

            if (highlightRow > activeValuesLength - 1) {
                highlightRow = 0;
            } else if (highlightRow < 0) {
                highlightRow = activeValuesLength - 1;
            }

            return highlightRow;
        }

        function drawHighlightRow(highlightRow) {
            var popupListItems = eCrash.fieldPopup.get('hint').find('li');
            popupListItems.removeClass('highlight');
            if (highlightRow !== null) {
                scrollIntoView(
                    popupListItems
                        .filter(':eq(' + highlightRow + ')')
                        .addClass('highlight'),
                    eCrash.fieldPopup.get('hint')
                );
            }
        }

        function generateMessagesFromList(valueList) {
            var messages = {
                keys: [],
                values: [],
                length: 0
            };
            
            for (var x = 0; x < valueList.length; x++) {
                if (valueList.keys[x] != valueList.values[x]) {
                    messages.keys.push(valueList.keys[x]);
                    messages.values.push(valueList.keys[x] + ' ' + valueList.values[x]);
                    messages.length++;
                } else {
                    messages.keys.push(valueList.keys[x]);
                    messages.values.push(valueList.keys[x]);
                    messages.length++;
                }
            }

            return messages;
        }

        function filterListByValue(valueList, value, ele, dropdownType) {
            var activeValues = {
                keys: [],
                values: [],
                length: 0
            };
            
            if (dropdownType == 'multiselect') {
                var splitValue = $.trim(value).split(";");
                for (var x = 0; x < valueList.length; x++) {
                    for (var y=0; y < splitValue.length; y++){
                        if (valueList.keys[x].toLowerCase().indexOf(splitValue[y].toLowerCase()) == -1) {
                            continue;
                        }

                        if (valueList.keys[x].toLowerCase().indexOf(splitValue[y].toLowerCase()) == 0) {
                            if($.inArray( valueList.keys[x], activeValues.keys) == -1){
                                activeValues.keys.push(valueList.keys[x]);
                                activeValues.values.push(valueList.values[x]);
                                activeValues.length++;
                            }
                        }
                    }
                }

                return activeValues;
            } else {

                for (var x = 0; x < valueList.length; x++) {
                    
                    if (valueList.keys[x].toLowerCase().indexOf(value.toLowerCase()) == -1) {
                        continue;
                    }

                    if (valueList.keys[x].toLowerCase().indexOf(value.toLowerCase()) == 0) {
                        activeValues.keys.push(valueList.keys[x]);
                        activeValues.values.push(valueList.values[x]);
                        activeValues.length++;
                    }
                }
                
                return activeValues;
            }
        }

        return function(func) {
            var activeValues = func() || {length: 0};
            var previousFilter = null;
            var highlightRow = null;

            return function(e) {
                if (isEventStopped(e)) return;

                var messages;
                var ele = $(e.target);
                if($(ele).closest('tr').hasClass('multiselect')) {
                    var dropdownType = 'multiselect';
                    if (e.type == 'keydown' && e.which != 9) {
                        // Up, Down, Page Down, Page Up
                        if (e.which == 38 || e.which == 40 || e.which == 34 || e.which == 33) {
                            highlightRow = calculateHighlightRow(activeValues.length, highlightRow, e.which);
                            drawHighlightRow(highlightRow);
                            return;
                        }
                    } else if (e.type == 'focus' || e.type == 'focusin' || e.type == 'keyup') {
                        if (e.type == 'focus' || e.type == 'focusin') {
                            eCrash.fieldPopup.destroy('error', ele); //destroy eny error messages if focusin
                        }
                        
                        var filterValue = ele.val();
                        var lastChar = filterValue.slice(-1);
                        
                        if(lastChar == ';') {
                            activeValues = func() || {length: 0};
                        } else {
                            var filterMatcher = new RegExp('^' + previousFilter, 'i');
                            if (!filterMatcher.test(filterValue)) {
                                activeValues = func() || {length: 0};
                            }
                        }

                        if (previousFilter == filterValue) {
                            return;
                        }

                        activeValues = filterListByValue(activeValues, filterValue, ele, dropdownType);
                        previousFilter = filterValue;
                        highlightRow = null;

                        messages = generateMessagesFromList(activeValues);
                        
                        if (messages.length == 0) {
                            eCrash.fieldPopup.destroy('hint');
                            return;
                        }

                        eCrash.fieldPopup.clear('hint');
                        eCrash.fieldPopup.show('hint', ele, messages.values);
                        
                        drawHighlightRow(highlightRow);
                        eCrash.displayChildRows(ele);

                    } else if ((e.type == 'blur' || e.type == 'focusout' || (e.type == 'keydown' && e.which == 9))) {

                        eCrash.fieldPopup.destroy('hint');
                        previousFilter = null;

                        if (highlightRow !== null) {
                            //using mouse click & tab
                            var previousValue = $(ele).val();
                            var splitValue = previousValue.split(";");
                            
                            if (activeValues.keys.length >= 1) {
                                
                                var lastChar = previousValue.slice(-1);
                                
                                if(previousValue == ''){
                                    ele.val((activeValues.keys[highlightRow]+';').replace(/^;/,'').toUpperCase());
                                } 
                                else if(lastChar != ';') {
                                    var n = previousValue.lastIndexOf(";");
                                    var previousValue = previousValue.slice(0,n);
                                    
                                    var uppercaseActiveValues = [];
                                    
                                    $.each( activeValues.keys, function( key, value ) {
                                        uppercaseActiveValues.push(value.toUpperCase());
                                    });

                                    var tempValue = (previousValue+';'+activeValues.keys[highlightRow]+';').replace(/^;/,'').toUpperCase();
                                    var previousValuesArray = previousValue.split(';');
                                    
                                    $.each( previousValuesArray, function( key, value ) {
                                        if (uppercaseActiveValues.indexOf(value) == -1) {
                                            tempValue = tempValue.replace(value.toUpperCase()+';', "");
                                        }
                                    });

                                    var lastCharactor = tempValue.slice(-1);

                                    if(lastCharactor == " " || lastCharactor == ';') {
                                        ele.val(tempValue);
                                    } else {
                                        ele.val(tempValue+';');
                                    }
                                }
                                else {
                                    ele.val((previousValue+activeValues.keys[highlightRow]+';').replace(/^;/,'').toUpperCase());
                                }
                            }
                            
                            highlightRow = null;

                            setTimeout(function(){
                                $(ele).focus();
                            }, 100);
                        }
                    }

                } else {

                    var dropdownType = 'single';
                    if (e.type == 'keydown' && e.which != 9) {
                        // Up, Down, Page Down, Page Up
                        if (e.which == 38 || e.which == 40 || e.which == 34 || e.which == 33) {
                            highlightRow = calculateHighlightRow(activeValues.length, highlightRow, e.which);
                            drawHighlightRow(highlightRow);
                            return;
                        }
                    } else if (e.type == 'focus' || e.type == 'focusin' || e.type == 'keyup') {
                        if (e.type == 'focus' || e.type == 'focusin') {
                            eCrash.fieldPopup.destroy('error', ele); //destroy eny error messages if focusin
                        }
                        
                        var filterValue = ele.val();
                        var filterMatcher = new RegExp('^' + previousFilter, 'i');
                        if (!filterMatcher.test(filterValue)) {
                            activeValues = func() || {length: 0};
                        }

                        if (previousFilter == filterValue) {
                            return;
                        }

                        activeValues = filterListByValue(activeValues, filterValue, ele, dropdownType);
                        previousFilter = filterValue;
                        highlightRow = null;
                        messages = generateMessagesFromList(activeValues);
                        if (messages.length == 0) {
                            eCrash.fieldPopup.destroy('hint');
                            return;
                        } else if (messages.length == 1 && (e.type == 'keyup' && (e.which != 8 && e.which != 46))) {
                            ele.val(messages.keys[0]);
                        }
                        eCrash.fieldPopup.clear('hint');
                        eCrash.fieldPopup.show('hint', ele, messages.values);
                        
                        drawHighlightRow(highlightRow);
                    } else if (e.type == 'blur' || e.type == 'focusout' || (e.type == 'keydown' && e.which == 9)) {

                        eCrash.fieldPopup.destroy('hint');
                        previousFilter = null;

                        if (highlightRow !== null) {
                            ele.val(activeValues.keys[highlightRow]);
                        }
                    }
                    eCrash.displayChildRows(ele);
                }
            }
        }
    })();

    /**
     * ValueFormat logic
     * This will format the value of a field as it is entered. (date fields, etc)
     */
    eCrash.valueFormat = function(func) {
        return function(e) {
            console.log('valueFormat', func);
            if (isEventStopped(e)) return;
            if (e.altKey || e.ctrlKey || e.metaKey || e.which <= 46 || e.which >= 112) return;

            var ele = $(e.target);

            var addition = func(ele.val());
            if (addition) {
                ele.val(ele.val() + addition);
            }
        }
    }

    /**
     * AutoTab Logic
     */
    eCrash.autoTab = function(func) {
        return function(e) {
            console.log('autoTab', func);
            if (isEventStopped(e)) return;
            if (e.altKey || e.ctrlKey || e.metaKey || e.which <= 46 || e.which >= 112) return;

            var ele = $(e.target);

            if (func(ele.val())) {
                var newEvent = $.Event('focusout');
                newEvent.target = e.target;
                ele.trigger(newEvent);
                if (!newEvent.isDefaultPrevented()
                    && !newEvent.isImmediatePropagationStopped()
                    && !newEvent.isPropagationStopped()
                ) {
                    focusNextElement(ele);
                }
            }
        }
    }

    /**
     * AutoFill Logic
     * This will fill other fields according to a mapping legend.
     */
    eCrash.autoFill = function(fields, valueList) {
        return function(e) {
            console.log('autoFill', fields, valueList);
            if (isEventStopped(e)) return;

            var ele = $(e.target);
            var newValue = null;

            if (valueList) {
                newValue = valueList(ele.val());
            } else if (ele.prop('type') != 'checkbox' || (ele.prop('type') == 'checkbox' && ele.is(':checked'))) {
                newValue = ele.val();
            }

            for (var i = 0; i < fields.length; i++) {
                $('#' + fields[i]).val(newValue);
            }
        }
    }

    /**
     * CustomFunction Logic
     * Used to inject custom field specific functionality
     */
    eCrash.customFunction = function(func) {
        return function(e) {
            console.log('customFunction', func);
            if (isEventStopped(e)) return;

            return func(e);
        }
    }
    
    /** Run this by Custom Function **/
    eCrash.loadCityList = function(func) {
        return function(e) {
            var activeEle = $(e.target);
            var eleID = activeEle.attr('id');
            var $stateField = $("#"+eleID);
            var searchString = $stateField.val(); /* state value to be searched */

            if ($.trim(searchString).length > 0) {
                getSubElementByState($stateField); /* get City List by State */
            }   
        }
    }
    

    // Custom and/or global functionality

    eCrash.focusCenterView = function(e) {
        console.log('focusCenterView');
        if (isEventStopped(e)) return;

        if (e.type == 'focusin' || e.type == 'focus') {
            // Try to maintain the element as close to center as possible.

            var $target = $(e.target);
            var $parent = $target.offsetParent();

            $parent.scrollTop(
                -($parent.height() / 2) + ($parent.scrollTop() + $target.offset().top) + $target.height() / 2
            );
        }
    }
    eCrash.globalFunction.push(eCrash.focusCenterView);

    eCrash.isRequired = function(e) {
        console.log('isRequired');
        if (isEventStopped(e)) return;

        var ele = $(e.target);

        // Detect 'required' class
        if (e.type == 'save' || e.type == 'focusout') {
            if (ele.hasClass('required') && !ele.hasClass('skipped') && $.trim(ele.val()) === '') {

                if($(ele).closest('.ui-tabs-panel').length > 0) {
                    if($(ele).closest('.ui-tabs-panel').is(':visible')) {
                        eCrash.fieldPopup.destroy('error', ele); //destroy the current popup
                        eCrash.fieldPopup.show('error', ele, ['Field is required.']); //create new
                        return false;
                    }
                } else {
                    eCrash.fieldPopup.destroy('error', ele); //destroy the current popup
                    eCrash.fieldPopup.show('error', ele, ['Field is required.']); //create new
                    return false;
                }
                
            }
        }
    }
    eCrash.globalFunction.push(eCrash.isRequired);

    /**
     * Dynamic Verification
     */
    eCrash.isDynamicVerificationPhase = function() {
        // We're not in the Dynamic Verification phase, so we're going to ignore that we were called.
        return (typeof(eCrash.data.dynamicVerification.previousValues) != 'undefined');
    }
    
    /**
     * Will be invoked only for dynamic verfication entry stage(pass 2)
     */
    eCrash.initDynamicVerification = function() {
        eCrash.setCitationDetails();
        $(".formPageSection table > tbody  > tr.elem_nonprior").each(function(index, tr) {
            $(this).css( "background-color", "#EFEFEF" );
            $(this).closest("tr").find("input,textarea").css("background-color", "#EFEFEF" );
        });
    }
    
    /**
     * Set the Updated Vehicle Number
     */
    eCrash.setNewVehicleNumber = function() {
        var vehicleUnits = $(document).find("input[id^='Vehicle_UnitNumber']");
        $.each(vehicleUnits, function(a,b){
            var previousValue = eCrash.dynamicVerification.getPreviousFieldValue(b);
            if(previousValue){ // Load Only the Value if has Previous Value
                $(b).val(previousValue);
                $(b).closest(".form-page").find("input[name^='Person_VehicleUnitNumber_Hidden']").val(previousValue); // Change the Value of Paired Vehicle Unit Number
            }
        });
    }

    /**
     * To check whether its a vin field
    */
    eCrash.isVinField = function(ele) {
        var isVin = 0;
        if($(ele).closest('tr').hasClass('vinNumber')) {
            isVin = 1;
        }

        return isVin;
    }

    eCrash.dynamicVerification = $.extend(function(e) {
        if (isEventStopped(e)) return;

        var tc = eCrash.tc;
        var previousField = e.target;
        var previousValue = eCrash.dynamicVerification.getPreviousFieldValue(previousField);

        var ele = $(e.target);
        if (eCrash.isDeletedTabByElementName(ele)) {
            return ;
        }
        
        var keyedValue = null;
        
        // We're not in the Dynamic Verification phase, so we're going to ignore that we were called.
        if (previousValue === false || previousValue === null || $(ele).hasClass('not-validate') || $(ele).hasClass('disable-dynamic-verification')) {
            // To skip the discrepancy validation in pass 2 after the keyer pressed the Ctrl+d
            if ($(ele).hasClass('not-validate')) {
                $(ele).data('lastKeyedValue', $(ele).val());
                $(ele).val('');
                //Removing not-validate and adding disable-dynamic-verfication to do this particular field clear event only once.
                $(ele).removeClass('not-validate');
                $(ele).addClass('disable-dynamic-verification');
            }
            
            return;
        }
        
        if (ele.attr('readonly')
            || ele.attr('disabled')
            || ele.attr('type') == 'hidden'
            || (ele.data('dynamicVerification.disabled') && !eCrash.isVinField(ele))) {
            return;
        }

        if (ele.is(':radio, :checkbox') && ele.attr('id').indexOf('AddressGui') == -1) {
            var eleGroup = $('input[name="' + ele.attr('name') + '"]');
            keyedValue = eCrash.fieldValue(eleGroup);

            if ((e.type == 'keydown' && e.which == 9) || e.type == 'mouseup' || e.type == 'click') {
                if (e.type == 'mouseup') {
                    eleGroup.data('captureClick', true);
                    return;
                } else if (e.type == 'click') {
                    if (eleGroup.data('captureClick')) {
                        eleGroup.removeData('captureClick');
                    } else {
                        return;
                    }
                }

                // Do validation.
                if (previousValue === keyedValue) {
                    eCrash.fieldPopup.destroy('error', eleGroup);
                    return true;
                }

                if (eleGroup.data('lastKeyedValue') === keyedValue) {
                    eleGroup.data('acceptedValue', true);
                    eCrash.fieldPopup.destroy('error', eleGroup);
                    return true;
                }

                eleGroup
                    .data('lastKeyedValue', keyedValue)
                    .removeAttr('checked').data('checked', false)
                    .removeData('acceptedValue');

                eCrash.fieldPopup.destroy('error', eleGroup);

                if(ele.is(':checkbox')) {
                    var youEntered = eCrash.dynamicVerification.getCheckboxText(keyedValue);
                    var theyEntered = eCrash.dynamicVerification.getCheckboxText(previousValue);
                } else {
                    var youEntered = eCrash.dynamicVerification.getFieldLabelByValue(eleGroup, keyedValue);
                    var theyEntered = eCrash.dynamicVerification.getFieldLabelByValue(eleGroup, previousValue);
                }

                eCrash.fieldPopup.destroy('error', eleGroup);
                eCrash.fieldPopup.show('error', eleGroup, [
                    'Value does not match previous value entered in pass1'
                ]);

                return fullEventStop(e);
            } else if (e.type == 'save') {
                if (keyedValue == previousValue) {
                    return true;
                } else if (eleGroup.data('acceptedValue')) {
                    return true;
                }
                
                if(ele.is(':checkbox')) {
                    /**
                     * @TODO: Required an alternative fix for 16 element checkbox.
                     */
                    if (eleGroup.prop('name') == 'element16_field') {
                        return true;
                    }
                    
                    var theyEntered = eCrash.dynamicVerification.getCheckboxText(previousValue);
                } else {
                    var theyEntered = eCrash.dynamicVerification.getFieldLabelByValue(eleGroup, previousValue);
                }
                
                //to show error message on corresponding tabs
                openTab(ele);
                
                eCrash.fieldPopup.show('error', e.target, ['Value does not match previous value entered in pass1']);
                
                return fullEventStop(e);
            }

            return;
        } else if (ele.is(':checkbox') && ele.attr('id').indexOf('AddressGui') != -1) {
            eCrash.fieldPopup.destroy('error', ele);
        } else if (ele.attr('id').indexOf('AddTrailerGui') != -1) {
            return true;
        } 

        keyedValue = ele.val().toUpperCase();

        if ((e.type == 'keydown' && e.which == 9) || e.type == 'focusout' || e.type == 'blur') {

            if($(ele).closest('tr').hasClass('multiselect')) {
                //multiselect field
                eCrash.fieldPopup.destroy('error', e.target);

                if(previousValue == '' && keyedValue == '') {
                    return true;
                }
                    
                var kv = keyedValue;
                var lastChar = kv.slice(-1);

                if (lastChar == ';') {
                    return;
                }

                if(e.which == 9) {
                    var previousValueList = $.trim(previousValue).split(";");
                    var keyedValueList = $.trim(keyedValue).split(";");
                    var previousValueListSize = previousValueList.length;

                    if(keyedValue == '') {
                        var keyedValueListSize = 0;
                    } else {
                        var keyedValueListSize = keyedValueList.length;
                    }
                    
                    var match = 1;
                    for (var i=0; i < keyedValueList.length; i++) {
                        if(keyedValueList[i] != '') {
                            if($.inArray( keyedValueList[i], previousValueList) == -1){
                                match = 0;
                                break;
                            }
                        }
                    }

                    if((previousValueListSize == keyedValueListSize) && match == 1) {
                        //values are matching
                        eCrash.fieldPopup.destroy('error', e.target);
                        return true;
                    }

                    ele.data('lastKeyedValue', keyedValue);
                    ele.val('');
                    ele.removeData('acceptedValue');

                    eCrash.fieldPopup.destroy('error', e.target);
                    eCrash.fieldPopup.show('error', e.target, [
                        'Value does not match previous value entered in pass1'
                    ]);
                }
                
                return fullEventStop(e);
             } else {
                //other than multiselet field
                eCrash.fieldPopup.destroy('error', e.target);

                if (e.which == 9 || eCrash.isVinField(ele)) {

                    if (previousValue == '' && keyedValue == '') {
                        return true;
                    }

                    if (previousValue == keyedValue) {
                        eCrash.fieldPopup.destroy('error', e.target);
                        return true;
                    }

                    ele.data('lastKeyedValue', keyedValue);
                    ele.val('');
                    ele.removeData('acceptedValue');
                    
                    eCrash.fieldPopup.show('error', e.target, [
                        'Value does not match previous value entered in pass1'
                    ]);
                }

                return fullEventStop(e);
            }

    } else if (e.type == 'save') {

            if($(ele).closest('tr').hasClass('multiselect')){
                //multiselect field
                var previousValueList = $.trim(previousValue).split(";");
                var kv = keyedValue;
                var lastChar = kv.slice(-1);

                if (lastChar == ';') {
                    var keyedValueList = $.trim(keyedValue.slice(0, -1)).split(";");
                } else {
                    var keyedValueList = $.trim(keyedValue).split(";");
                }
                
                var previousValueListSize = previousValueList.length;
                
                if(keyedValue == '') {
                    var keyedValueListSize = 0;
                } else {
                    var keyedValueListSize = keyedValueList.length;
                }

                var match = 1;
                for (var i=0; i < keyedValueList.length; i++)
                {
                    if(keyedValueList[i] != '') {
                        if($.inArray( keyedValueList[i], previousValueList) == -1){
                            match = 0;
                            break;
                        }
                    }
                }

                if(previousValue == '' && keyedValue == '') {
                    return true;
                } else {
                    if((previousValueListSize == keyedValueListSize) && match == 1) {
                    //values are matching
                        return true;
                    } else if (ele.data('acceptedValue')) {
                        return true;
                    }
                }

                //to show error message on corresponding tabs
                openTab(ele);

                setTimeout(function(){
                    eCrash.fieldPopup.show('error', e.target, ['Value does not match previous value entered in pass1']);
                }, 500);

                return fullEventStop(e);
            } else {
                if(previousValue == '' && keyedValue == '') {
                    return true;
                } else {
                    if (keyedValue == previousValue) {
                        return true;
                    } else if (ele.data('acceptedValue')) {
                        return true;
                    }
                }
                
                //to show error message on corresponding tabs
                openTab(ele);

                setTimeout(function(){
                    eCrash.fieldPopup.show('error', e.target, ['Value does not match previous value entered in pass1']);
                }, 500);

                return fullEventStop(e);
            }
            
        }
    }, {
        disableOnField: function(field, disabled) {
            $(field).data('dynamicVerification.disabled', disabled);
        },
        getPreviousFieldValue: function(field) {
            var data = eCrash.data.dynamicVerification;
            // We're not in the Dynamic Verification phase, so we're going to ignore that we were called.
            if (typeof(data.previousValues) == 'undefined') {
                return false;
            }

            var ele = $(field);
            if (typeof data.previousValues[ele.attr('name')] == 'undefined') {
                return '';
            }

            return data.previousValues[ele.attr('name')].toUpperCase();
        },
        getFieldLabelByValue: function(eleGroup, value) {
            return eleGroup.filter(function() {
                return ($(this).attr('value') == value);
            }).get(0).nextSibling.data;
        },
        getCheckboxText : function(value) {
            var checkboxText = (typeof(value) == 'undefined' || $.trim(value) == '' || 
            value == null) ? 'unchecked' : 'checked';

            return checkboxText;
        }
    });

    /** @todo Figure out how to attach this functionality (and possibly others) on page load if existing data. */
    
    eCrash.getOriginalDataFieldId = function(field) {
        var fieldInfo = eCrash.formHelpers.getFieldInfo(field);
        return fieldInfo.fieldParts.join('_') + 'Original-t' + fieldInfo.tableInstance;
    }

    eCrash.saveOriginalData = function() {
        return $.extend(
            function(e) {
                if (isEventStopped(e) || $.inArray(e.type, ['focusout', 'save']) == -1) {
                    return;
                }
                var vv = eCrash.vv;
                var currentVal = $(e.target).val();
                var fieldInfo = eCrash.formHelpers.getFieldInfo(e.target);
                var fieldName = fieldInfo.fieldParts.join('_');
                var originalFieldId = eCrash.getOriginalDataFieldId(e.target);
                var vinId = 'Vehicle_Vin-t' + fieldInfo.tableInstance;
                var initLastValues = false;

                if (typeof(vv.vinElementsAll[vinId]) == 'undefined') {
                    vv.initVinElements($('#' + vinId));
                    if (eCrash.isDynamicVerificationPhase()) {
                        initLastValues = true;
                    }
                }
                for (var elem in vv.vinElementsAll[vinId]) {
                    var elemObj = vv.vinElementsAll[vinId][elem];
                    if (initLastValues) {
                        vv.vinElementsAll[vinId][elem].lastValue =
                        $('#' + eCrash.getOriginalDataFieldId(vv.vinElementsAll[vinId][elem].obj)).val();
                    }
                    if (elemObj.name == fieldName) {
                        if (currentVal != elemObj.lastValue) {
                            $('#' + originalFieldId).val(elemObj.lastValue);
                            vv.vinElementsAll[vinId][elem].lastValue = currentVal;
                        }
                    }
                }
            },
            {errorMessage: ''}
        );
    }

    eCrash.syncHiddenVehicleUnitNumber = function() {
        return $.extend(
            function(e) {
                console.log('syncHiddenVehicleUnitNumber');
                if (isEventStopped(e)) return;

                if (e.type != 'keyup' && e.type != 'focusout') {
                    return;
                }

                var defaultValue = e.target.defaultValue.toUpperCase();
                var newValue = $(e.target).val().toUpperCase();

                eCrash.formHelpers.filterGroups('Person', function(i, group) {
                    group.each(function() {
                        var fieldInfo = eCrash.formHelpers.getFieldInfo(this);
                        if (fieldInfo.fieldParts[1] == 'VehicleUnitNumber') {
                            if ($(this).attr('type') == 'hidden') {
                                if ($(this).data('originalValue')) {
                                    var originalValue = $(this).data('originalValue');
                                } else {
                                    var originalValue = this.defaultValue.toUpperCase();
                                    $(this).data('originalValue', originalValue);
                                }

                                if (originalValue == defaultValue) {
                                    $(this).val(newValue);
                                }
                            }

                            return false;
                        }
                    });

                    return false;
                });
            },
            {errorMessage: ''}
        );
    }
    
    //12345678901234567, 4T3ZF13C0WV0H37Z1 (1 p-match), 4T3ZF13C0WU0H37Z1 (exact),
    //4T3ZF13C0WU0H47Z1 (multiple)
    eCrash.vinValidation = function() {
        function processDynamicFields(ele, handleVin, handleOthers) {
            // Copy Make/Model/Year/Status from previous pass.
            eCrash.formHelpers.getFieldInstanceGroup(ele).each(function() {
                var $this = $(this);
                var fieldName = eCrash.formHelpers.getFieldInfo($this).fieldParts[1];

                switch (fieldName) {
                    case 'Vin':
                        handleVin($this);
                        break;

                    case 'Make':
                    case 'Model':
                    case 'ModelYear':
                    case 'VinStatus':
                        handleOthers($this);
                        break;
                }
            });
        }

        function runDynamicVerification(e) {
            var ele = $(e.target);

            if (!((e.type == 'keydown' && e.which == 9) || e.type == 'focusout' || e.type == 'blur' || e.type == 'save')) {
                    return true;
            }
            
            eCrash.dynamicVerification.disableOnField(e.target, false);
            var dynamicResult = eCrash.dynamicVerification(e);
            eCrash.dynamicVerification.disableOnField(e.target, true);

            if (dynamicResult === true) {
                var previousField = e.target;

                var dynamicValue = eCrash.dynamicVerification.getPreviousFieldValue(previousField);
                if (dynamicValue == ele.val().toUpperCase() && dynamicValue != '') {
                    ele.data('dynamic-same', true);

                    var last = null;
                    ele.val(dynamicValue); //Copy uppercase value
                    processDynamicFields(ele,
                        function($this) {
                            eCrash.data.lastVin[ele.attr('id')] = eCrash.dynamicVerification.getPreviousFieldValue($this);
                            last = $this;
                        },
                        function($this) {
                            var previousField = $this;
                            $this
                                .attr('readonly', 'readonly')
                                .val(eCrash.dynamicVerification.getPreviousFieldValue(previousField))
                                .addClass('skipped');
                            if ($this.attr('tabindex')) {
                                $this
                                    .data('tabindex', $this.attr('tabindex'))
                                    .removeAttr('tabindex');
                            }

                            last = $this;
                        }
                    );
                } else if (ele.data('dynamic-same')) {
                    ele.data('dynamic-same', false);
                    processDynamicFields(ele, function($this) {
                        // Do Nothing...
//                      eCrash.data.lastVin[ele.attr('id')] = null;
                    }, function ($this) {
                        $this
                            .removeAttr('readonly')
                            .val('')
                            .removeClass('skipped');
                        if ($this.data('tabindex')) {
                            $this
                                .attr('tabindex', $this.data('tabindex'))
                                .removeData('tabindex');
                        }
                    });
                }
            }
        }

        return $.extend(
            function(e) {
                console.log('vinValidation');
                if (isEventStopped(e)) return false;
                if (eCrash.isDynamicVerificationPhase()) {
                    eCrash.dynamicVerification.disableOnField(e.target, true);
                    var dynResult = runDynamicVerification(e);
                    // if dynamic verification has stopped event we need to return false
                    if (isEventStopped(e)) return false;
                }

                var ele = $(e.target);
                var vinId = ele.attr('id');
                var vin = $.trim(ele.val());
                if ((e.type == 'focusout' || e.type == 'blur' || e.type == 'save')
                    && ($(e.target).val() != e.target.defaultValue
                            || (typeof(eCrash.data.lastVin[vinId]) != 'undefined'
                                    && eCrash.data.lastVin[vinId].toUpperCase() != vin.toUpperCase()
                                )
                       )
                ){
                        // making sure that we wont run validation process if another validation process is being run
                        var vinResult = eCrash.vv.isVinDialogOpened() ? {status: false} : eCrash.vv.validateVin(e);
                        eCrash.fieldPopup.destroy('error', ele);
                        ele.removeClass('invalid');


                        if (vinResult.status === false) {
                            var fieldName = ele.parents('label').text();
                            if (!fieldName) fieldName = 'Value';

                            var errorMessage = vinResult.errorMessage;
                            if (typeof(vinResult.errorMessage) != 'undefined' && vinResult.errorMessage != '') {
                                ele.addClass('invalid');
                                eCrash.fieldPopup.show(
                                    'error',
                                    ele,
                                    [eCrash.parseErrorMessage(errorMessage, fieldName, '')]
                                );
                            }
                            fullEventStop(e);
                        }

                        return vinResult.status;
                    }

                return true;
            },
            {errorMessage: "%fieldName% %validationLevel% must be 17 alpha-numeric characters. 'I', 'O', and 'Q' are not valid."}
        );
    }
    

    // Individual Criteria Functions. Hook into above logic for the full experience.

    /**
     * Defines a value list and wraps access to it.
     * @param valueList string
     * @param depId string Id of the element this value depends on.
     * @return function
     */
    eCrash.defineValueList = function(valueList, depId) {
        /**
         * @param key string
         * @return object|string|null object: {keys: [], values: []}
         */
        return function(key) {
            var theList = eCrash.valueLists[valueList];
            if (typeof(key) == 'undefined') {
                if (typeof(depId) == 'undefined') {
                    return theList;
                } else {
                    var depValue = $('#' + depId).val();
                    var depList = {keys: [], values: [], length: 0};

                    if (depValue.length == 0) {
                        return depList;
                    }

                    for (var i = 0; i < theList.length; i++) {
                        if (theList.keys[i].indexOf(depValue) === 0) {
                            depList.keys.push(theList.keys[i].substr(depValue.length));
                            depList.values.push(theList.values[i]);
                            depList.length++;
                        }
                    }

                    return depList;
                }
                return theList;
            } else {
                if (typeof(depId) !== 'undefined') {
                    key = $('#' + depId).val() + key;
                }

                var index = $.inArray(key, eCrash.valueLists[valueList].keys);
                if (index >= 0) {
                    return eCrash.valueLists[valueList].values[index];
                }else {
                    return null;
                }
            }
        }
    }

    /**
     * Determines if a value is in a preset list.
     * @param valueList string
     * @param depId string|undefined
     * @return Function
     */
    eCrash.inValueList = function(valueList, depId) {
        return $.extend(
            function(value) {
                if (typeof(depId) == 'undefined') {
                    return ($.inArray(value.toUpperCase(), eCrash.valueListsKeysUpperCase[valueList]) >= 0);
                } else {
                    return (eCrash.defineValueList(valueList, depId)(value) !== null);
                }
            },
            {errorMessage: '%fieldName% %validationLevel% be in the provided list.'}
        );
    }
    
    eCrash.inValueMultiSelectList = function(valueList, depId) {
        return $.extend(
            function(value) {
                var value = $.trim(value);
                if (typeof(depId) == 'undefined') {
                    var lastChar = value.slice(-1);
                    if (lastChar == ';') {
                            value = value.slice(0, -1);
                    }
                    var splitValue = value.split(";");
                    var valid = true;
                    for (var y=0; y < splitValue.length; y++) {

                        if(splitValue[y] != ' ' || splitValue[y] != ';' || splitValue[y] != undefined) {
                            if($.inArray($.trim(splitValue[y]), eCrash.valueListsKeysUpperCase[valueList]) >= 0) {
                            } else {
                                valid = false;
                                break;
                            }
                        }
                    }
                    return valid;
                } else {
                    var splitValue = value.split(";");
                    var valid = true;
                    for (var y=0; y < splitValue.length; y++) {
                        if(splitvalue[y] != '' || splitvalue[y] != ';') {
                            if(eCrash.defineValueList(valueList, depId)(splitvalue[y]) !== null) {
                                valid = false;
                                break;
                            }
                        }
                    }
                    return valid;

                }
            },
            {errorMessage: '%fieldName% %validationLevel% be in the provided list.'}
        );
    }

    eCrash.inValueMultiSelectDuplicateFilter = function(valueList, depId) {
        return $.extend(
            function(value) {
                var value = $.trim(value);
                var lastChar = value.slice(-1);
                if (lastChar == ';') {
                        value = value.slice(0, -1);
                }
                var splitValue = value.split(";");
                var valid = true;

                var sorted_arr = splitValue.sort();
                var results = [];  
                for (var i = 0; i < splitValue.length; i++) {
                    if (sorted_arr[i + 1] == sorted_arr[i]) {
                        results.push(sorted_arr[i]);  
                    }
                }

                if(results.length > 0) {
                    valid = false;
                }
                
                return valid;
                
            },
            {errorMessage: '%fieldName% %validationLevel% not be contain duplicates.'}
        );
    }

    eCrash.isDateWithMY = function() {
        var isDateMDY = eCrash.isDateMDY();
        return $.extend(
            function(value) {
                return (
                    isDateMDY(value)
                    || /^(0?[1-9]|1[0-2])\/[0-9]{4}$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid M/Y or M/D/Y date.'}
        );
    }

    eCrash.isDate = function() {
        return $.extend(
            function(value) {
                return (Date.parse(value) > 0);
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid date.'}
        );
    }

    eCrash.isDateMDY = function() {
        return $.extend(
            function(value) {
                try {
                    var date = $.datepicker.parseDate('mm/dd/yy', value);
                    return (
                        $.datepicker.formatDate('yy', date).length == 4
                            && $.datepicker.formatDate('mm/dd/yy', date) == value
                    );
                } catch (e) {
                    return false;
                }
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid date.'}
        );
    }

    // Used for validating if chars entered so far are in valid date format
    eCrash.isDatePartial = function() {
    return $.extend(
        function(value) {
            return (
                    ///^[0-9]{0,2}[/]?[0-9]{0,2}[/]?[0-9]{0,4}$/.test(value)
                    /^\d{0,2}[/]?\d{0,2}[/]?\d{0,4}$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain numeric characters and /.'}
        );
    }

    eCrash.isValidPhoneNumber = function() {
    return $.extend(
        function(value) {
            return (
                    /[^a-zA-Z\u00A1-\u00FF\r\n]$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain numeric and special characters. No alpha characters.'}
        );
    }

    eCrash.isValidPhoneNumberImmediate = function() {
    return $.extend(
        function(value) {
            return (
                    /^\d{0,3}[-]?\d{0,3}[-]?\d{0,4}$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain numeric characters and dashes.'}
        );
    }

    eCrash.isTime = function() {
        return $.extend(
            function(value) {
                return (
                    /\d?\d:\d\d/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid time.'}
        );
    }

    eCrash.isTime24Hour = function() {
        return $.extend(
            function(value) {
                return (
                    /^([01]?[0-9]|2[0-3]):[0-5]?[0-9]$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid 24 hour time.'}
        );
    }

    eCrash.isTime12Hour = function() {
        return $.extend(
            function(value) {
                return (
                    /^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM]$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% be a valid 12 hour time.'}
        );
    }
    
    eCrash.isNumeric = function() {
        return $.extend(
            function(value) {
                return (
                    /^[0-9]*\.?[0-9]$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain numeric characters.'}
        );
    }

    eCrash.isNumericandDot = function() {
        return $.extend(
            function(value) {
                return (
                    /^[0-9]*\.?[0-9]*$/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain numeric characters.'}
        );
    }
    
    eCrash.isAlpha = function() {
        return $.extend(
            function(value) {
                return !(
                    /[^A-Za-z]/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain alpha characters.'}
        );
    }
    
    eCrash.isAlphaNumeric = function() {
        return $.extend(
            function(value) {
                return !(
                    /[^0-9A-Za-z]/.test(value)
                );
            },
            {errorMessage: '%fieldName% %validationLevel% only contain alpha-numeric characters.'}
        );
    }

    eCrash.isVin = function() {
        return $.extend(
            function(value) {
                return
                    (value.length != 0 && value.length != 17) || /[IOQ]/i.test(value);
            },
            {errorMessage: "%fieldName% %validationLevel% must be 17 alpha-numeric characters. 'I', 'O', and 'Q' are not valid."}
        );
    }
    
    eCrash.isValidVin = function(value) {
        return (value.length != 17) || /[IOQ]/i.test(value) || /[^0-9A-Za-z]/.test(value);
    }
    
    eCrash.isUnitNumberUnique = function() {
        return $.extend(
            function(value, e) {
                var result = true;
                var sourceFieldInfo = eCrash.formHelpers.getFieldInfo(e.target);
                var vehicleGroup = eCrash.formHelpers.fieldGroups.Vehicle;
                
                $.each(vehicleGroup, function(groupIndex, groupItem) {
                    if (groupIndex == sourceFieldInfo.tableInstance) {
                        return true;
                    }
                    var existingUnitNumber = eCrash.formHelpers.getFieldValue(
                        sourceFieldInfo['groupBy'], 
                        groupIndex, 
                        sourceFieldInfo['fieldParts'][1]
                    );
                    if (value == existingUnitNumber) {
                        result = false;
                        return false;
                    }
                });
                
                return result;
            },
            {errorMessage: '%fieldName% should be unique.'}
        );
    }

    eCrash.isLengthWithinMax = function(max) {
        return eCrash.byLengthMax(max);
    }

    eCrash.isLengthWithinMin = function(min) {
        return eCrash.byLengthMax(min);
    }

    eCrash.byLength = function(min, max) {
        return $.extend(
            function(value) {
                if (typeof(max) == 'undefined') max = min;
                return (value.length >= min && value.length <= max);
            },
            {errorMessage: '%fieldName% %validationLevel% be between ' + min + ' and ' + max + ' length.'}
        );
    }

    eCrash.byLengthMin = function(min) {
        return $.extend(
            function(value) {
                return (value.length >= min);
            },
            {errorMessage: '%fieldName% %validationLevel% be more than or equal to ' + min + ' characters.'}
        );
    }

    eCrash.byLengthMax = function(max) {
        return $.extend(
            function(value) {
                return (value.length <= max);
            },
            {errorMessage: '%fieldName% %validationLevel% be less than or equal to ' + max + ' characters.'}
        );
    }

    eCrash.checkSpecialChars = function() {
        return $.extend(
            function(value) {               
                try {
                    var specialChars = /[!_%*()+=?[\]|;"]/;
                    return !(specialChars.test(value));
                } catch (e) {
                    return false;
                }   
            },
            {errorMessage: 'Characters: ! _ % * ( ) + = ? [ ] | ; " are not allowed'}
        );
    }
    
    /** AlphaCharacters Only **/
    eCrash.isAlphaNumericChar = function() {
        return $.extend(
            function(value) {               
                try {
                    var alphaNumericChar = /[~@#$^{}:<>',!_%*()+=?[\]\/|;."]/;
                    return !(alphaNumericChar.test(value));
                } catch (e) {
                    return false;
                }   
            },
            {errorMessage: 'Special Characters are not allowed'}
        );
    }
    
    // Only used for GPSOther, Longitude and Lattitude report form fields //
    eCrash.checkSpecialCharsLongLat = function() {
        return $.extend(
            function(value) {               
                try {
                    var specialChars = /[!_%*()+=?[\]|;]/;
                    return !(specialChars.test(value));
                } catch (e) {
                    return false;
                }   
            },
            {errorMessage: 'Characters: ! _ % * ( ) + = ? [ ] | ;  are not allowed'}
        );
    }

    // ########## Only used by the valueFormat logic ##########

    eCrash.asDate = function() {
        return function(value) {
            var ele = $(document.activeElement); /* tried this $(':focus');  returns undefined or e.target is undefined */

            return eCrash.asStaticFormat('Date','mm/dd/yyyy', ele);
        }
    }

    eCrash.asDateMDY = eCrash.asDate;

    eCrash.asTime = function() {
        return function(value) {
            var ele = $(document.activeElement);
            
            return eCrash.asStaticFormat('TimeShort','hh:mm', ele);
        }
    }
    
    eCrash.asTime12Hr = function() {
        return function(value) {
            var fieldValue = value.trim();
            if (fieldValue.length == 2) {
                fieldValue += ":";
            } else if(fieldValue.length == 5) {
                fieldValue += " ";
            }
            
            $(document.activeElement).val(fieldValue);
        }
    }
    
    eCrash.asPhone = function() {
        return function(value) {
            var ele = $(document.activeElement);

            return eCrash.asStaticFormat('Phone','###-###-####', ele);
        }
    }

    eCrash.asStaticFormat = function(format, placeholder, element) {
        var elementID = element.attr('id');
        new InputMask().Initialize($("#"+elementID),
        {
            mask: InputMaskDefaultMask[format], 
            placeHolder: placeholder,
        }); 
    }

    /** Check Future Date **/
    eCrash.isFutureDate = function() {
        return $.extend(
            function(value) {
                var ele = $(this)[0].document.activeElement;
                var fieldValue = value;
                var fieldName = eCrash.formHelpers.getFieldLabelByElement(ele);
                var todayDate = new Date(getCurrentDateMDY());
                var outputDate = new Date(value);

                if(todayDate >= outputDate){
                    return true;
                }else{
                    $(ele).val($(ele).val().substr(0,6)); /* remove year if future date */
                    return false;
                }
            },
            {errorMessage: '%fieldName% %validationLevel% not be a future date.'}
        );
    }
    
    function getCurrentDateMDY(){
        var today = DateFormat.format.date(new Date(), "M/dd/yyyy");

        return today;
    }

    //@todo hook it to Save and ignore it
    eCrash.vv = {
        vinElem : {},
        vinStatusElem : {},
        modelYearElem : {},
        makeElem : {},
        modelElem : {},
        //TODO: store lastValue in the object's data
        vinElements: {
            vinElem: {name: 'Vehicle_Vin', alias: 'VIN', obj: {}, lastValue: ''},
            vinStatusElem: {name: 'Vehicle_VinStatus', obj: {}, lastValue: ''},
            modelYearElem: {name: 'Vehicle_ModelYear', alias: 'Year', obj: {}, lastValue: ''},
            makeElem: {name: 'Vehicle_Make', obj: {}, alias: 'Make', lastValue: ''},
            modelElem: {name: 'Vehicle_Model', obj: {}, alias: 'Model', lastValue: ''}
        },
        vinElementsAll: {},
        progressDialog : {},
        json: {},
        vehicle: {},
        vinRevalidate: true,
        validationHistory: {},
        initVinElements: function(vinElem) {
            var tc = eCrash.tc;

            this.vinElem = vinElem;
            var vinId = this.vinElem.attr('id');
            var elemPostfix = vinId.substr(vinId.indexOf('-'));

            for (elem in this.vinElements) {
                var elemId = this.vinElements[elem].name + elemPostfix;
                this[elem] = $('#' + elemId);
                this.vinElements[elem].obj = $('#' + elemId);
            }
            this.vinElementsAll[vinId] = this.vinElements;
        },
        validateVin: function(e) {
            //clear previous value
            this.json = null;
            this.vehicle = null;

            this.vinElem = $(e.target);

            this.initVinElements($(e.target));

            var vin = $.trim(eCrash.fieldValue(this.vinElements.vinElem.obj));
            var vinId = this.vinElements.vinElem.obj.attr('id');

            var resultFalse = {status: false};
            var resultTrue = {status: true};

            vin = vin.toUpperCase();

            // preventing form from not being saved when VIN status is E and VIN is not changed
            if (e.type == 'save' &&
                this.vinStatusElem.val() == 'E' &&
                typeof(eCrash.data.lastVin[vinId]) != 'undefined' &&
                eCrash.data.lastVin[vinId].toUpperCase() === vin
            ) {
                return resultTrue;
            }

            if (vin.length == 0) {
                this.vinElements.vinStatusElem.obj.val('');
                this.makeVinElementsReadOnly(false);
                eCrash.data.lastVin[vinId] = '';
                return resultTrue;
            }

            if (typeof(eCrash.data.lastVin[vinId]) != 'undefined' &&
                eCrash.data.lastVin[vinId].toUpperCase() !== vin){
                this.vinRevalidate = true;
            }

            if ((typeof(eCrash.data.lastVin[vinId]) != 'undefined' &&
                eCrash.data.lastVin[vinId].toUpperCase() === vin) ||
                this.prefillFromHistory()) {
                var result = resultTrue;
                if (this.vinRevalidate && this.vinElements.vinStatusElem.obj.val() !== 'V') {
                    result = {status: false, errorMessage: 'This VIN was already validated and considered invalid'};
                }
                return result;
            }

            this.vinRevalidate = true;
            eCrash.data.lastVin[vinId] = vin;

            this.makeVinElementsReadOnly(false);
            
            if (eCrash.isValidVin(vin)) {
                this.vinElements.vinStatusElem.obj.val('E');
                var result = {status: false, errorMessage: "VIN must be 17 alpha-numeric characters. 'I', 'O', and 'Q' are not valid."};
                return result;
            }
            else {
                data = {
                    vin: vin,
                    csrfToken: $('#csrf').val()
                };
                this.asyncVinValidation(data, '/admin/invalid-vin/potential-match-json', 'Performing VIN validation');
                // returning false to stop form from saving before validation process finished
                return resultFalse;
            }
        },
        isVinDialogOpened: function() {
            return $("#vinValidationDialog").is(':data(dialog)');
        },
        makeVinElementsReadOnly: function(readOnly){
            $([this.makeElem, this.modelElem, this.modelYearElem]).each(function(i, elem){elem.attr('readonly', readOnly);});
        },
        asyncVinValidation: function(data, url, message) {
            if (typeof(message) == 'undefined') {
                var message = 'Please wait...';
            }
            this.progressDialog = window.progressDialogManager.create().show(message);
            $.ajax({
                type: 'GET',
                url: window.baseUrl + url,
                dataType: 'json',
                success: this.jsonResultHandler,
                error:this.ajaxErrorHandler,
                data: data
            });
        },
        addToHistory: function(vehicle) {
            var vv = eCrash.vv;
            if (vehicle.VIN == '') {
                return;
            }
            if (typeof(vv.validationHistory[vv.vinElem.attr('id')]) == 'undefined') {
                vv.validationHistory[vv.vinElem.attr('id')] = {};
            }
            vv.validationHistory[vv.vinElem.attr('id')][vehicle.VIN] = vehicle;
        },
        prefillFromHistory: function(){
            vv = eCrash.vv;
            if (typeof(vv.validationHistory[vv.vinElem.attr('id')]) != 'undefined'
                && typeof(vv.validationHistory[vv.vinElem.attr('id')][vv.vinElem.val()]) != 'undefined'
            ) {
                var vinDataExists = false;
                vehicleHistory = vv.validationHistory[vv.vinElem.attr('id')][vv.vinElem.val()];
                vv.setVinAndFriends(vehicleHistory, true);
                for (var i in vehicleHistory) {
                    if ($.inArray(i, ['Status', 'VIN']) == -1 && vehicleHistory[i] != '') {
                        vinDataExists = true;
                        break;
                    }
                }
                // if one of the fields Year/Make/Model is not empty - these fields will be pre-filled and we need to
                // set focus on the field that is next to Year/Make/Model fields
                if (vinDataExists) {
                    $('#' + vv.vinElem.attr('id').replace('Vehicle_Vin-', 'Vehicle_InsuranceCompany-')).focus();
                }
                else {
                    vv.modelYearElem.focus();
                    vv.makeVinElementsReadOnly(false);
                }

                return true;
            }
            return false;
        },
        setVinAndFriends: function(vehicle, readonly) {
            var vv = eCrash.vv;
            if (typeof(vehicle.Status) == 'undefined') {
                vehicle.Status = 'V';
            }

            vv.addToHistory(vehicle);
            eCrash.data.lastVin[vv.vinElements.vinElem.obj.attr('id')] = vehicle.VIN;
            vv.vinElements.vinStatusElem.obj.val(vehicle.Status);

            var vinFieldInfo = eCrash.formHelpers.getFieldInfo(vv.vinElements.vinElem.obj);
            var vinId = vinFieldInfo.fieldParts.join('_') + '-t' + vinFieldInfo.tableInstance;

            for (elem in vv.vinElements) {
                var elemObj = vv.vinElements[elem];
                if (typeof(vehicle[elemObj.alias]) != 'undefined') {
                    var originalFieldId = eCrash.getOriginalDataFieldId(elemObj.obj);
                    if (vehicle[elemObj.alias] != elemObj.lastValue) {
                        $('#' + originalFieldId).val(elemObj.lastValue);
                        vv.vinElementsAll[vinId][elem].lastValue = vehicle[elemObj.alias];
                    }
                    elemObj.obj.val(vehicle[elemObj.alias]);
                    if (elemObj.alias != 'VIN') {
                        elemObj.obj.attr('readonly', readonly);
                    }
                }
            }
        },
        skipVinFunc: function() {
            var vv = eCrash.vv;
            $("#vinValidationDialog").dialog().remove();
            vv.setVinAndFriends({VIN:'',Year:'',Make:'',Model:'',Status:''}, false);
            vv.modelYearElem.focus();
        },
        invalidVinFunc: function() {
            var vv = eCrash.vv;
            vinStatus = 'E';
            vv.vinElements.vinStatusElem.obj.val(vinStatus);
            vv.addToHistory({'VIN':vv.vinElem.val(),Year:'',Make:'',Model:'',Status:vinStatus});
            $("#vinValidationDialog").dialog().remove();
            vv.modelYearElem.focus();
        },
        reEnterVinFunc: function(vinStatus) {
            var vv = eCrash.vv;

            $("#vinValidationDialog").dialog().remove();
            vv.vinRevalidate = false;
            // if there is no potential match - sending VIN to the history
            if (vv.json.count == 0){
                this.addToHistory({'VIN':vv.vinElem.val(),Status:vinStatus});
            }

            vv.vinElements.vinStatusElem.obj.val(vinStatus);
            vv.vinElements.vinElem.obj.focus();
        },
        okFunc: function() {
            var vv = eCrash.vv;
            vv.setVinAndFriends(vv.vehicle, true);
            $("#vinValidationDialog").dialog().remove();
            var vinId = vv.vinElem.attr('id');
            $('#' + vinId.replace('Vehicle_Vin-', 'Vehicle_InsuranceCompany-')).focus();
        },
        acceptFunc: function() {
            var vv = eCrash.vv;
            var checkedVinRadio = $("input[name='potentialVinMatchGroup']:checked");
            if (checkedVinRadio.length == 1) {
                var vehicle = vv.json.vehicles[checkedVinRadio.val()];
                vv.setVinAndFriends(vehicle, true);
                checkedVinRadio.attr('checked', false);
                $("#vinValidationDialog").dialog().remove();
                var vinId = vv.vinElem.attr('id');
                $('#' + vinId.replace('Vehicle_Vin-', 'Vehicle_InsuranceCompany-')).focus();
            }
            else {
                $('.selectOneErrorMsg').show();
            }
        },
        jsonResultHandler: function(json) {
            if (hasCsrfError(json)) {
                // TODO: Need business clarification
                return;
            }

            var vv = eCrash.vv;
            var tc = eCrash.tc;
            vv.json = json;
            var vehicles = json.vehicles;
            var vin = $.trim(eCrash.fieldValue(vv.vinElem));
            console.log("JSON Success - ", json);
            vv.progressDialog.hide(true);

            if (json.count == 0) {
                if (typeof(json.errorMessage) != 'undefined') {
                    vv.vinElements.vinElem.obj.focus();
                    return;
                }
                $('<div id="vinValidationDialog">' +
                    'This VIN could not be decoded successfully<br/>' +
                    '<div style="padding-left:20px;padding-top: 10px;">' +
                        'Click <b>Yes</b> to accept the invalid VIN.<br/>' +
                        'Click <b>No</b> to re-enter VIN.<br/>' +
                        
                    '</div>' +
                '</div>').dialog({
                    modal: true,
                    title: 'Invalid VIN',
                    width: 450,
                    zIndex: 0,
                    buttons: [
                        //change status to E, focus on year field
                        {text: "Yes", click: vv.invalidVinFunc},
                        //mark vin as invalid, left focus to the vin field, previously entered fields are present
                        {text: "No", click: function(){vv.reEnterVinFunc('E');}},
                        
                    ],
                    close: vv.skipVinFunc
                });
            }
            //exact match. Also we need to display this dialog when using tag conversion
            else if (json.count == 1 && vehicles[0].VIN === vin) {
                var dialogText = 'Click <b>Ok</b> to accept VIN.<br/>Click <b>Cancel</b> to re-enter VIN.';
                vv.vehicle = json.vehicles[0];
                $('<div id="vinValidationDialog">' +
                        '<b>VIN:</b> ' + vv.vehicle.VIN + '<br/>' +
                        '<b>Year:</b> ' + vv.vehicle.Year + '<br/>' +
                        '<b>Make:</b> ' + vv.vehicle.Make + '<br/>' +
                        '<b>Model:</b> ' + vv.vehicle.Model + '<br/><br/>' +
                        dialogText +
                 '</div>').dialog({
                    modal: true,
                    title: 'Accept VIN',
                    width: 450,
                    zIndex: 0,
                    buttons: [
                        // accepting VIN as valid, filling out Year, Make, Model and VIN fields
                        {text: "Ok", click: vv.okFunc},
                        // mark vin as valid, left focus on the vin field, previously entered fields are present
                        {text: "Cancel", click: function(){vv.reEnterVinFunc('V');}}
                    ],
                    close: function(){vv.reEnterVinFunc('V');}
                });
            }
            // >= 1 VIN match
            else if ( json.count >= 1 ) {
                console.log("more than 1 match or VIN mismatch", json.vehicles);
                var potentialVinMatchHtml =
                    '<div id="vinValidationDialog"> ' +
                        '<table class="hor-zebra">' +
                            '<thead>' +
                                '<tr>' +
                                    '<td></td><td>VIN</td><td>Year</td><td>Make</td><td>Model</td>' +
                                '</tr>' +
                            '</thead>' +
                            '<tbody>';

                $.each(vehicles, function(i, vehicle) {
                    potentialVinMatchHtml +=
                        '<tr>' +
                            '<td>' +
                                '<input type="radio" name="potentialVinMatchGroup" value="' + i + '"' +
                                    ((i == 0) ? ' checked="checked" ' : '') +
                                '/>' +
                            '</td>' +
                            '<td>' + vehicle.VIN + '</td>' +
                            '<td>' + vehicle.Year + '</td>' +
                            '<td>' + vehicle.Make + '</td>' +
                            '<td>' + vehicle.Model + '</td>' +
                        '</tr>';
                });

                potentialVinMatchHtml +=
                            '</tbody>' +
                        '</table>' +
                        '<div style="display:none;color:red;text-align:center;" class="selectOneErrorMsg">' +
                            'Please select at least one.' +
                        '</div>' +
                        'Click <b>Add</b> to accept VIN.<br/>' +
                        'Click <b>Cancel</b> to re-enter VIN.' +
                    '</div>';

                $(potentialVinMatchHtml).dialog({
                    modal: true,
                    title: 'Potential VIN matches',
                    width: 600,
                    buttons: [
                        // accepting VIN as valid, filling out Year, Make, Model and VIN fields
                        {text: "Add", click: vv.acceptFunc},
                        //mark vin as invalid, left focus to the vin field, previously entered fields are present
                        {text: "Cancel", click: function(){vv.reEnterVinFunc('E');}}
                    ],
                    close: function(){vv.reEnterVinFunc('E');}
                });
            }
        },
        ajaxErrorHandler: function (xhr, ajaxOptions, thrownError) {
            console.log("JSON Failure - ", xhr.status, ajaxOptions, thrownError);
            var vv = eCrash.vv;
            vv.progressDialog.hide(true);
            vv.vinStatusElem.val('E');
            vv.vinElem.focusout().focus(); //trigger error
        }
    };




    // ########## HELPER FUNCTIONS ##########

    function fullEventStop(e) {
        e.stopImmediatePropagation();
        e.stopPropagation();
        e.preventDefault();
    }

    function isEventStopped(e) {
        return (e.isDefaultPrevented()
            || e.isImmediatePropagationStopped()
            || e.isPropagationStopped());
    }

    /**
     * Parses an error message and replaces the fieldName and validationLevel.
     * @param errorMessage string
     * @param fieldName string
     * @param validationLevel string
     * @return string
     */
    eCrash.parseErrorMessage = function(errorMessage, fieldName, validationLevel) {
        return errorMessage.replace('%fieldName%', fieldName).replace('%validationLevel%', validationLevel);
    }

    /**
     * Contains helper functions for dealing with field-level popups.
     */
    eCrash.fieldPopup = (function() {

        var activeMessages = [];

        /**
         * Creates a popup of type with the message(s) specified.
         * @param type string
         * @param ele jQuery|DOMElement
         * @param messages array string
         */
        function show(type, ele, messages) {
            //to show error message on corresponding tabs
            openTab(ele);
            
            setActivePageByElement(ele);
            var popup = get(type);
            var ele = $(ele);
            if (popup.length == 0 || popup.prop('owner') != ele.attr('id')) {
                if (popup.length > 0 && popup.prop('owner') != ele.attr('id')) {
                    destroy(type);
                }

                $(document.body).append('<div id="' + type + 'Popup" class="' + type + 'Popup popup"><ul></ul></div>');
                popup = get(type);
                popup.prop('owner', ele.attr('id'));
            }

            popup.hide();

            // Filter existing messages out of the new message list, so as not to duplicate any.
            messages = $.grep(messages, function(value) {
                return ($.inArray(value, activeMessages) == -1);
            });
            activeMessages = $.merge(activeMessages, messages);

            if (messages.length > 0) {
                $('ul', popup).append('<li>' + messages.join('</li><li>') + '</li>');
            }
            
            var docHeight = $(document).height();
            var popupHeight = $("div[id='"+ type +"Popup']").height();
            var offset = ele.offset();
            var offsetLeft = offset.left;
            var offsetTop = offset.top;
            var eleOuterHeight = ele.outerHeight();
            
            // The last 10 pixels are just for buffer space.
            if (popup.outerHeight() + offsetTop + eleOuterHeight + 10 > docHeight) {
                popup.height(docHeight - eleOuterHeight - offsetTop - 10);
            }
            
            var formStatusHeight = $("#formStatus").height();
            var formStatusOffset = $("#formStatus").offset();
            var formStatusOffsetTop = formStatusOffset.top;
            var popupTop = offsetTop + eleOuterHeight;
            if (popupTop > formStatusOffsetTop) {
                var bottomElemSpace = eleOuterHeight + formStatusHeight + 10;
                if (popupHeight > docHeight) {
                    popupHeight = docHeight - bottomElemSpace;
                }
                popup.height(popupHeight);
                var popupSpace = popupHeight + bottomElemSpace;
                popupTop = docHeight - popupSpace;
				if (ele.siblings('span').length > 0) {
					offsetLeft += ele.siblings('span').width() + 10;
				}                
            }
            
            popup.css({
                top: popupTop,
                left: offsetLeft,
                width: 'auto'
            });

            popup.show();
        }

        /**
         * Gets the popup element, if it exists.
         * @param type string
         */
        function clear(type) {
            get(type).html('<ul></ul>');
            activeMessages = [];
        }

        /**
         * Gets the popup element, if it exists.
         * @param type string
         * @return jQuery|DOMElement
         */
        function get(type) {
            return $('#' + type + 'Popup');
        }

        /**
         * Destroys the popup and all data in it.
         * @param type string
         * @param ele jQuery|DOMElement
         */
        function destroy(type, ele) {
            var popup = get(type);
            if (typeof(ele) == 'undefined' || popup.prop('owner') == $(ele).attr('id')) {
                popup.empty().remove();
                activeMessages = [];
            }
        }
        
        function destroyByEvent(e) {
            var ele = $(e.target);
            destroy('error', ele);
        }

        return {
            show: show,
            clear: clear,
            get: get,
            destroy: destroy,
            destroyByEvent: destroyByEvent
        };
    })();

    /**
     * Return a function that will trigger a fake event on another element.
     * @param id
     * @param type
     */
    eCrash.fakeEvent = function(id, type) {
        return function() {
            var e = new $.Event(type);
            e.target = $('#' + id)[0];

            eCrash.fields[id].autoFill(e);
        }
    }

    // Thanks to this site for the following two functions:
    // http://javascript.nwbox.com/cursor_position/
    function getSelectionStart(o) {
        if (o.createTextRange) {
            var range = document.body.createTextRange();  
            var r = range.duplicate(); 
            r.moveEnd('character', o.value.length);
                
            if (r.text == '') {
                return o.value.length;
                return o.value.lastIndexOf(r.text);
                }
        } else {
            return o.selectionStart;
        }
    }

    function getSelectionEnd(o) {
        if (o.createTextRange) {
            var range = document.body.createTextRange();  
            var r = range.duplicate();  
            r.moveStart('character', -o.value.length)
            return r.text.length
        } else {
            return o.selectionEnd;
        }
    }
    
    function getSelectionRange(ele) {
        return [getSelectionStart(ele), getSelectionEnd(ele)];
    }

    /** Get Sub List City **/
    function getSubElementByState(field) {

        var id = $(field).attr("id");
        var searchString = $(field).val(); /* the State Field Value */
        var indexes = [];

        for(var i = 0 ; i < eCrash.valueLists.City.values.length ; i++)
        {
            if(eCrash.valueLists.City.values[i] == searchString)
            {
                indexes.push(i); /* Cities where State equal to searchString */
            }
        }

        /* Initialize dynamicFields to be used by the City Field */
        eCrash.valueLists.dynamicFields.keys = [];
        eCrash.valueLists.dynamicFields.values = [];
        eCrash.valueLists.dynamicFields['length'] = 0;

        /* Push All City Data into the Array */
        $.each(indexes, function(key, val){
            eCrash.valueLists.dynamicFields.keys.push(eCrash.valueLists.City.keys[val]);
            eCrash.valueLists.dynamicFields.values.push(eCrash.valueLists.City.keys[val]);
        })
        eCrash.valueLists.dynamicFields['length'] = indexes.length;
    }

// form interaction helpers



    eCrash.formHelpers = (function() {
        var fieldGroups = {};
        var fieldInfoById = {};
        var personArray = ['FirstName', 'MiddleName', 'LastName', 'NameSuffix', 'DateOfBirth', 'DriversLicenseNumber', 
                           'DriversLicenseJurisdiction', 'HomePhone', 'InjuryStatus', 'VehicleUnitNumber', 'Sex'];
        var addressArray = ['Address', 'Address2', 'City', 'State', 'ZipCode'];
        var personInfo = $.merge(personArray, addressArray);
        
        /** @todo Add fieldGroup merging and related functionality. */
        // Merging would be based on groupBy (Person, Vehicle) and UnitNumber or PartyId
        // Related would further expand on that to Person.VehicleUnitNumber = Vehicle.UnitNumber
        
        function getAddressFields() {
            return addressArray;
        }
        
        function getPersonInfoFields() {
            return personInfo;
        }
        
        function getFieldInfo(field) {
            var ele = $(field);
            var id = ele.prop('id');
            if (id == '') {
                return false;
            }

            if (typeof(fieldInfoById[id]) == 'undefined') {
                fieldInfoById[id] = parseFieldInfoFromId(id);
            }

            return fieldInfoById[id];
        }
        
        /** Get Div Tab Number **/
        function getDivTabNumber(field) {
            var id = $(field).attr("id");
            var tabNumber = parseInt(id.split('-').splice(1,1).join('-'));

            return tabNumber;
        }
        
        function getFieldLabelByElement(field) {
            var label = field.parentNode.firstChild.innerText;

            return label;
        }

        function parseFieldInfoFromId(id) {
            var fieldInfo = {
                fieldParts: [],
                groupBy: '',
                tableInstance: null,
                fieldInstance: null,
                fieldValue: null,
                prefix: ''
            };
            var fieldParts = [];

            if (id.indexOf('__') != -1) {
                fieldParts = id.split('__');
                id = fieldParts[1];
                fieldInfo.prefix = fieldParts[0];
            }

            fieldParts = id.split('-');
            fieldInfo.fieldParts = fieldParts[0].split('_');
            fieldInfo.groupBy = fieldInfo.fieldParts[0];

            $.each(fieldParts, function(k, v) {
                if (k == 0) {
                    return;
                }

                switch (v[0]) {
                    case 't':
                        fieldInfo.tableInstance = v.substr(1);
                        break;

                    case 'f':
                        fieldInfo.fieldInstance = v.substr(1);
                        break;

                    case 'v':
                        fieldInfo.fieldValue = v.substr(1);
                        break;
                }
            });

            return fieldInfo;
        }
        
        function cleanUpFieldValue(ele,stringToRemove){
            return ele.val(ele.val().replace(stringToRemove,""));
        }

        function addNewField(ele) {
            var ele = $(ele);
            var fieldInfo = getFieldInfo(ele);
            if (fieldInfo === false) {
                return;
            }

            if (typeof(fieldGroups[fieldInfo.groupBy]) == 'undefined') {
                fieldGroups[fieldInfo.groupBy] = {};
            }
            if (typeof(fieldGroups[fieldInfo.groupBy][fieldInfo.tableInstance]) == 'undefined') {
                fieldGroups[fieldInfo.groupBy][fieldInfo.tableInstance] = $();
            }

            fieldGroups[fieldInfo.groupBy][fieldInfo.tableInstance].push(ele[0]);
        }

        function parseAllFields() {
            eCrash.formHelpers.fieldGroups = fieldGroups = {};
            parseNewFields('#formPages');
        }

        function parseNewFields(ele) {
            $('input, textarea', ele).each(function() {
                addNewField($(this));
            });
        }

        function getFieldInstanceGroup(ele) {
            var fieldInfo = getFieldInfo(ele);

            if (fieldInfo !== false) {
                return fieldGroups[fieldInfo.groupBy][fieldInfo.tableInstance];
            } else {
                return $();
            }
        }

        function filterGroups(groupBy, filter) {
            var filteredGroups = {};
            $.each(fieldGroups[groupBy], function(k, v) {
                if (filter(k, v) == true) {
                    filteredGroups[k] = v;
                }
            });

            return filteredGroups;
        }

        function keyGroup(objs, keyer) {
            return mapObjectToMultiKeys(objs, keyer);
        }

        function getFieldValue(groupName, index, fieldName) {
            if (typeof(fieldGroups[groupName][index]) == 'undefined') {
                return;
            }
            var result = null;
            $.each(fieldGroups[groupName][index], function(index, field) {
                var fieldInfo = getFieldInfo(field);
                if (fieldInfo.fieldParts[1] == fieldName) {
                    result = $(field).val();
                    return false;
                }
            });
            
            return result;
        }
        
        function bindElementsByEventsToFunction(events, elemSelector, func) {
            var eventsList = events.join(' ');
            var elemList = $(elemSelector);
            if (elemList.length > 0) {
                $(elemSelector).on(eventsList, func);
            }
        }
        
        function toggleRequiredFieldByFlag(elem, isRequired) {
            if (elem != null) {
                if (isRequired) {
                    if (!elem.hasClass('required')) {
                        elem.addClass('required');
                        var elemVal = elem.val();
                        if ($.trim(elemVal) == '') {
                            eCrash.fieldPopup.show('error', elem, ['Field is required.']);
                            return false;
                        }
                    }
                } else {
                    if (elem.hasClass('required')) {
                        elem.removeClass('required');
                        eCrash.fieldPopup.destroy('error', elem);
                    }
                }
            }
        }
        
        return {
            fieldGroups: fieldGroups,
            parseAllFields: parseAllFields,
            parseNewFields: parseNewFields,
            filterGroups: filterGroups,
            keyGroup: keyGroup,
            getFieldInstanceGroup: getFieldInstanceGroup,
            getFieldInfo: getFieldInfo,
            getFieldValue: getFieldValue,
            getDivTabNumber: getDivTabNumber, /* Add New Helper Div Tab Number */
            getFieldLabelByElement: getFieldLabelByElement, /* Helper for getting Label Value */
            bindElementsByEventsToFunction: bindElementsByEventsToFunction,
            getPersonInfoFields: getPersonInfoFields,
            getAddressFields: getAddressFields,
            cleanUpFieldValue: cleanUpFieldValue, 
            toggleRequiredFieldByFlag: toggleRequiredFieldByFlag
        };
    })();



    // Form layout/opening logic
    function focusNextElement(ele) {
        var tabIndex = $(ele).attr('tabindex') || $(ele).data('tabindex');
        $(':input[tabindex="' + (tabIndex * 1 + 1) + '"]:first').focus();
    }
    
    // hooks into the image viewer window.
    var imageWindow = createImageWindow();

    function activatePageLayout() {
        $('.form-page').hide();
        $('#page-0').show();

        $('#formSubmit').bind('click', function() {$('#formContainer').submit();});
        $('#formPageForward').bind('click', pageForward);
        $('#formPageBack').bind('click', pageBack);
        $('#formPageAdd').bind('click', pageAdd);
        $('#formBadImage').bind('click', badImageAction);
        $('#formRekeyImage').bind('click', rekeyImageAction);
        $('#formDiscardImage').bind('click', discardImageAction);
        $('#formReorderImage').bind('click', reorderImageAction);
        $('#formNotes').bind('click', parent.common.noteWindow.reportNotes);
        $('#formNotesViewOnly').bind('click', parent.common.noteWindow.reportNotesViewOnly);
        $('#formContainer').bind('submit', formSave);
        $('#formExit').bind('click', formExit);

        $(document).shortkeys({
            'Ctrl+Shift+s': function() {
                $('#formReorderImage').click();
                $('#formSubmit').click();
            },
            'Ctrl+Shift+b': function() {$('#formBadImage').click();},
            'Ctrl+Alt+n': function() {
                if ($('#formNotes').length > 0) {
                    $('#formNotes').click();
                } else {
                    $('#formNotesViewOnly').click();
                }
            },
            'Ctrl+Shift+e': function() {$('#formExit').click();},
            'Ctrl+Shift+c': function() {$('#formPageAdd').click();},
            'Ctrl+Shift+y': function() {$('#formRekeyImage').click();},
            'Ctrl+Shift+r': function() {$('#formDiscardImage').click();},
            'PageDown': function() {$('#formPageForward').click();},
            'PageUp': function() {$('#formPageBack').click();},
            'Ctrl+r': function() {return false;},
            'F5': function() {return false;},
            'Ctrl+Shift+d': function() { pageDelete();},
            'Ctrl+d': function() {
                // To skip the discrepancy validation in pass 2
                if ($('#entryStage').val() == 'dynamic-verification') {
                    var field = document.activeElement;
                    var type = $(field).prop('type');
                    
                    if (type == 'text' || type == 'textarea') {
                        if (!$(field).hasClass('disable-dynamic-verification')) {
                            $(field).addClass('not-validate discrepancy-remove-highlighter');

                            setInterval(function() {
                                $(field).removeClass('discrepancy-remove-highlighter');
                            }, 1000);
                        }
                    }
                }
            }
        });

        $('ul#formPageList li').on('click', pageNavigate);

        // This has been commented out for the Universal form.
        // We will need a switch or config to re-enable for state forms.
//      $('#formPages').scroll(function() {imageWindow.scrollSync(this);});

        $('#formPages input:visible:first').focus();
    }

    eCrash.setActivePage = function setActivePage(page, focusElement, preserveMessage) {
        var pageNumber = page.prevAll().length;

        if (preserveMessage !== true) {
            eCrash.fieldPopup.destroy('error');
        }
        $('.form-page').not(page).hide();

        page.show();
        if (focusElement) {
            if (focusElement == 'first') {
                $(window).scrollTop(0);
                $(':input:enabled:first', page).focus();
            } else if (focusElement == 'last') {
                $(window).scrollTop($(window).scrollTop(90000));
                $(':input:visible:last', page).focus();
            }
        }

        // If you don't have the alternatiff plugin this WILL throw an error
        try {
            imageWindow.setSelectedImage(page.prevAll().length);
        } catch (err) {
            console.log(err);
        }

        // If no further pages then disable the forward button.
        if (page.next().length == 0) {
            $('#formPageForward').attr('disabled', 'disabled');
        } else {
            $('#formPageForward').removeAttr('disabled');
        }

        // If no previous pages then disable the back button.
        if (page.prev().length == 0) {
            $('#formPageBack').attr('disabled', 'disabled');
        } else {
            $('#formPageBack').removeAttr('disabled');
        }

        // Highlight the new active page.
        $('ul#formPageList li').removeClass('activePage');
        $($('ul#formPageList li').get(pageNumber)).addClass('activePage');
    }

    function setActivePageByNumber(pageNumber, focusElement) {
        eCrash.setActivePage($('#page-' + pageNumber), focusElement);
    }

    function setActivePageByElement(ele, preserveMessage) {
        eCrash.setActivePage($(ele).parents('.form-page'), null, preserveMessage);
    }

    function pageForward() {
        /*
        /****************** OLD Code Starts *************************************
        var nextPage = $('.form-page:visible', '#formPages').next();

        if (nextPage.length > 0) {
            eCrash.setActivePage(nextPage, 'first');
        }
        
        /****************** OLD Code End ****************************************/
        
        var $focusEle = $('#formContainer .form-page input:focus');
        if ($focusEle) {
            $section = $focusEle.closest('.formPageSection');
        }
        if ($section.length > 0) {
            
         var $nextTab = $section.find('.ui-tabs-nav li.ui-state-active').nextAll( 'li:visible:not(".delete-tab"):first ');
         if ($nextTab.length > 0) {
                $nextTab.find('a').trigger('click');
            }
        
        }
    }

    function pageBack() {
        /*
        /****************** OLD Code Starts *************************************
        var prevPage = $('.form-page:visible', '#formPages').prev();

        if (prevPage.length > 0) {
            eCrash.setActivePage(prevPage, 'last');
        }
        
        /****************** OLD Code End ****************************************/
        
        var $focusEle = $('#formContainer .form-page input:focus');
        if ($focusEle) {
            $section = $focusEle.closest('.formPageSection');
        }
        if ($section.length > 0) {
            var $prevTab = $section.find('.ui-tabs-nav li.ui-state-active').prevAll( 'li:visible:not(".delete-tab"):first ');
            if ($prevTab.length > 0) {
                $prevTab.find('a').trigger('click');
            }
        }
    }
    
    function pageDelete() {
        $focusEle = $('#formContainer .form-page input:focus');
        $section = $focusEle.closest('.formPageSection');
        var len = $section.find('.ui-tabs-nav li.ui-tabs-tab').not(".delete-tab").length;
        if (len>1) {
            var section_id = $section.attr('id').match(/\d+/); 
            var tabCurrent = $section.find('ul.ui-tabs-nav li.ui-state-active').text();
            var tab_section = (section_id == 2) ? 'Vehicle' : 'Other Party';

            if (!confirm('Do you want to delete '+tab_section+' Tab #'+tabCurrent+'?')){
                return false
            }
            
            $section.find('ul.ui-tabs-nav li.ui-tabs-active').hide();
            $section.find('ul.ui-tabs-nav li.ui-tabs-active').removeClass('ui-tabs-active').addClass("delete-tab");
            
            var tabLast = $section.find('.ui-tabs-nav li.ui-tabs-tab').last().text();
            var tabCurrent = $section.find('ul.ui-tabs-nav li.ui-state-active').text();
            var $lastTab = $section.find('.ui-tabs-nav li.ui-tabs-tab').not(".delete-tab").last();
            
            if (tabLast == tabCurrent) {
               var $lastTab = $section.find('.ui-tabs-nav li.ui-tabs-tab').not(".delete-tab").last();
            }
            
            $lastTab.find('ul.ui-tabs-nav li').addClass('ui-tabs-active');
            $lastTab.find('a').trigger('click');
        }
    }

    function pageNavigate() {
        setActivePageByNumber($(this).text() * 1 - 1, 'first');
    }

    function pageAdd() {
        // Query the server for the list of available pages.
        // Display dialog to user with list.
        // Once user selects one, query the server for it.
        // Add a page to the end of the list with the returned HTML.
        // Optional: Re-run autofill and/or valueList

        function requestAvailablePages() {
            $.ajax({
                type: 'post',
                url: window.baseUrl + '/data/report-entry/available-pages',
                data: {
                    csrf: $('#csrf').val()
                },
                success: listPages
            });
        }

        function listPages(data) {
            var pageListPopup = $('#newPageListPopup');
            if (pageListPopup.length == 0) {
                pageListPopup = $('<div id="newPageListPopup"></div>').appendTo(window.document.body);
            } else {
                pageListPopup.empty();
            }

            function addPageDialogHandler(){
                requestPageData($('input', pageListPopup).filter(':checked').val());
            }

            $('#newPageListPopup').bind('keyup.addpage', function(e) {
                if (e.keyCode == 13) {
                    addPageDialogHandler();
                }
            });
            $.each(data, function(key, value) {
                pageListPopup.append('<div><label><input type="radio" name="pageName" value="' + key + '" /> ' + value + '</label></div>');
            });
            $('input[type="radio"]:first', pageListPopup).attr('checked', 'checked');
            // create dialog, list result
            pageListPopup.dialog({
                modal: true,
                resizable: false,
                title: 'Add Optional/Duplicate Page',
                buttons: {
                    'Add Page': function() {
                        addPageDialogHandler();
                    },
                    'Cancel': function() {
                        $(this).dialog('close');
                    }
                }
            });
        }

        function requestPageData(pageName) {
            $.ajax({
                type: 'post',
                url: window.baseUrl + '/data/report-entry/add-page',
                data: {
                    pageName: pageName,
                    csrf: $('#csrf').val()
                },
                success: addNewPage
            })
        }

        function addNewPage(data) {
            $('#newPageListPopup').unbind('keyup.addpage');
            $('#newPageListPopup').dialog('close');

            var pageNumber = parseInt($('#formPageList li:last').text());
            $.each(data.pageContents, function(i) {
                $('<div id="page-' + (pageNumber) + '" class="form-page" style="display: none;"></div>')
                    .appendTo('#formPages')
                    .html(data.pageContents[i])
                    .prepend('<input type="hidden" name="_pages[' + (pageNumber) + ']" value="' + (data.baseNames[i]) + '" />');
                $('<li></li>')
                    .appendTo('#formPageList')
                    .text((pageNumber+1));
                eCrash.formHelpers.parseNewFields($('#page-' + (pageNumber)));

                pageNumber++;
            });

            // Set focus on the newly added page
            setActivePageByNumber(pageNumber-1, 'first');

            $('#formPageForward').removeAttr('disabled');

            $.globalEval(data.header);
        }

        requestAvailablePages();
    }

    function submitFormImageAction(action, skipValidation) {
        $('#formContainer').attr('action', window.baseUrl + '/data/report-entry/' + action + '-image');
        $('#formContainer').data('skipValidation', skipValidation);
        $('#formContainer').submit();
    }

    function badImageAction() {
        requireNoteEntry({
            success: function() {
                submitFormImageAction('bad', true);
            }
        });
    }

    function rekeyImageAction() {
        requireNoteEntry({
            success: function() {
                submitFormImageAction('rekey', true);
            }
        });
    }

    function discardImageAction() {
        requireNoteEntry({
            success: function() {
                submitFormImageAction('discard', true);
            }
        });
    }

    function reorderImageAction() {
        requireNoteEntry({
            afterRender: function() {
                $('#reorder-yes, #reorder-no').bind('click', function() {
                    $this = $(this);
                    if ($this.attr('id') == 'reorder-yes') {
                        $('#reorder-date-container').show();
                    } else {
                        $('#reorder-date-container').hide();
                    }
                });
                $('#reorder-date').val($.datepicker.formatDate('yy-mm-dd', new Date())).datepicker({
                    dateFormat: 'yy-mm-dd',
                    beforeShow: function(input, inst) {
                        inst.dpDiv.css({marginTop: -input.offsetHeight + 'px', marginLeft: input.offsetWidth + 'px'});
                    }
                });
            },
            beforeSave: function() {
                var reorder = $('#reorder-yes, #reorder-no').filter(':checked');
                if (reorder.length == 0) {
                    alert('You must select either Yes or No to continue.');
                    return false;
                }

                return true;
            },
            success: function() {
                $('<input type="hidden" name="reorder" value="" />')
                    .val($('#reorder-yes, #reorder-no').filter(':checked').val())
                    .appendTo('#formContainer');
                $('<input type="hidden" name="reorder-date" value="" />')
                    .val($('#reorder-date').val())
                    .appendTo('#formContainer');

                submitFormImageAction('reorder', true);
            },
            appendContent:
                'Reorder: ' +
                '<label><input type="radio" name="reorder" id="reorder-yes" value="1" /> Yes</label>' +
                '<label><input type="radio" name="reorder" id="reorder-no" checked="checked" value="0" /> No</label>' +
                '<div id="reorder-date-container" class="ui-helper-hidden"><br />' +
                    '<label>Date: <input type="text" name="reorder-date" id="reorder-date" /></label>' +
                '</div>'
        });
    }
    
    /** 
    Before Saving I'm getting the least increments of page numbers and field names regardless if its deleted or not 
    because I'll be needing it when a vehicle has been deleted or reordered because the ordering of tabs is based on fieldname incrementation, 
    I'm just retaining the least page numbers and field incrementation and save it to an array then apply it to the remaining fields before saving.
    **/
    function setVehicleOrderByFieldIncrementValue() {
        
        /** Rearrange the Tabs and Page Number **/
        var $divOwner = $(document).find("#section-page-base-owner-02");
        var $liListOwner = $divOwner.find("ul.page_sorting > li").not(".delete-tab");
        var newPageOrder = [];
        var availablePageNumbers = [];
        var tabCount = 0;
        var output = "";
        
        /* Get All Field ID Name Pair Per Tab */
        var tabTableList = []; // List of Tables Containing the Input Fields
        var tabFieldList = []; // List of Field Inside the Tables
        var tabCount = $divOwner.find("div[id^='tabs']").length; // Total Tabs
        var deletedTabCount = $divOwner.find("div[id^='tabs'].delete-tab").length;  // Total Deleted Tabs
        var remainingTabCount = tabCount - deletedTabCount; // Total Remaining Tabs

        // This will retain proper count of tabs after deletion, this is done to preserve the field attributes of the deleted tabs              
        tabTableList = $divOwner.find("div div.form-page table").slice(0,remainingTabCount); 
        
        $.each(tabTableList, function(){
            var $tabElem = $(this);
            // Get all field list attributes for each remaining tab
            $.each($tabElem.find('td:not(.citations)').find("input, textarea"), function(key,val){ 
                var $elem = $(this);
                var id = $elem.attr("id");
                var name = $elem.attr("name");
                tabFieldList.push({"name": name,"id": id});
            });
        });
        
        /** 
        Get List of Party Page Numbers including deleted Pages 
        then subract the total count of deleted pages to 
        preserve proper page incrementation
        **/
        $divOwner.find('div div.form-page').slice(0, remainingTabCount).each(function (){
            $elem = $(this);
            pageID = $elem.attr("id").replace('page-',''); 
            availablePageNumbers.push(pageID);
        });
        
        /** Delete Elements with Class delete-tab **/
        $divOwner.find(".delete-tab").remove();
        
        if($liListOwner.length > 0){

            /** Get the New Tab Orders **/
            var tabId;
            var pageNumber;
            $.each($liListOwner, function(){
                tabCount++;
                tabId = $(this).find("a").attr("href").replace("#","");
                newPageOrder.push(tabId);
                pageNumber = $divOwner.find("#"+tabId+" > div.form-page").attr("id").replace("page-","");
            });

            /** Assign the Page Number to Each TABs Ascendingly **/
            $.each(newPageOrder, function(key,val){
                $divOwner.find("#"+val+" > div.form-page > input").attr("name","_pages["+availablePageNumbers[key]+"]");
                $divOwner.find("#"+val+" > div.form-page").attr("id","page-"+availablePageNumbers[key]);
            });

            /** For Tab Re Numbering **/
            var remainingTabs = $divOwner.find("ul.page_sorting > li").not(".delete-tab").find("a");
            var currentTabCount = remainingTabs.length;
            var newTabID = 0;
            var count = 0;

            if(currentTabCount > 0)
            {
                /** Set New Tab ID for DIV, UL & SPAN Value **/
                var $elem;
                var tabID;
                $.each(remainingTabs, function(key,val){
                    newTabID++;
                    $elem = $(this);
                    tabID = $elem.attr("href").replace("#","");
                    $divOwner.find("#"+tabID).attr("id","tabs-"+newTabID+"n"); /* Set Temp Tab ID for DIV */
                    $elem.attr("href","#tabs-"+newTabID); /* Set New Tab ID for UL */
                    $elem.text(newTabID); /* Set New Span Value for UL */
                });

                var $divTabs = $divOwner.find("div[id^='tabs']").not(".delete-tab"); /* Get Non Deleted Tabs */
                var fieldElementsArray = [];
                if($divTabs.length > 0)
                {
                    var tabNewID;
                    $.each($divTabs, function(key,val){
                        tabNewID = $(this).attr("id").replace('n',''); 
                        $(this).attr("id",tabNewID); /* Set New Tab ID for DIV */
                    });

                    /** Apply the New Element Sorting:Start **/
                    $divTabs.sort(function (a, b){
                        /* Get Int Val of Tab IDs */
                        a = eCrash.formHelpers.getDivTabNumber($(a)); 
                        b = eCrash.formHelpers.getDivTabNumber($(b));
                        
                        /* Arrange the element based on each ID */
                        if(a > b){
                            return 1;
                        }else if(a < b){
                            return -1;
                        }else{
                            return 0;
                        }
                    });
                    $divOwner.append($divTabs);  
                    /** Apply the New Element Sorting:End **/

                    /** Set the New Tab ID and Name:Start **/
                    var fieldNameElemListNew = $divTabs.find("table").find('td:not(.citations)').find("input, textarea");
                    $.each(fieldNameElemListNew, function(key,val){
                        var $elem = $(this);
                        $elem.attr("id",tabFieldList[key].id);
                        $elem.attr("name",tabFieldList[key].name);
                    })
                    /** Set the New Tab ID and Name:End **/
                }
            }
        }else{
            console.log("Tab Elements Not Found!");
        }       
    }
    
    /** 
    Check if Vehicle Unit Number has Proper Incrementation Upon Saving 
    **/
    function validateVehicleUnitNumber() {
        /** Check Sequence of Vehicle Unit Number **/
        var $divOwner = $(document).find("#section-page-base-owner-02");
        var $liListOwner = $divOwner.find("ul.page_sorting > li").not(".delete-tab"); // This returns the actual order of Tab(Vehicle) elements based on sorting
        
        if ($liListOwner.length > 0) {
            var isVehicleUnitNumberSync = true;
            // Since the Tab elements are already in the actual ordering, I will just have to compare the Vehicle Unit Number field value to the natural ordering of numbers 1, 2, 3,..
            $.each($liListOwner, function(a,b){ 
                var expectedVehicleUnitNumber = a + 1; // Expected Natural Order of Vehicle Unit Number
                var $tab = $(this).find("a");
                var tabID = $tab.attr("href").replace("#",""); // I'm looking for the equivalent Div of each List Tab since the Div arrangement doesnt happen real time,
                var currentVehicleUnitNumber = $divOwner.find("#"+tabID+" :input[id^='Vehicle_UnitNumber-']").val(); // I'm getting the current Vehicle Unit Number Value inside the Div
                    currentVehicleUnitNumber = parseInt(currentVehicleUnitNumber, 10); // This converts the current vehicle number to decimal format (base10) format because values could have leading zeros, I need this determine if it matches the natural order
                
                if(expectedVehicleUnitNumber != currentVehicleUnitNumber) {
                    isVehicleUnitNumberSync = false;
                }
            });
            
            return isVehicleUnitNumberSync;
        }
    }

    /**
     * Re-validate 'force' events on the form prior to saving.
     */
    function formSave(e) {
        var validateSuccess = true;
        var validateOrder = [
            'customFunction',
            'globalFunction',
            'validateForce',
            'validateForceImmediate'
        ];

        if ($('#formContainer').data('skipValidation')) {
            eCrash.windowClose.isAllowed(true);
            return true;
        }

        function validateElement() {
            var ele = $(this);            
            var id =  ele.attr('id'); 
            var new_ele =  $("#"+ele.attr('id')).attr('name');
            var field = null;
            
            if (typeof(eCrash.fields[new_ele]) !== 'undefined') {
                var field = eCrash.fields[new_ele];
            }
            
            var saveEvent = new $.Event('save');
            saveEvent.target = this;
            
            // Run both validateForce && validateForceImmediate to determine if the form is allowed to be saved.
            $.each(validateOrder, function(k, v) {
                if (v == 'globalFunction') {
                    return runValidationFunctions(eCrash.globalFunction, saveEvent);
                } else {
                    if (field && typeof(field[v]) !== 'undefined') {
                        return runValidationFunctions(field[v], saveEvent);
                    }
                }
            });

            // If either of those threw an error they would have stopped the event itself.
            if (isEventStopped(saveEvent)) {
                validateSuccess = false;

                // re-focus the page and element of the first invalid element.
                setActivePageByElement(this, true);
                ele.focus();
                event.preventDefault();
                return false;
            } else {
                return true;
            }
        }
        
        //Toggle Party Type field if required or not upon form saving before validation of each field
        function togglePartyTypeRequired() {
            var formHelpers = eCrash.formHelpers;

            var eleGroup = $("input[id*='Person_PersonType-t'][type='text']");
            //loop through each passenger party type
            $.each(eleGroup, function() {
                var ele = $(this);
                var groupElements = formHelpers.getFieldInstanceGroup(ele);

                //loop through all fields in same group
                var val = null;
                $.each(groupElements, function() {
                    // To skip the party type validation in other party based on the Party_Id hidden field
                    if (($(this).prop('name').indexOf('People') !== -1) && ($(this).prop('name').indexOf('Party_Id') !== -1)) {
                        // Match only the index of Party id in People i.e People[\d+][Party_Id]
                        return; //continue to next element in the same group
                    }
                    
                    var checkboxRadioArray = ['radio', 'checkbox'];
                    val = $(this).val();
                    if (($.trim(val) !== '' && $.inArray($(this).prop('type'), checkboxRadioArray) === -1) // For text fields
                        || ($.inArray($(this).prop('type'), checkboxRadioArray) !== -1 && $(this).prop('checked') === true)
                    ) {
                        if(!ele.hasClass('required')) {
                            ele.addClass('required'); //add required class
                        }
                        return false; //break from inner loop since 1 field in group has a value
                    }
                });
                //remove required class if no value on all fields in same group
                if(ele.hasClass('required') && $.trim(val) === '') {
                    ele.removeClass('required');
                }
            });
            return true;
        }

        $('#formPages .form-page').show();
        togglePartyTypeRequired();
        
        // re-validate every field in the form to make sure they all still pass.
        try {
            $("#formPages :input").each(function(i) {
                console.group(i, this.id, $(this).attr('name'));
                try {
                    var result = validateElement.call(this);
                } catch (err) {
                    result = false;
                }
                console.groupEnd();

                return result;
            });
        } catch (err) {
            alert('An internal error occurred when validating this form: ' + err);
            validateSuccess = false;
        }
        
        // If an element didn't validate then stop the form from saving.
        if (!validateSuccess) {
            eCrash.windowClose.isAllowed(false);

            return fullEventStop(e);
        } else {
            /** Check if Vehicle Unit Number is in Correct Sequence **/
            var confirmationMessage = "Are you sure you want to save the details entered in Form?";
            
            if (!validateVehicleUnitNumber()) {
                confirmationMessage = "The Vehicle Unit Number is out of sequence! \n"+confirmationMessage;
            }
            
            //confirmation prompt before saving the keyed report
            if (!confirm(confirmationMessage)) {
                return false;
            } else {
                /** Apply Proper Vehicle Field Incrementation and Page Number **/
                setVehicleOrderByFieldIncrementValue();
                
                /** Apply proper sequence number to citations **/
                eCrash.setCitationsOrderByArrayIndex();
            }
            
            if ($('#formSubmit').attr('disabled')) {
                return fullEventStop(e);
            }
            
            eCrash.windowClose.isAllowed(true);
            $('#formSubmit').attr('disabled', 'disabled');

            return true;
        }
    }

    /**
     * Exit the form (close the window) and prompt if there are any (text) values entered on the form.
     */
    function formExit(e) {
        //confirmation prompt before exit form
        if (!confirm('Are you sure you want to exit?')) return false;
        window.parent.close();
    }

    var fieldEventHooks = (function() {
        var functionalityHooks = {
            valueList: ['focusin', 'focusout', 'focus', 'blur', 'keyup', 'keydown'],
            customFunction: ['focusin', 'focusout', 'focus', 'blur', 'click'], 
            globalFunction: [],
            validateForce: ['focusout', 'blur'],
            validateForceImmediate: ['keypress'],
            validateSoft: ['focusout', 'blur'],
            valueFormat: ['keyup'],
            autoTab: ['keyup'],
            autoFill: ['focusout', 'blur']
        };

        function fieldEventHandler(e) {
            var eCrash = window.eCrash;
            var fields = (typeof(e.data) != 'undefined' && typeof(e.data.customFields) != 'undefined') ? e.data.customFields : eCrash.fields;

            $.each(functionalityHooks, function(k, v) {
                if (k == 'globalFunction') {
                    return runValidationFunctions(eCrash.globalFunction, e);
                }

                if ($.inArray(e.type, v) == -1
                    || typeof(fields[e.target.name]) == 'undefined'
                    || typeof(fields[e.target.name][k]) == 'undefined') {
                    return;
                }

                return runValidationFunctions(fields[e.target.name][k], e);
            });

            return !isEventStopped(e);
        }

        return {
            fieldEventHandler: function(e) {
                console.group(e.target.id, e, e.which, String.fromCharCode(e.which));
                try {
                    var result = fieldEventHandler(e);
                } catch (err) {

                    result = false;
                }
                console.groupEnd();

                return result;
            }
        };
    })();
    
    eCrash.sameAsOwnerDriver = (function() {
        
        var OWNER = 'Owner';
        var DRIVERM = 'Driver';
        var VEHICLE_OWNER = 'VEHICLE OWNER';
        var DRIVER = 'DRIVER';
        var PASSENGER = 'PASSENGER';
        
        var PERSON_TYPE = 'PersonType';
        var VEHICLE_UNIT_NUMBER = 'VehicleUnitNumber';
        var SAME_AS_DRIVER_ADD_GUI = 'SameAsDriverAddressGui';
        var SAME_AS_OWNER_ADD_GUI = 'SameAsOwnerAddressGui';
        var SAME_AS_DRIVER_GUI = 'SameAsDriverGui';
        
        var HIDDEN_PERSON_TYPE_FILTER = 'Person_PersonType_Hidden-t';
        
        var ALT_GROUP_ELEMENTS_KEY = 'altGroupElements';
        var MAPPED_ALT_GROUP_ELEMENTS_KEY = 'mappedAltGroupElements';
        var SPECIFIC_ELEMENTS_KEY = 'specificElements';
        var SELECTED_FIELDS_KEY = 'arraySelectedFields';
        
        var PASSENGER_SECTION = "div[id*='section-page-base-passenger']";
        var LAST_VALID_VEHICLE_NUMBER = 'lastValidVehicleNumber';
        var NAME_SPACE_KEY = 'nameSpace';
        
        var formHelpers = eCrash.formHelpers;
        
        function sameAsOwnerOrDriver() {
            return $.extend(
                function(e) {
                    if (isEventStopped(e)) return;

                    var ele = $(e.target);
                    var sameAsOwnerOrDriverElem = ele;

                    if (e.type == 'click') {

                        if (ele.is(':checked')) {
                            var groupElements = formHelpers.getFieldInstanceGroup(ele);

                            // Determine if we're the driver or owner, so we can match against the opposite.
                            var driverOrOwner, driverOrOwnerInverse, vehicleUnitNumber;
                            $.each(groupElements, function(i, ele) {
                                var fieldType = formHelpers.getFieldInfo(this).fieldParts[1];
                                if (fieldType == PERSON_TYPE) {
                                    var val = $(this).val();
                                    if (val == DRIVER || val == VEHICLE_OWNER) {
                                        driverOrOwner = val;
                                        driverOrOwnerInverse = elementHelpers.getDriverOwnerAltValue(driverOrOwner);
                                    }
                                }
                                if (fieldType == VEHICLE_UNIT_NUMBER) {
                                    vehicleUnitNumber = $(this).val();
                                }
                                if (vehicleUnitNumber && driverOrOwner) {
                                    return false;
                                }
                            });
                            if (!driverOrOwner) {
                                arguments.callee.errorMessage = 'This section is not an owner or driver.';
                                return false;
                            }
                            if (!vehicleUnitNumber) {
                                arguments.callee.errorMessage = 'This section does not have a vehicle unit number.';
                                return false;
                            }

                            // Find our alt group based on if we're the driver or owner.
                            var ownerSameAsDriverAddr = null;
                            var altGroupElements = formHelpers.filterGroups('Person', function(i, group) {
                                var result = false;
                                var isVehicleGroup = false;
                                var isOppositeType = false;
                                var isOwnerSameAsDriverAddr = false;

                                var section = elementHelpers.getSection(group);

                                group.each(function(k, ele) {
                                    var fieldType = formHelpers.getFieldInfo(ele).fieldParts[1]
                                    if (fieldType == PERSON_TYPE) {
                                        if ($(ele).val() == driverOrOwnerInverse) {
                                            isOppositeType = true;
                                        }
                                    }
                                    if (fieldType == VEHICLE_UNIT_NUMBER && $(ele).val() == vehicleUnitNumber) {
                                        isVehicleGroup = true;
                                    }
                                    /*added additional condition to make sure sameasdriveraddress checkbox with
                                      correct vehicle is processed*/
                                    if (driverOrOwnerInverse == VEHICLE_OWNER) {
                                        if (fieldType == SAME_AS_DRIVER_ADD_GUI && section == VEHICLE_OWNER && isVehicleGroup) {
                                            isOwnerSameAsDriverAddr = true;
                                            ownerSameAsDriverAddr = $(ele);
                                        }
                                        if (isOwnerSameAsDriverAddr && isVehicleGroup && isOppositeType) {
                                            result = true;
                                            return false;
                                        }
                                    } else if (isVehicleGroup && isOppositeType) {
                                        result = true;
                                        return false;
                                    }
                                });

                                return result;
                            });
                            
                            if (altGroupElements.length == 0) {
                                arguments.callee.errorMessage = 'No ' + driverOrOwnerInverse + ' section has been defined.';
                                return false;
                            }
                            //uncheck sameasdriveraddress first so we get the previous value of owner address
                            if (driverOrOwnerInverse == VEHICLE_OWNER && ownerSameAsDriverAddr != null) {
                                if (ownerSameAsDriverAddr.is(':checked')) {
                                    eCrashAddressData.uncheckSameAsDriverAddress(ownerSameAsDriverAddr, VEHICLE_OWNER, vehicleUnitNumber);
                                }
                            }

                            // We only want one, even if we filtered multiple
                            $.each(altGroupElements, function() {altGroupElements = this;return false;});
                            ele.data(ALT_GROUP_ELEMENTS_KEY, altGroupElements);

                            var altGroupKeyed = false;
                            altGroupElements.not(':hidden').each(function() {
                                if ($(this).val() != null
                                    && $.trim($(this).val()) != '') {

                                    altGroupKeyed = true;
                                    return false;
                                }
                            });

                            if (!altGroupKeyed) {
                                arguments.callee.errorMessage = 'No values have been keyed for '+driverOrOwnerInverse+' yet.';
                                return false;
                            }

                            // For convience and speed, do a one-time map of the alt group's fieldInfo
                            // to easy-to-access object keys
                            var mappedAltGroupElements = mapObjectToMultiKeys(
                                altGroupElements,
                                function(i, obj) {
                                    var fieldInfo = formHelpers.getFieldInfo(obj);
                                    return [fieldInfo.fieldParts[1]];
                                }
                            );
                            ele.data(MAPPED_ALT_GROUP_ELEMENTS_KEY, mappedAltGroupElements);
                            
                            /*add more specific to the sameAsOwner class by adding element's id to distingiush
                              if more than 1 vehicle*/
                            var personInfo = formHelpers.getPersonInfoFields();
                            groupElements
                                .bind('focusin.sameAsOwner-' + sameAsOwnerOrDriverElem.attr('id'), function() {
                                    $(this).select();
                                })
                                .each(function() {
                                    var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];

                                    var ele = $(this);
                                    var altEle = $(mappedAltGroupElements[fieldName]);
                                    
                                    if ($.inArray(fieldName, personInfo) >= 0) {
                                        ele.data('previousValue', ele.val());
                                        ele.val(altEle.val());

                                        altEle.bind('focusout.sameAsOwner-'+ sameAsOwnerOrDriverElem.attr('id'), function() {
                                            // We need to wait for any latent live bindings to complete before we do this.
                                            setTimeout(function() {
                                                ele.val(altEle.val());
                                            }, 1);
                                        });
                                    }
                                });
                            ele.data(LAST_VALID_VEHICLE_NUMBER, vehicleUnitNumber);
                            focusNextElement(eCrash.getInjuryStatusNextElement(ele));
                        } else {
                            // Undo everything we've done
                            var vehicleUnitNumber = driverAddress.uncheckSameAsOwner(ele);
                        }
                        passengerAddress.toggleSameAsOwner(ele, vehicleUnitNumber, ele.data(MAPPED_ALT_GROUP_ELEMENTS_KEY));
                    }

                    return true;
                },
                {errorMessage: ''}
            );
        }
        
        function addressSameAsOwnerOrDriver(){
            return $.extend(
                function(e) {
                    if (isEventStopped(e)) return;
                    
                    var ele = $(e.target);
                    if (e.type == 'click') {
                        var addressVars = eCrashAddressData.getAddressVars(ele);
                        var groupElements = formHelpers.getFieldInstanceGroup(ele);
                        var vehicleNumber = elementHelpers.getVehicleNumber(groupElements);
                        var eleSection = elementHelpers.getSection(groupElements);
                        var lastAddressElement = ele; //default to current checkbox
                        var elemParams = [];
                        elemParams[SELECTED_FIELDS_KEY] = formHelpers.getAddressFields();
                        elemParams[SPECIFIC_ELEMENTS_KEY] = ALT_GROUP_ELEMENTS_KEY;

                        if (ele.is(':checked')) {
                            lastAddressElement = eCrashAddressData.getLastAddressElement(ele, elemParams);
                            var retVal = eCrashAddressData.linkAddressData(ele, elemParams, eleSection, addressVars, vehicleNumber);
                            if (!retVal) {
                                return false;
                            } else {
                                ele.data(LAST_VALID_VEHICLE_NUMBER, vehicleNumber);
                            }
                        } else {
                            // Undo everything we've done
                            eCrashAddressData.unlinkAddressData(ele, elemParams, eleSection, addressVars, vehicleNumber);
                            lastAddressElement = eCrashAddressData.getLastAddressElement(ele, elemParams);      
                        }
                        focusNextElement(lastAddressElement);
                    }
                    return true;
                },
                {errorMessage: ''}
            );
        }

        /* Starts: Trailer Page Replication Feature */
        function addVechileTrailer(){
            return $.extend(
                function(e) {
                    if (isEventStopped(e)) return;

                    var ele = $(e.target);
                     if (e.type == 'click') {
                        var $section;
                        var $focusEle = $('#formContainer .form-page input:focus');
                        if ($focusEle) {
                          $section = $focusEle.closest('.formPageSection');
                        }
                        var $tabId = $focusEle.parents('div').closest('.ui-tabs-panel').attr('id');
                        if ($section) 
                        {
                          $section.find('.st-nav-add').trigger('click');
                          $section.find('a[href="#'+$tabId+'"]').trigger('click');
                          //rearrange tabs
                          var $items = $section.find('.ui-tabs-nav').children();
                          $lastItem  = $items.last();
                          //remove last item from DOM
                          $lastItem.detach();
                          //set last item after current active element
                          var $order = $('li').index($('li.ui-state-active'));
                          $items.eq($order).after($lastItem);
                          //copy data from Current vehicle to newly added trailer
                          var activeTabValues = [];
                          var $currentTabPageId = ele.parents('div').parents('div').attr('id');
                            try 
                            {
                            $( document ).ajaxComplete(function() {
                                var $nextTabId = elementHelpers.getNextTabId();
                                var $nextTabPageId = elementHelpers.getNextTabPageId($nextTabId);
                                
                                // Exclude Party,Vehicle Unit id's
                                $("#" + $currentTabPageId + " [name^=People]:not(input[id*='Person_PartyId_Hidden-t'],input[id*='Person_VehicleUnitNumber_Hidden-t'])").each(function(i) {
                                    //store in array except Party,Vehicle Unit id's
                                    activeTabValues[i] = this.value;
                                });
                                
                                $("#" + $nextTabPageId + " [name^=People]:not(input[id*='Person_PartyId_Hidden-t'],input[id*='Person_VehicleUnitNumber_Hidden-t'])").each(function(i) {
                                    //copy the values to trailer page
                                    this.value = activeTabValues[i];
                                });
                                
                                //Hide trailer button for Trailers
                                $("#" + $nextTabPageId + " :input[id^=Vehicle_AddTrailerGui]").closest('.add-trailer').hide();
                            });
                          } catch (err) {
                            console.log(err);
                          }
                        }
                      }
                                return true;
                            },
                            {errorMessage: ''}
                        );
        }

         /* Ends: Trailer Page Replication Feature */
        
        var elementHelpers = {          
            getSection : function(groupElements) {
                var elementSection = VEHICLE_OWNER;
                $.each(groupElements, function() {
                    var fieldInfo = formHelpers.getFieldInfo(this);
                    var fieldName = fieldInfo.fieldParts.join('_');

                    if (fieldName == 'Person_PersonType') {
                        elementSection = PASSENGER;
                    } else if (fieldName == 'Person_PersonType_Hidden'){
                        elementSection = ($(this).val == DRIVER) ? DRIVER : VEHICLE_OWNER;
                    }
                });
                return elementSection;
            },

            getDriverOwnerAltValue: function(value) {
                return (value == VEHICLE_OWNER) ? DRIVER : VEHICLE_OWNER;
            },

            getLastByTabIndex : function(groupElements, params) {
                var tabIndex = 0;
                var lastAddressTabIndex = 0;
                $.each(groupElements, function() {
                    var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];
                    if ((!params[SELECTED_FIELDS_KEY]) || (params[SELECTED_FIELDS_KEY] && ($.inArray(fieldName, params[SELECTED_FIELDS_KEY]) != -1))) {
                        tabIndex =  parseInt($(this).attr('tabindex')) || parseInt($(this).data('tabindex'));
                        if(tabIndex > lastAddressTabIndex){
                            lastAddressTabIndex = tabIndex;
                        }
                    }
                });
                return $(':input[tabindex="' + lastAddressTabIndex + '"]');
            },

            getFirstNoPreviousValue : function(groupElements, params) {
                var noPrevValElement = false;
                $.each(groupElements, function() {
                    var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];
                    if ((!params[SELECTED_FIELDS_KEY]) || (params[SELECTED_FIELDS_KEY] && ($.inArray(fieldName, params[SELECTED_FIELDS_KEY]) != -1))) {
                        var previousData = $(this).data('previousValue');
                        $(this).val(previousData);

                        if (previousData == null || previousData == '') {
                            noPrevValElement = $(this);
                            return false;
                        }
                    }
                });
                return noPrevValElement;
            },

            isAnyDataEntry : function(groupElements, params){
                var isAnyDataEntry = false;
                groupElements.each(function() {
                    var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];
                    if ((!params[SELECTED_FIELDS_KEY]) || (params[SELECTED_FIELDS_KEY] && ($.inArray(fieldName, params[SELECTED_FIELDS_KEY]) != -1))) {
                        if ($(this).val() != null && $.trim($(this).val()) != '') {
                            isAnyDataEntry = true;
                            return false;
                        }
                    }
                });
                return isAnyDataEntry;
            },

            //params consists of properties/attributes for binding elements
            bindGroupToElement : function(ele, params){
                var groupElements = formHelpers.getFieldInstanceGroup(ele);
                var nameSpace = (params[NAME_SPACE_KEY]!= '') ? params[NAME_SPACE_KEY] : '';
                var mappedAltGroupElements = params[MAPPED_ALT_GROUP_ELEMENTS_KEY];

                groupElements
                    .bind(params['groupBindEvent'] + nameSpace, function() {
                        $(this).select();
                    })
                    .each(function() {
                        var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];

                        var elem = $(this);
                        var sourceElement = $(mappedAltGroupElements[fieldName]);

                        if ((!params[SELECTED_FIELDS_KEY]) || (params[SELECTED_FIELDS_KEY] && ($.inArray(fieldName, params[SELECTED_FIELDS_KEY]) != -1))) {
                            elem.data('previousValue', elem.val());
                            elem.val(sourceElement.val());

                            sourceElement.bind(params['sourceBindEvent'] + nameSpace, function() {
                                // We need to wait for any latent live bindings to complete before we do this.
                                setTimeout(function() {
                                    elem.val(sourceElement.val());
                                }, 1);
                            });
                        }
                    });
            },

            //params consists of properties/attributes for binding elements
            unbindGroupFromElement : function(ele, params){
                var nameSpace = (params[NAME_SPACE_KEY]!= '') ? params[NAME_SPACE_KEY] : '';
                var mappedAltGroupElements = params[MAPPED_ALT_GROUP_ELEMENTS_KEY];

                if (ele.data(params[SPECIFIC_ELEMENTS_KEY])) {
                    ele.data(params[SPECIFIC_ELEMENTS_KEY]).unbind(nameSpace);
                }
                var groupElements = formHelpers.getFieldInstanceGroup(ele);
                groupElements
                    .unbind(nameSpace)
                    .each(function() {
                        var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];
                        var sourceElement = $(mappedAltGroupElements[fieldName]);

                        if ((!params[SELECTED_FIELDS_KEY]) || (params[SELECTED_FIELDS_KEY] && ($.inArray(fieldName, params[SELECTED_FIELDS_KEY]) != -1))) {
                            var previousData = $(this).data('previousValue');
                            $(this).val(previousData);

                            sourceElement.unbind(nameSpace);
                        }
                    });
            },

            getVehicleNumber : function(groupElements) {
                var vehicleUnitNumber = '';
                $.each(groupElements, function(i, ele) {
                    var fieldType = formHelpers.getFieldInfo(this).fieldParts[1];
                    if (fieldType == VEHICLE_UNIT_NUMBER) {
                        vehicleUnitNumber = $(this).val();
                        return false;
                    }
                });

                return vehicleUnitNumber;
            },
            
            /*if elementVal is empty and fieldName is PersonType we are only checking if vehicle number is valid by 
             * checking if the vehicle number is associated with a driver or owner*/
            getElement : function(fieldName, elementVal, vehicleNumber) {
                if ($.trim(elementVal) != '') {
                    elementVal = elementVal.toUpperCase();
                    var ownerCaps = OWNER.toUpperCase();
                    if (elementVal.indexOf(ownerCaps) !== -1) {
                        elementVal = VEHICLE_OWNER;
                    }
                }
                var referredElement = false;
                var vehicleNumberElements = $("input[id*='Person_VehicleUnitNumber_Hidden-'][value='"+ vehicleNumber +"']");
                if (vehicleNumberElements.length > 0) {
                    vehicleNumberElements.each(function() {
                        var vehicleElement = $(this);
                        var vehicleElementGroup = formHelpers.getFieldInstanceGroup(vehicleElement);
                        if (vehicleElementGroup.length > 0) {
                            vehicleElementGroup .each(function() {
                                var vehicleGroupElementName = formHelpers.getFieldInfo(this).fieldParts[1];
                                if (vehicleGroupElementName == fieldName && (elementVal == '' || elementVal == $(this).val())) {
                                    referredElement = $(this);
                                    return false;
                                }
                            });
                        }
                    });
                }
                return referredElement;
            },

            /* It returns the Next Tab element id attribute */
            getNextTabId: function() {
                return $('li.ui-state-active').next().children().attr('href');
            },

            /* It returns the Next Tab Page id attribute */
            getNextTabPageId: function(tabId) {
                return $(tabId).children().attr('id');
                
            }
        }

        var linkElemFieldsData = {
            getMappedAltGroup: function(driverOwnerFlag, vehicleNumber) {
                var driverOwnerElem = elementHelpers.getElement(PERSON_TYPE, driverOwnerFlag, vehicleNumber);
                var altGroupElements = linkElemFieldsData.getAltGroup(driverOwnerElem);
                return altGroupElements[MAPPED_ALT_GROUP_ELEMENTS_KEY];
            },

            verifyAltGroup : function(ele, altElement) {
                if (!altElement) {
                    eCrash.fieldPopup.destroy('error', ele);
                    eCrash.fieldPopup.show('error', ele, ['Vehicle Number does not match to any Vehicle Owner/Driver Vehicle \n\
                        Number in this report.']);
                    return false;
                } else {
                    eCrash.fieldPopup.destroy('error', ele);
                    var altGroupElements = linkElemFieldsData.getAltGroup(altElement);
                    return altGroupElements;
                }
            },

            getAltGroup : function(altElement) {
                var altGroupElements = [];
                altGroupElements[ALT_GROUP_ELEMENTS_KEY] = formHelpers.getFieldInstanceGroup(altElement);
                // For convience and speed, do a one-time map of the alt group's fieldInfo
                // to easy-to-access object keys
                altGroupElements[MAPPED_ALT_GROUP_ELEMENTS_KEY] = mapObjectToMultiKeys(
                    altGroupElements[ALT_GROUP_ELEMENTS_KEY],
                    function(i, obj) {
                        var fieldInfo = formHelpers.getFieldInfo(obj);
                        return [fieldInfo.fieldParts[1]];
                    }
                );
                return altGroupElements;
            },

            bindTargetToSource : function(elemPatternFilter, eleSection, ele, driverOwnerFlag, elemParams, vehicleNumber) {
                var driverOwnerElem = elementHelpers.getElement(PERSON_TYPE, driverOwnerFlag, vehicleNumber);
                var altGroupElements = linkElemFieldsData.getAltGroup(driverOwnerElem);
                elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = altGroupElements[MAPPED_ALT_GROUP_ELEMENTS_KEY];
                var bindNameSpace = elemParams[NAME_SPACE_KEY];
                elemParams = eCrashAddressData.setAddressBindParams(elemParams, bindNameSpace, altGroupElements[MAPPED_ALT_GROUP_ELEMENTS_KEY]);    

                if (eleSection == PASSENGER) {
                    ele.data(elemParams[SPECIFIC_ELEMENTS_KEY], altGroupElements[ALT_GROUP_ELEMENTS_KEY]);
                    elemParams[NAME_SPACE_KEY] = bindNameSpace + '-' + ele.attr('id');
                    elementHelpers.bindGroupToElement(ele, elemParams);
                } else {
                    var filteredPassengers = $(elemPatternFilter);
                    filteredPassengers.each(function(){
                        var elem = $(this);
                        var groupElements = formHelpers.getFieldInstanceGroup(elem);
                        var elemVehicleNumber = elementHelpers.getVehicleNumber(groupElements);
                        if (elemVehicleNumber == vehicleNumber) {
                            elem.data(elemParams[SPECIFIC_ELEMENTS_KEY], altGroupElements[ALT_GROUP_ELEMENTS_KEY]);
                            elemParams[NAME_SPACE_KEY] = bindNameSpace + '-' + elem.attr('id');
                            elementHelpers.bindGroupToElement(elem, elemParams);
                        }
                    });
                }
            },

            unbindTargetFromSource : function(elemPatternFilter, driverOwnerFlag, elemParams, vehicleNumber) {
                elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = linkElemFieldsData.getMappedAltGroup(driverOwnerFlag, vehicleNumber);
                var unbindNameSpace = elemParams[NAME_SPACE_KEY];
                var filteredPassengers = $(elemPatternFilter);

                filteredPassengers.each(function(){
                    var elem = $(this);
                    var groupElements = formHelpers.getFieldInstanceGroup(elem);
                    var elemVehicleNumber = elementHelpers.getVehicleNumber(groupElements);
                    if (elemVehicleNumber == vehicleNumber) {
                        elemParams[NAME_SPACE_KEY] = unbindNameSpace + '-' + elem.attr('id');
                        elementHelpers.unbindGroupFromElement(elem, elemParams);
                    }
                }); 
            }
        }

        var eCrashAddressData = {
            getAddressVars : function(ele) {
                var addressVars = [];
                //Get the flag from element id
                addressVars['driverOwnerFlag'] = (ele.attr('id').indexOf(OWNER) !== -1) ? OWNER : DRIVERM;
                addressVars['inverseFlag'] = (addressVars['driverOwnerFlag'] == OWNER) ? DRIVERM : OWNER;
                addressVars['addressLabel'] = 'addressSameAs' + addressVars['driverOwnerFlag'];
                addressVars['addressLabelInverse'] = 'addressSameAs' + addressVars['inverseFlag'];
                addressVars['inverseElemId'] = ele.attr('id').replace(addressVars['driverOwnerFlag'], addressVars['inverseFlag']);
                addressVars['inverseElem'] = $("input[id='" + addressVars['inverseElemId'] + "'][type='checkbox']");

                return addressVars;
            },

            getLastAddressElement : function(ele, elemParams) {
                var lastAddressElement = ele;
                var groupElements = formHelpers.getFieldInstanceGroup(ele);
                if (ele.is(':checked')) {
                    lastAddressElement = elementHelpers.getLastByTabIndex(groupElements, elemParams);
                } else {
                    var firstElemNoPrevVal = elementHelpers.getFirstNoPreviousValue(groupElements, elemParams);
                    if (firstElemNoPrevVal) {
                        //get previous element of first address element with no previous value via tab index
                        var firstElemNoPrevValTabIndex = parseInt(firstElemNoPrevVal.attr('tabindex')) || parseInt(firstElemNoPrevVal.data('tabindex'));
                        lastAddressElement = $(':input[tabindex="' + (firstElemNoPrevValTabIndex - 1) + '"]');
                    } else {
                        //get last Address element based on tab index
                        lastAddressElement = elementHelpers.getLastByTabIndex(groupElements, elemParams);
                    }
                }
                return lastAddressElement;
            },

            setAddressBindParams : function(elemParams, addressLabel, mappedAltGroupElements) {
                elemParams['groupBindEvent'] = 'focusin';
                elemParams['sourceBindEvent'] = 'focusout';
                elemParams[NAME_SPACE_KEY] = addressLabel;
                elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = mappedAltGroupElements;

                return elemParams;
            },

            isSourceAddressChecked : function(altElemValue, vehicleNumber) {
                var altCheckbox = false;
                var sourceElem = elementHelpers.getElement(PERSON_TYPE, altElemValue, vehicleNumber);
                var altGroupElements = linkElemFieldsData.getAltGroup(sourceElem);
                altGroupElements[ALT_GROUP_ELEMENTS_KEY].each(function(){
                    var elemId = $(this).attr('id');
                    if(elemId.indexOf(SAME_AS_DRIVER_ADD_GUI) !== -1 || elemId.indexOf(SAME_AS_DRIVER_GUI) !== -1) {
                        altCheckbox = $(this);
                        return false;
                    }
                });
                var elemSourceChecked = (altCheckbox.is(':checked')) ? true : false;
                return elemSourceChecked;
            },

            linkAddressData : function(ele, elemParams, eleSection, addressVars, vehicleNumber) {
                var addressLabel = '.' + addressVars['addressLabel'];
                var altElement = elementHelpers.getElement(PERSON_TYPE, addressVars['driverOwnerFlag'], vehicleNumber);
                if (eleSection == PASSENGER) {
                    if (!passengerAddress.checkVehicleNumber(ele, vehicleNumber, addressVars['driverOwnerFlag'])) {
                        return false;
                    }
                    if (addressVars['inverseElem'].is(':checked')) {
                        passengerAddress.shiftAddress(elemParams, addressVars, vehicleNumber);
                    }
                }
                else if (eleSection == VEHICLE_OWNER) { //if sameasdriveraddress in owner section is checked, uncheck sameasowwner
                    ownerAddress.checkSameAsOwner(vehicleNumber);
                }
                var altGroupElements = linkElemFieldsData.verifyAltGroup(ele, altElement);
                if (!altGroupElements) {
                    return false;
                }
                ele.data(elemParams[SPECIFIC_ELEMENTS_KEY], altGroupElements[ALT_GROUP_ELEMENTS_KEY]);

                var altGroupMapped = elementHelpers.isAnyDataEntry(altGroupElements[ALT_GROUP_ELEMENTS_KEY], elemParams);
                if (!altGroupMapped) {
                    eCrash.fieldPopup.destroy('error', ele);
                    eCrash.fieldPopup.show('error', ele, ['No address values keyed on referred ' + addressVars['driverOwnerFlag'] + ' Info tab.']);
                    return false;
                } else {
                    eCrash.fieldPopup.destroy('error', ele);
                }
                elemParams = eCrashAddressData.setAddressBindParams(elemParams, addressLabel, altGroupElements[MAPPED_ALT_GROUP_ELEMENTS_KEY]);                     
                var altCheckbox = false;
                var altElemValue = altElement.val();
                if (eleSection == PASSENGER && ((altElemValue == VEHICLE_OWNER) || (altElemValue == DRIVER))) {
                    altCheckbox = eCrashAddressData.isSourceAddressChecked(altElemValue, vehicleNumber);
                }
                if (eleSection == VEHICLE_OWNER || eleSection == DRIVER || !altCheckbox) {
                    elemParams[NAME_SPACE_KEY] = addressLabel + '-' + ele.attr('id');
                    elementHelpers.bindGroupToElement(ele, elemParams);
                }
                passengerAddress.processBind(ele, eleSection, elemParams, addressVars, vehicleNumber);

                return true;
            },

            unlinkAddressData : function(ele, elemParams, eleSection, addressVars, vehicleNumber) {
                // Undo everything we've done
                var validVN = elementHelpers.getElement(PERSON_TYPE, '', vehicleNumber);
                if (!validVN) {
                    vehicleNumber = ele.data(LAST_VALID_VEHICLE_NUMBER);
                }
                var addressLabel = '.' + addressVars['addressLabel'];
                elemParams[NAME_SPACE_KEY] = addressLabel + '-' + ele.attr('id');
                elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = linkElemFieldsData.getMappedAltGroup(addressVars['driverOwnerFlag'], vehicleNumber);
                elementHelpers.unbindGroupFromElement(ele, elemParams);
                if (eleSection == VEHICLE_OWNER || eleSection == DRIVER) {
                    passengerAddress.processUnbind(ele, eleSection, elemParams, addressVars, vehicleNumber);
                }
            },

            //unbind code when sameasdriveraddress checkbox is unchecked
            uncheckSameAsDriverAddress : function(elem, elemSection, vehicleNumber) {
                var addressVars = eCrashAddressData.getAddressVars(elem);
                var elemParams = [];
                elemParams[SELECTED_FIELDS_KEY] = formHelpers.getAddressFields();
                elemParams[SPECIFIC_ELEMENTS_KEY] = ALT_GROUP_ELEMENTS_KEY;

                //unlink (same as unchecking)
                eCrashAddressData.unlinkAddressData(elem, elemParams, elemSection, addressVars, vehicleNumber);//unbind current vehicle
                if (elem.is(':checked')) {
                    elem.prop('checked',false);
                }
            }
        }

        var ownerAddress = {
            checkSameAsOwner : function(vehicleNumber) {
                var sameAsOwnerElem = elementHelpers.getElement(SAME_AS_DRIVER_GUI, 'Y', vehicleNumber);
                if (sameAsOwnerElem && sameAsOwnerElem.is(':checked')) {
                    vehicleNumber = driverAddress.uncheckSameAsOwner(sameAsOwnerElem);
                    passengerAddress.toggleSameAsOwner(sameAsOwnerElem, vehicleNumber, sameAsOwnerElem.data(MAPPED_ALT_GROUP_ELEMENTS_KEY));
                }
            }
        }

        var driverAddress = {
            uncheckSameAsOwner : function(ele) { //moved uncheck of same as owner to a function
                if (ele.data(ALT_GROUP_ELEMENTS_KEY)) {
                    ele.data(ALT_GROUP_ELEMENTS_KEY).unbind('.sameAsOwner-' + ele.attr('id'));
                }
                var vehicleUnitNumber = '';
                formHelpers.getFieldInstanceGroup(ele)
                    .unbind('.sameAsOwner-' + ele.attr('id'))
                    .each(function() {
                        var fieldType = formHelpers.getFieldInfo(this).fieldParts[1];
                        var previousData = $(this).data('previousValue');
                        if (previousData !== null && previousData != undefined) {
                            $(this).val(previousData);
                        }
                        if (fieldType == VEHICLE_UNIT_NUMBER) {
                            vehicleUnitNumber = $(this).val();
                        }
                    });
                ele.prop('checked',false);
                return vehicleUnitNumber;
            }
        }

        var passengerAddress = {
            checkVehicleNumber : function(ele, vehicleNumber, label) {
                if($.trim(vehicleNumber) == '') {
                    eCrash.fieldPopup.destroy('error', ele);
                    eCrash.fieldPopup.show('error', ele, ['No vehicle number has been keyed for this Passenger yet. \n\
                        Vehicle Number must match with a ' + label + ' Vehicle Number in this report']);
                    return false;
                } else {
                    eCrash.fieldPopup.destroy('error', ele);
                    return true;
                }
            },

            shiftAddress : function(elemParams, addressVars, vehicleNumber) {
                elemParams[NAME_SPACE_KEY] = '.' + addressVars['addressLabelInverse']+ '-' + addressVars['inverseElem'].attr('id');
                elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = linkElemFieldsData.getMappedAltGroup(addressVars['inverseFlag'], vehicleNumber);

                elementHelpers.unbindGroupFromElement(addressVars['inverseElem'], elemParams);
                addressVars['inverseElem'].prop('checked',false);
                eCrash.fieldPopup.destroy('error', addressVars['inverseElem']);
            },

            processBind : function(ele, eleSection, elemParams, addressVars, vehicleNumber) {
                var altCheckbox = false;
                var isPassengerSource = false;
                var altElement = elementHelpers.getElement(PERSON_TYPE, addressVars['driverOwnerFlag'], vehicleNumber);
                var altElemValue = altElement.val();
                var inverseAltElemValue = elementHelpers.getDriverOwnerAltValue(altElemValue);
                //check if source checkbox in owner or driver section is checked
                if (eleSection == PASSENGER && ((altElemValue == VEHICLE_OWNER) || (altElemValue == DRIVER))) {
                    isPassengerSource = true;
                    altCheckbox = eCrashAddressData.isSourceAddressChecked(altElemValue, vehicleNumber);
                }
                if (eleSection == VEHICLE_OWNER || eleSection == DRIVER|| (isPassengerSource && altCheckbox)) {
                    if (eleSection == VEHICLE_OWNER || eleSection == DRIVER) {
                        inverseAltElemValue = elementHelpers.getDriverOwnerAltValue(eleSection);
                        elemParams[NAME_SPACE_KEY] = '.' + addressVars['addressLabelInverse'];
                    } else {
                        elemParams[NAME_SPACE_KEY] = '.' + addressVars['addressLabel'];
                    }
                    var sameAsField = (eleSection == VEHICLE_OWNER) ? SAME_AS_OWNER_ADD_GUI : SAME_AS_DRIVER_ADD_GUI;
                    var elemPatternFilter = "input[id*='"+ sameAsField +"'][type='checkbox']:checked";
                    if (eleSection == DRIVER) {
                        elemPatternFilter  = PASSENGER_SECTION + " " + elemPatternFilter;
                    }
                    linkElemFieldsData.bindTargetToSource(elemPatternFilter, eleSection, ele, inverseAltElemValue, elemParams, vehicleNumber);
                }
            },

            processUnbind : function(ele, eleSection, elemParams, addressVars, vehicleNumber) {
                var inverseSection = elementHelpers.getDriverOwnerAltValue(eleSection);
                var sameAsField = (eleSection == VEHICLE_OWNER) ? SAME_AS_OWNER_ADD_GUI : SAME_AS_DRIVER_ADD_GUI;
                var elemPatternFilter = "input[id*='" + sameAsField + "'][type='checkbox']:checked";
                if (eleSection == DRIVER) {
                    elemPatternFilter  = PASSENGER_SECTION + " " + elemPatternFilter;
                }
                elemParams[NAME_SPACE_KEY] = '.' + addressVars['addressLabelInverse'];      
                linkElemFieldsData.unbindTargetFromSource(elemPatternFilter, inverseSection, elemParams, vehicleNumber);
                linkElemFieldsData.bindTargetToSource(elemPatternFilter, eleSection, ele, eleSection, elemParams, vehicleNumber);
            },

            //bind/unbind passenger addresess to same as owner
            toggleSameAsOwner : function(elem, vehicleNumber, mappedAltGroupElements) {         
                var addressVars = [];
                addressVars['driverOwnerFlag'] = OWNER; //virtually this is the same as 'SameAsOwnerAddress'
                addressVars['inverseFlag'] = DRIVERM;
                addressVars['addressLabel'] = 'addressSameAs' + addressVars['driverOwnerFlag'];
                addressVars['addressLabelInverse'] = 'addressSameAs' + addressVars['inverseFlag'];
                var eleSection = DRIVER; //set the section as Driver since Same As Owner is in that section
                var elemParams = [];
                elemParams[SELECTED_FIELDS_KEY] = formHelpers.getAddressFields();
                elemParams[SPECIFIC_ELEMENTS_KEY] = ALT_GROUP_ELEMENTS_KEY;

                if (elem.is(':checked')) { //bind passenger sameasdriveraddress checkboxes
                    var addressLabel = '.' + addressVars['addressLabel'];
                    elemParams = eCrashAddressData.setAddressBindParams(elemParams, addressLabel, mappedAltGroupElements);                  
                    passengerAddress.processBind(elem, eleSection, elemParams, addressVars, vehicleNumber);
                } else {
                    //unbind passenger sameasdriveraddress checkboxes (same as unchecking)
                    elemParams[MAPPED_ALT_GROUP_ELEMENTS_KEY] = mappedAltGroupElements;
                    passengerAddress.processUnbind(elem, eleSection, elemParams, addressVars, vehicleNumber);
                }
            },

            mapByVehicleNumber : function(e) {
                var ele = $(e.target);
                /* tabbing, focusout, blur, saving - events triggered from passenger vehicle number textbox to 
                 * map to owner/driver vehicle number. Added keydown event to keep focus on passenger vehicle number
                 * textbox after performing a tab and vehicle number entered is invalid.
                 */
                if ((e.type == 'keydown' && e.which == 9) || e.type == 'focusout' || e.type == 'blur' || e.type == 'save') {
                    var vehicleNumber = ele.val();
                    var groupElements = formHelpers.getFieldInstanceGroup(ele);

                    eCrash.fieldPopup.destroy('error', ele);
                    var validPassVN = elementHelpers.getElement(PERSON_TYPE, '', vehicleNumber);
                    if (($.trim(vehicleNumber) != '') && !validPassVN) {
                        $.each(groupElements, function() { //empty all address fields
                            var fieldName = formHelpers.getFieldInfo(this).fieldParts[1];
                            var addressFields = formHelpers.getAddressFields();
                            if ($.inArray(fieldName, addressFields) != -1) {
                                $(this).val('');
                            }
                        });
                        
                        var popupMsg = 'Vehicle Number does not match to any Vehicle Owner/Driver Vehicle Number in this report.';
                        eCrash.fieldPopup.destroy('error', ele);
                        eCrash.fieldPopup.show('error', ele, [popupMsg]);

                        ele.focus();
                        return fullEventStop(e); //stop event
                    } else { //if vehicle number is not empty and invalid
                        var retVal = true;
                        $.each(groupElements, function() {
                            var elem = $(this);
                            var fieldType = formHelpers.getFieldInfo(this).fieldParts[1];
                            //find checked checkbox
                            if ((fieldType == SAME_AS_OWNER_ADD_GUI || fieldType == SAME_AS_DRIVER_ADD_GUI) && elem.is(':checked')) {
                                var addressVars = eCrashAddressData.getAddressVars(elem);
                                var eleSection = elementHelpers.getSection(groupElements);
                                var elemParams = [];
                                elemParams[SELECTED_FIELDS_KEY] = formHelpers.getAddressFields();
                                elemParams[SPECIFIC_ELEMENTS_KEY] = ALT_GROUP_ELEMENTS_KEY;

                                //unlink previous vehicle number (same as unchecking)
                                eCrashAddressData.unlinkAddressData(elem, elemParams, eleSection, addressVars, vehicleNumber);//unbind current vehicle

                                //link/map to new vehicle number (same as checking)
                                retVal = eCrashAddressData.linkAddressData(elem, elemParams, eleSection, addressVars, vehicleNumber);//bind new vehicle
                                if (retVal) {
                                    ele.data(LAST_VALID_VEHICLE_NUMBER, vehicleNumber);
                                }
                                //put focus on next element after address fields
                                var lastAddressElement = eCrashAddressData.getLastAddressElement(elem, elemParams);
                                focusNextElement(lastAddressElement);
                                fullEventStop(e); //stop event once mapping successfull to avoid triggering/blocking other events

                                return false;
                            }
                        });
                        return retVal;
                    }
                }
            },
            
            bindPersonFieldsForPersonTypeRequired :  function() {
                var events = ['focusout', 'blur'];
                var func = eCrash.passenger.checkEnteredData;
                var personInfo = formHelpers.getPersonInfoFields();
                $.each(personInfo, function(i, elem){
                    var elemSelector = "#formPages input[id*='Person_"+elem+"-'][type='text']";
                    formHelpers.bindElementsByEventsToFunction(events, elemSelector, func);
                });
            },
            
            checkEnteredData : function(e) {
                var ele = $(e.target);
                var fieldName = formHelpers.getFieldInfo(ele).fieldParts[1];
                var groupElements = formHelpers.getFieldInstanceGroup(ele);
                var isRequired = false;
                var isPersonTypeCheck = false;
                var isVehicleNumCheck = false;
                var fieldElem = null;
                if (fieldName != PERSON_TYPE) {
                    isPersonTypeCheck = true;
                    if ($.trim(ele.val()) != '') {
                        isRequired = true;
                    }
                } else if (fieldName == PERSON_TYPE) {
                    if (e.type == 'focusout' || e.type == 'blur') {
                        isVehicleNumCheck = true;
                        if (ele.val() == PASSENGER) {
                            isRequired = true;
                        }
                    } else if (e.type == 'focusin'){
                        isPersonTypeCheck = true;
                    }
                } 
                var personInfoFields = formHelpers.getPersonInfoFields();
                $.each(groupElements, function() {
                    var elem = $(this);
                    var fieldType = formHelpers.getFieldInfo(this).fieldParts[1];
                    if (isPersonTypeCheck) {
                        if (fieldType == PERSON_TYPE) {
                            fieldElem = elem;
                        }
                        if (($.inArray(fieldType, personInfoFields) >= 0) && ($.trim(elem.val()) != '')) {
                            isRequired = true;
                        }
                    } else if (isVehicleNumCheck) {
                        if (fieldType == VEHICLE_UNIT_NUMBER) {
                            fieldElem = elem;
                            return false;
                        }
                    }
                });
                eCrash.formHelpers.toggleRequiredFieldByFlag(fieldElem, isRequired);
            }
        }
        
        return {
            ownerAllInfo : sameAsOwnerOrDriver,
            owner : addressSameAsOwnerOrDriver,
            driver : addressSameAsOwnerOrDriver,
            passengerAddress : passengerAddress,
            trailer : addVechileTrailer,
        };
    })();
    
    eCrash.sameAsOwner = eCrash.sameAsOwnerDriver.ownerAllInfo;
    eCrash.addressSameAsDriver = eCrash.sameAsOwnerDriver.driver;
    eCrash.addressSameAsOwner = eCrash.sameAsOwnerDriver.owner;
    eCrash.passenger = eCrash.sameAsOwnerDriver.passengerAddress;
    eCrash.vehicleTrailer = eCrash.sameAsOwnerDriver.trailer;
    
    function runValidationFunctions(validationFunctions, e) {
        $.each(validationFunctions, function(k, f) {
            if (f(e) === false) {
                fullEventStop(e);
            }

            if (isEventStopped(e)) {
                return false;
            }
        });

        if (isEventStopped(e)) {
            return false;
        }

        return true;
    }

    function makeRadioButtonsUncheckable(e) {
        var ele = $(e.target);

        if (ele.data('checked')) {
            ele.removeAttr('checked');
        } else {
            $('input:radio[name="' + ele.attr('name') + '"]').data('checked', false);
        }

        ele.data('checked', ele.is(':checked'));
    }
    
    function requireNoteEntry(options) {
        options = $.extend({
            afterRender: $.noop,
            success: $.noop,
            failure: $.noop,
            beforeSave: $.noop,
            title: 'Note Required',
            prefixContent: '',
            appendContent: ''
        }, options);
        var dialogContent = $(
            '<div>' +
                options.prefixContent +
                '<textarea id="report-entry-note"></textarea><br />' +
                options.appendContent +
            '</div>'
        );

        function saveNote() {
            var noteText = $.trim($('#report-entry-note').val());
            if (noteText == '') {
                alert('You must enter a note');
                return;
            }
            if (options.beforeSave() === false) {
                return;
            }

            var loader = progressDialogManager.create().show();
            $.post(
                window.baseUrl + '/data/report-entry/notes',
                {
                    note: $('#report-entry-note').val(),
                    csrf: $('#csrf').val()
                },
                function(data) {
                    // Will log to console if it does, no other action to take though
                    hasCsrfError(data);

                    destroyDialog(true);
                    loader.hide(true);
                }
            );
        }

        function destroyDialog(success) {
            dialogContent.dialog('option', 'hide');

            try {
                if (typeof(success) != 'undefined' && success === true) {
                    options.success();
                } else {
                    options.failure();
                }
            } catch (err) {
                alert('An error occurred while processing the response. Data may not be saved successfully.');
            }

            dialogContent.dialog('destroy');
            dialogContent.remove();
        }

        dialogContent.dialog({
            title: 'Note Required',
            width: 300,
            minWidth: 300,
            height: 'auto',
            modal: true,
            buttons: {
                'Save': saveNote,
                'Cancel': destroyDialog
            }
        });
        options.afterRender();
    }

    eCrash.focusHashField = function() {
        // yes, this function seems redundant since it is almost exactly executed again in
        // setFieldFocus.js - however, if it is removed the functionality doesn't work
        // must keep it in BOTH places. I'm not even sure where/when this is called.
        var hash = parent.window.location.hash;

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

            //$(window).focus(); //-no need here, only on setFieldFocus.js
            if (hashParams.focusField) {
                var field = '#' + hashParams.focusField;
                setActivePageByElement(field);
                //$(field).focus(); //-no need here, only on setFieldFocus.js
            }
        }
    }
    
    eCrash.makeValueListsKeysUpperCase = function() {
        $.each(eCrash.valueLists, function(valueList, keyValueLists) {
            eCrash.valueListsKeysUpperCase[valueList] = [];
            $.each(keyValueLists.keys, function(i, code) {
                eCrash.valueListsKeysUpperCase[valueList].push(code.toUpperCase());
            });
        });
    }

    eCrash.formTemplates = $.extend({}, common.formTemplates);
    eCrash.windowClose = $.extend({}, common.windowClose(eCrash), eCrash.windowClose);

    $(document).on("click focusout keydown keypress keyup focusin mouseup", "#formPages input, #formPages textarea", fieldEventHooks.fieldEventHandler);
    $(document).on("click", "#formPages input:radio", makeRadioButtonsUncheckable); 
    $(document).on("focusout", "#formPages input[id*='AddressGui'][type='checkbox']", eCrash.fieldPopup.destroyByEvent);    
    $(document).on("focusout keydown blur save", "#formPages input[id^='Person_VehicleUnitNumber-t'][type='text']", eCrash.passenger.mapByVehicleNumber);
    $(document).on("focusin focusout blur", "#formPages input[id^='Person_PersonType-'][type='text']:visible", eCrash.passenger.checkEnteredData);
    
    $(function() {
        eCrash.windowClose.initialize();
        window.parent.opener.reportEntry.setAllowUnload(false);
        activatePageLayout();
        parent.common.noteWindow.displayNotesIfExist();
        eCrash.formHelpers.parseAllFields();
        prefetchNextImage();
        eCrash.focusHashField();
        eCrash.formTemplates.bind();
        
        eCrash.passenger.bindPersonFieldsForPersonTypeRequired();
        eCrash.makeValueListsKeysUpperCase();
    });

})();

