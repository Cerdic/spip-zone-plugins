function targetLinks() {
	var where;
	where="_blank";

	$("a[href*='://']:not([href^="+liens_sortants_site+"])")
	  .attr('target',where)
		.attr('rel','external')
		.addClass('external');
}
if (window.jQuery)
	(function($){
		if(typeof onAjaxLoad == "function") onAjaxLoad(targetLinks);
		$('document').ready(targetLinks);
	})(jQuery);

