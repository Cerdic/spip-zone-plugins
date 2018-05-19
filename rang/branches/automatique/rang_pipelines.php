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
	
	if (
		// S'il y a bien des objets qu'on veut trier
		isset($tables_objets_selectionnes)
		and !empty($tables_objets_selectionnes)
		// On cherche un objet en rapport avec le squelette
		and $objet_info = rang_trouver_objet_liste($flux['args']['fond'])
		// Cet objet fait partie de ceux qu'on veut pouvoir trier
		and in_array($objet_info['table_objet_sql'], $tables_objets_selectionnes)
		// On cherche l'objet correspondant à la page en cours
		// Si la page sur laquelle on est fait partie des contextes qui peut avoir des rangs à trier
		//and in_array(_request('exec'), rang_get_contextes())
	) {
		// Si pas déjà présent, on ajoute l'info de l'objet sur le tableau
		if (strpos($flux['data']['texte'], 'data-objet=') === false) {
			$flux['data']['texte'] = preg_replace('/<table/i', '<table data-objet="'.$objet_info['objet'].'"', $flux['data']['texte']);
		}
		
		// Si pas déjà, on ajoute l'info d'identifiant sur chaque ligne
		if (
			// Si pas de id_objet déjà dans le tableau
			strpos($flux['data']['texte'], 'data-id_objet') === false
			// Et qu'il y a des cellules avec les ids
			and preg_match('%<td[^>]+?class=("|\')[^>]*?id%is', $flux['data']['texte'])
		) {
			include_spip('inc/filtres');
			
			$flux['data']['texte'] = preg_replace_callback(
				'%(<tbody[^>]*?>)(.*?)</tbody>%is',
				function ($matches) {
					$lignes = preg_replace_callback(
						'%<tr([^>]*?)>(.*?)</tr>%is',
						function ($matches) {
							// On cherche le numéro d'id
							preg_match('%<td[^>]+?class=("|\')[^>]*?id[^>]*?>(.*?)</td>%is', $matches[2], $trouver);
							$id = supprimer_tags($trouver[2]);
							
							return '<tr' . $matches[1] . "data-id_objet=\"$id\"" . '>' . $matches[2] . '</tr>';
						},
						$matches[2]
					);
					
					return $matches[1] . $lignes . '</tbody>';
				},
				$flux['data']['texte']
			);
		}
		
		$objet = $objet_info['objet'];
		
		// récupérer le type de parent…
		include_spip('base/objets_parents');
		$parent       = type_objet_info_parent($objet);
		$parent_champ = $parent['0']['champ'];
		$id_parent    = $flux['args']['contexte'][$parent_champ];

		// suffixe de la pagination : particularité des objets historiques
		switch ($objet) {
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
				$suffixe_pagination = $objet;
				break;
		}

		// Calcul du JS à insérer avec les paramètres
		$ajout_script = recuperer_fond(
			'prive/squelettes/inclure/rang',
			array(
				'suffixe_pagination' => $suffixe_pagination,
				'objet'              => table_objet($objet),
				'id_parent'          => $id_parent,
			)
		);

		// et hop, on insère le JS calculé
		$flux['data']['texte'] = str_replace('</table>', '</table>' . $ajout_script, $flux['data']['texte']);
	}
	
	return $flux;
}

/**
 * Insertion dans le pipeline pre_edition pour le classer l'objet quand on le publie
 *
 * @param    array $flux Données du pipeline
 * @return    array        Données du pipeline
 */
function rang_pre_edition($flux) {
	$rang_max = lire_config('rang/rang_max');

	if (isset($rang_max) && !empty($rang_max) && $flux['args']['action'] == 'instituer') {
		$liste_objets  = lire_config('rang/objets');
		$table         = $flux['args']['table'];

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
