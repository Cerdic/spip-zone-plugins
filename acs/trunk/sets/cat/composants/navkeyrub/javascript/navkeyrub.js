
jQuery(document).ready(
	function() {
		var shtimer = false;
		function initNKR() {
			jQuery("div.cNavKeyRub").each(function(i, nkr) {
				var c = jQuery(nkr).attr("class");
				var r = new RegExp(".*cNKRtimer([0-9]+).*", "g");
				var d = 400;
				if (c.match(r))
					d = c.replace(r, "$1");
				jQuery("li.menu-item", nkr).each(function(i, limi) {
				  jQuery("ul.hidden", limi).hide().removeClass("hidden").addClass("hidden_by_js");
				  jQuery(limi).hover(function(){
				  	window.clearTimeout(shtimer);
				  	jQuery("ul.to_close_by_js", limi).removeClass("to_close_by_js").parents("ul.to_close_by_js").removeClass("to_close_by_js");
				  	jQuery("ul.to_close_by_js").stop(true, true).hide().removeClass("to_close_by_js");
					  jQuery("ul.hidden_by_js", limi).filter(":first").stop(true, true).show(d);
					},function(){
							jQuery("ul.hidden_by_js", limi).addClass("to_close_by_js");
							shtimer = window.setTimeout( function() {
								jQuery("ul.to_close_by_js").stop(true, true).hide(d, function() {
									jQuery(this).removeClass("to_close_by_js");
								});
							}, d);
					});
				});
			});
		}
		initNKR();
	  onAjaxLoad(initNKR);
	}
);
jQuery(document).unload(function() {
	jQuery("div.cNavKeyRub li.menu-item").unbind('mouseenter mouseleave');
});
