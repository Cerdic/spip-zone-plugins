// Inside the function "this" will be "document" when called by ready()
// and "the ajaxed element" when called because of onAjaxLoad
var colorbox_init = function() {
	var options = {
		transition:box_settings.transition,
		speed:box_settings.speed,
		maxWidth:box_settings.maxWidth,
		maxHeight:box_settings.maxHeight,
		minWidth:'400',
		slideshowStart:box_settings.str_slideshowStart,
		slideshowStop:box_settings.str_slideshowStop,
		current:box_settings.str_current,
		previous:box_settings.str_previous,
		next:box_settings.str_next,
		close:box_settings.str_close
	};
	
	// passer le portfolio en mode galerie la box
	if (box_settings.selecteur_galerie){
		$(box_settings.selecteur_galerie, this)
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(jQuery.extend({}, options, {rel:'galerieauto',slideshow:true,slideshowAuto:false}))
		.addClass("hasbox");
	}

	if (box_settings.traiter_toutes_images) {
		// selectionner tous les liens vers des images
		$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']",this).not('.hasbox')
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(options) // activer la box
		.addClass("hasbox") // noter qu\'on l\'a vue
		;
	}

	// charger la box sur autre chose
	if (box_settings.selecteur_commun){
		$(box_settings.selecteur_commun).not('.hasbox')
		.colorbox(options)
		.addClass("hasbox") // noter qu\'on l\'a vue
		;
	}
};
