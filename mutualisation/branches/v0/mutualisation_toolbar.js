jQuery(document).ready(function($) {
	
	$('.toolbar-block .toolbar-icon').mouseover(function() {
		$(this).next('.toolbar-info').show();
	});
	$('.toolbar-block .toolbar-icon').mouseout(function() {
		$(this).next('.toolbar-info').hide();
	});

});
