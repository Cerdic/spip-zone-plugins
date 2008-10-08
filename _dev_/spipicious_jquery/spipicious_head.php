<?php

function spipicious_insert_head($flux){
	global $visiteur_session;
	
	if(!$visiteur_session['id_auteur'])
		return;

	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	
	$selecteur = generer_url_public('selecteurs_tags');
    
	$flux .= <<<EOS
		<script type="text/javascript"><!--
		
	(function($) {
	var appliquer_selecteur_cherche_mot = function() {

		// chercher l'input de saisie
		var spipicious = jQuery('input[@name=tags][autocomplete!=off]');
		var id_groupe = jQuery("#select_groupe").val();
		var id_article = jQuery("#spipicious_id").val();
		spipicious.autocomplete('$selecteur',
			{
				extraParams:{id_article:id_article,id_groupe:id_groupe},
				delay: 200,
				autofill: false,
				minChars: 1,
				multiple:true,
				multipleSeparator:";",
				formatItem: function(data, i, n, value) {
					return data[0];
				},
				formatResult: function(data, i, n, value) {
					return data[1];
				},
			}
		);
		spipicious.result(function(event, data, formatted) {
			if (data[2] > 0) {
				jQuery(me)
				.end();
			}
			else{
				return data[1];
			}
		});
	};
		$(function(){
			appliquer_selecteur_cherche_mot();
			onAjaxLoad(appliquer_selecteur_cherche_mot);
		});
	})(jQuery);
// --></script>
EOS;
	return $flux;
}

?>