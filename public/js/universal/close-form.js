(function() {
	eCrash.windowClose = (function() {
		function hasDataChanged() {
			var dataChanged = false;
			$('#formPages input:visible').each(function() {
				if ($(this).val() != this.defaultValue) {
					dataChanged = true;
					return false;
				}
			});

			return dataChanged;
		}

		return {
			hasDataChanged: hasDataChanged
		}
	})();
})()


