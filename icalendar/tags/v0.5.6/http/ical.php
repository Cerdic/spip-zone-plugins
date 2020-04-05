<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Implémentation d'un serveur REST pour iCal
 * (le vrai truc bien serait d'implémenter au moins une partie de CalDAV, mais on en est pas là)
 */ 

/**
 * Gestion des erreurs (apparemment il n'y a rien de prévu pour iCal)
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne le contenu de l'erreur à renvoyer dans la réponse
 */
function http_ical_erreur_dist($code, $requete, $reponse) {
	$reponse->setStatusCode($code);
	$reponse->setContent('');
	
	return $reponse;
}

/*
 * GET sur la racine du serveur iCal
 * http://site/http.api/ical
 */
function http_ical_get_index_dist($requete, $reponse) {
	return $reponse;
}

/*
 * GET sur une collection
 * http://site/http.api/ical/all
 */
function http_ical_get_collection_dist($requete, $reponse) {
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();
	
	// Pour l'instant on va simplement chercher un squelette du nom de la collection
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($ics = recuperer_fond("http/$format/$collection", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set('Content-Type', 'text/calendar');
		$reponse->headers->set('Content-Disposition', 'attachment; filename="'.$collection.'.ics"');
		$reponse->setContent($ics);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$fonction_erreur = charger_fonction('erreur', "http/$format/");
		$response = $fonction_erreur(404, $requete, $reponse);
	}
	
	return $reponse;
}

/*
 * GET sur une ressource
 * http://site/http.api/ical/all/123
 */
function http_ical_get_ressource_dist($requete, $reponse) {
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$ressource = $requete->attributes->get('ressource');
	
	// Quelque soit la collection, tous les événements ont le même squelette
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de l'évenement en essayant de faire au mieux
	$contexte = array(
		'id_evenement' => $ressource,
		'ressource' => $ressource,
	);
	$contexte = array_merge($requete->query->all(), $requete->attributes->all(), $contexte);
	
	if ($ics = recuperer_fond("http/$format/event", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set('Content-Type', 'text/calendar');
		$reponse->headers->set('Content-Disposition', 'attachment; filename="'.$collection.'_'.$ressource.'.ics"');
		$reponse->setContent($ics);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$fonction_erreur = charger_fonction('erreur', "http/$format/");
		$response = $fonction_erreur(404, $requete, $reponse);
	}
	
	return $reponse;
}
