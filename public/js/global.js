/**
 * @copyright (c) 2011 LexisNexis. All rights reserved.
 */
function mapObjectToMultiKeys(objs, keyer) {
	var keyedObjs = {};
	$.each(objs, function(i, obj) {
		var keyedValues = keyer(i, obj);
		if (keyedValues === false) {
			return;
		}

		var keyedValuesLength = keyedValues.length;
		var currentLevel = keyedObjs;
		$.each(keyedValues, function(k, v) {
			if (typeof(currentLevel[v]) == 'undefined') {
				if (k == keyedValuesLength - 1) {
					currentLevel[v] = [];
				} else {
					currentLevel[v] = {};
				}
			}

			currentLevel = currentLevel[v];
		});
		currentLevel.push(obj);
	});

	return keyedObjs;
}

function hasCsrfError(data) {
	
	var hasCsrfError = false;

	if (typeof data['csrferror'] != "undefined"  
			&& data['csrferror']) {
		console.error('ERROR: CSRF token mismatch!');
		hasCsrfError = true;
	}
	
	return hasCsrfError;
}
/**
 * From: http://stackoverflow.com/questions/1805808/3782959#3782959
 * Minor modifications, specifically to check for potential error conditions.
 */
function scrollIntoView(element, container) {
	element = $(element);
	container = $(container);
	if (element.length == 0 || container.length == 0) {
		return;
	}

	var containerTop = container.scrollTop();
	var containerBottom = containerTop + container.height();
	var elemTop = element[0].offsetTop;
	var elemBottom = elemTop + element.height();

	if (elemTop < containerTop) {
		container.scrollTop(elemTop);
	} else if (elemBottom > containerBottom) {
		container.scrollTop(elemBottom - container.height());
	}
}

