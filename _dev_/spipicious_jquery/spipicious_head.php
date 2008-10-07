<?php

function spipicious_affichage_final($page){

	if (!strpos($page, 'formulaire_spipicious_ajax'))
		return $page;

	$autocompleter = find_in_path('javascript/jquery.autocomplete.js');
	$autocompletecss = find_in_path('jquery.autocomplete.css');

	$selecteur = generer_url_public('selecteurs_tags');

    $incHead = <<<EOS
		<script type='text/javascript' src='$autocompleter'></script>
		<link rel="stylesheet" href="$autocompletecss" type="text/css" media="all" />
		<script  type="text/javascript"><!--
		
	(function($) {
	var appliquer_selecteur_cherche_mot = function() {

		// chercher l'input de saisie
		var me = jQuery('input[@name=tags][autocomplete!=off]');
		var id_groupe = jQuery("#select_groupe").val();
		var id_article = jQuery("#spipicious_id").val();
		me.autocomplete('$selecteur',
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
				me.result(function(event, data, formatted) {
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

	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

?>