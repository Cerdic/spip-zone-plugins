
jQuery(document).ready(
	function() {
		function _setHover() {
			jQuery(".cRubnav li.menu-item:not(.on),.cNavKeyRub li.menu-item").hover(function(){
			  jQuery("ul.hidden", this).hide().filter(":first").show("fast");
			},function(){
				jQuery("ul.hidden", this).stop(true,true).hide();
			});
		}
		_setHover();
	  onAjaxLoad(_setHover);
	}
);
jQuery(document).unload(function() {
	jQuery(".cRubnav li.menu-item:not(.on),.cNavKeyRub li.menu-item").unbind('mouseenter mouseleave');
});