window.progressDialogManager = (function() {
	var progressDialogs = [];

	function createNew() {
		var progressDialog;

		function create() {
			progressDialog = $(
				'<div style="width:220px; height:19px; text-align:center;">' +
					'<img src="' + window.baseUrl + '/images/ajax-loader_1.gif"/>' +
				'</div>'
			).dialog({
				modal: true,
				autoOpen: false,
				draggable: false,
				closeOnEscape: false,
				resizable: false,
				minHeight: 0,
				minWidth: 0,
				zIndex: 0
			});
			window.progressDialog = progressDialog;
			progressDialog.parents('.ui-dialog:first').find('.ui-dialog-titlebar-close').remove();

			progressDialogs.push(progressDialog);
		}

		function get() {
			if (!progressDialog) {
				create();
			}

			return progressDialog;
		}

		function show(title) {
			title = title || false;
			get().parents('.ui-dialog:first').find('.ui-dialog-titlebar')[title === false ? 'hide' : 'show']();
			get().dialog('option', 'title', title).dialog('open');
			return this;
		}

		function hide(remove) {
			if (remove) {
				get().dialog().remove();
			}
			else {
				get().dialog('close');
			}
			return this;
		}

		return {
			show: show,
			hide: hide
		}
	}

	function hideAll() {
		$.each(progressDialogs, function() {
			$(this).dialog('close');
		});
	}

	return {
		create: createNew,
		hideAll: hideAll
	};
})();
(function() {
	var inputWindow;
	var allowUnload = true;
	var alreadyOpenUrlWindow;
	var view = {
		left: (screen.availLeft) ? screen.availLeft : 0,
		top: (screen.availTop) ? screen.availTop : 0,
		width: (screen.availWidth) ? screen.availWidth : screen.width,
		height: (screen.availHeight) ? screen.availHeight : screen.height
	};
	var windowHeight;

	function setUnloadWarning(){
		allowUnload = false;
		$(window).bind('beforeunload', function(){
			if (allowUnload == false) {
				return 'If you leave this page, image entry window will be closed and information will not be saved';
			}
		});
		$(window).on("unload", function(e) {
			if (allowUnload) return;
			allowUnload = true;
			closeInputWindow();
		});
	}

	function openInputWindow(inputUrl, unloadWarning, fullscreen) {
		if (typeof(alreadyOpenUrlWindow) == 'undefined') {
			alreadyOpenUrlWindow = "blah";
		}

		if (alreadyOpenUrlWindow != inputUrl.replace(/#.*$/, "") ||
			typeof(inputWindow) == 'undefined' || inputWindow.closed) {
			if (typeof(fullscreen) == 'undefined') {
				windowHeight = view.height;
        windowWidth = view.width/2;
        windowViewLeft = windowWidth;
			} else {
				windowHeight = view.height;
        windowWidth = view.width;
        windowViewLeft = 0;
			}
			
			var windowFeatures = 'left=' + windowWidth +
				'px,top=' + windowHeight +
				',outerHeight=' + windowHeight +
				',width=' + windowWidth +
				',status=0,toolbar=0,location=0,directories=0,menubar=0,resizable=1,scrollbars=1';
			if ( typeof(inputWindow) != 'undefined' && !inputWindow.closed) {
				inputWindow.close();
			}
			inputWindow = window.open(inputUrl, 'formInput', windowFeatures);
			sizeWindow(inputWindow);
			if (typeof(unloadWarning) == 'undefined' || unloadWarning){
				setUnloadWarning();
			}
			// gotta do the 'replace' to eliminate the field to be focused
			alreadyOpenUrlWindow = inputUrl.replace(/#.*$/, "");
		}
	}

	function closeInputWindow(){
		inputWindow.close();
	}

	function getAllowUnload(){
		return allowUnload;
	}

	function setAllowUnload(paramAllowUnload){
		allowUnload = paramAllowUnload;
	}

	function sizeWindow(inputWindow) {
		$(inputWindow).ready(function() {
			inputWindow.resizeTo(windowWidth, windowHeight);
			inputWindow.moveTo(windowViewLeft,  view.top);
		});
	}

	window.reportEntry = {
		openInputWindow: openInputWindow,
		closeInputWindow: closeInputWindow,
		getAllowUnload: getAllowUnload,
		setAllowUnload: setAllowUnload
	}
})();

/*
 * Extracted from form-logic.js
 */
function createImageWindow()
{
	var imageWindow;

	function isImageWindowReady() {
		return (typeof(imageWindow) !== 'undefined'
			&& !imageWindow.closed
			&& typeof(imageWindow.imageViewer) !== 'undefined');
	}

	function openWindow(path, bindKeyCombos) {
		if (typeof(bindKeyCombos) == 'undefined') {
			bindKeyCombos = true;
		}
		var view = {
			left: (screen.availLeft) ? screen.availLeft : 0,
			top: (screen.availTop) ? screen.availTop : 0,
			width: (screen.availWidth) ? screen.availWidth : screen.width,
			height: (screen.availHeight) ? screen.availHeight : screen.height
		};
		var windowHeight = view.height;
    var windowWidth = view.width/2;
    var windowViewLeft = 0;
		var windowFeatures = 'left='+windowViewLeft+',top=0,width=' + windowWidth +
			',outerHeight=' + windowHeight +
			',status=0,toolbar=0,location=0,directories=0,menubar=0,resizable=1,scrollbars=0';
		imageWindow = window.open('', 'imageViewer', windowFeatures);
		$(imageWindow).ready(function() {
			if (typeof(imageWindow.alreadyResized) == 'undefined') {
				imageWindow.resizeTo(windowWidth, windowHeight);
				imageWindow.moveTo(windowViewLeft, view.top);
			}
			
			imageWindow.location = window.location.href.replace(window.location.pathname, window.baseUrl + path);
		});
	}

	function closeWindow() {
		if (typeof(imageWindow) !== 'undefined' && !imageWindow.closed) {
			imageWindow.close();
		}
	}

	function keyPressPass(imageViewerAction) {
		return function() {
			if (isImageWindowReady()) {
				imageWindow.imageViewer[imageViewerAction]();
			}
		}
	}

	function scrollSync(ele) {
		if (!isImageWindowReady()) return;

		var scrollPercent = 0;
		var scrollTop = 0;
		var scrollHeight = 0;

		if (typeof(ele.scrollMaxY) != 'undefined') { // IE
			scrollTop = ele.scrollTop;
		} else { // FF, Chrome
			scrollTop = $(ele).scrollTop();
		}
		scrollHeight = ele.scrollHeight - ele.clientHeight;
		scrollPercent = scrollTop / scrollHeight;

		imageWindow.imageViewer.scrollPercentage(scrollPercent);
	}

	function setSelectedImage(pageNumber) {
		if (!isImageWindowReady()) return;

		imageWindow.imageViewer.goToPage(pageNumber);
	}

	return {
		openWindow: openWindow,
		closeWindow: closeWindow,
		scrollSync: scrollSync,
		setSelectedImage: setSelectedImage,
		keyPressPass: keyPressPass
	}
}

$(function() {
	if (typeof($().dataTable) == 'undefined') {
		return;
	}

	$('table.dataTable').dataTable({
		bJQueryUI: true,
		bPaginate: false,
		bFilter: false,
		bSort: false,
		bInfo: false,
		sDom: 't'
	});
});

function closeOnOpenerChange() {
	if (!window.opener) {
		return;
	}

	var closeMe = function() {
		window.close();
	};

	if (window.opener.attachEvent) {
		window.opener.attachEvent('onunload', closeMe);
	} else if (window.opener.addEventListener) {
		window.addEventListener('unload', closeMe, false);
	}
}

function checkConcurrentUserLogin() {
	$.ajax({
		type: 'post',
		url: window.baseUrl + '/check-concurrent-user-login',
		data: {
			updateLastActivity: 'false', 
			csrf: $('#csrfToken').text()
		},
		success: function(response) {
			if (response.logout) {
				alert('Your session will be terminated due to another user login with same User ID and Password. \n' +
					  'Please contact the Administrator.');
				// if report form windows are opened we need close them
				if (!window.reportEntry.getAllowUnload()) {
					window.reportEntry.setAllowUnload(true);
					window.reportEntry.closeInputWindow();
				}
				window.location.href = window.baseUrl + "/logout";
			}
		}
	});
}

function redirectToLogin() {
	window.location = window.baseUrl + '/';
    //window.location = window.baseUrl + '/logout';
}

/**
 * Global function to handle ajax request error
 */
$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
    if (thrownError == "timeout") {
        alert('Looks like the server is taking to long to respond, please try again after sometime!');
    } else {
        alert('There was a problem while loading the contents. Please try again! (' + thrownError + ')');
    }
    
    common.utility.fullEventStop(event);
});

