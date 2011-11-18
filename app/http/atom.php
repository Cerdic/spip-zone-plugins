<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Implémentation d'un serveur REST pour APP (AtomPub)
 */ 


/*
 * GET sur la racine du serveur Atom
 * http://site/rest.api/atom
 */
function http_atom_get_index_dist(){
	
}

/*
 * GET sur une collection
 * http://site/rest.api/atom/patates
 */
function http_atom_get_collection_dist($collection){
	// Pour l'instant on va simplement chercher un squelette du nom de la collection
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($flux = recuperer_fond("http/atom/$collection", $_GET)){
		header('Status: 200 OK');
		header("Content-type: application/atom+xml; charset=utf-8");
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
