<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Filtrer les boucles pour ne pas afficher le ou les secteurs configurés comme à exclure.
 * La configuration du plugin influe sur le rendu du pipeline.
 *
 * @pipeline pre_boucle
 *
 * @param Boucle $boucle
 *        Objet boucle de SPIP correspond à la boucle en cours de traitement.
 *
 * @return Boucle
 *         La boucle dont la condition `where` a été modifiée ou pas.
 */
function exclure_sect_pre_boucle($boucle){

	// On détermine si la configuration du plugin :
	// - autorise à considérer le critère tout comme le critère tout_voir
	// - définit bien des secteurs à exclure
	include_spip('inc/config');
	$configuration = lire_config('secteur');

	// On essaye de déterminer une exclusion de secteur si:
	// - le critère tout_voir n'est pas utilisé
	// - le critère tout n'est pas utilisé
	// - le critère tout est utilisé et la configuration du plugin distingue tout et tout_voir
	// - on est pas dans l'espace privé
	// - le nom de la boucle est vide ou différent de 'calculer_langues_utilisees'
	// - on n'est pas dans un boucle adressant une base externe
	// - il existe des secteurs à exclure !
	if (empty($boucle->modificateur['tout_voir'])
		and (empty($boucle->modificateur['tout']) or (!empty($boucle->modificateur['tout']) and ($configuration['tout'] != 'oui')))
		and !test_espace_prive()
		and (empty($boucle->nom) or (!empty($boucle->nom) and ($boucle->nom != 'calculer_langues_utilisees')))
		and ($boucle->sql_serveur == '')
		and !empty($configuration['exclure_sect'])
	) {
		// Extraire la table et les critères de la boucle
		$table_objet = $boucle->id_table;
		$criteres = $boucle->criteres;

		// Il ne faut rien exclure si :
		// - un critère id_secteur inclusif et explicite existe dans la boucle ({id_secteur = xx}, {id_secteur == regexp},
		//	 {id_secteur IN x, y, z}).
		// - ou un critère id_<objet> inclusif et explicite existe dans la boucle et que la configuration l'autorise
		//   ({id_<type_objet> = xx}, {id_<type_objet> == regexp}, {id_<type_objet> IN x, y, z} et aussi le critère non
		//   inclusif {!id_<type_objet>}.
		include_spip('inc/exclure_sect_utils');
		include_spip('base/objets');
		if (!critere_id_est_explicite($criteres, 'id_secteur')
			and (!$configuration['idexplicite']
				or ($configuration['idexplicite']
					and ($id_table_objet = id_table_objet($table_objet))
					and !critere_id_est_explicite($criteres, $id_table_objet)
				)
			)
		) {
			// On calcule la liste des secteurs à exclure sous forme d'une chaine 's1, s2, s3...'.
			$secteurs_exclus = implode(array_map('sql_quote', $configuration['exclure_sect']), ',');

			if (in_array($table_objet, array('articles', 'rubriques', 'syndic'))) {
					$boucle->where[] = "sql_in('$table_objet.id_secteur', '$secteurs_exclus', 'NOT')";
			} elseif ($table_objet == 'breves') {
					$boucle->where[] = "sql_in('$table_objet.id_rubrique', '$secteurs_exclus', 'NOT')";
			} elseif ($table_objet == 'forum') {
				$select_article = "sql_get_select('id_article', 'spip_articles', sql_in('id_secteur', '$secteurs_exclus'))";
				$where = array(
					sql_quote('NOT'),
					array(
						sql_quote('AND'),
						"sql_in('forum.objet', sql_quote('article'))",
						"sql_in('id_objet', $select_article)"
					)
				);
				// $boucle->where[] = $where;
			} else {
				// Autres objets via un pipeline adéquat ??

			}
		}
	}

	return $boucle;
}
