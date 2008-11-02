// Inside the function "this" will be "document" when called by ready() 
// and "the ajaxed element" when called because of onAjaxLoad 
var nyro_init = function() {
	if (nyro_traiter_toutes_images) {
		// selectionner tous les liens vers des images
		$("a[@type=\'image/jpeg\'],a[@type=\'image/png\'],a[@type=\'image/gif\']",this)
		.addClass("nyroceros") // noter qu\'on l\'a vue
		.attr("onclick","") // se debarrasser du onclick de SPIP
		.nyroModal({bgColor: nyro_bgcolor}); // activer le nyro
	}
	// passer le portfolio en mode galerie de nyro
	$(nyro_selecteur_galerie, this)
	.attr("rel","galerie-portfolio");

	// charger nyro sur autre chose
	$(nyro_selecteur_commun).nyroModal({bgColor: nyro_bgcolor});

  // preload images
	if (nyro_preload) {
	  $.fn.preload = function() {
	    var url;
	    return this.each(function() {
	      if ((url = $(this).attr("href")) && url.match(/\.(jpg|jpeg|png|gif)$/ )) {
	        var img = new Image;
	        img.src = url;
	      }
	    });
	  }
	  $.fn.nyroModal.settings.endShowContent = function(elts,settings) {
	    $(".nyroModalNext").preload();
	  } 
	  $(".nyroceros[@rel]:eq(0)").preload();
	}
};