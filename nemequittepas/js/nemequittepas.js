function nemequittepas() {
	jQuery('.formulaire_editer form').areYouSure( {'message':'Voulez-vous vraiment quitter la page et perdre votre saisie ?'} );
}

jQuery(document).ready(function () {
	jQuery(window).load(function() {
		nemequittepas();
		onAjaxLoad(nemequittepas);
	});
});