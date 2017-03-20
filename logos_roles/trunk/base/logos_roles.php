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
	foreach (lister_logos_roles() as $role => $nom_role) {
		$suffixes[$role] = strtoupper(substr($role, 4));
	}

	/* Pour chaque objet éditorial existant, ajouter un traitement sur
	   les logos */
	if (isset($GLOBALS['spip_connect_version'])) {
		foreach (lister_tables_objets_sql() as $table => $valeurs) {
			if ($table !== 'spip_documents') {
				foreach ($suffixes as $role => $suffixe_balise) {

					$interfaces = logos_roles_ajouter_traitement_automatique(
						$interfaces,
						'trouver_logo_par_role(%s, '.objet_type($table).', $Pile[1][\''.id_table_objet($table).'\'], '.$role.')',
						strtoupper('LOGO_'.objet_type($table) . $suffixe_balise)
					);
				}
			}
		}
	}

	return $interfaces;
}