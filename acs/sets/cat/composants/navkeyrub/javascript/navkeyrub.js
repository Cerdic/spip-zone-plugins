
jQuery(document).ready(
	function() {
		var shtimer = false;
		var shdelai = 400;
		function _setHover() {
			jQuery("div.cNavKeyRub li.menu-item").each(function(i, limi) {
			  jQuery("ul.hidden", limi).hide().removeClass("hidden").addClass("hidden_by_js");
			  jQuery(limi).hover(function(){
			  	window.clearTimeout(shtimer);
			  	jQuery("ul.to_close_by_js", limi).removeClass("to_close_by_js");
			  	jQuery("ul.to_close_by_js").stop(true, true).hide().removeClass("to_close_by_js");
				  jQuery("ul.hidden_by_js", limi).filter(":first").stop(true, true).show("fast");
				},function(){
						jQuery("ul.hidden_by_js", limi).addClass("to_close_by_js");
						shtimer = window.setTimeout( function() {
							jQuery("ul.to_close_by_js").stop(true, true).hide("fast").removeClass("to_close_by_js");
						}, shdelai);
				});
			});
		}
		_setHover();
	  onAjaxLoad(_setHover);
	}
);
jQuery(document).unload(function() {
	jQuery("div.cNavKeyRub li.menu-item").unbind('mouseenter mouseleave');
});
