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
	$(box_settings.selecteur_galerie, this)
	.attr("onclick","") // se debarrasser du onclick de SPIP
	.colorbox(jQuery.extend({}, options, {rel:'galerie',slideshow:true,slideshowAuto:false}))
	.addClass("colorbox");

	if (box_settings.traiter_toutes_images) {
		// selectionner tous les liens vers des images
		$("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']",this).not('.colorbox')
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.colorbox(options) // activer la box
		.addClass("colorbox") // noter qu\'on l\'a vue
		;
	}

	// charger la box sur autre chose
	$(box_settings.selecteur_commun).not('.colorbox')
	.colorbox(options)
	.addClass("colorbox") // noter qu\'on l\'a vue
	;
};
