/**
 *jQuery.ScrollTab - Scrolling multiple tabs.
 *
 * This is a cleaned up / fixed / stripped down version of the  'ScrollableTab' plugin
 *
 * Notable differences:
 * Additions:
 * * Reduced reliance on javascript width set/checks for scrolling and used proper
 *		encapsulation & css instead.
 * * Now works with internet explorer
 * Removals:
 * * No "resizeable" implementation
 *
 * very loosely based on 'ScrollableTab' plugin
 * @copyright (c) 2010 Astun Technology Ltd - http://www.astuntechnology.com
 * Dual licensed under MIT and GPL.
 * Date: 28/04/2010
 * @author Aamir Afridi - aamirafridi(at)gmail(dot)com | http://www.aamirafridi.com
 * @version 1.0
 */
;(function($){
	//Global plugin settings
	var settings = {
		'animationSpeed' : 300, //The speed in which the tabs will animate/scroll
		'loadLastTab':false, //When tabs loaded, scroll to the last tab - default is the first tab
		'add':null
	}

	$.fn.scrolltab = function(options){

		return this.each(function(){
			var	$config = $.extend({}, settings, options); //Extend the options if any provided
			var $tabs = $(this);
			// These fields are calculated as needed
			var allViewable = false;
			var viewableArea = null;
			var tabWidth = null;
			var arrowPrev = 'st-nav-prev-' + $config.name;
			var arrowNext = 'st-nav-next-' + $config.name;


			var $tabList = $tabs.find('ul.ui-tabs-nav')
				.wrap('<div class="st-nav-wrap"/>')
				.wrap('<div class="st-tabs-nav-wrap"/>')
				.removeClass('ui-corner-all');
			var $nav = $tabs.find('div.st-nav-wrap');
			
			updateAllViewable();
			updateButtons();

			$tabs.bind('scroll', function (event, direction){
				scrollTabListPanel(direction);
			});

			// resizeComplete is from jquery.resizecomplete.js
			if ($.fn.resizeComplete) {
				$tabs.resizeComplete(function(){

					var currentViewable = allViewable;
					updateAllViewable();

					if (currentViewable != allViewable) {
						// Viewable distance has been resized
						updateButtons();
						if (allViewable) {
							scrollTabList(0);
						}
					}
				});
			}

			$tabs.tabs({
				add: function(event, ui) {

					// jquery.ui.tabs puts this in the wrong place so relocate it
					var $newTab = $tabs.find('#tabs-' + (ui.index + 1));
					$tabs.append($newTab);
					activeTab = $('div[id*=tabs-]', $tabs).filter(':not(.ui-tabs-hide)');
					$newTab.height(activeTab.height());
					showLoading($newTab);
					updateAllViewable();
                    $tabs.tabs('option', 'active', $newTab.attr('id'));
					scollTabListEnd();
					
					if ($config.add != null) {
						$config.add($newTab);
					}
				},
                activate: function (event, ui) {
                    var tabNumber = ui.newTab.index() + 1;
                    $tabs.find("#tabs-"+tabNumber).find('input[type="text"]:first').focus();
                 }
				/*
                SHow Event Renamed to Activate 
                show: function(event, ui) {
					var tabNumber = ui.index + 1;
					$(':input:enabled:first', $tabs.find('#tabs-' + tabNumber)).focus();
				}*/
			});

			$.fn.addTab = function ($tabs, tabCount) {
				$tabs.find('ul.ui-tabs-nav').append('<li><a href="#tabs-' + tabCount + '">' + tabCount + '</a></li>');
				$tabs.append("<div id='tabs-" + tabCount + "'></div>");
				$tabs.tabs("refresh");
				
				var $newTab = $tabs.find('#tabs-' + tabCount);
				$tabs.append($newTab);
				activeTab = $('div[id*=tabs-]', $tabs).filter(':not(.ui-tabs-hide)');
				$newTab.height(activeTab.height());
				showLoading($newTab);
				updateAllViewable();
				$tabs.find('ul.ui-tabs-nav li').removeClass('ui-state-active ui-tabs-active');
				$tabs.tabs("option", "active", tabCount - 1);
				scollTabListEnd();

				if ($config.add != null) {
					$config.add($newTab);
				}
			};
            $.fn.deleteTab = function ($tabs,tabCurrent) {
                var $divOtherParty = $(document).find("#section-page-base-passenger-03");
                $divOtherParty.find("#tabs-"+tabCurrent).remove();
                $nav.find('ul.ui-tabs-nav li.ui-tabs-active').hide();
                $nav.find('ul.ui-tabs-nav li.ui-tabs-active').removeClass('ui-tabs-active').addClass("delete-tab");;
                var tabLast = $nav.find('ul.ui-tabs-nav li.ui-tab:visible:last').text();
                
                if(tabLast == tabCurrent)
                {
                   tabLast = tabLast-1;
                }
                var $newTab = $tabs.find('#tabs-' + tabLast);
				activeTab = $('div[id*=tabs-]', $tabs).filter(':not(.ui-tabs-hide)');
				updateAllViewable();
				$tabs.find('ul.ui-tabs-nav li').removeClass('ui-state-active ui-tabs-active');
				$tabs.tabs("option", "active", tabLast - 1);
				scollTabListEnd();
                
            };

			// Replace contents of an element with the loading gif
			function showLoading($element)
			{
				$element.html('<img src="' + window.baseUrl + '/images/ajax-loader.gif" alt="loading..."/>');
			}

			function updateButtons()
			{
				updateArrowButtons();
				addAddButton();
                addDeleteButton();

				$tabs.find('.ui-state-default').hover(
					function(){ $(this).addClass('ui-state-hover'); },
					function(){ $(this).removeClass('ui-state-hover'); }
				);
			}

			function addAddButton()
			{
				if ($config.disableAddButton) {
					return;
				}
				// Should only ever have one, so remove others if they exist.
				$tabs.find('.st-nav-add').detach();
				$nav.prepend(
					$('<div/>')
						.addClass('st-nav-add ui-state-default ui-corner-all')
						.append($('<span/>')
							.addClass('ui-icon ui-icon-plusthick'))
							.click(function(){

								// How many tabs do we have?
								var tabCount = $nav.find('ul.ui-tabs-nav li').length;
								tabCount++;

								$("ul.ui-tabs-nav").addTab($tabs, tabCount);
							})
				);
			}


function addDeleteButton()
{

if ($config.disableDeleteButton) {
return;
} 


				// Should only ever have one, so remove others if they exist.
                $tabs.find('.st-nav-delete').detach();

                $('<a/>').addClass('st-nav-delete ui-state-default ui-corner-all')
						.append($('<span/>')
							.addClass('ui-icon ui-icon-trash'))
							.click(function(){
                                var tabCurrent = $nav.find('ul.ui-tabs-nav li.ui-tabs-active').text();
                                var tabLength = $nav.find("ul.ui-tabs-nav li").not(".delete-tab").length; 
                                if(tabLength>1)  {

                                    if (confirm('Do you want to delete Other Party Tab #'+tabCurrent+'?')) {
                                         $("ul.ui-tabs-nav").deleteTab($tabs,tabCurrent);
                                      } else {
                                        
                                      }

                                   
                                }
							})
				.insertBefore("#section-page-base-passenger-03 .st-nav-add");;	
	}

			// Attach the prev/next arrows to navigation
			function addArrows()
			{
				$nav.prepend(
					$('<div/>')
						.addClass('st-nav-button st-nav-prev ' + arrowPrev + ' ui-state-active ui-corner-tl ui-corner-bl')
						.addClass('ui-state-disabled')
						.attr('title', 'Previous Tab')
						.height($tabList.outerHeight() - 1)
						.append($('<span/>')
							.addClass('ui-icon ui-icon-carat-1-w')
							.html('Previous tab'))
						.click(function(){
							if (!$(this).hasClass('ui-state-disabled')) {
								$tabList.trigger('scroll',['prev']);
							}
							return false;
						}),
					$('<div/>')
						.addClass('st-nav-button st-nav-next ' + arrowNext + ' ui-state-active ui-corner-tr ui-corner-br')
						.attr('title', 'Next Tab')
						.height($tabList.outerHeight() - 1)
						.append($('<span/>')
							.addClass('ui-icon ui-icon-carat-1-e')
							.html('Next tab'))
						.click(function() {
							if (!$(this).hasClass('ui-state-disabled')) {
								$tabList.trigger('scroll',['next']);
							}
							return false;
						})
				);
			}

			// Scroll the tab list 1 full view
			function scrollTabListPanel(direction)
			{
				scrollTabList(getNextPageStart(direction));
				return true;
			}

			// Scroll the tab list all the way to the end (rightmost)
			function scollTabListEnd()
			{
				if (!allViewable) {

					// Farthest tab, adjust for offset, maximize viewable area
					scrollTabList(-((farthestTab() + getLocation()) - viewableArea));
				}
                

			}

			// Scroll the tab list some distance
			function scrollTabList(dist)
			{
				if ($tabList.data('scrolling'))	{
					// Don't start scrolling again if we already are or it will be a disaster
					return false;
				}

				$tabList.data('scrolling', true);
				$tabList.animate({
					marginLeft: dist
				},
				{
					duration: $config.animationSpeed,
					complete: function() {
						$tabList.data('scrolling', false);
						updateArrowButtons();
					}
				});

				return true;
			}

			// Calculates if all tabs are viewable, sets 'allTabsViewable' variable.
			// Run this only when the view might change (add, remove or resize), othwerwise use 'allTabsViewable'
			function updateAllViewable()
			{
				viewableArea = $tabList.parent().width();
				// If the right side of the last tab is < the viewable area everything is viewable
				allViewable = farthestTab() < viewableArea;
			}

			// Finds the distance to the end (right side) of the last tab
			function farthestTab()
			{
				var lastTab = $tabList.children('li:last');
				tabWidth = lastTab.width();

				var farthest = lastTab.offset().left
					+ lastTab.width()
					+ tabWidth;

				return farthest;
			}

			// Get the position to move to to view the next page/viewable tab area
			function getNextPageStart(direction)
			{
				var distanceMax = (direction == 'prev')
					? 0
					: farthestTab($tabList);

				var distance;
				if (direction == 'prev') {
					distance = parseInt($tabList.css('margin-left')) + viewableArea;
				}
				else if (direction == 'next') {
					// tabWidth/2 accounts for tabs that might have been cut off >= 50% on the right in the last view
					distance = viewableArea - parseInt($tabList.css('margin-left')) - (tabWidth / 2);
				}
				else {
					return false;
				}

				if (distance > distanceMax) {
					distance = distanceMax;
				}

				if (distance != distanceMax || direction != 'prev') {
					// Make sure the leftmost tab isn't cut off.
					distance = adjustTab(distance, $tabList);
				}

				return distance;
			}

			// Disable/enable arrows as needed
			function updateArrowButtons()
			{
				if (!allViewable) {
					$buttons = $nav.find('.st-nav-button');
					if ($buttons.length == 0) {
						addArrows();
						addAddButton()
					}

					var location = getLocation();
					$buttons.removeClass('ui-state-disabled');

					if (location == 0) {
						$nav.find('.' + arrowPrev).addClass('ui-state-disabled');
					}
					else if (location >= farthestTab()) {
						$nav.find('.' + arrowNext).addClass('ui-state-disabled');
					}
				}
				else
				{
					// If all are viewable save some space and remove buttons
					$('.' + arrowPrev + ', .' + arrowNext).detach();
				}
			}

			// Gets the current location of the view pane (margin-left offset)
			function getLocation()
			{
				return Math.abs(parseInt($tabList.css('margin-left')));
			}

			// Adjust the view so that we don't view a partial tab on the left
			function adjustTab(position, $element)
			{
				var leftmost = position;
				var leftwidth = 0;
				var location = getLocation();
				$element.children('li').each(function() {

					var myLeft = $(this).offset().left + location - $(this).width();
					var myRight = myLeft + $(this).width();

					// Find the div that is interrupted
					if (myLeft < Math.abs(position)
						&& myRight > Math.abs(position)) {

						leftmost = myLeft;
						leftwidth = $(this).width();
						return;
					}
				});

				return -(leftmost - leftwidth);
			}
		});
	}
})(jQuery);

