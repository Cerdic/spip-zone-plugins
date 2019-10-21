<?php
/**
 * Ce fichier contient la fonction de déclaration des configurations de collections de l'api `ezcollection`.
 * Elle appelle le pipeline `liste_ezcollection` pour les plugins qui le souhaitent.
 *
 * @package SPIP\EZCOLLECTION\COLLECTION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Déclare les collections accessibles via HTTP GET.
 * Par défaut, le plugin ne propose aucune collection.
 *
 * @pipeline liste_ezcollection
 *
 * @return array Description des collections.
**/
function inc_ezcollection_declarer_dist() {

	// Initialisation en static pour les performances du tableau de toutes les collections
	static $collections = array();

	if (empty($collections)) {
		// Les tableaux sont de la forme [plugin][collection] => configuration de la collection.
		// Un configuration est un tableau au format suivant :
		//

		// Le plugin REST Factory ne fournit auciune collection par défaut. Il convient à chaque plugin utilisateur
		// de fournir ses configurations.
		$collections = pipeline('liste_ezcollection', $collections);
	}

	return $collections;
}
