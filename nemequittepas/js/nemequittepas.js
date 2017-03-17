$(document).ready(function () {
    $(function() {
		// With a custom message
		$('.formulaire_editer form').areYouSure( {'message':'Voulez-vous vraiment quitter la page et perdre votre saisie ?'} );
	});
});