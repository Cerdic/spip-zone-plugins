function init_nemequittepas() {
	jQuery('.formulaire_editer form').areYouSure( {'message':'Voulez-vous vraiment quitter la page et perdre votre saisie ?'} );
}

jQuery(document).ready(function () {
	init_nemequittepas();
	onAjaxLoad(init_nemequittepas);
});