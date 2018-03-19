jQuery(function(){

	// mise à jour du prix affiché en fonction du choix des options
	if($('.js-achat-form').length) {
		function updatePrixProduit() {
			// calculer le prix du produit + des options
			var prixProduit = parseFloat($('.js-achat-form input[name=prix_produit]').val());
			$('.js-achat-form .editer_options_produit input[type=radio]:checked').each(function(){
				prixProduit += parseFloat($(this).data('prixoption'));
			});
			// formater
			// TODO : pouvoir utiliser une autre monnaie que l'euro
			prixProduit = prixProduit.toFixed(2).replace('\.',',')+' €';
			$('.js-achat-form').find('.prix').html(prixProduit);
		}
		
		updatePrixProduit();
		$('.js-achat-form .editer_options_produit input[type=radio]').on('click', function() {
			updatePrixProduit();
		});
	}
	
});