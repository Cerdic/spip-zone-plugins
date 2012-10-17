<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Plugin Groupes pour Spip 2.0
 * Licence GPL (c) 2008 Matthieu Marcillaud
 */

/**
 * Insertion dans les pipelines insert_head et header_prive
 * Insérer les js du séleceteur générique s'ils ne sont pas déjà là
 *
 * @param string $flux
 */
function grappes_inserer_javascript($flux){
	include_spip('selecteurgenerique_fonctions');
	$flux .= selecteurgenerique_verifier_js($flux);
	return $flux;
}

/**
 * Insertion dans le pipeline afficher_contenu_objet
 * Ajouter le bloc des grappes aux pages qui peuvent êtres liées à une grappe
 * 
 * @param array $flux La liste des champs pour les diogenes
 */
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