window.common = {};

$(document).ready(function() {
	$('#logout').click(function(){
		allowUnload = window.reportEntry.getAllowUnload();
		if (allowUnload){
			return true;
		}
		if (confirm(
			'Are you sure you want to logout? Image entry window will be closed and information will not be saved')) {
			window.logoutClicked = true;
			window.reportEntry.setAllowUnload(true);
			window.reportEntry.closeInputWindow();
		}
		return false;
	});
	
	//layout.phtml footer links - removed inline js in phtml
	$('div#cright a').click(function(){
		var url = $(this).attr('href');
		window.open(url);
		return false;
	});

    setTimeout( function() { 
      /* ECH-4917 */
      clearPasswords();
      $('input[type="password"]').each( function() {
          var theId = fixedEncodeURIComponent($(this).attr('id')); 
          if ( theId ) {
            var cmd = 'clearPassword("' + theId + '")';
            $(this).focus( function() { setTimeout( cmd, 250); } );
          }
	  });

      $('input[type="password"][class="inpTempPwd"]').each( function() {
          var theId = fixedEncodeURIComponent($(this).attr('id')); 
          if ( theId ) {
              var cmd = 'clearPassword("' + theId + '")';
              $(this).change( function() { setTimeout( cmd, 250); } );
          }
      });
    }, 250);
    
    $('.dataTable').on('xhr.dt', function(e, settings, json, xhr){
        if (hasCsrfError(json.data)) {
            common.utility.fullEventStop(e);
            // TODO: This alert will be removed in future
            alert('Token mismatch!');
            redirectToLogin();
        }
    });
});

function clearPasswords()
{
    $('input[type="password"]').val('');
}

function clearPassword(id)
{
    if ( id )
    {
        $('input[type="password"]#' + id).val('');
    }
}


function stripeTable($table) {

	$table.find('tr:visible:not(skip-stripe)').each(function(){
		$parent = $(this).closest('table');

		$parent.data('rowClassToggle', !$parent.data('rowClassToggle'));
		$(this).addClass($parent.data('rowClassToggle') ? 'even' : 'odd');
	});
}
/**
 * Redirects window according to given URL.
 * @param string url - URL to redirect
 * @param bool preserveParameters - it true then parameters that is a part of a current window URL will be passed
 *	while redirecting
 */
function redirect(url, preserveParameters) {
	var href = '';
	if (typeof(preserveParameters) == 'undefined' || preserveParameters == false) {
		href = window.baseUrl + url;
	} else {
		href = window.location.href.replace(
			window.location.pathname,
			window.baseUrl + url
		);
	}
	window.location.href = href;
}

function getHashFromUrl() {
	var hash = parent.window.location.hash;
	var hashParams = {};

	if (hash.length > 1) {
		hash = hash.substr(1);

		$.each(hash.split('&'), function(key, value) {
			var parts = value.split('=');
			if (typeof parts[1] == 'undefined') {
				parts[1] = null;
			} else {
				parts[1] = decodeURIComponent(parts[1]);
			}

			hashParams[decodeURIComponent(parts[0])] = parts[1];
		});
	}

	return hashParams;
}

function prefetchNextImage() {
	$.ajax({
		method: 'post',
		url: window.location.href.replace(window.location.pathname, window.baseUrl + '/data/report-entry/prefetch-next-image')
	});
}

/**
 * To handle the Jquery server side dataTable error.
 * @param object XMLHttpRequest object
 * @param string Error type
 * @param string Error message
 */
function dataTableError(data, textStatus, errorThrown) {
    console.error('DataTable ERROR: [' + textStatus + '] ' + errorThrown + '!');
}

/**
 * To update global csrf token value used in Ajax.
 * @param string Error type
 */
function refreshCSRF(csrfToken) {
    $('#csrfToken').text(csrfToken);
}

/*
 * More stringent version of encodeURIComponent - UTF-8 encoding of special characters
 * To be more stringent in adhering to RFC 3986 (which reserves !, ', (, ), and *), 
 * even though these characters have no formalized URI delimiting uses, the following can be safely used:
 * Reference - https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/encodeURIComponent
 * @param string str
 * @returns string Unicode-escaped string
 */
function fixedEncodeURIComponent(str) {
  return encodeURIComponent(str).replace(/[!'()*]/g, function(c) {
    return '%' + c.charCodeAt(0).toString(16);
  });
}

