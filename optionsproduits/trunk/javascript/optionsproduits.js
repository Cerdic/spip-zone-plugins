jQuery(function(){

	// mise à jour du prix affiché en fonction du choix des options
	if($('.js-achat-form').length) {
		function updatePrixOptionObjet() {
			// calculer le prix du objet + des options
			var prixObjet = parseFloat($('.js-achat-form input[name=prix_objet]').val())||0;
            $('.js-achat-form .editer_options_objet input[type=radio]:checked').each(function(){
				prixObjet += parseFloat($(this).data('prixoption'))||0;
				console.log(parseFloat($(this).data('prixoption')));
			});
			// formater
			// TODO : pouvoir utiliser une autre monnaie que l'euro
			prixObjet = prixObjet.toFixed(2).replace('\.',',')+' €';
			$('.js-achat-form').find('.js-prix_objet_valeur').html(prixObjet);
		}
		
		updatePrixOptionObjet();
		$('.js-achat-form .editer_options_objet input[type=radio]').on('click', function() {
			updatePrixOptionObjet();
		});
	}
	
});