/*
 * Function pour les liens externes uniquement
 */
function targetLinks() {
	var where;
	where="_blank";

	jQuery("area[href*='://']").add("a[href*='://']").filter(":not([href^='"+links_site+"']):not([href^='javascript:']):not([href^='mailto:'])")
	  .attr('target',where)
		.attr('rel','external noopener noreferrer')
		.addClass('external')
		.each(function(){
			if(jQuery(this).text()){
				title =  jQuery(this).text().trim().replace(/\r/,'').replace(/\t/,'').replace(/\n/g,'') + " " + js_nouvelle_fenetre;
			}else{
				title = " " + js_nouvelle_fenetre;
			}
			if(jQuery(this).attr("title")) title = jQuery(this).attr("title") + " " + js_nouvelle_fenetre;
			jQuery(this).attr("title",title);
	});
}

/*
 * Function pour les documents uniquement
 */
function targetDocs() {
	var where;
	where="_blank";
	var reg=new RegExp(js_nouvelle_fenetre,"gi");
	
	if (typeof links_doc != "undefined") {
		var extensions = links_doc.split(',');
		for(var i = 0; i < extensions.length; i++){
			jQuery("a[href$='"+extensions[i]+"']").add("area[href$='"+extensions[i]+"']")
			  .attr('target',where)
				.attr('rel','blank')
				.addClass('blank')
				.addClass('spip_doc')
				.each(function(){
					var my_ext = extensions[i].replace('.','');
					jQuery(this).addClass(my_ext);
					if(jQuery(this).text()){
						title =  jQuery(this).text().trim().replace(/\r/,'').replace(/\t/,'').replace(/\n/g,'') + " " + js_nouvelle_fenetre;
					}else{
						title = " " + js_nouvelle_fenetre;
					}
					if((jQuery(this).attr("title"))&&(jQuery(this).attr("title").match(reg) == false)){ 
						title = jQuery(this).text().trim().replace(/\r/,'').replace(/\t/,'').replace(/\n/g,'') + " " + js_nouvelle_fenetre;
					}	
					jQuery(this).attr("title",title);
			});	
		}
	}
}


if (window.jQuery)
	(function($){
		if(typeof onAjaxLoad == "function") onAjaxLoad(targetLinks);
		if(typeof links_site != "undefined"){ $(document).ready(targetLinks); }
		if(typeof links_doc != "undefined") { $(document).ready(targetDocs); }
	})(jQuery);
