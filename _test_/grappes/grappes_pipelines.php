<?php

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

function grappes_inserer_js_recherche_objet(){
	return <<<EOS
		
		function rechercher_objet(id_selecteur, page_selection) {
			// chercher l'input de saisie
			var me = jQuery(id_selecteur+' input[@name=nom_objet]');
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

function grappes_inserer_javascript($flux){
	
	$js = grappes_inserer_js_recherche_objet();
	$js = 
		'<script type="text/javascript" src="'
		. find_in_path('javascript/jquery.autocomplete.js')
		. '"></script>'
		. "\n"

		. '<link rel="stylesheet" type="text/css" '
		. 'href="'.find_in_path('iautocompleter.css').'" />'
		. "\n"

		. '<script type="text/javascript"><!--'
		. "\n"
		. $js
		. "\n"
		. '// --></script>'
		. "\n";

	return $flux.$js;	
}

/**
 * Ajoute aux pages qui peuvent etres lies a une grappe
 * un formulaire pour lister les grappes lies 
 * et en ajouter de nouvelles
**/

function grappes_affiche_milieu($flux){
	if ($exec = $flux['args']['exec']){
		switch ($exec){
			case 'articles':
				$source = 'articles';
				$id_source = $flux['args']['id_article'];
				break;			
			case 'auteur_infos':
				$source = 'auteurs';
				$id_source = $flux['args']['id_auteur'];
				break;			
			case 'breves_voir':
				$source = 'breves';
				$id_source = $flux['args']['id_breve'];
				break;
			case 'naviguer':
				$source = 'rubriques';
				$id_source = $flux['args']['id_rubrique'];
				break;
			case 'mots_edit':
				$source = 'mots';
				$id_source = $flux['args']['id_mot'];
				break;	
			case 'sites':
				$source = 'syndic';
				$id_source = $flux['args']['id_syndic'];
				break;			
			default:
				$source = $id_source = '';
				break;	
		}
		if ($source && $id_source) {
			// seulement s'il existe une grappe liable a cet objet
			if (sql_countsel('spip_grappes',"liaisons REGEXP '(^|,)$source($|,)'")) {
				$lister_objet = charger_fonction('lister_objets','inc');
				$flux['data'] .= $lister_objet('grappe',$source,$id_source);
			}
		}
	}
	
	return $flux;	
}

?>
