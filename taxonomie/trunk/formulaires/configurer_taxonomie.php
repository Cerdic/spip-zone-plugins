<?php
/**
 * Gestion du formulaire de configuration du plugin
 *
 * @package SPIP\TAXONOMIE\CONFIGURATION
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire propose la liste des langues possibles.
 * L'utilisateur doit cocher les langues qu'il souhaite utiliser parmi les langues possibles.
 *
 * @return array
 * 		Tableau des données à charger par le formulaire (affichage ou données de configuration).
 * 		- `_langues`			: (affichage) codes de langue et libellés des langues possibles.
 * 		- `langues_utilisees`	: (configuration) la liste des langues utilisées. Par défaut, le plugin
 * 								  propose la langue française.
 */
function formulaires_configurer_taxonomie_charger() {
	$valeurs = array();

	// Liste des langues possibles gérées par le plugin
	include_spip('inc/taxonomie');
	$langues_possibles = explode(':', _TAXONOMIE_LANGUES_POSSIBLES);
	foreach ($langues_possibles as $_code_langue) {
		$valeurs['_langues'][$_code_langue] = traduire_nom_langue($_code_langue);
	}

	// Liste des langues réellement utilisées
	$valeurs['langues_utilisees'] = lire_config('taxonomie/langues_utilisees');

	return $valeurs;
}

/**
 * Vérification des saisies : il est indispensable de choisir au moins une langue.
 *
 * @return array
 * 		Tableau des erreurs d'absence de langue saisie ou tableau vide si aucune erreur.
 */
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