
/**
 * Reformats the standard universal form in to a tabbed 'sectional' format.
 **/
$(function(){

	cleanupUniversal();

	var readOnlyForm = ($("#readOnlyForm:hidden").val() == 1);
	activateScrollTab(createSections(getPageInfo()), readOnlyForm);

	addShortcuts(readOnlyForm);
	
	/** Delete Vehicle Bind **/
	$('#formDeleteVehicle').bind('click', formDeleteVehicle);

	function addShortcuts(readOnlyForm)
	{
		if (!readOnlyForm) {
			$(document).shortkeys({
				'Ctrl+t': function() { addNewTab(); } // Create new tab
			});
			
			/** Create Owner Drive Vehicle Delete Button **/
			var $divOwner = $(document).find("#section-page-base-owner-02");
			var $liListOwner = $divOwner.find("ul.page_sorting > li > a");
			var $deleteVehicleButton = "<a class='st-nav-delete ui-state-default ui-corner-all' name='formDeleteVehicle' id='formDeleteVehicle' title='Delete Vehicle/Party'><span class='ui-icon ui-icon-trash'></span></a>";
			if($liListOwner.length > 0) {
				$divOwner.find(".st-nav-add").before($deleteVehicleButton);
			}
		}
		
		$(document).shortkeys({
			'Ctrl+1': function() { focusTab('tabs-1'); }, // Focus page 1
			'Ctrl+2': function() { focusTab('tabs-2'); }, // Focus page 2
			'Ctrl+3': function() { focusTab('tabs-3'); }, // Focus page 3
			'Ctrl+4': function() { focusTab('tabs-4'); }, // Focus page 4
			'Ctrl+5': function() { focusTab('tabs-5'); }, // Focus page 5
			'Ctrl+6': function() { focusTab('tabs-6'); }, // Focus page 6
			'Ctrl+7': function() { focusTab('tabs-7'); }, // Focus page 7
			'Ctrl+8': function() { focusTab('tabs-8'); }, // Focus page 8
			'Ctrl+9': function() { focusTab('tabs-9'); }, // Focus page 9
			'Ctrl+Tab': function() { focusNextTab();}, // Switch to next tab
			'Ctrl+Shift+Tab': function() { focusPrevTab();}, // Switch to previous tab
			'Ctrl+Shift+1': function() { focusSection('section-page-base-incident-01'); }, // Move to section 1
			'Ctrl+Shift+2': function() { focusSection('section-page-base-owner-02'); }, // Move to section 2
			'Ctrl+Shift+3': function() { focusSection('section-page-base-passenger-03');} // Move to section 3
		});

		function focusSection(sectionId)
		{
			var $section = $('#'+sectionId);
			$('#formPages').scrollTop($section.offset().top-150);

			var $fieldArea = $section.find('div.ui-tabs-panel:not(ui-tabs-hide)');
			if ($fieldArea.length == 0) {
				$fieldArea = $section;
			}

			$fieldArea.find('input:enabled:first').focus();
		}

		function focusPrevTab()
		{
			var $section = getSection();
			if ($section.length > 0) {
				var $prevTab = $section.find('.ui-tabs-nav li.ui-state-active').prev();
				if ($prevTab.length > 0) {
					$prevTab.find('a').trigger('click');
				}
			}
		}

		function focusNextTab()
		{
			var $section = getSection();
			if ($section.length > 0) {
				var $nextTab = $section.find('.ui-tabs-nav li.ui-state-active').next();
				if ($nextTab.length > 0) {
					$nextTab.find('a').trigger('click');
				}
			}
		}

		function addNewTab()
		{
			var $section = getSection();
			if ($section) {
				$section.find('.st-nav-add').trigger('click');
			}
		}

		function focusTab(tabId)
		{
			var $section = getSection();
            var tabNumber = tabId.split('-').splice(1,1).join('-');
            var tabIndex = tabNumber -1; 
			if ($section && !eCrash.isDeletedTabByTabIndex($section,tabIndex)) {
				$section.find('a[href="#'+tabId+'"]').trigger('click');
			}
		}

		function getSection()
		{
			var $section;
			var $focusEle = $('#formContainer .form-page input:focus');
			if ($focusEle) {
				$section = $focusEle.closest('.formPageSection');
			}
			return $section
		}
	}


	function cleanupUniversal()
	{
		// Universal hides all but page 1, will re-hide with tabs
		$('.form-page').show();
		// Remove stuff from navigation panel that is now handled by the tabs.
		$('#formPageAdd, #formPageForward, #formPageBack, #formPageList').remove();

		eCrash.setActivePage = function (page, focusElement, preserveMessage) {

			var section = page.closest('.formPageSection');
			if (section.hasClass('ui-tabs')) {
                section.tabs('option', 'active', page.parent().attr('id'));
			}
			return false;
		}

		eCrash.focusHashField();
	}

	function getPageInfo()
	{
		// A page group/section will be made for each unique page name
		var pageInfo = {};
		$('input[name^="_pages"]:hidden').each(function() {

			var pageName = $(this).val();
			if (!$.isArray(pageInfo[pageName])) {
				pageInfo[pageName] = new Array();
			}

			pageInfo[pageName].push($(this));
		});
		return pageInfo;
	}

	function createSections(pageInfo)
	{
		// Create a place to put the new sections
		var $formPageSections = $('<div/>').attr('id','formPageSections');
		$('#formPages').append($formPageSections);

		// Put contents in tabbed format into the new sections
		$.each(pageInfo, function(pageName, $elements) {
                        pageName = fixedEncodeURIComponent(pageName);
			// Each section will be in its own tabsdiv
			var $thisSection = $('<div/>')
				.addClass('formPageSection')
				.attr('id','section-' + pageName);

			var $tabLabels = "";

				/** Add Class To Vehicle List **/
				if(pageName == 'page-base-owner-02'){
					$tabLabels = $("<ul class='page_sorting' />");	
				}else{
					$tabLabels = $("<ul/>");
				}
			
			$.each($elements, function(index, $element) {
                                index = fixedEncodeURIComponent(index);
				index++;
				var tabName = 'tabs-' + index;
				$tabLabels.append(
					$('<li/>')
						.append($('<a/>')
							.attr('href', '#' + tabName)
							.html(index))
				);
				$thisSection.append(
					$('<div>')
						.attr('id', tabName)
						.html($element.parent())
				);
			});
			if (pageName != 'page-base-incident-01') {
				$thisSection.prepend($tabLabels);

			}

			// Moving the section in to the section wrapper
			$formPageSections.append(
				$thisSection
			);
		});

		return $formPageSections;
	}

	function activateScrollTab($wrapperDiv, readOnlyForm)
	{
		var tabSectionOwner = $wrapperDiv.find('#section-page-base-owner-02');
		var tabsOwner = tabSectionOwner.tabs({select: pageShow});
		
		var tabSectionPassenger = $wrapperDiv.find('#section-page-base-passenger-03');
		var tabsPassenger = tabSectionPassenger.tabs({select: pageShow});
		
		tabsOwner.scrolltab({add:pageAdd, name: 'owner', disableAddButton: readOnlyForm, disableDeleteButton: true});
		tabsPassenger.scrolltab({add:pageAdd, name: 'passenger', disableAddButton: readOnlyForm, disableDeleteButton: false});
	}

	function pageShow()
	{
		eCrash.fieldPopup.destroy('error');
	}
    
	function pageAdd($newTab) {

		eCrash.fieldPopup.destroy('error');
		requestPageData(getPageName($newTab));

		// For universal form, pageAdd only ever adds the same page the section contains
		function requestPageData(pageName) {

			$.ajax({
				type: 'post',
				dataType: "json",
				url: window.baseUrl + '/data/report-entry/add-page',
				data: {
					pageName: pageName,
					csrf: $('#csrf').val()
				},
				success: addNewPage
			})
		}
        
		function addNewPage(data) {
			var pageNumber = $('input[name^="_pages"]').length;

			$newTab.html(
				$('<div id="page-' + (pageNumber) + '" class="form-page"/>')
					.html(data.pageContents[0])
					.prepend('<input type="hidden" name="_pages[' + (pageNumber) + ']" value="' + (data.baseNames[0]) + '" />')
			);
            
            eCrash.incrementCitationIndex();
            $('#page-' + pageNumber + ' .citations').find('input').each(function(key, element) {
                eCrash.resetElementAttributes(element, eCrash.getCitationIndex(), 0);
            });
            
            eCrash.setCitationPartyIdByPageNumber(pageNumber);
            
            elementnewFieldHide();
            
            $("#page-" + pageNumber + " table > tbody > tr.dynamicField").prop("style", "");
            $("#section-page-base-incident-01 table > tbody > tr.dynamicField").prop("style", "");
            
            eCrash.formHelpers.parseNewFields($('#page-' + (pageNumber)));
            $('input[type="text"]:first', '#page-' + pageNumber).focus();
            $.globalEval(data.additionalScript);
            var entryStage = $('#entryStage').val();
            if(entryStage == 'dynamic-verification') {
                $('.elem_nonprior').css("background-color", "#EFEFEF");
                $(".formPageSection table > tbody > tr.elem_nonprior").each(function(index, tr) { 
                    $(this).closest("tr").find("input,textarea").css("background-color", "#EFEFEF");
                });
            }
        }

		function getPageName($element)
		{
			// All values for _pages will be the same in a section, so its fine to just use :first
			return $element.closest('.formPageSection').find('input[name^=_pages]:first').val();
		}
	}
	
	/** Delete Vehicle Active Tab **/
	function formDeleteVehicle(e) {
		var $divOwner = $(document).find("#section-page-base-owner-02");
		var $liListOwner = $divOwner.find("ul.page_sorting > li").not(".delete-tab");
		var vehicleTabLength = $liListOwner.length;
		if(vehicleTabLength > 1){
			/* Check if has active Tab and Get Tab Number if it has */
		 	var hasActiveTab = $divOwner.find("ul.page_sorting > li").hasClass("ui-state-active");
		 	var tabToDelete = $divOwner.find("ul.page_sorting > li.ui-state-active > a").attr("href").replace("#","");
		 	var tabNumber = tabToDelete.split('-').splice(1,1).join('-');
			var $activeTab = $divOwner.find("ul.page_sorting > li.ui-state-active");
			var newDefault;
			if (!confirm('Do you want to delete Vehicle Tab #'+tabNumber+'?')){
				return false;
			} else {
                //to clear hided tab elements value
                $divOwner.find('#'+tabToDelete+' input, ' + '#'+tabToDelete+' textarea').each(function() {
                    if($(this).prop('type') == 'checkbox') {
                        $(this).prop( "checked", false );
                    }
                    else if($(this).prop('type') == 'radio') {
                        $(this).prop('checked', false);
                    }
                    else {
                        $(this).val('');
                    }
                });
				/** Hide Element instead of remove() due to encountering error with the scrolltab.js library when a tab is directly removed **/
				if(hasActiveTab){
					$divOwner.find("#"+tabToDelete).hide().addClass("delete-tab"); /* Hide Div Element */
					$activeTab.hide().addClass("delete-tab"); /* Hide LI Active Tab */
					newDefault = $divOwner.find("ul.page_sorting > li").not(".delete-tab").last().addClass("ui-tabs-active ui-state-active"); 
					newDefaultID = $divOwner.find("ul.page_sorting > li").not(".delete-tab").last().find("a").attr("href").replace("#",""); /* show Default Tab by click event  */
                    $divOwner.find('a[href="#'+newDefaultID+'"]').trigger('click');
				}
			}
		}
	}
});