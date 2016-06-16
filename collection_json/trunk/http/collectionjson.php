<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('collectionjson_fonctions');

/**
 * Contenu collection+json d'une erreur
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne le contenu de l'erreur à renvoyer dans la réponse
 */
function http_collectionjson_erreur_dist($code, $requete, $reponse) {
	$reponse->setStatusCode($code);
	$erreur = array('code' => "$code");
	
	switch ($code) {
		case '401':
			$erreur['title'] = _T('http:erreur_401_titre');
			$erreur['message'] = _T('http:erreur_401_message');
			break;
		case '404':
			$erreur['title'] = _T('http:erreur_404_titre');
			$erreur['message'] = _T('http:erreur_404_message');
			break;
		case '415':
			$erreur['title'] = _T('http:erreur_415_titre');
			$erreur['message'] = _T('http:erreur_415_message');
			break;
		default:
			$erreur = false;
	}
	
	// Si on reconnait une erreur on l'encapsule dans une collection avec erreur
	if ($erreur) {
		include_spip('inc/filtres');
		
		$reponse->headers->set('Content-Type', 'application/json');
		$reponse->setContent(json_encode(array(
			'collection' => array(
				'version' => '1.0',
				'href' => url_absolue(self('&')),
				'error' => $erreur,
			),
		)));
	}
	else {
		$reponse->setContent('');
	}
	
	return $reponse;
}

/**
 * Index général de l'API
 * http://site/http.api/collectionjson/
 *
 * @param Request $requete	: L'objet Request contenant la requête HTTP
 * @param Response $reponse : L'objet Response qui contiendra la
 *							  réponse envoyée à l'utilisateur
 *
 * @return Response Retourne un objet Response modifié suivant ce
 *					qu'on a trouvé
 */
function http_collectionjson_get_index_dist($requete, $reponse) {
	include_spip('base/objets');
	include_spip('inc/autoriser');
	
	$links = array();
	foreach (lister_tables_objets_sql() as $table => $desc) {
		if (autoriser('get_collection', table_objet($table))) {
			$links[] = array(
				'rel' => 'collection',
				'name' => table_objet($table),
				'prompt' => _T($desc['texte_objets']),
				'href' => rtrim(url_absolue(self('&')), '/') . '/' . table_objet($table) . '/',
			);
		}
	}
	
	$json = array(
		'collection' => array(
			'version' => '1.0',
			'href' => url_absolue(self('&')),
			'links' => $links,
		),
	);
	
	// On le passe tout ça dans un pipeline avant de retourner la réponse
	$json = pipeline(
		'http_collectionjson_get_index_contenu',
		array(
			'args' => array(
				'requete' => $requete,
				'reponse' => $reponse,
			),
			'data' => $json,
		)
	);
	
	$reponse->setStatusCode(200);
	$reponse->setCharset('utf-8');
	$reponse->headers->set('Content-Type', 'application/json');
	$reponse->setContent(json_encode($json));

	return $reponse;
}

/**
 * GET sur une collection
 * http://site/http.api/collectionjson/patates
 * 
 * @param Request $requete L'objet Request contenant la requête HTTP
 * @param Response $reponse L'objet Response qui contiendra la réponse envoyée à l'utilisateur
 * @return Response Retourne un objet Response modifié suivant ce qu'on a trouvé
 */
function http_collectionjson_get_collection_dist($requete, $reponse) {
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_collection = charger_fonction('get_collection', "http/$format/$collection/", true)) {
		$reponse = $fonction_collection($requete, $reponse);
	}
	// Sinon on essaye de trouver différentes méthodes pour produire le JSON
	else {
		$json = collectionjson_get_collection($collection, $contexte);

		// COMPAT : à remplacer par le nouveau pipeline "collectionjson_get_collection"
		// peut-être à supprimer directement si personne ne l'a jamais utilisé…
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
		
		// Si on finit avec un truc vide, on génère une 404
		if (empty($json)) {
			$fonction_erreur = charger_fonction('erreur', 'http/collectionjson/');
			$response = $fonction_erreur(404, $requete, $reponse);
		}
		// Sinon on encode et on renvoie correctement la réponse
		else {
			$json = json_encode($json);
			
			$reponse->setStatusCode(200);
			$reponse->setCharset('utf-8');
			$reponse->headers->set('Content-Type', 'application/json');
			$reponse->setContent($json);
		}
	}
	
	return $reponse;
}

/*
 * GET sur une ressource
 * http://site/http.api/collectionjson/patates/1234
 * 
 * @param Request $requete L'objet Request contenant la requête HTTP
 * @param Response $reponse L'objet Response qui contiendra la réponse envoyée à l'utilisateur
 * @return Response Retourne un objet Response modifié suivant ce qu'on a trouvé
 */
