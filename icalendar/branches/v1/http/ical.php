<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Implémentation d'un serveur REST pour iCal
 * (le vrai truc bien serait d'implémenter au moins une partie de CalDAV, mais on en est pas là)
 */ 


/*
 * GET sur la racine du serveur iCal
 * http://site/rest.api/ical
 */
function http_ical_get_index_dist(){
	
}

/*
 * GET sur une collection
 * http://site/rest.api/ical/all
 */
function http_ical_get_collection_dist($collection){
	// Pour l'instant on va simplement chercher un squelette du nom de la collection
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($flux = recuperer_fond("http/ical/$collection", $_GET)){
		header('Status: 200 OK');
		header("Content-type: text/calendar; charset=utf-8");
		echo $flux;
		exit;
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		header('Status: 404 Not Found');
		exit;
	}
}

/*
 * GET sur une ressource
 * http://site/rest.api/ical/patates
 */
function http_ical_get_ressource_dist($collection, $ressource){
	// Quelque soit la collection, tous les événements ont le même squelette
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de l'évenement en essayant de faire au mieux
	$contexte = array(
		'id_evenement' => $ressource,
		'ressource' => $ressource,
	);
	$contexte = array_merge($_GET, $contexte);
	
	if ($flux = recuperer_fond("http/ical/event", $contexte)){
		header('Status: 200 OK');
		header("Content-type: text/calendar; charset=utf-8");
		echo $flux;
		exit;
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		header('Status: 404 Not Found');
		exit;
	}
}

?>
