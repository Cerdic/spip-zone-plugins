<?php
/**
 * Utilisations de pipelines par Rang
 *
 * @plugin     Rang
 * @copyright  2016
 * @author     Peetdu
 * @licence    GNU/GPL
 * @package    SPIP\Rang\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/rang_api');
include_spip('inc/config');

/**
 * Declaration du champ Rang sur les objets sélectionnés
 *
 * @param array $tables
 * @return array
 */
function rang_declarer_tables_objets_sql($tables) {
	$tables_objets_selectionnes = lire_config('rang/objets');
	
	// Tant qu'on n'a rien rajouté, on commence par lister les tables qui ont DEJA un champ rang !
	$tables_deja_rang = rang_lister_tables_deja_rang($tables);
	
	// On déclare le champ "rang" sur les tables demandées
	if (is_array($tables_objets_selectionnes)) {
		foreach ($tables_objets_selectionnes as $table) {
			// Mais on ne déclare le champ que s'il n'existait pas déjà !
			if (!isset($tables[$table]['field']['rang'])) {
				$tables[$table]['field']['rang'] = "SMALLINT NOT NULL";
			}
		}
	}
	
	return $tables;
}

/**
 * Calculer et Inserer le JS qui gére le tri par Drag&Drop dans le bon contexte (la page ?exec=xxxxx)
 *
 * @param    array $flux Données du pipeline
 * @return    array        Données du pipeline
 */
function rang_recuperer_fond($flux) {
	$tables_objets_selectionnes = lire_config('rang/objets');
	
	if (isset($tables_objets_selectionnes) AND !empty($tables_objets_selectionnes)) {

		// Gestion du contexte : dans quelle page insérer le JS ?
		if (in_array(_request('exec'), rang_get_contextes())
			&& in_array($flux['args']['fond'], rang_get_sources())
			&& strpos($flux['data']['texte'], 'data-objet=')
		) {
			// recuperer le nom de l'objet
			preg_match('/data-objet=["\'](\w+)["\']/', $flux['data']['texte'], $result);
			$objet_nom  = $result[1];
			$objet_type = objet_type($objet_nom);

			// insérer le script de tri si on a bien un objet à ranger
			if ($objet_type) {
				// récupérer le type de parent…
				include_spip('base/objets_parents');
				$parent       = type_objet_info_parent($objet_type);
				$parent_champ = $parent['champ'];
				$id_parent    = $flux['args']['contexte'][$parent_champ];

				// suffixe de la pagination : particularité des objets historiques
				switch ($objet_type) {
					case 'article':
						$suffixe_pagination = 'art';
						break;
					case 'site':
						$suffixe_pagination = 'sites';
						break;
					case 'breve':
						$suffixe_pagination = 'bre';
						break;
					default:
						$suffixe_pagination = $objet_type;
						break;
				}

				// Calcul du JS à insérer avec les paramètres
				$ajout_script = recuperer_fond('prive/squelettes/inclure/rang', array(
					'suffixe_pagination' => $suffixe_pagination,
					'objet'              => $objet_nom,
					'id_parent'          => $id_parent,
				));

				// et hop, on insère le JS calculé
				$flux['data']['texte'] = str_replace('</table>', '</table>' . $ajout_script, $flux['data']['texte']);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition pour le classer l'objet quand on le publie
 *
 * @param    array $flux Données du pipeline
 *
 * @return    array        Données du pipeline
 */
function rang_pre_edition($flux) {
	$rang_max = lire_config('rang/rang_max');

	if (isset($rang_max) && !empty($rang_max) && $flux['args']['action'] == 'instituer') {

		$liste_objets = lire_config('rang/objets');
		$table        = $flux['args']['table'];

		if (in_array($table, $liste_objets)) {
			$id_objet = $flux['args']['id_objet'];

			// cas des objets avec statut
			if (isset($flux['data']['statut']) && $flux['data']['statut'] == 'publie') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
			// cas des mots clés
			if ($table == 'spip_mots') {
				$flux['data']['rang'] = rang_classer_dernier($table, $id_objet);
			}
		}
	}
	return $flux;
}