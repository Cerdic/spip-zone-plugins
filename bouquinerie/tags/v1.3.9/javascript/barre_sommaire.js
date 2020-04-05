(function($){
$(document).ready(function(){
	function charger_barre() { 
		$('.formulaire_spip textarea.inserer_barre_sommaire').barre_outils('sommaire');		
	}
	charger_barre();
	onAjaxLoad(charger_barre);
});
})(jQuery);