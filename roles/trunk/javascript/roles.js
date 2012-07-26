;(function ($) {
jQuery(document).ready(function(){
	spip_chosen = function() {
		$("select.selection_roles").chosen();
	}
	spip_chosen();
	onAjaxLoad(spip_chosen);
});
})(jQuery);
