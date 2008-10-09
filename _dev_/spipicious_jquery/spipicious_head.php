<?php

function spipicious_insert_head($flux){
	global $visiteur_session;
	
	if($visiteur_session['id_auteur']){

	include_spip('selecteurgenerique_fonctions');
	$contenu = selecteurgenerique_verifier_js($flux);
	
	$selecteur = generer_url_public('selecteurs_tags');
    
	$contenu .= <<<EOS
		<script type="text/javascript"><!--
			function deletetag(tag){
				var tag = tag;
				jQuery('input#remove_tag').val(tag).parents('form').submit().end();
				return false;
			}
	(function($) {
	var spipicious = jQuery('input[@name=tags][autocomplete!=off]');
	var appliquer_selecteur_cherche_mot = function() {
		var spipicious = jQuery('input[@name=tags][autocomplete!=off]');
		// chercher l'input de saisie
		var id_article = jQuery("#spipicious_id").val();
		spipicious.autocomplete('$selecteur',
			{
				extraParams:{id_article:id_article},
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
				jQuery(spipicious)
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
	return $contenu;
	}
}

?>