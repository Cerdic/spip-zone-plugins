<?php
/**
 * Gestion du formulaire de configuration du plugin
 *
 * @package SPIP\TAXONOMIE\ADMINISTRATION
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Chargement des données : le formulaire propose la liste des boussoles accessibles
 * à partir des serveurs que le site client a déclaré.
 *
 * @uses lister_rangs()
 *
 * @return array
 * 		le tableau des données à charger par le formulaire :
 *
 * 		- 'boussole' : l'alias de la boussole choisie
 * 		- '_boussoles' : la liste des boussoles accessibles
 * 		- 'message_erreur' : message d'erreur éventuel retourné par un des serveurs interrogés
 * 		- 'editable' : booleen à false si une erreur est survenue
 */
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

/**
 * Vérification des saisies : aucune nécessaire, le formulaire ne proposnt que des boutons
 * radio dont un est toujours actif.
 *
 * @return array
 * 		Tableau des erreur toujours vide.
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