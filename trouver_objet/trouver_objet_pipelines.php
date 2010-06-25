<?php

/**
 * Plugin trouver_objet javascript entierement pompe - avec remerciement
 * Plugin Grappes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

function trouver_objet_inserer_js_recherche_objet(){
	return <<<EOS

		function rechercher_objet(id_selecteur, page_selection) {
			// chercher l'input de saisie
			var me = jQuery(id_selecteur+' input[name=nom_objet]');
			me.autocomplete(page_selection,
					{
						delay: 200,
						autofill: false,
						minChars: 1,
						multiple:false,
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
						jQuery(id_selecteur + ' #pid_objet').val(data[2]);
						jQuery(id_selecteur + ' input[type="submit"]').focus();
						jQuery(me)
						.end();
					}
					else{
						return data[1];
					}
				});
			};
EOS;
}

function trouver_objet_inserer_javascript($flux){
	//ne pas doublonner > verifie si grappe existe et lui laisse la priorite
	if (defined('_DIR_PLUGIN_GRAPPES')) return $flux;
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);

	$js = trouver_objet_inserer_js_recherche_objet();
	$js = "<script type='text/javascript'><!--\n/*trouver_objet*/ \n$js\n // --></script>\n ";

	return $flux.$js;
}


?>
