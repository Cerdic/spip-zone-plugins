<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Contenu collection+json d'une erreur
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne le contenu de l'erreur à renvoyer dans la réponse
 */
function http_collectionjson_erreur_dist($code, $requete, $reponse){
	$reponse->setStatusCode($code);
	$erreur = array('code' => "$code");
	
	switch ($code){
		case '401':
			$erreur['title'] = _T('http:erreur_401_titre');
			$erreur['message'] = _T('http:erreur_401_message');
			break;
		case '404':
			$erreur['title'] = _T('http:erreur_404_titre');
			$erreur['message'] = _T('http:erreur_404_message');
			break;
		default:
			$erreur = false;
	}
	
	// Si on reconnait une erreur on l'encapsule dans une collection avec erreur
	if ($erreur){
		include_spip('inc/filtres');
		$reponse->headers->set('Content-Type', 'application/json');
		$reponse->setContent(json_encode(array(
			'collection' => array(
				'version' => '1.0',
				'href' => url_absolue(self()),
				'error' => $erreur,
			),
		)));
	}
	else{
		$reponse->setContent('');
	}
	
	return $reponse;
}

/**
 * GET sur une collection
 * http://site/http.api/json/patates
 * 
 * @param Request $requete L'objet Request contenant la requête HTTP
 * @param Response $reponse L'objet Response qui contiendra la réponse envoyée à l'utilisateur
 * @return Response Retourne un objet Response modifié suivant ce qu'on a trouvé
 */
function http_collectionjson_get_collection_dist($requete, $reponse){
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_collection = charger_fonction('get_collection', "http/$format/$collection/", true)){
		$reponse = $fonction_collection($requete, $reponse);
	}
	// Sinon on essaye de trouver différentes méthodes pour produire le JSON et en déduire les headers
	else {
		// Allons chercher un squelette de base qui génère le JSON de la collection demandée
		// Le squelette prend en contexte les paramètres du GET uniquement
		if ($json = recuperer_fond("http/$format/$collection", $contexte)){
			// On décode ce qu'on a trouvé
			$json = json_decode($json);
			// Et on le passe dans un pipeline
			$json = pipeline(
				'http_collectionjson_get_collection_contenu',
				array(
					'args' => array(
						'requete' => $requete,
						'reponse' => $reponse,
					),
					'data' => $json,
				)
			);
			// Enfin on le réencode en JSON
			$json = json_encode($json);
		
			$reponse->setStatusCode(200);
			$reponse->setCharset('utf-8');
			$reponse->headers->set('Content-Type', 'application/json');
			$reponse->setContent($json);
		}
		// Si on ne trouve rien c'est que ça n'existe pas
		else{
			// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			$reponse = $fonction_erreur(404, $requete, $reponse);
		}
	}
	
	return $reponse;
}

/*
 * GET sur une ressource
 * http://site/http.api/json/patates/1234
 * 
 * @param Request $requete L'objet Request contenant la requête HTTP
 * @param Response $reponse L'objet Response qui contiendra la réponse envoyée à l'utilisateur
 * @return Response Retourne un objet Response modifié suivant ce qu'on a trouvé
 */
function http_collectionjson_get_ressource_dist($requete, $reponse){
	include_spip('base/objets');
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_ressource = charger_fonction('get_ressource', "http/$format/$collection/", true)){
		$reponse = $fonction_ressource($requete, $reponse);
	}
	// Sinon on essaye de trouver différentes méthodes pour produire le JSON et en déduire les headers :
	// - par une fonction dédiée au JSON
	// - par un squelette
	// - par un échafaudage générique
	else{
		$ressource = $requete->attributes->get('ressource');
		$cle = id_table_objet($collection);
		$contexte = array(
			$cle => $ressource,
			'ressource' => $ressource,
		);
		$contexte = array_merge($requete->query->all(), $requete->attributes->all(), $contexte);
		$json = array();
	
		// S'il existe une fonction dédiée au contenu d'une ressource de cette collection, on l'utilise
		// Cette fonction ne doit retourner QUE le contenu JSON
		if ($fonction_ressource_contenu = charger_fonction('get_ressource_contenu', "http/$format/$collection/", true)){
			$json = $fonction_ressource_contenu($requete, $reponse);
		}
		// Sinon on essaye de le remplir avec un squelette
		else{
			// Pour l'instant on va simplement chercher un squelette du type de la ressource
			// Le squelette prend en contexte les paramètres du GET + l'identifiant de la ressource en essayant de faire au mieux
			if ($skel = trim(recuperer_fond("http/$format/$collection-ressource", $contexte))){
				// On décode ce qu'on a trouvé pour avoir un tableau PHP
				$json = json_decode($skel);
			}
		}
	
		// Si on n'a toujours aucun contenu json, on en échafaude un avec les API d'objets
		if (empty($json)){
			$table_collection = table_objet_sql($collection);
			$objets = lister_tables_objets_sql();
		
			// Si la collection fait partie des objets SPIP et qu'on trouve la ligne de l'objet en question
			// On ne montre par défaut que les champs *éditables*
			if (
				isset($objets[$table_collection])
				and $description = $objets[$table_collection]
				and $objet = sql_fetsel($description['champs_editables'], $table_collection, "$cle = ".intval($ressource))
			){
				include_spip('inc/filtres');
			
				$data = array();
				foreach ($objet as $champ=>$valeur){
					$data[] = array('name' => $champ, 'value' => $valeur);
				}
			
				$json = array(
					'collection' => array(
						'version' => '1.0',
						'href' => url_absolue(self()),
						'items' => array(
							array(
								'href' => url_absolue(self()),
								'links' => array(
									array('rel' => 'edit', 'href' => $GLOBALS['meta']['adresse_site']."/http.api/$format/$collection/$ressource"),
									array('rel' => 'alternate', 'type' => 'text/html', 'href' => url_absolue(generer_url_entite($ressource, objet_type($collection)))),
								),
								'data' => $data,
							),
						)
					),
				);
			}
		}
	
		// On passe le json dans un pipeline
		$json = pipeline(
			'http_collectionjson_get_ressource_contenu',
			array(
				'args' => array(
					'requete' => $requete,
					'reponse' => $reponse,
				),
				'data' => $json,
			)
		);
	
		// Si le json n'est pas vide
		if (!empty($json)){
			// On le réencode en vrai JSON
			$json = json_encode($json);
		
			// Et la réponse est ok
			$reponse->setStatusCode(200);
			$reponse->setCharset('utf-8');
			$reponse->headers->set('Content-Type', 'application/json');
			$reponse->setContent($json);
		}
		// Si on ne trouve rien c'est que ça n'existe pas
		else{
			// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			$reponse = $fonction_erreur(404, $requete, $reponse);
		}
	}
	
	return $reponse;
}

