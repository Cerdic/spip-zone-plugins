<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

function grappes_inserer_js_recherche_objet(){
		$contenu =
<<<EOS
		function rechercher_objet(id_selecteur, page_selection) {
			// chercher l'input de saisie
			var me = jQuery(id_selecteur+' input[name=nom_objet]');
			me.autocomplete(
					{
						source: function( request, response ) {
							$.ajax({
								url: page_selection,
								data:{
									q:extractLast( request.term )
								},
								success: function(data) {
									datas = selecteur_format(data);
									response( $.map( datas, function( item ) {
										return item;
									}));
								}
							});
						},
						delay: 200,
						select:function( event, ui ) {
							if (ui.item.result > 0) {
								jQuery(id_selecteur + ' #pid_objet').val(ui.item.result);
								jQuery(id_selecteur + ' input[type="submit"]').focus();
								jQuery(me)
									.end();
							}else{
								return ui.item.entry;
							}
							return false;
						}
					}
				);
			}
EOS;
	return $contenu;
}

function grappes_inserer_javascript($flux){
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);

	$js = grappes_inserer_js_recherche_objet();
	$js = "<script type='text/javascript'><!--\n$js\n// --></script>\n";

	return $flux.$js;
}

/**
 * Ajoute aux pages qui peuvent etres lies a une grappe
 * un formulaire pour lister les grappes lies
 * et en ajouter de nouvelles
**/
function grappes_afficher_contenu_objet($flux){
	if ($objet = $flux['args']['type']
		//and in_array(table_objet_sql($objet), pipeline('grappes_objets_lie', array()))
		AND ($id_objet = intval($flux['args']['id_objet']))
		AND sql_countsel('spip_grappes',"liaisons REGEXP '(^|,)".table_objet($objet)."($|,)'")
		
	){
		$texte = recuperer_fond(
			'prive/squelettes/inclure/grappes_lister_objets',
			array(
				'objet'=>'grappes',
				'source'=>$objet,
				'id_source'=>$id_objet
			)
		);
		$flux['data'] .= $texte;
	}
	
	return $flux;
}

function grappes_grappes_objets_lies($array){
	$array['articles'] = _T('grappes:item_groupes_association_articles');
	$array['auteurs'] = _T('grappes:item_groupes_association_auteurs');
	$array['mots'] = _T('grappes:item_groupes_association_mots');
	$array['rubriques'] = _T('grappes:item_groupes_association_rubriques');
	$array['documents'] = _T('grappes:item_groupes_association_documents');
	$array['syndic'] = _T('grappes:item_groupes_association_syndic');

	return $array;
}
?>
