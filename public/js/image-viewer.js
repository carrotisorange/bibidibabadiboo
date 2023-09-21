
(function() {
	var eCrash = {};
	var imageView, imageViewImage;
	var imageThumbnails, imageThumbnailContainer, imageThumbnailContainers, imageThumbnailImages;
	var imageHighlightClass = 'image-thumbnail-highlight';

	window.eCrash = eCrash;

	function getImageHighlight() {
		return $('.' + imageHighlightClass, imageThumbnailContainer);
	}

	function activateSelectedImage() {
		var highlightContainer = getImageHighlight();

		var highlightOffset = highlightContainer.offset();
		var parentOffset = imageThumbnailContainer.offset();
		var trueDistance = highlightOffset.top - parentOffset.top;
		var centerHighlight = trueDistance + highlightContainer.height() / 2;

		imageViewImage.attr('src', $('img', highlightContainer).attr('src'));
		imageView.scrollTop(0);

		imageThumbnails.scrollTop(trueDistance);

//        imageThumbnailContainer.animate({top: imageThumbnails.height() / 2 - centerHighlight});
	}

	function navigateThumbnail() {
		getImageHighlight().removeClass(imageHighlightClass);
		$(this).parent().addClass(imageHighlightClass);

		activateSelectedImage();
	}

	function prefetchImages() {
		var fetchDelay = 50;

		var i = 1;
		window.setTimeout(function prefetchImage() {
			var pageImage = $(imageThumbnailImages.get(i));

			if (i + 1 < eCrash.pageImages.length) {
				pageImage.one('load', function() {window.setTimeout(prefetchImage, fetchDelay);});
			}
			if (pageImage.parent('.' + imageHighlightClass).length > 0) {
				pageImage.one('load', navigateThumbnail);
			}
			pageImage.attr('src', window.baseUrl + '/' + eCrash.pageImages[i]);

			i++;
		}, fetchDelay);
	}

	function scrollImagePercent(percent) {
		var scrollHeight = imageView[0].scrollHeight - imageView[0].clientHeight;
		imageView.scrollTop(scrollHeight * percent);
	}

	function selectImage(pageNumber) {
		imageThumbnailContainers.removeClass(imageHighlightClass);
		$(imageThumbnailContainers.get(pageNumber)).addClass(imageHighlightClass);

		activateSelectedImage();
	}

	eCrash.scrollImagePercent = scrollImagePercent;
	eCrash.selectImage = selectImage;

	// Image load initialization
	$(function() {
		imageView = $('#image-view');
		imageViewImage = $('#image-view-image');
		imageThumbnails = $('#image-thumbnails');
		imageThumbnailContainer = $('#image-thumbnail-container');
		imageThumbnailContainers = $('.image-thumbnail-container', imageThumbnailContainer);
		imageThumbnailImages = $('.image-thumbnail-image', imageThumbnails)

		$(window).resize(activateSelectedImage);
		imageThumbnailImages.click(navigateThumbnail);
		activateSelectedImage();
	});

	$(window).load(function() {
		prefetchImages();
	});
})();
