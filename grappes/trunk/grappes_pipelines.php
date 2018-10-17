<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 *
 * Fichier de pipelines du plugin
 *
 * @package Grappes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans les pipelines insert_head et header_prive (SPIP)
 * Insérer les js du séleceteur générique s'ils ne sont pas déjà là
 *
 * @param string $flux
 * 	Le contenu textuel de la balise #INSERT_HEAD
 * @return string
 * 	Le contenu modifié
 */
function grappes_inserer_javascript($flux) {
	include_spip('plugins/installer');
	if (spip_version_compare($GLOBALS['spip_version_branche'], '3.2.0', '<')) {
		include_spip('selecteurgenerique_fonctions');
		$flux .= selecteurgenerique_verifier_js($flux);
	}
	return $flux;
}


/**
 * Pipeline jqueryui_forcer pour demander au plugin l'insertion des scripts pour .sortable()
 *
 * @param array $plugins
 * @return array
 */
function grappes_jqueryui_forcer($plugins) {
	if (test_espace_prive()) { // On envoie que si on est dans l'espace prive
		$plugins[] = 'jquery.ui.core';
		$plugins[] = 'jquery.ui.widget';
		$plugins[] = 'jquery.ui.mouse';
		$plugins[] = 'jquery.ui.sortable';
		$plugins[] = 'jquery.ui.droppable';
		$plugins[] = 'jquery.ui.draggable';
	}
	return $plugins;
}


/**
 * Insertion dans le pipeline afficher_contenu_objet (SPIP)
 *
 * Ajouter le bloc des grappes aux pages d'objets pouvant être liés à une grappe
 *
 * @param array $flux
 * 	Le contexte du pipeline
 * @return array $flux
 * 	Le contexte du pipeline modifié
 */
function grappes_afficher_contenu_objet($flux) {
	if ($objet = $flux['args']['type']
		//and in_array(table_objet_sql($objet), pipeline('grappes_objets_lie', array()))
		and ($id_objet = intval($flux['args']['id_objet']))
		and sql_countsel('spip_grappes', "liaisons REGEXP '(^|,)".table_objet($objet)."($|,)'")
	) {
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

/**
 * Insertion dans le pipeline grappes_objets_lies (Plugin Grappes)
 *
 * Définis le tableau des objets pouvant être liés aux grappes, la clé est le type d'objet (au pluriel),
 * la valeur, le label affiché dans le formulaire d'édition de grappe
 *
 * @param array $array
 * 	Le tableau du pipeline
 * @return array $array
 * 	Le tableau complété
 */
function grappes_grappes_objets_lies($array) {
	$array = is_array($array) ? $array : array();
	$array['articles'] = _T('grappes:item_groupes_association_articles');
	$array['auteurs'] = _T('grappes:item_groupes_association_auteurs');
	$array['mots'] = _T('grappes:item_groupes_association_mots');
	$array['rubriques'] = _T('grappes:item_groupes_association_rubriques');
	$array['documents'] = _T('grappes:item_groupes_association_documents');
	$array['syndic'] = _T('grappes:item_groupes_association_syndic');

	return $array;
}
