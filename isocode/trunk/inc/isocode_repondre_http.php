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

/**
 * Initialise le contenu d'une réponse qui se présente comme un tableau associatif.
 * En particulier, la fonction stocke les éléments de la requête, positionne le bloc d'erreur
 * par défaut à ok, et récupère le schéma de données lié au plugin.
 *
 * @param Symfony\Component\HttpFoundation\Request $requete
 *      Objet requête fourni par le plugin Serveur HTTP abstrait.
 *
 * @return array
 *      Le contenu d'une réponse de l'API SVP est un tableau associatif à 5 entrées:
 *      - `requete` : sous-tableau des éléments de la requête
 *      - `erreur`  : sous-tableau des éléments descriptifs d'une erreur (status 200 par défaut)
 *      - `schema`  : la version du schéma du plugin SVP hébergé par le serveur
 *      - `version` : la version du plugin SVP API HTTP fonctionnant sur le serveur
 *      - `donnees` : le tableau des objets demandés fonction de la requête (vide)
 */
function reponse_isocode_initialiser_contenu($requete) {

	// Récupération du schéma de données de SVP et de la version du plugin SVP API HTTP
	include_spip('inc/config');
	$schema = lire_config('isocode_base_version');
	include_spip('inc/filtres');
	$informer = charger_filtre('info_plugin');
	$version = $informer('isocode', 'version', true);

	// Stockage des éléments de la requête
	// -- La méthode
	$demande = array('methode' => $requete->getMethod());
	// -- Les éléments format, collection et ressource
	$demande = array_merge($demande, $requete->attributes->all());
	// -- Les critères de filtre comme la catégorie ou la compatibilité SPIP fournis comme paramètres de l'url.
	//    Si on utilise une URL classique avec spip.php il faut exclure certains paramètres.
	$demande['filtres'] = $requete->query->all();
	$demande['filtres'] = array_diff_key(
		$demande['filtres'],
		array_flip(array('action', 'arg', 'lang', 'var_zajax'))
	);
	// -- Le format du contenu de la réponse est toujours le JSON
	$demande['format_contenu'] = 'json';

	// Initialisation du bloc d'erreur à ok par défaut
	$erreur['status'] = 200;
	$erreur['type'] = 'ok';
	$erreur['element'] = '';
	$erreur['valeur'] = '';
	$erreur['title'] = _T('isocode:erreur_200_ok_titre');
	$erreur['detail'] = _T('isocode:erreur_200_ok_message');

	// On intitialise le contenu avec les informations collectées.
	// A noter que le format de sortie est initialisé par défaut à json indépendamment de la demande, ce qui permettra
	// en cas d'erreur sur le format demandé dans la requête de renvoyer une erreur dans un format lisible.
	$contenu = array(
		'requete' => $demande,
		'erreur'  => $erreur,
		'schema'  => $schema,
		'version' => $version,
		'donnees' => array()
	);

	return $contenu;
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
function reponse_isocode_expliquer_erreur($erreur, $collection) {

	// Calcul des paramètres qui seront passés à la fonction de traduction.
	// -- on passe toujours la collection qui est vide uniquement pour l'erreur de serveur.
	$parametres = array(
		'element'    => $erreur['element'],
		'valeur'     => $erreur['valeur'],
		'collection' => $collection
	);
	// -- on complète avec une chaine extra si elle existe que l'on supprime ensuite comme index de la réponse.
	if (isset($erreur['extra'])) {
		$parametres['extra'] = $erreur['extra'];
		unset($erreur['extra']);
	}

	// Traduction du libellé de l'erreur et du message complémentaire.
	$prefixe = 'svpapi:erreur_' . $erreur['status'] . '_' . $erreur['type'];
	$erreur['title'] = _T("${prefixe}_titre", $parametres);
	$erreur['detail'] = _T("${prefixe}_message", $parametres);

	return $erreur;
}


/**
 * Finalise la réponse à la requête en complétant le header et le contenu mis au préalable
 * au format JSON.
 *
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse tel qu'initialisé par le serveur HTTP abstrait.
 * @param array                                     $contenu
 * 		Tableau du contenu de la réponse qui sera retourné en JSON.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Retourne l'objet réponse dont le contenu et certains attributs du header sont mis à jour.
 */
function reponse_isocode_construire($reponse, $contenu) {

	// Charset UTF-8 et statut de l'erreur.
	$reponse->setCharset('utf-8');
	$reponse->setStatusCode($contenu['erreur']['status']);

	// Format JSON exclusif pour les réponses.
	$reponse->headers->set('Content-Type', 'application/json');
	$reponse->setContent(json_encode($contenu));

	return $reponse;
}
