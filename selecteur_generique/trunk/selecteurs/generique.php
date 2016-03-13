<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function selecteurs_generique_dist() {
	include_spip('base/objets');
	include_spip('inc/filtres');
	include_spip('inc/texte');
	
	$trouver_table = charger_fonction('trouver_table', 'base');
	$tables = lister_tables_objets_sql();
	$search = trim(_request('q'));
	$resultats = array();
	$limite = 5;
	
	if (!$search) {
		return $resultats;
	}
	
	// Pouvoir personnaliser le nombre de résultats
	if ($limite_perso = intval(_request('limite')) and $limite_perso > 0) {
		$limite = $limite_perso;
	}
	
	// On ne garde que les objets demandés… si demandé
	if ($objets = _request('objets') and is_array($objets)) {
		$objets = array_flip(array_map('table_objet_sql', $objets));
		$tables = array_intersect_key($tables, $objets);
	}
	
	// On exclut s'il faut
	if ($objets_exclus = _request('objets_exclus') and is_array($objets_exclus)) {
		$objets_exclus = array_flip(array_map('table_objet_sql', $objets_exclus));
		$tables = array_diff_key($tables, $objets_exclus);
	}
	
	// On parcourt ensuite toutes les tables, en cherchant les meilleurs résultats par titre
	foreach ($tables as $table => $desc) {
		$cle_objet = id_table_objet($table);
		$objet = objet_type($table);
		
		// Seulement si on trouve un champ titre de la table
		if (
			(
				// S'il y a une déclaration ET que c'est un truc super simple du genre "champ_simple AS titre"
				(
					isset($desc['titre'])
					and preg_match(';(?:^|,)\s*([^,\s]+)\s*as\s*titre\s*(,|$);i', $desc['titre'], $champ)
					and !preg_match(';\W;', $champ[1])
					and $champ = trim($champ[1])
				)
				// Sinon si on trouve un champ titre
				or (isset($desc['field']['titre']) and $champ = 'titre')
				// Sinon si on trouve un champ nom
				or (isset($desc['field']['nom']) and $champ = 'nom')
			)
			and (
				// Seulement quand ça débute pareil en priorité
				$trouve = sql_allfetsel(
					$champ . ', ' . $cle_objet,
					$table,
					"$champ LIKE ".sql_quote("${search}%"),
					'',
					'',
					"0,$limite"
				)
				or
				// Sinon n'importe où dans le titre
				$trouve = sql_allfetsel(
					$champ . ', ' . $cle_objet,
					$table,
					"$champ LIKE ".sql_quote("%${search}%"),
					'',
					'',
					"0,$limite"
				)
			)
		) {
			// On ajoute le titre de l'objet
			$resultats[] = array(
				'label' => '<strong>' . _T($desc['texte_objets']) . '</strong>',
				'value' => ' ',
			);
			
			foreach ($trouve as $resultat) {
				$id_objet = $resultat[$cle_objet];
				
				if (function_exists('appliquer_traitement_champ')) {
					$titre = appliquer_traitement_champ(
						$resultat[$champ],
						'titre',
						table_objet($table),
						array('objet' => $objet, 'id_objet' => $id_objet)
					);
				}
				else {
					$titre = typo($resultat[$champ]);
				}
				
				$resultats[] = array(
					'label' => $titre,
					'value' => $objet.$id_objet,
				);
			}
		}
	}
	
	return json_encode($resultats);
}
