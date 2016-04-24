<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * @param $requete
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
 * @param $erreur
 *
 * @return mixed
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
 * @param $reponse
 * @param $contenu
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
