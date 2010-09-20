
/*
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$
*/

(function($) {
	
	// attendre document chargé 
	$(document).ready(function() {
	
		// commencer par tout replier
		$("#gins-contenu div.gins-item").hide();
		
		// afficher le premier
		$("#gins-contenu div.gins-item:first").show();
		
		// accrocher l'event sur les boutons
		$("#gins-menu a").click(function () {
			$("#gins-menu a").removeClass("highlight");
			$(this).addClass("highlight");
			$("#gins-contenu div.gins-item").hide();
			$("#gins-"+$(this).attr('name')).show();
		}); 

	});
	
})(jQuery);
