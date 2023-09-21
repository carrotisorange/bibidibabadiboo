/**
 * To adjust the form page content top position based on header content
 */
function adjustFormPageTop() {
    $('#formPages').css('top', $('.formPages-header').css('height'));
}

$(document).ready(function() {
    cleanupUniversal();
    
    // Will be trigger when the iframe content is ready to adjust the form content position
    $('#formIframe').ready(function(){
        adjustFormPageTop();
        eCrash.setNewVehicleNumber();
    });
    
    $('tr:has(.required)').addClass('requiredRow');
    $(document).on('click',"#element16_field",function(){
        elementnewFieldHide();
    });
    
    // Uncheck radio on double click
    $(document).on('dblclick', '.uncheck-radio input[type="radio"]', unCheckRadio);
    
    // Global variable to keep the incremented value of citation index.
    eCrash.lastCitationIndex = 1;

    eCrash.citation = 'CITATION';
    eCrash.violation = 'VIOLATION';
    
    eCrash.resetElementAttributes = function(element, itemIndex, lastTabIndex) {
        // To change the first matcing digit alone.
        var elementName = $(element).prop('name').replace(/\d+/, itemIndex);
        var elementId = $(element).prop('id').replace(/\d+/, parseInt(itemIndex) + 1);
        elementId = elementId.replace(/\//g, '-');
        $(element).prop('name', elementName);
        $(element).prop('id', elementId);
        
        if ($(element).prop('type') != 'hidden') {
            // To focus the cursor in order while pressing tab key
            if (lastTabIndex != 0) {
                $(element).prop('tabindex', lastTabIndex);
            }
            
            if ($(element).prop('type') == 'radio' || $(element).prop('type') == 'checkbox') {
                if (eCrash.getPreviousValueByElementName(elementName) == $(element).val()) {
                    $(element).prop('checked', true);
                } else if ($(element).val() == eCrash.citation || $(element).val() == eCrash.violation) {
                    if (typeof eCrash.data.dynamicVerification.previousValues !== 'undefined'
                        || typeof eCrash.data.previousValues !== 'undefined') {
                        var $itemContainer = $(element).closest('.item-content');
                        var checkCitationOrViolation = eCrash.getCitationViolationByElementName(itemIndex);
                        
                        $(element).closest('.item-content').find('.citation_or_violation input[type="radio"]').prop('checked', false);
                        if (checkCitationOrViolation == eCrash.citation) {
                            $(element).closest('.item-content').find('input[type="radio"][value="' + eCrash.citation + '"]').prop('checked', true);
                            eCrash.hideViolationLayout($itemContainer);
                        } else {
                            $(element).closest('.item-content').find('input[type="radio"][value="' + eCrash.violation + '"]').prop('checked', true);
                            eCrash.hideCitationLayout($itemContainer);
                        }
                    } else {
                        $(element).closest('.item-content').find('input[type="radio"][value="' + eCrash.citation + '"]').prop('checked', true);
                    }
                } else {
                    $(element).prop('checked', false);
                }
            } else {
                $(element).val(eCrash.getPreviousValueByElementName(elementName));
            }
        } else if (eCrash.getPreviousValueByElementName(elementName) != '') {
            // To set the previous value for Party_Id hidden field.
            $(element).val(eCrash.getPreviousValueByElementName(elementName));
        }
    }
    
    eCrash.getPreviousValueByElementName = function(elementName) {
        if (typeof eCrash.data.previousValues !== 'undefined'
            && typeof eCrash.data.previousValues[elementName] !== 'undefined') {
            return eCrash.data.previousValues[elementName];
        }
        
        return '';
    }

    eCrash.getCitationViolationByElementName = function(itemIndex) {
        var citationOrViolationElementName = 'Citations[' + itemIndex + '][Citation_Or_Violation]';

        if (typeof eCrash.data.dynamicVerification.previousValues !== 'undefined'
            && typeof eCrash.data.dynamicVerification.previousValues[citationOrViolationElementName] !== 'undefined') {            
            
            return eCrash.data.dynamicVerification.previousValues[citationOrViolationElementName];
        } else if (typeof eCrash.data.previousValues !== 'undefined'
            && typeof eCrash.data.previousValues[citationOrViolationElementName] !== 'undefined') {            
            
            return eCrash.data.previousValues[citationOrViolationElementName];
        }
    }
    
    eCrash.addCitation = function(e) {
        var $itemContainer = $(this).closest('.form-page').find('.citations');
        var $item = $itemContainer.find('.item-content:first').clone();
        
        // Last field tab index in the citation section of current persion
        var lastTabIndex = $item.find('input:not([type="hidden"]):last').prop('tabindex');
        
        eCrash.incrementCitationIndex();
        var citationIndex;
        if ($itemContainer.data('citationIndex') !== undefined) {
            citationIndex = $itemContainer.data('citationIndex');
        } else {
            citationIndex = eCrash.getCitationIndex();
        }
        
        $item.find('input').each(function(key, element) {
            eCrash.resetElementAttributes(element, citationIndex, lastTabIndex);

            eCrash.setCitationDropdownElement(element, citationIndex);
        });      
        
        // Show Delete icon after 1st row
        $item.find('.delete-item').removeClass('hide');
        
        // Hide Add icon after 1st row
        $item.find('.add-item').addClass('hide');
        
        // Add New Citation to Citation Section
        $itemContainer.append($item);
        
        // First field auto focus on the newly added element
        $itemContainer.find('.item-content:last').find('input:not([type="hidden"]):first').focus();
    }
    
    /**
     * To increment citation index
     */
    eCrash.incrementCitationIndex = function() {
        eCrash.lastCitationIndex += 1;
    }
    
    eCrash.getCitationIndex = function() {
        return eCrash.lastCitationIndex;
    }
    
    // Apply the driver party id to citation party id
    eCrash.setCitationPartyIdByPageNumber = function(pageNumber) {
        var peoplePartyId = $('#page-' + pageNumber).find('input[name^=People][name$="Party_Id]').last().val();
        // To set driver party id to citation party ids.
        $('#page-' + pageNumber + ' .citations').find('input[name^=Citations][name$="Party_Id]').each(function(key, element) {
            $(element).val(peoplePartyId);
        });
    }
    
    eCrash.deleteCitation = function(e) {
        var $itemContainer = $(this).closest('.form-page').find('.citations');
        if ($itemContainer.find('.item-content').length > 1) {
            $(this).closest('.item-content').remove();
        }
        
        // Set auto focus to the first field of last citation details group
        $itemContainer.find('.item-content:last').find('input:not([type="hidden"]):first').focus();
    }

    eCrash.changeCitationLayout = function(e) {
        var $itemContainer = $(this).closest('.item-content');
        
        if (e.target.value == eCrash.violation) {
            $itemContainer.find('input[type="radio"][value="' + eCrash.citation + '"]').prop('checked', false);
            eCrash.hideCitationLayout($itemContainer);
        } else {
            $itemContainer.find('input[type="radio"][value="' + eCrash.violation + '"]').prop('checked', false);
            eCrash.hideViolationLayout($itemContainer);
        }
    }

    eCrash.hideCitationLayout = function($itemContainer) {
        $itemContainer.find('.pa-citation').addClass('hide');
        $itemContainer.find('.pa-violation').removeClass('hide');
        $itemContainer.find('.pa-citation input[type="text"]').val('');
    }

    eCrash.hideViolationLayout = function($itemContainer) {
        $itemContainer.find('.pa-violation').addClass('hide');
        $itemContainer.find('.pa-citation').removeClass('hide');
        $itemContainer.find('.pa-violation input[type="text"]').val('');     
    }
    
    /**
     * To add and set citation details in pass 2 entry stage
     */
    eCrash.setCitationDetails = function() {
        var arrayPartyID = [];
        var previousValues;
        if (eCrash.data.previousValues) {
            previousValues = eCrash.data.previousValues;
        } else if (eCrash.data.dynamicVerification.previousValues) {
            previousValues = eCrash.data.dynamicVerification.previousValues;
        } else {
            previousValues = {};
        }
        
        $.each(previousValues, function(index, value) {
            if ((index.indexOf('Citations') !== -1) && (index.indexOf('Party_Id') !== -1)) {
                // Will match only the index of Party id in citations i.e Citations[\d+][Party_Id]
                var tempCitationIndex = index.split('[')[1];
                var citationIndex = tempCitationIndex.split(']')[0];
                var currentPage;
                // Iterate all the rendered persons to find the correct person
                // @TODO: Try to avoid the each function by improving the jQuery selector
                $('input[name^=People][name$="Party_Id]').each(function(i, element) {
                    if (value == $(element).val()) {
                        currentPage = $(element).closest('.form-page');
                    }
                });
                
                if (typeof(currentPage) !== 'undefined') {
                    var $itemContainer = $(currentPage).find('.citations');
                    // citationIndex will be used as the index of citation details to be added
                    $itemContainer.data('citationIndex', citationIndex);
                    $itemContainer.closest('.form-page').find('.add-item:not(.hide)').trigger('click');
                    $itemContainer.removeData('citationIndex').removeAttr('citationIndex');
                    
                    if ($.inArray(value, arrayPartyID) === -1) {
                        // To skip the citation details addition at first time per person
                        arrayPartyID.push(value);
                        
                        // Remove the initially added blank citation/violation details
                        $itemContainer.find('.item-content:first').remove();
                        $itemContainer.find('.delete-item').addClass('hide');
                        $itemContainer.find('.add-item').removeClass('hide');
                    }
                }
            }
        });
    }
    
    eCrash.isDeletedTabByElementName = function(element) {
        return $(element).closest('.ui-tabs-panel').hasClass('delete-tab');
    }
    
    eCrash.isDeletedTabByTabIndex = function($section, tabIndex) {
        return $section.find('ul.ui-tabs-nav > li:eq("' + tabIndex + '")').hasClass('delete-tab');
    }
    
    eCrash.getInjuryStatusNextElement = function(element) {
        return $(element).closest('.form-page').find('input[id*="Person_InjuryStatus-t"][type="text"]').last();
    }
    
    //Show/Hide Table rows based On selectio of element values
    eCrash.displayChildRows = function(ele) {
            if($(ele).closest('tr').hasClass('hasChild')) {
                $section = $(ele).closest('.formPageSection');
                
                var elementName = ele.attr('name');
                var codeMapName = elementName.slice(elementName.lastIndexOf("[") + 1, elementName.lastIndexOf("]"));
                var value = $.trim(ele.val());
                var lastChar = value.slice(-1);
                if (lastChar == ';') {
                    value = value.slice(0, -1);
                }
                 value = value.toUpperCase();
                
                var valueArray = value.split(";");
                var codeMapNameCodes = eCrash.valueLists[codeMapName];
                
                for(var i = 0; i < codeMapNameCodes.keys.length; i++){
                    var keyName = codeMapNameCodes.keys[i];
                    var className = codeMapNameCodes.class_name[codeMapNameCodes.keys.indexOf(keyName)];
                    
                    if($.inArray(keyName.toUpperCase(), valueArray) != -1) {
                        $section.find('.' + className).removeClass('hide').addClass("show");
                    } else {
                        $section.find('.' + className).removeClass('show').addClass("hide");
                        $section.find('.' + className).find('input:text').val("");
                    }
                }
            }
    }
    
    eCrash.setCitationDropdownElement = function(element, citationIndex) {
        if ($(element).parent().hasClass('citation-dropdown') && $(element).is('input:text')) {
            var elementName = $(element).attr('name').split('[').pop().replace(']', '');
            if (typeof(eCrash.fields) !== 'undefined') {
                // Copy the validation function from existing element to the dynamically added new element.
                eCrash.fields['Citations[' + citationIndex + '][' + elementName + ']'] = eCrash.fields['Citations[0][' + elementName + ']'];
            }
        }
    }
    
    /**
     * Citation array index value is not been set properly if the default values are set from the FormModifier.
     * So that need to Re-order the citation index.
     */
    eCrash.setCitationsOrderByArrayIndex = function(e) {
        $('.citations').find('.item-content').each(function(key, value) {
            $(this).find('input, textarea').each(function(index, element) {
                $(element).prop('name', $(element).prop('name').replace(/\d+/, key));
                $(element).prop('id', $(element).prop('id').replace(/\d+/, key));
            });
        });
    }
    
    $(document).on('click', '.add-item', eCrash.addCitation);
    $(document).on('click', '.delete-item', eCrash.deleteCitation);
    $(document).on('click', '.citation_or_violation input[type="radio"]', eCrash.changeCitationLayout);
    
    $(document).on('click', "#formClear", function() {
        var auto_keyed = $('#hasAutoKeyed').val();
        var inputTypes = ['button', 'submit', 'reset', 'hidden'];
        
        if (auto_keyed == 1) {
            $('#hasAutoKeyed').val(0);
        }
        
        $('#formPageSections input').each(function() {
            if ($.inArray($(this).prop('type'), inputTypes) == -1) {
                if($(this).prop('type') == 'checkbox') {
                    $(this).prop( "checked", false );
                }
                else if($(this).prop('type') == 'radio') {
                    $(this).prop('checked', false);
                }
                else {
                    $(this).val('');
                }
            }
        });
        
        $('#formPageSections textarea').each(function() {
            $(this).val('');
        });
    });
    
    //check auto extraction
    if ($('#hasAutoExtracted').val() == 0 && $('#showAutoExtractionAlert').val() == 1 && $('#entryStage').val() == 'all') {
        alert('Auto Extraction process has not completed');
    }
});

function elementnewFieldHide(){
    if ($("#element16_field").prop('checked')) {
        $('.element16_field:not(.dynamicField)').removeClass('hide');
        $('.dynamicField').prop('style', '');
    } else {
        $('.element16_field').addClass('hide');
        $('.element16_field input').each(function(){
            $(this).val('');
        });
        
        $('.citations .delete-item:not(.hide)').each(function(){
            $(this).trigger('click');
        });
    }
}

function cleanupUniversal()
{
    // Remove stuff from navigation panel that is now handled by the tabs.
    $('#formPageAdd, #formPageForward, #formPageBack, #formPageList').remove();
}

function openTab(ele)
{
    if($(ele).closest('.ui-tabs-panel').attr('id') != undefined) {
        $( $(ele).closest('.ui-tabs-panel').parent().find('ul').find('li') ).each(function( index , value) {
            if('#'+$(ele).closest('.ui-tabs-panel').attr('id') == $( value ).find('a').attr('href')) {
                $( value ).find('a').trigger('click');
                return;
            }
        });
    }
}

// Added function to unselect radio button
function unCheckRadio() {
    if($(this).prop('checked')){
        $(this).prop('checked', false);
    }
}
