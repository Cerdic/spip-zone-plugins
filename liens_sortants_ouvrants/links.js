function targetLinks() {
	var where;
	where="_blank";

	$("a[href*='://']:not([href^="+liens_sortants_site+"])")
	  .attr('target',where)
		.attr('rel','external')
		.addClass('external')
		.each(function(){
			title =  "(nouvelle fenêtre)";
			if($(this).attr("title")) title = $(this).attr("title") + " (nouvelle fenêtre)";
			$(this).attr("title",title);
		});
}
if (window.jQuery)
	(function($){
		if(typeof onAjaxLoad == "function") onAjaxLoad(targetLinks);
		$('document').ready(targetLinks);
	})(jQuery);