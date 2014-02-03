jQuery(function(){
	if (jQuery('html.lazy').length){

		function adaptive_image_show(){
			console.log('show');
			console.log(this);
			jQuery(this).removeClass('lazy');
			this.loaded = true;
		}

		// TODO : optimizations
		var options = {
			threshold       : 0,
			failure_limit   : 0,
			appear:adaptive_image_show
		};

		// move .lazy flag to .adapt-img-wrapper and launch lazyload
		jQuery('.adapt-img-wrapper').addClass('lazy').lazyload(options);
		jQuery('html').removeClass('lazy');
	}

});
