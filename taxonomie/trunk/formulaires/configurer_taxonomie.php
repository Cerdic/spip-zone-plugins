<?php
/**
 * Fonctions CVT du formulaire de configuration du plugin
 *
 * @package SPIP\TAXONOMIE\ADMINISTRATION
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_taxonomie_charger() {
	$valeurs = array();

	// Liste des langues possibles gérées par le plugin
	$langues_possibles = lire_config('taxonomie/langues_possibles');
	foreach ( $langues_possibles as $_code_langue) {
		$valeurs['_langues'][$_code_langue] = traduire_nom_langue($_code_langue);
	}

	// Liste des langues réellement utilisées
	$valeurs['langues_utilisees'] = lire_config('taxonomie/langues_utilisees');

	return $valeurs;
}

function formulaires_configurer_taxonomie_verifier() {
	$erreurs = array();

	$obligatoires = array('langues_utilisees');
	foreach ($obligatoires as $_obligatoire) {
		if (!_request($_obligatoire))
			$erreurs[$_obligatoire] = _T('info_obligatoire');
	}

	return $erreurs;
}

?>