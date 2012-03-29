function padd_create_slideshow() {
	jQuery('div#slideshow-box').append('<div id="slideshow-controller"><span id="jqc-pages"></span></div>');
	len = jQuery('div#slideshow-box .item').length;
	jQuery('div#slideshow-box .dir-button-l').css('z-index', len + 2);
	jQuery('div#slideshow-box .dir-button-r').css('z-index', len + 3);
	jQuery('div#slideshow-box .dir-button').hide();
	jQuery('div#slideshow-box div.list').cycle({
		fx                : 'fade',
		speed             : 1000,
		timeout           : 3000,
		cleartypeNoBg     : true,
		activePagerClass  : 'jqc-active',
		pager             : '#jqc-pages',
		pause             : true,
		pagerAnchorBuilder: function (index,elem) {
			return '<button class="jqc-button jqc-button-pages" id="jqc-button-' + index + '" value="' + index + '"><span>' + (index+1) + '</span></button>';
		}
 	});
	jQuery('div#slideshow-box').hover(
		function() {
			jQuery('div#slideshow-box .dir-button').fadeIn(250);
		},
		function() {
			jQuery('div#slideshow-box .dir-button').fadeOut(250);
		}
	);
}



jQuery(document).ready(function() {
	jQuery.noConflict();


	padd_create_slideshow();
	
	jQuery('#intro > div > ul').superfish({
		autoArrows: true,
		hoverClass: 'hover',
		speed     : 500,
		delay     : 0,
		animation : {
			opacity: 'show',
			height : 'show'
		}
	});
	
	jQuery("#carousel").jCarouselLite({
		btnNext: ".next",
		btnPrev: ".prev",
		visible: 4,
		circular: false
	});
	
});