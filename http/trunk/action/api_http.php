<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_once _DIR_PLUGIN_HTTP.'vendor/autoload.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Porte d'entrée des API lecture/écriture orientés REST
 * 
 * Cette action gère donc la partie "serveur HTTP" en redirigeant les méthodes (GET, PUT, etc) vers les fonctions spécifiques
 *
 * http://www.site.tld/http.api/atom/patates => feed, GET=liste (critères possibles), POST=création
 * http://www.site.tld/http.api/atom/patates/1234 => entry, GET=lecture, PUT=mise à jour
 */
function action_api_http_dist(){
	// On crée les informations sur la requête cliente
	$requete = Request::createFromGlobals();
	// On crée déjà la réponse, mais totalement vide, qui sera modifiée et remplie au fil du temps
	$reponse = new Response();
	
	// On passe dans un pipeline avant toute vraie requête à SPIP
	list($requete, $reponse) = pipeline(
		'http_pre_requete',
		array($requete, $reponse)
	);
	
	// Il faut au moins le format dans l'argument sinon rien
	if (!$arg = $requete->query->get('arg')){
		$reponse->setStatusCode('404');
	}
	else{
		// On récupère les trois informations possibles, seul $format est obligatoire :
		// http.api/atom
		// http.api/atom/patates
		// http.api/atom/patates/1234
		list($format, $collection, $ressource) = explode('/', $arg);
		define('_SET_HTML_BASE', true);
		
		// Si le format n'a pas le bon format, on arrête
		if (!preg_match('/^[\w]+$/', $format)){
			$reponse->setStatusCode('404');
		}
		else{
			// On garde en mémoire dans la requête les infos trouvées précédemment
			$requete->attributes->add(array(
				'format' => $format,
				'collection' => $collection,
				'ressource' => $ressource,
			));
			$methode = $requete->getMethod();
			
			// Avec le format, on cherche une fonction d'erreur propre au format, qui est obligatoire
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			
			// On cherche ce qu'on est en train de demander avec cette URL
			// S'il n'y a pas de collection c'est l'index
			if (!$collection){
				$type_reponse = 'index';
			}
			// Sinon s'il n'y a que la collection sans la ressource
			elseif (!$ressource){
				$type_reponse = 'collection';
			}
			// Sinon c'est une ressource
			else{
				$type_reponse = 'ressource';
			}
			
			// Le GET peut se faire sur : la racine du serveur, une collection, une ressource
			if (
				$methode == 'GET'
				and $fonction = charger_fonction("get_$type_reponse", "http/$format/", true) // http_atom_get_XXX()
			){
				// Si on a l'autorisation, on lance la fonction trouvée
				if (autoriser("get_$type_reponse", $collection, $ressource)){ // autoriser_patates_get_XXX_dist()
					$reponse = $fonction($requete, $reponse);
				}
				// Sinon on lève une 401
				else{
					$reponse = $fonction_erreur(401,$requete, $reponse);
				}
			}
			// Pour le POST, on ne gère que sur une collection (à voir si des gens ont des cas particuliers qui nécessiteraient plus...)
			elseif (
				$methode == 'POST'
				and $type_reponse == 'collection'
				and $fonction = charger_fonction("post_$type_reponse", "http/$format/", true)
			){
				// Si on a l'autorisation, on lance la fonction trouvée
				if (autoriser("post_$type_reponse", $collection, $ressource)){ // autoriser_patates_post_collection_dist()
					$reponse = $fonction($requete, $reponse);
				}
				// Sinon on lève une 401
				else{
					$reponse = $fonction_erreur(401,$requete, $reponse);
				}
			}
			// Pour le PUT, on ne gère que sur une ressource (pareil, à voir s'il faut quand même…)
			elseif (
				$methode == 'PUT'
				and $type_reponse == 'ressource'
				and $fonction = charger_fonction("put_$type_reponse", "http/$format/", true)
			){
				// Si on a l'autorisation, on lance la fonction trouvée
				if (autoriser("put_$type_reponse", $collection, $ressource)){ // autoriser_patates_put_ressource_dist()
					$reponse = $fonction($requete, $reponse);
				}
				// Sinon on lève une 401
				else{
					$reponse = $fonction_erreur(401,$requete, $reponse);
				}
			}
			// Pour le DELETE, on ne gère que sur une ressource
			elseif (
				$methode == 'DELETE'
				and $type_reponse == 'ressource'
				and $fonction = charger_fonction("delete_$type_reponse", "http/$format/", true)
			){
				// Si on a l'autorisation, on lance la fonction trouvée
				if (autoriser("delete_$type_reponse", $collection, $ressource)){ // autoriser_patates_delete_ressource_dist()
					$reponse = $fonction($requete, $reponse);
				}
				// Sinon on lève une 401
				else{
					$reponse = $fonction_erreur(401,$requete, $reponse);
				}
			}
			// Si on a trouvé aucune fonction correspondant aux paramètres, ça n'existe pas
			else{
				$reponse = $fonction_erreur(404, $requete, $reponse);
			}
		}
	}
	
	// On le passe tout ça dans un pipeline avant de retourner la réponse
	$reponse = pipeline(
		'http_final',
		array(
			'args' => array(
				'requete' => $requete,
			),
			'data' => $reponse,
		)
	);

	//  Enfin, s'il n'y a pas eu d'exit en amont, on envoie la réponse
	$reponse->prepare($requete);
	$reponse->send();
}