function http_collectionjson_get_ressource_dist($requete, $reponse){
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_ressource = charger_fonction('get_ressource', "http/$format/$collection/", true)) {
		$reponse = $fonction_ressource($requete, $reponse);
	}
	// Sinon on essaye de trouver différentes méthodes pour produire le JSON et en déduire les headers :
	// - par une fonction dédiée au JSON
	// - par un squelette
	// - par un échafaudage générique
	else {
		$json = array();
		
		// S'il existe une fonction dédiée au contenu d'une ressource de cette collection, on l'utilise
		// Cette fonction ne doit retourner QUE le contenu JSON
		if ($fonction_ressource_contenu = charger_fonction('get_ressource_contenu', "http/$format/$collection/", true)) {
			$json = $fonction_ressource_contenu($requete, $reponse);
		}
		// Sinon on essaye de le remplir avec un squelette
		else {
			// Pour l'instant on va simplement chercher un squelette du type de la ressource
			// Le squelette prend en contexte les paramètres du GET + l'identifiant de la ressource en essayant de faire au mieux
			include_spip('base/objets');
			$ressource = $requete->attributes->get('ressource');
			$cle = id_table_objet($collection);
			$contexte = array(
				$cle => $ressource,
				'ressource' => $ressource,
			);
			$contexte = array_merge($requete->query->all(), $requete->attributes->all(), $contexte);
			
			if ($skel = trim(recuperer_fond("http/$format/$collection-ressource", $contexte))) {
				// On décode ce qu'on a trouvé pour avoir un tableau PHP
				$json = json_decode($skel, true);
			}
		}
		
		// Si on n'a toujours aucun contenu json, et que la collection est bien un objet SPIP,
		// on en échafaude un avec les API d'objets
		if (
			empty($json)
			and $table_collection = table_objet_sql($collection)
			and $objets = lister_tables_objets_sql()
			and isset($objets[$table_collection])
		) {
			$objet = objet_type($collection);
			$id_objet = intval($ressource);
			
			$item = collectionjson_get_objet($objet, $id_objet);
			
			$json = array(
				'collection' => array(
					'version' => '1.0',
					'href' => url_absolue(self('&')),
					'items' => array(
						$item,
					)
				),
			);
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
		if (!empty($json)) {
			// On le réencode en vrai JSON
			$json = json_encode($json);
			
			// Et la réponse est ok
			$reponse->setStatusCode(200);
			$reponse->setCharset('utf-8');
			$reponse->headers->set('Content-Type', 'application/json');
			$reponse->setContent($json);
		}
		// Si on ne trouve rien c'est que ça n'existe pas
		else {
			// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			$reponse = $fonction_erreur(404, $requete, $reponse);
		}
	}
	
	return $reponse;
}

/**
 * POST sur une collection : création d'une nouvelle ressource
 * http://site/http.api/collectionjson/patates
 *
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function http_collectionjson_post_collection_dist($requete, $reponse) {
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_ressource = charger_fonction('post_collection', "http/$format/$collection/", true)) {
		$reponse = $fonction_ressource($requete, $reponse);
	}
	// Sinon on échafaude en utilisant l'API des objets
	else {
		include_spip('base/objets');
		$objet= objet_type($collection);
		$reponse = collectionjson_editer_objet($objet, 'new', $requete->getContent(), $requete, $reponse);
	}
	
	return $reponse;
}

/**
 * PUT sur une ressource : modification d'une ressource existante
 * http://site/http.api/collectionjson/patates/1234
 *
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function http_collectionjson_put_ressource_dist($requete, $reponse) {
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	
	// S'il existe une fonction globale, dédiée à ce type de ressource, qui gère TOUTE la requête, on n'utilise QUE ça
	// Cette fonction doit donc évidemment renvoyer un objet Response valide
	if ($fonction_ressource = charger_fonction('put_ressource', "http/$format/$collection/", true)) {
		$reponse = $fonction_ressource($requete, $reponse);
	}
	// Sinon on échafaude en utilisant l'API des objets
	else {
		include_spip('base/objets');
		$id_objet = intval($requete->attributes->get('ressource'));
		$objet= objet_type($collection);
		$reponse = collectionjson_editer_objet($objet, $id_objet, $requete->getContent(), $requete, $reponse);
	}
	
	return $reponse;
}

/**
 * Vue générique d'un objet en JSON
 * 
 * Cette fonction sert à mutualiser le code d'échafaudage pour générer le GET d'un objet.
 * 
 * @param string $objet Type de l’objet dont on veut générer la vue
 * @param int $id_objet Identifiant de l’objet dont on veut générer la vue
 * @param string $contenu Optionnellement, les champs SQL déjà récupérés de l’objet, pour éviter de faire une requête
 */
