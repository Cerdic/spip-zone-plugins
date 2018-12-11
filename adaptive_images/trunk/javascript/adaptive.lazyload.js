$(function() { (function($){
	if ($('html.lazy').length){
		// TODO : optimizations
		var options = {
			threshold       : 2*$(window).height(),
			failure_limit   : 0,
			appear: function () {
				$(this).removeClass('lazy');
				this.loaded = true;
			}
		};
		// move .lazy flag to .adapt-img-wrapper and launch lazyload
		$('.adapt-img-wrapper').addClass('lazy').lazyload(options);
		$('html').removeClass('lazy');
	}

})(jQuery); });
