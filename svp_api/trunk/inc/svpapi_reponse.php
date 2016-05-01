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
 * Initialise le contenu d'une réponse qui se présente comme un tableau associatif.
 * En particulier, la fonction stocke les éléments de la requête, positionne le bloc d'erreur
 * par défaut à ok, et récupère le schéma de données du plugin SVP.
 *
 * @param Symfony\Component\HttpFoundation\Request $requete
 *      Objet requête fourni par le plugin Serveur HTTP abstrait.
 *
 * @return array
 *      Le contenu d'une réponse de l'API SVP est un tableau associatif à 4 entrées:
 *      - `requete` : sous-tableau des éléments de la requête
 *      - `erreur`  : sous-tableau des éléments descriptifs d'une erreur (status 200 par défaut)
 *      - `schema`  : la version du schéma du plugin SVP hébergé par le serveur
 *      - `donnees` : le tableau des objets demandés fonction de la requête (vide)
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
	$demande['format_contenu'] = isset($parametres['format']) ? $parametres['format'] : 'json';

	// Initialisation du bloc d'erreur à ok par défaut
	$erreur['status'] = 200;
	$erreur['type'] = 'ok';
	$erreur['element'] = '';
	$erreur['valeur'] = '';
	$erreur['title'] = _T('svpapi:erreur_200_ok_titre');
	$erreur['detail'] = _T('svpapi:erreur_200_ok_message');

	// On intitialise le contenu avec les informations collectées.
	// A noter que le format de sortie est initialisé par défaut à json indépendamment de la demande, ce qui permettra
	// en cas d'erreur sur le format demandé dans la requête de renvoyer une erreur dans un format lisible.
	$contenu = array(
		'requete' => $demande,
		'erreur'  => $erreur,
		'schema'  => $schema,
		'donnees' => array()
	);

	return $contenu;
}


/**
 * Récupère la liste des plugins de la table spip_plugins éventuellement filtrés par les critères
 * additionnels positionnés dans la requête.
 * Les plugins fournis sont toujours issus d'un dépôt hébergé par le serveur ce qui exclu les plugins
 * installés sur le serveur et non liés à un dépôt (par exemple un zip personnel).
 * Chaque objet plugin est présenté comme un tableau dont tous les champs sont accessibles comme un
 * type PHP simple, entier, chaine ou tableau.
 *
 * @uses normaliser_champs()
 *
 * @param array $where
 *      Tableau des critères additionnels à appliquer au select.
 *
 * @return array
 *      Tableau des plugins dont l'index est le préfixe du plugin.
 *      Les champs de type id ou maj ne sont pas renvoyés.
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
 * Récupère la liste des dépôts hébergés par le serveur.
 * Contrairement aux plugins et paquets les champs d'un dépôt ne nécessitent aucun formatage.
 *
 * @param array $where
 *      Tableau des critères additionnels à appliquer au select (non utilisé).
 *
 * @return array
 *      Tableau des dépôts.
 *      Les champs de type id ou maj ne sont pas renvoyés.
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
function normaliser_champs($type_objet, $objet) {

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

/**
 * Complète le bloc d'erreur avec le titre et l'explication de l'erreur.
 *
 * @param array $erreur
 * 		Tableau initialisé avec les éléments de base de l'erreur (`status`, `type`, `element` et `valeur`).
 *
 * @return array
 * 		Tableau de l'erreur complété avec le titre (index `title`) et le descriptif (index `detail`).
 */
function reponse_expliquer_erreur($erreur) {

	$prefixe = 'svpapi:erreur_' . $erreur['status'] . '_' . $erreur['type'];
	$parametres = array(
		'element' => $erreur['element'],
		'valeur'  => $erreur['valeur']
	);

	$erreur['title'] = _T("${prefixe}_titre", $parametres);
	$erreur['detail'] = _T("${prefixe}_message", $parametres);

	return $erreur;
}


/**
 * Finalise la réponse à la requête en complétant le header et le contenu mis au préalable
 * au format demandé.
 *
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse tel qu'initialisé par le serveur HTTP abstrait.
 * @param array                                     $contenu
 * 		Tableau du contenu de la réponse qui sera retourné selon le format défini.
 * @param string                                    $format_reponse
 * 		Format de la réponse. Seul le format JSON est supporté.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Retourne l'objet réponse dont le contenu et certains attributs du header sont mis à jour.
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
