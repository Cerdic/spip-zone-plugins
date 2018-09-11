<?php


/**
 * Ajouter un traitement automatique sur une balise
 *
 * On peut restreindre l'application du traitement au balises appelées dans un
 * type de boucle via le paramètre optionnel $table.
 *
 * @param array $interfaces
 *    Les interfaces du pipeline declarer_tables_interfaces
 * @param string $traitement
 *    Un format comme pour sprintf, dans lequel le compilateur passera la valeur de la balise
 * @param string $balise
 *    Le nom de la balise à laquelle on veut appliquer le traitement
 * @param string $table (optionnel)
 *    Un type de boucle auquel on veut restreindre le traitement.
 */
function logos_roles_ajouter_traitement_automatique($interfaces, $traitement, $balise, $table = 0) {

	$table_traitements = $interfaces['table_des_traitements'];

	if (! isset($table_traitements[$balise])) {
		$table_traitements[$balise] = array();
	}

	/* On essaie d'être tolérant sur le nom de la table */
	if ($table) {
		include_spip('base/objets');
		$table = table_objet($table);
	}

	if (isset($table_traitements[$balise][$table])) {
		$traitement_existant = $table_traitements[$balise][$table];
	}

	if (!isset($traitement_existant) or (! $traitement_existant)) {
		$traitement_existant = '%s';
	}

	$interfaces['table_des_traitements'][$balise][$table] = sprintf($traitement, $traitement_existant);

	return $interfaces;
}

/**
 * Déclaration des filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *	   Déclarations d'interface pour le compilateur
 * @return array
 *	   Déclarations d'interface pour le compilateur
 */
function logos_roles_declarer_tables_interfaces($interfaces) {

	include_spip('logos_roles_fonctions');

	$suffixes = array();
	foreach (lister_roles_logos() as $role => $options) {
		$suffixes[$role] = strtoupper(substr($role, 4));
	}

	/* Pour chaque objet éditorial existant, ajouter un traitement sur
	   les logos */
	if (isset($GLOBALS['spip_connect_version'])) {
		foreach (lister_tables_objets_sql() as $table => $valeurs) {
			if ($table !== 'spip_documents') {
				foreach ($suffixes as $role => $suffixe_balise) {
					// Les balises des rôles perso ne retournent rien à la base. On
					// utilise ce traitement pour trouver le bon logo.
					$interfaces = logos_roles_ajouter_traitement_automatique(
						$interfaces,
						'trouver_logo_par_role(%s, \'' . objet_type($table) . '\', $Pile[$SP][\''.id_table_objet($table).'\'], \''.$role.'\')',
						strtoupper('LOGO_' . objet_type($table) . $suffixe_balise)
					);

					// Le massicot ne déclare lui-même que les rôles logo et
					// logo_survol. On s'en occupe ici.
					if (test_plugin_actif('massicot')
						and (! in_array($role, array('logo', 'logo_survol')))) {
						$interfaces = logos_roles_ajouter_traitement_automatique(
							$interfaces,
							'massicoter_logo(%s, \'' . objet_type($table) . '\', $Pile[$SP][\''.id_table_objet($table).'\'], \''.$role.'\')',
							strtoupper('LOGO_' . objet_type($table)) . $suffixe_balise
						);
					}

					// Forcer les dimensions du logo pour qu'elle correspondent à ce qui
					// est déclaré dans la définition du rôle
					$interfaces = logos_roles_ajouter_traitement_automatique(
						$interfaces,
						'forcer_dimensions_role(%s, \'' . objet_type($table) . '\', $Pile[$SP][\''.id_table_objet($table).'\'], \''.$role.'\')',
						strtoupper('LOGO_' . objet_type($table) . $suffixe_balise)
					);
				}
			}
		}

		// Sans oublier les balises #LOGO_SITE_SPIP…
		foreach ($suffixes as $role => $suffixe_balise) {
			// Les balises des rôles perso ne retournent rien à la base. On
			// utilise ce traitement pour trouver le bon logo.
			$interfaces = logos_roles_ajouter_traitement_automatique(
				$interfaces,
				'trouver_logo_par_role(%s, \'site\', 0, \''.$role.'\')',
				strtoupper('LOGO_SITE_SPIP' . $suffixe_balise)
			);

			// Le massicot ne déclare lui-même que les rôles logo et
			// logo_survol. On s'en occupe ici.
			if (test_plugin_actif('massicot')
				and (! in_array($role, array('logo', 'logo_survol')))) {
				$interfaces = logos_roles_ajouter_traitement_automatique(
					$interfaces,
					'massicoter_logo(%s, \'site\', 0, \''.$role.'\')',
					strtoupper('LOGO_SITE_SPIP' . $suffixe_balise)
				);
			}

			// Forcer les dimensions du logo pour qu'elle correspondent à ce qui
			// est déclaré dans la définition du rôle
			$interfaces = logos_roles_ajouter_traitement_automatique(
				$interfaces,
				'forcer_dimensions_role(%s, \'site\', 0, \''.$role.'\')',
				strtoupper('LOGO_SITE_SPIP' . $suffixe_balise)
			);
		}
	}

	return $interfaces;
}

/**
 * Ajouter les rôles qui vont bien pour les logos de documents
 *
 * On se base sur le code fourni dans le README du plugin « Rôles de documents »
 */
function logos_roles_declarer_tables_objets_sql($tables) {

	include_spip('logos_roles_fonctions');
	include_spip('base/objets');

	$noms_tables = array_map(
		'table_objet_simple',
		array_filter(array_keys($tables), 'is_string')
	);

	$roles_logos = lister_roles_logos(null, null, $noms_tables);

	if (is_array($roles_logos)) {
		$nouveaux_roles_titres = array();
		$nouveaux_roles_objets = array();

		foreach ($roles_logos as $role => $options) {
			$nouveaux_roles_titres[$role] = $options['label'];
			foreach ($options['objets'] as $objet) {
				$nouveaux_roles_objets[table_objet_simple($objet)][] = $role;
			}
		}

		foreach ($nouveaux_roles_objets as $objet => $choix) {
			$nouveaux_roles_objets[$objet] = array(
				'choix' => $choix,
				'defaut' => 'document',
				'principaux' => $choix,
			);
		}

		// anciens rôles (par défaut 'logo' et 'logo_survol' pour tous les objets)
		$anciens_roles_titres = is_array($tables['spip_documents']['roles_titres']) ?
			$tables['spip_documents']['roles_titres'] : array();
		$anciens_roles_objets = is_array($tables['spip_documents']['roles_objets']) ?
			$tables['spip_documents']['roles_objets'] : array();

		// on mélange le tout
		$roles_titres = array_merge($anciens_roles_titres, $nouveaux_roles_titres);
		$roles_objets = array_merge($anciens_roles_objets, $nouveaux_roles_objets);

		array_set_merge($tables, 'spip_documents', array(
			'roles_titres' => $roles_titres,
			'roles_objets' => $roles_objets
		));
	}

	return $tables;
}
