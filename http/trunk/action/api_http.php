<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Porte d'entrée des API lecture/écriture orientés REST
 * Cette action gère donc la partie "serveur HTTP" en redirigeant les méthodes (GET, PUT, etc) vers les fonctions spécifiques
 *
 * http://www.site.tld/http.api/atom/patates => feed, GET=liste (critères possibles), POST=création
 * http://www.site.tld/http.api/atom/patates/1234 => entry, GET=lecture, PUT=mise à jour
 */
function action_api_http_dist(){	
	// Il faut au moins le format dans l'argument sinon rien
	if (!$arg = _request('arg')){
		header('Status: 404 Not Found');
		exit;
	}
	else{
		list($format, $collection, $ressource) = explode('/', $arg);
		define('_SET_HTML_BASE', true);
		
		// Si le format n'a pas le bon format ou que le fichier avec l'implémentation n'existe pas, on arrête
		if (!preg_match('/^[\w]+$/', $format)){
			header('Status: 404 Not Found');
			exit;
		}
		
		$methode = $_SERVER["REQUEST_METHOD"];
		
		// Si on est dans une méthode où il FAUT poster quelque chose
		if (in_array($methode, array('POST', 'PUT', 'PATCH'))){
			// On récupère le contenu
			$contenu = trim(file_get_contents("php://input"));
		}
		
		// On cherche ce qu'on est en train de demander avec cette URL
		// S'il n'y a pas de collection c'est l'index
		if (!$collection){
			$type = 'index';
		}
		// Sinon s'il n'y a que la collection sans la ressource
		elseif (!$ressource){
			$type = 'collection';
		}
		// Sinon c'est une ressource
		else{
			$type = 'ressource';
		}

		// Le GET peut se faire sur : la racine du serveur, une collection, une ressource
		if ($methode == 'GET'
			and $fonction = charger_fonction("get_$type", "http/$format/", true) // http_atom_get_index()
		){
			// On teste l'autorisation sinon 401
			if (
				autoriser("get_$type", $collection, $ressource) // autoriser_patates_get_collection_dist()
			){
				$fonction($collection, $ressource);
			}
			else{
				header('Status: 401 Unauthorized');
				exit;
			}
		}
		// Si la fonction n'existe pas ça n'existe pas
		else{
			header('Status: 404 Not Found');
			exit;
		}
	}
}

?>
