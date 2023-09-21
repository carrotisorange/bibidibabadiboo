/**
 * This plugin can be found at: http://rikrikrik.com/jquery/shortkeys/
 *
 * This has been custom-modified to:
 *     Allow shortcuts even on input elements.
 *     Encapsulate jQuery/$ usage.
 *     Formatting
 *     Adding extra 'specialKeys' character mappings.
 *     Allow the $.shortkeys() to be called multiple times. (Near complete re-organization/re-write)
 */

(function($) {
	var initialized = false;

	var specialKeys = {
		"backspace":8,"tab":9,"return":13,"shift":16,"ctrl":17,"alt":18,"pause":19,"capslock":20,
		"esc":27,"space":32,"pageup":33,"pagedown":34,"end":35,"home":36,"left":37,"up":38,
		"right":39,"down":40,"insert":45,"del":46,';':59,"0":96,"1":49,"2":50,"3":51,"4":52,
		"5":53,"6":54,"7":55,"8":56,"9":57,"*":106,"+":107,"-":109,".":110,',':188,"/":191,
		"f1":112,"f2":113,"f3":114,"f4":115,"f5":116,"f6":117,"f7":118,"f8":119,"f9":120,"f10":121,
		"f11":122,"f12":123,"numlock":144,"scroll":145,"meta":224
	};
	var keys = [];
	var callbacks = [];
	var keysDown = [];
	var split = '+';

	function addNewKeyCombos(newKeyCombos) {
		$.each(newKeyCombos, function(keyCombo, callback) {
			var splitKeys = keyCombo.split(split);
			var quickArr = [];

			$.each(splitKeys, function(index, value) {
				quickArr.push(convertToNumbers(value));
			});
			quickArr.sort();

			keys.push(quickArr);
			callbacks.push(callback);
		});
	}

	function convertToNumbers(input) {
		input = input.toUpperCase();
		if (specialKeys[input] != undefined) {
			return specialKeys[input];
		}

		return input.toUpperCase().charCodeAt(0);
	}

	function keyAdd(keyCode) {
		if ($.inArray(keyCode, keysDown) === -1) {
			keysDown.push(keyCode);
			keysDown.sort();
		}
	}

	function keyRemove(keyCode) {
		for (var i in keysDown) {
			if (keysDown[i] == keyCode) {
				keysDown.splice(i, 1);
			}
		}

		keysDown.sort();
	}

	function keyTest(keyCombo) {
		if (keyCombo.length != keysDown.length) return false;

		for (var j in keyCombo) {
			if (keyCombo[j] != keysDown[j]) {
				return false;
			}
		}

		return true;
	}

	function keyRemoveAll() {
		keysDown = [];
	}

	function initialize() {
		if (initialized) return;

		for (var x in specialKeys) {
			specialKeys[x.toUpperCase()] = specialKeys[x];
		}

		$(document)
			.unbind('.shortkeys')
			.bind('keydown.shortkeys', function(e) {
				keyAdd(e.keyCode);

				$.each(keys, function(x, keyCombo) {
					if (!keyTest(keyCombo))	return true;

					e.preventDefault();
					e.stopImmediatePropagation();
					e.originalEvent.returnValue = false;

					if (typeof(console) != 'undefined') console.log('called!');
					callbacks[x]();

					return false;
				});
			})
			.bind('keyup.shortkeys', function(e) {
				keyRemove(e.keyCode);
			});
		$(window)
			.unbind('.shortkeys')
			.bind('focus.shortkeys blur.shortkeys', function() {
				keyRemoveAll();
			})

		initialized = true;
	}

	$.fn.shortkeys = function(newKeyCombos, settings) {
		initialize();

		addNewKeyCombos(newKeyCombos);

		return this;
	}
})(jQuery);
