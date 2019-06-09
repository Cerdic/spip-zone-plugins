<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions de construction du contenu des réponses aux
 * requête à l'API SVP.
 *
 * @package SPIP\SVPAPI\REPONSE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


if (!defined('_SVPAPI_CHAMPS_MULTI_PLUGIN')) {
	/**
	 * Liste des champs de l'objet plugin contenant un texte multi.
	 */
	define('_SVPAPI_CHAMPS_MULTI_PLUGIN', 'nom,slogan');
}
if (!defined('_SVPAPI_CHAMPS_SERIALISES_PLUGIN')) {
	/**
	 * Liste des champs de l'objet plugin contenant un tableau sérialisé.
	 */
	define('_SVPAPI_CHAMPS_SERIALISES_PLUGIN', '');
}
if (!defined('_SVPAPI_CHAMPS_VERSION_PLUGIN')) {
	/**
	 * Liste des champs de l'objet plugin contenant un numéro de version pouvant être
	 * normalisé (exemple: 012.001.023 au lieu de 12.1.23).
	 */
	define('_SVPAPI_CHAMPS_VERSION_PLUGIN', 'vmax');
}
if (!defined('_SVPAPI_CHAMPS_LISTE_PLUGIN')) {
	/**
	 * Liste des champs de l'objet plugin contenant un texte au format liste dont chaque
	 * élément est séparé par une virgule.
	 */
	define('_SVPAPI_CHAMPS_LISTE_PLUGIN', 'branches_spip,tags');
}

if (!defined('_SVPAPI_CHAMPS_MULTI_PAQUET')) {
	/**
	 * Liste des champs de l'objet paquet contenant un texte multi.
	 */
	define('_SVPAPI_CHAMPS_MULTI_PAQUET', 'description');
}
if (!defined('_SVPAPI_CHAMPS_SERIALISES_PAQUET')) {
	/**
	 * Liste des champs de l'objet paquet contenant un tableau sérialisé.
	 */
	define('_SVPAPI_CHAMPS_SERIALISES_PAQUET', 'auteur,credit,licence,copyright,dependances,procure,traductions');
}
if (!defined('_SVPAPI_CHAMPS_VERSION_PAQUET')) {
	/**
	 * Liste des champs de l'objet paquet contenant un numéro de version pouvant être
	 * normalisé (exemple: 012.001.023 au lieu de 12.1.23).
	 */
	define('_SVPAPI_CHAMPS_VERSION_PAQUET', 'version, version_base');
}
if (!defined('_SVPAPI_CHAMPS_LISTE_PAQUET')) {
	/**
	 * Liste des champs de l'objet paquet contenant un texte au format liste dont chaque
	 * élément est séparé par une virgule.
	 */
	define('_SVPAPI_CHAMPS_LISTE_PAQUET', 'branches_spip');
}


/**
 * Transforme, pour un objet plugin ou paquet, les champs sérialisés, multi et liste (chaine d'éléments séparés
 * par une virgule) en tableau et supprime des champs de type version les 0 à gauche des numéros.
 *
 * @uses normaliser_multi()
 * @uses denormaliser_version()
 *
 * @param string $type_objet
 * 		Type d'objet à normaliser, soit `plugin` ou `paquet`.
 * @param array  $objet
 * 		Tableau des champs de l'objet `plugin` ou `paquet` à normaliser.
 *
 * @return array
 * 		Tableau des champs de l'objet `plugin` ou `paquet` normalisés.
 */
function inc_normaliser_champs_dist($type_objet, $objet) {

	$objet_normalise = $objet;

	// Traitement des champs multi et sérialisés
	$champs_multi = explode(',', constant('_SVPAPI_CHAMPS_MULTI_' . strtoupper($type_objet)));
	$champs_serialises = explode(',', constant('_SVPAPI_CHAMPS_SERIALISES_' . strtoupper($type_objet)));
	$champs_version = explode(',', constant('_SVPAPI_CHAMPS_VERSION_' . strtoupper($type_objet)));
	$champs_liste = explode(',', constant('_SVPAPI_CHAMPS_LISTE_' . strtoupper($type_objet)));

	if ($objet) {
		include_spip('plugins/preparer_sql_plugin');
		include_spip('svp_fonctions');
		foreach ($objet as $_champ => $_valeur) {
			if (in_array($_champ, $champs_multi)) {
				// Passer un champ multi en tableau indexé par la langue
				$objet_normalise[$_champ] = normaliser_multi($_valeur);
			}

			if (in_array($_champ, $champs_serialises)) {
				// Désérialiser un champ sérialisé
				$objet_normalise[$_champ] = unserialize($_valeur);
			}

			if (in_array($_champ, $champs_version)) {
				// Retourne la chaine de la version x.y.z sous sa forme initiale, sans
				// remplissage à gauche avec des 0.
				$objet_normalise[$_champ] = denormaliser_version($_valeur);
			}

			if (in_array($_champ, $champs_liste)) {
				// Passer une chaine liste en tableau
				$objet_normalise[$_champ] = $_valeur ? explode(',', $_valeur) : array();
			}
		}
	}

	return $objet_normalise;
}
