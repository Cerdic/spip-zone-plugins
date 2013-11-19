<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Implémentation d'un serveur REST pour APP (AtomPub)
 */ 

/**
 * Rien, car en Atom il n'y a malheureusement pas de gestion des erreurs pour l'instant
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne une chaîne vide
 */
function http_atom_erreur_dist($code, $requete, $reponse){
	$reponse->setStatusCode($code);
	$reponse->setContent('');
	return $reponse;
}

/*
 * GET sur une collection
 * http://site/http.api/atom/patates
 */
function http_atom_get_collection_dist($requete, $reponse){
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();
	
	// Pour l'instant on va simplement chercher un squelette du nom de la collection
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($flux = recuperer_fond("http/atom/$collection", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set('Content-Type', 'application/atom+xml');
		$reponse->setContent($flux);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$reponse->setStatusCode(404);
	}
	
	return $reponse;
}

/*
 * GET sur une ressource
 * http://site/http.api/atom/patates/1234
 */
function http_atom_get_ressource_dist($requete, $reponse){
	// Pour l'instant on va simplement chercher un squelette du nom de la ressource
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de la ressource en essayant de faire au mieux
	include_spip('base/objets');
	$collection = $requete->attributes->get('collection');
	$ressource = $requete->attributes->get('ressource');
	$cle = id_table_objet($collection);
	$contexte = array(
		$cle => $ressource,
		'ressource' => $ressource,
	);
	$contexte = array_merge($requete->query->all(), $contexte);
	
	if ($flux = recuperer_fond("http/atom/$collection-ressource", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set('Content-Type', 'application/atom+xml');
		$reponse->setContent($flux);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$reponse->setStatusCode(404);
	}
	
	return $reponse;
}

