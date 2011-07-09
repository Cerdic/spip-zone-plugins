
jQuery(document).ready(
	function() {
		function _setHover() {
			jQuery(".cRubnav li.menu-item:not(.on)").hover(function(){
			  jQuery("ul.hidden", this).hide().filter(":first").show("fast");
			},function(){
				jQuery("ul.hidden", this).hide();
			});
		}
		_setHover();
	  onAjaxLoad(_setHover);
	}
);
jQuery(document).unload(function() {
	jQuery(".cRubnav li.menu-item:not(.on)").unbind('mouseenter mouseleave');
});

