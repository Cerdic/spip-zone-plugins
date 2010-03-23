/**
 * Fonction a lancer lors de la verification du formulaire qui s'occupe d'empecher la sauvegarde
 */

function crayon_affiche_submit(me,id){
	// Si aucune erreur
	if(id.find('p.erreur:visible').length == 0){
		// On (re)active les combinaisons de touches par defaut des crayons
		jQuery(id).find("textarea.crayon-active,input.crayon-active[type=text]").unbind('keypress').keypress(function(e){
			// Clavier pour sortir, annuler (esc)
			if (e.keyCode == 27) {
				me
				.cancelcrayon();
			}
			// Clavier pour sauver
			if ((e.ctrlKey && (
				/* ctrl-s ou ctrl-maj-S, firefox */
				((e.charCode||e.keyCode) == 115) || ((e.charCode||e.keyCode) == 83))
				/* ctrl-s, safari */
				|| (e.charCode==19 && e.keyCode==19))){
				id
				.submit();
			}
			var maxh = this.className.match(/\bmaxheight(\d+)?\b/);
			if (maxh) {
				maxh = maxh[1] ? parseInt(maxh[1]) : 200;
				maxh = this.scrollHeight < maxh ? this.scrollHeight : maxh;
				if (maxh > this.clientHeight) {
					$(this).css('height', maxh + 'px');
				}
			}
		});
		// Reafficher le bouton de validation
		id.find('.crayon-submit').show();
	}
	else{
		// desactiver les raccourcis clavier des crayons (sauf le ESC pour annuler)
		id.find("textarea.crayon-active,input.crayon-active[type=text]").unbind('keypress').keypress(function(e){
			if (e.keyCode == 27) {
				me
				.cancelcrayon();
			};
		});
		// Cacher le bouton de validation
		id.find('.crayon-submit').hide();
	}
}

jQuery.validator.setDefaults({
	errorElement:"p",
	errorClass: "erreur",
	errorPlacement: function(error, element) {
		error.appendTo( element.parents("li") );
		element.parents("li").addClass('erreur');
	}
});