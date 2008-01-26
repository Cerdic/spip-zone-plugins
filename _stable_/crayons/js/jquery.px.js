(function($){
	$.fn.px = function(prop) {
		var val;
		if($.browser.msie) {
			$('<span>Mi<\/span>')
			.appendTo(this[0])
			.each(function(){
				val = parseInt($(this).width()) + 'px';
			})
			.remove();
		} else {
			val = this.css(prop);
		}
		return val;
	};

})(jQuery);