function collectionjson_get_objet($objet, $id_objet, $champs=array()) {
	include_spip('inc/filtres');
	
	$collection = table_objet($objet); // l'objet au pluriel
	$table_sql = table_objet_sql($objet);
	$cle = id_table_objet($objet);
	$description = lister_tables_objets_sql($table_sql);
	
	// S'il n'y a pas de champs, on fait une requête, par défaut on récupère uniquement les champs éditables
	if (empty($champs)) {
		$select = isset($description['champs_editables']) ? $description['champs_editables'] : '*';
		$champs = sql_fetsel($select, $table_sql, $cle . ' = ' . intval($id_objet));
	}
	
	$data = array();
	foreach ($champs as $champ=>$valeur) {
		$data[] = array('name' => $champ, 'value' => $valeur);
	}
	
	$item = array(
		'href' => url_absolue("http.api/collectionjson/$collection/$id_objet"),
		'links' => array(
			array('rel' => 'edit', 'href' => url_absolue("http.api/collectionjson/$collection/$id_objet")),
			array('rel' => 'alternate', 'type' => 'text/html', 'href' => url_absolue(generer_url_entite($id_objet, $objet))),
		),
		'data' => $data,
	);
	
	return $item;
}

/**
 * Édition générique d'un objet en JSON
 * 
 * Cette fonction sert à mutualiser le code d'échafaudage entre le POST et le PUT pour créer ou modifier un objet.
 * 
 * @param string $objet Type de l’objet à éditer
 * @param int $id_objet Identifiant de l’objet à éditer
 * @param string $contenu Contenu JSON de l’objet à éditer
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function collectionjson_editer_objet($objet, $id_objet, $contenu, $requete, $reponse) {
	// Si la requête a bien un contenu et qu'on a bien un tableau PHP et qu'on a au moins le bon tableau "data"
	if (
		$contenu
		and $json = json_decode($contenu, true)
		and is_array($json)
		and isset($json['collection']['items'][0]['data'])
		and $data = $json['collection']['items'][0]['data']
		and is_array($data)
	) {
		include_spip('inc/filtres');
		include_spip('base/objets');
		$cle_objet = id_table_objet($objet);
		$new = !intval($id_objet);
		
		// Pour chaque champ envoyé, on va faire un set_request() de SPIP
		foreach ($data as $champ) {
			if (isset($champ['name']) and isset($champ['value'])) {
				set_request($champ['name'], $champ['value']);
			}
		}
		
		// On va chercher la fonction de vérification de cet objet
		$erreurs = array();
		if ($fonction_verifier = charger_fonction('verifier', "formulaires/editer_$objet/", true)) {
			$erreurs = $fonction_verifier($id_objet);
		}
		
		// On passe les erreurs dans le pipeline "verifier" (par exemple pour Saisies)
		$erreurs = pipeline('formulaire_verifier', array(
			'args' => array(
				'form' => "editer_$objet",
				'args' => array($id_objet),
			),
			'data' => $erreurs,
		));
		
		// S'il y a des erreurs, on va générer un JSON les listant
		if ($erreurs) {
			$reponse->setStatusCode(400);
			$reponse->headers->set('Content-Type', 'application/json');
			$reponse->setCharset('utf-8');
			
			$json_reponse = array(
				'collection' => array(
					'version' => '1.0',
					'href' => url_absolue(self('&')),
					'error' => array(
						'title' => _T('erreur'),
						'code' => 400,
					),
					'errors' => array(),
				),
			);
			
			foreach ($erreurs as $nom => $erreur) {
				$json_reponse['collection']['errors'][$nom] = array(
					'title' => $erreur,
					'code' => 400,
				);
			}
			$reponse->setContent(json_encode($json_reponse));
		}
		// Sinon on continue le traitement
		else {
			$retours = array();
			if ($fonction_traiter = charger_fonction('traiter', "formulaires/editer_$objet/", true)) {
				$retours = $fonction_traiter($id_objet);
			}
			
			// On passe dans le pipeline "traiter"
			$retours = pipeline('formulaire_traiter', array(
				'args' => array(
					'form' => "editer_$objet",
					'args' => array($id_objet),
				),
				'data' => $retours,
			));
			
			// Si on a bien modifié l'objet sans erreur
			if (!$retours['message_erreur'] and $id_objet = $retours[$cle_objet]) {
				// On va cherche la fonction qui génère la vue d'une ressource
				if ($fonction_ressource = charger_fonction('get_ressource', 'http/collectionjson/', true)) {
					// On ajoute à la requête, l'identifiant de la nouvelle ressource
					$requete->attributes->set('ressource', $id_objet);
					$reponse = $fonction_ressource($requete, $reponse);
				}
				// Si c'était une création, on renvoie 201
				if ($new) {
					$reponse->setStatusCode(201);
				}
			}
		}
	}
	else {
		// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
		$fonction_erreur = charger_fonction('erreur', "http/collectionjson/");
		$reponse = $fonction_erreur(415, $requete, $reponse);
	}
	
	return $reponse;
}
