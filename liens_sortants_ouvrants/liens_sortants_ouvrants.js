function targetLinks() {
	var where;
	where="_blank";

	$("a[href*='://']:not([href^="+liens_sortants_site+"])")
	  .attr('target',where)
		.attr('rel','external')
		.addClass('external')
		.each(function(){
			title =  "(nouvelle fenetre)";
			if($(this).attr("title")) title = $(this).attr("title") + " (nouvelle fenetre)";
			$(this).attr("title",title);
		});
}
if (window.jQuery)
	(function($){
		if(typeof onAjaxLoad == "function") onAjaxLoad(targetLinks);
		$('document').ready(targetLinks);
	})(jQuery);