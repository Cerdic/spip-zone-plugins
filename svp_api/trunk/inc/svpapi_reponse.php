<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


if (!defined('_SVPAPI_CHAMPS_MULTI_PLUGIN')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_MULTI_PLUGIN', 'nom,slogan');
}
if (!defined('_SVPAPI_CHAMPS_SERIALISES_PLUGIN')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_SERIALISES_PLUGIN', '');
}
if (!defined('_SVPAPI_CHAMPS_VERSION_PLUGIN')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_VERSION_PLUGIN', 'vmax');
}

if (!defined('_SVPAPI_CHAMPS_MULTI_PAQUET')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_MULTI_PAQUET', 'description');
}
if (!defined('_SVPAPI_CHAMPS_SERIALISES_PAQUET')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_SERIALISES_PAQUET', 'auteur,credit,licence,copyright,dependances,procure,traductions');
}
if (!defined('_SVPAPI_CHAMPS_VERSION_PAQUET')) {
	/**
	 *
	 */
	define('_SVPAPI_CHAMPS_VERSION_PAQUET', 'version, version_base');
}

/**
 * @param Symfony\Component\HttpFoundation\Request	$requete
 *
 * @return array
 */
function reponse_initialiser_contenu($requete) {

	// Récupération du schéma de données de SVP
	include_spip('inc/config');
	$schema = lire_config('svp_base_version');

	// Stockage des éléments de la requête
	// -- La méthode
	$demande = array('methode' => $requete->getMethod());
	// -- Les éléments format, collection et ressource
	$demande = array_merge($demande, $requete->attributes->all());
	// -- Les critères additionnels comme la catégorie ou la compatibilité SPIP fournis comme paramètres de l'url
	$parametres = $requete->query->all();
	$demande['criteres'] = array_intersect_key($parametres, array_flip(array('categorie', 'compatible_spip')));
	// -- Le format du contenu de la réponse fourni comme paramètre de l'url
	$demande['format'] = isset($parametres['format']) ? $parametres['format'] : 'json';

	// Initialisation du bloc d'erreur à ok par défaut
	$erreur['status'] = 200;
	$erreur['type'] = '';

	// On intitialise le contenu avec les informations collectées.
	// A noter que le format de sortie est initialisé par défaut à json indépendamment de la demande, ce qui permettra
	// en cas d'erreur sur le format demandé dans la requête de renvoyer une erreur dans un format lisible.
	$contenu = array(
		'requete'	=> $demande,
		'erreur'	=> $erreur,
		'schema' 	=> $schema,
		'items'		=> array());

	return $contenu;
}


/**
 * @param array	$where
 *
 * @return array
 */
function reponse_collectionner_plugins($where) {

	// Initialisation de la collection
	$plugins = array();

	// Récupérer la liste des plugins (filtrée ou pas).
	// Les plugins appartiennent forcément à un dépot logique installés sur le serveur. Les plugins
	// installés directement sur le serveur, donc hors dépôt sont exclus.
	$from = array('spip_plugins', 'spip_depots_plugins AS dp');
	$select = array('*');
	$where = array_merge(array('dp.id_depot>0', 'dp.id_plugin=spip_plugins.id_plugin'), $where);
	$group_by = array('spip_plugins.id_plugin');
	$collection = sql_allfetsel($select, $from, $where, $group_by);

	// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par les préfixes.
	// On transforme les champs multi en tableau associatif indexé par la langue et on désérialise les
	// champs sérialisés.
	if ($collection) {
		foreach ($collection as $_plugin) {
			unset($_plugin['id_plugin']);
			unset($_plugin['id_depot']);
			$plugins[$_plugin['prefixe']] = normaliser_champs('plugin', $_plugin);
		}
	}

	return $plugins;
}


/**
 * @param array	$where
 *
 * @return array
 */
function reponse_collectionner_depots($where) {

	// Initialisation de la collection
	$depots = array();

	// Récupérer la liste des dépôts
	$from = array('spip_depots');
	$select = array('*');
	$collection = sql_allfetsel($select, $from, $where);

	// Refactorer le tableau de sortie du allfetsel en supprimant
	// les champs id_depot et maj.
	if ($collection) {
		foreach ($collection as $_depot) {
			unset($_depot['id_depot']);
			unset($_depot['maj']);
			$depots[] = $_depot;
		}
	}

	return $depots;
}


/**
 * @param string	$type_objet
 * @param array		$objet
 *
 * @return array
 */
function normaliser_champs($type_objet, $objet) {

	$objet_normalise = $objet;

	// Traitement des champs multi et sérialisés
	$champs_multi = explode(',', constant('_SVPAPI_CHAMPS_MULTI_' . strtoupper($type_objet)));
	$champs_serialises = explode(',', constant('_SVPAPI_CHAMPS_SERIALISES_' . strtoupper($type_objet)));
	$champs_version = explode(',', constant('_SVPAPI_CHAMPS_VERSION_' . strtoupper($type_objet)));

	if ($objet) {
		include_spip('plugins/preparer_sql_plugin');
		include_spip('svp_fonctions');
		foreach($objet as $_champ => $_valeur) {
			if (in_array($_champ, $champs_multi)) {
				$objet_normalise[$_champ] = normaliser_multi($_valeur);
			}

			if (in_array($_champ, $champs_serialises)) {
				$objet_normalise[$_champ] = unserialize($_valeur);
			}

			if (in_array($_champ, $champs_version)) {
				$objet_normalise[$_champ] = denormaliser_version($_valeur);
			}
		}
	}

	return $objet_normalise;
}

/**
 * @param array	$erreur
 *
 * @return array
 */
function reponse_expliquer_erreur($erreur) {

	$prefixe = 'svpapi:erreur_' . $erreur['status'] . '_' . $erreur['type'];
	$parametres = array(
		'element'	=> $erreur['element'],
		'valeur'	=> $erreur['valeur']
		);

	$explication['title'] = _T("${prefixe}_titre", $parametres);
	$explication['detail'] = _T("${prefixe}_message", $parametres);

	return $explication;
}


/**
 * @param Symfony\Component\HttpFoundation\Response	$reponse
 * @param array										$contenu
 * @param string									$format_reponse
 *
 * @return mixed
 */
function reponse_construire($reponse, $contenu, $format_reponse) {

	$reponse->setCharset('utf-8');
	$reponse->setStatusCode($contenu['erreur']['status']);

	if ($format_reponse == 'json') {
		$reponse->headers->set('Content-Type', 'application/json');
		$reponse->setContent(json_encode($contenu));
	}

	return $reponse;
}
