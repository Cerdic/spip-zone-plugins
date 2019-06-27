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
function http_ical_erreur_dist($code, $requete, $reponse){
	$reponse->setStatusCode($code);
	$reponse->setContent('');
	return $reponse;
}

/*
 * GET sur une collection
 * http://site/http.api/ical/all
 */
function http_ical_get_collection_dist($requete, $reponse){
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();
	
	// Pour l'instant on va simplement chercher un squelette du nom de la collection
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($flux = recuperer_fond("http/ical/$collection", $contexte)){
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
 * http://site/http.api/ical/event
 */
 
function http_ical_get_ressource_dist($requete, $reponse){

	// Quelque soit la collection, tous les événements ont le même squelette
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de l'évenement en essayant de faire au mieux
	$ressource = $requete->attributes->get('ressource');

	// Quelque soit la collection, tous les événements ont le même squelette
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de l'évenement en essayant de faire au mieux
	$contexte = array(
		'id_evenement' => $ressource,
		'ressource' => $ressource,
	);
	$contexte = array_merge($requete->query->all(), $contexte);
	
	if ($flux = recuperer_fond("http/ical/event", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set("Content-type: text/calendar; charset=utf-8");
		$reponse->setContent($flux);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$reponse->setStatusCode(404);
	}
	
	return $reponse;
}

