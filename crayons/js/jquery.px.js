// Ce plugin permet de recuperer css(fontSize) en px meme sous MSIE
(function($){
	$.fn.px = function(prop) {
		var val;
		if($.browser.msie) {
			$('<span><\/span>')
			.css({display: 'block', width: '1em'})
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
