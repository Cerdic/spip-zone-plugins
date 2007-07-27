<?php

function spipicious_affichage_final($page){

	if (!strpos($page, 'form_spipicious_ajax'))
		return $page;

	$iautocompleter = find_in_path('javascript/iautocompleter.js');
	$autocompletecss = find_in_path('jquery.autocomplete.css');
	$urlselecteur = parametre_url(generer_url_ecrire('selecteur_generique','quoi=tag'),id_article, $id_article, '\\x26');

    $incHead = <<<EOS
		<script type='text/javascript' src='$iautocompleter'></script>
		<link rel="stylesheet" href="$autocompletecss" type="text/css" media="all" />
		<script  type="text/javascript"><!--
	
	var appliquer_selecteur_cherche_mot = function() {

		// chercher l'input de saisie
		var inp = jQuery('input[@name=tags]', this);

		// ne pas reappliquer si on vient seulement de charger les suggestions
		if (!inp[0] || inp[0].autoCFG) return;

		// attacher l'autocompleter
		inp.each(function() {
			var me = this;
			var id_groupe = $("#select_groupe").val();
			var id_article = $("#spipicious_id").val();
			jQuery(this)
			.Autocomplete({
				'source': '$urlselecteur'+'\x26id_article='+id_article+'\x26id_groupe='+id_groupe,
				'delay': 200,
				'autofill': false,
				'helperClass': "autocompleter",
				'selectClass': "selectAutocompleter",
				'minchars': 2,
				'mustMatch': true,
				'inputWidth': true,
				'cacheLength': 20,
				'multiple' : true,
				'multipleSeparator' : "; ",
				fx : {type: "fade", duration: 400},
				'onShow' : function(suggestionBox, suggestionIframe) {
					jQuery('.autocompleter, .selectAutocompleter').fadeTo(300,0.8);
				},
				'onSelect': 
				function(li) {
					if (li.id > 0) {
						jQuery(me)
						.end();
					}
				}
			});
			jQuery('.autocompleter, .selectAutocompleter').css('opacity',0.7);
		});
	}
	jQuery(document).ready(appliquer_selecteur_cherche_mot);
	onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_mot, 200);});
// --></script>
EOS;

	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

?>