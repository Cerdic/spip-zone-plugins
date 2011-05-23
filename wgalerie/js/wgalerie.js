$(document).ready(function() {
	$('a.wgalerie_img').fancybox({
		'transitionIn': 'none',
		'transitionOut': 'none',
		'titlePosition': 'over',
		'overlayOpacity': 0.75,
		'titleFormat': function(title, currentArray, currentIndex, currentOpts) {
			return '<span id="fancybox-title-over">' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
		}
	});
});