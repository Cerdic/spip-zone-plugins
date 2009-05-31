jQuery(function(){
	var init = function() {			
		if(!jQuery.carto || !jQuery.carto.ready)
			return window.setTimeout(init,10);
		var carto = new jQuery.carto(cfg_standard);
		carto.mapMarker = mapMarker;
		carto.loadMarkersWithTooltip();
		var carto_forum = new jQuery.carto(cfg_forum);
		carto_forum.mapMarker = mapMarker_forum;
		carto_forum.loadMarkersWithTooltip(null,{id_article:jQuery("meta[@name=annotations_article]").attr("content")});
		//if you want to load the annotations text inside an overlay window use the next line instead of the previous one
		//carto.loadMarkersWithOverlay();
		var init_window = function() {
			if(!jQuery.carto.annotate_window) 
				return window.setTimeout(init_window,10);
			jQuery.carto.annotate_window.init(carto);
		};
		if(jQuery("#annotate_window").size()) 
			init_window();
	}
	window.setTimeout(init,10);
});
