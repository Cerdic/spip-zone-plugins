<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Définition du critère {archive} et {!archive} plus pratique à utiliser que {est_archive=1} ou
 * {est_archive=0}.
 *
 */
function critere_archive_dist($idb, &$boucles, $critere) {

	// Initialisation de la table sur laquelle porte le critère
	include_spip('base/objets');
	$boucle = &$boucles[$idb];
	$table = table_objet_sql($boucle->id_table);

	// Vérifier que la table fait bien partie de la liste autorisée à utiliser l'archivage.
	include_spip('inc/config');
	$tables_autorisees = lire_config('archobjet/objets_archivables', array());
	if (in_array($table, $tables_autorisees)) {
		// Définition de la valeur du champ est_archive en fonction de l'existence du not ou pas
		$valeur = ($critere->not == '!') ? 0 : 1;
		$champ = 'est_archive';

		// Création du critère sur le champ 'est_archive'.
		$boucle->where[] = array("'='", "'$champ'", $valeur);
	}
}
