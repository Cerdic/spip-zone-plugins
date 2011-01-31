// Inside the function "this" will be "document" when called by ready()
// and "the ajaxed element" when called because of onAjaxLoad
var nyro_init = function() {
	// On cache les embed et object qui n'ont pas de wmode=transparent pour eviter qu'ils
	// passent par dessus la modale
	if(!navigator.platform.match('Mac')){
		jQuery.fn.nyroModal.settings.processHandler = function(){
			jQuery('embed[wmode!=transparent]:visible').addClass('nyro_cache').css('visibility','hidden')
				.parents('object').addClass('nyro_cache').css('visibility','hidden');
		}
		jQuery.fn.nyroModal.settings.endRemove = function(){
			jQuery('.nyro_cache').removeClass('.nyro_cache').css('visibility','visible');
		}
	}
	if (nyro_traiter_toutes_images) {
		// selectionner tous les liens vers des images
		jQuery("a[type=\'image/jpeg\'],a[type=\'image/png\'],a[type=\'image/gif\']",this)
		.addClass("nyroceros") // noter qu\'on l\'a vue
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.nyroModal({bgColor: nyro_bgcolor}); // activer le nyro
	}
	// passer le portfolio en mode galerie de nyro
	jQuery(nyro_selecteur_galerie, this)
	.attr("rel","galerie-portfolio");

	// charger nyro sur autre chose
	jQuery(nyro_selecteur_commun).nyroModal({bgColor: nyro_bgcolor});

  // preload images
	if (nyro_preload) {
		jQuery.fn.preload = function() {
	    var url;
	    return this.each(function() {
	      if ((url = $(this).attr("href")) && url.match(/\.(jpg|jpeg|png|gif)$/ )) {
	        var img = new Image;
	        img.src = url;
	      }
	    });
	  }
	jQuery.fn.nyroModal.settings.endShowContent = function(elts,settings) {
		jQuery(".nyroModalNext").preload();
	  }
	jQuery(".nyroceros[rel]:eq(0)").preload();
	}
};
