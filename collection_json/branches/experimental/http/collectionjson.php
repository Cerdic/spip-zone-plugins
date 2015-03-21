<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Construire une réponse de l'API collection+JSON
 *
 * @param int $code         : Le code de réponse HTTP
 * @param array $donnees    : les données à retourner dans la réponse
 * @param Request $requete  : L'objet Request contenant la requête HTTP
 * @param Response $reponse : Un objet Reponse à compléter avec les
 *                            données passées dans les deux premiers
 *                            arguments.
 *
 * @return Response : Un objet Response complété avec les deux
 *                    premiers arugments
 */
function http_collectionjson_reponse ($code, $donnees, $requete, $reponse) {

	$json = json_encode(array(
		'collection' => array_merge(array(
			'version' => '1.1',
		), $donnees),
	));

	$reponse->setCharset('utf-8');
	$reponse->headers->set('Content-Type', 'application/vnd.collection+json');
	$reponse->setStatusCode($code);
	$reponse->setContent($json);

	return $reponse;
}

/**
 * Contenu collection+json d'une erreur
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne le contenu de l'erreur à renvoyer dans la réponse
 */
function http_collectionjson_erreur_dist($code, $requete, $reponse){

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
		case '415':
			$erreur['title'] = _T('http:erreur_415_titre');
			$erreur['message'] = _T('http:erreur_415_message');
			break;
		default:
			$erreur = false;
	}

	// Si on reconnait une erreur on l'encapsule dans une collection avec erreur
	if ($erreur){
		include_spip('inc/filtres');
		$contenu = array(
			'href' => url_absolue(self()),
			'error' => $erreur,
		);
	} else {
		$contenu = array();
	}

	return http_collectionjson_reponse($code, $contenu, $requete, $reponse);
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
function http_collectionjson_get_index($requete, $reponse) {

	include_spip('base/objets');

	$links = array();
	foreach (lister_tables_objets_sql() as $table => $desc) {
		$links[] = array(
			'rel' => table_objet($table),
			'prompt' => _T($desc['texte_objets']),
			'href' => url_absolue(self()) . table_objet($table) . '/',
		);
	}

	$retour = array(
		'href' => url_absolue(self()),
		'links' => $links,
	);

	// On le passe tout ça dans un pipeline avant de retourner la réponse
	$retour = pipeline(
		'http_collectionjson_get_index_contenu',
		array(
			'args' => array(
				'requete' => $requete,
				'reponse' => $reponse,
			),
			'data' => $retour,
		)
	);

	return http_collectionjson_reponse(200, $retour, $requete, $reponse);
}

/**
 * GET sur une collection
 * http://site/http.api/collectionjson/patates
 *
 * @param Request $requete L'objet Request contenant la requête HTTP
 * @param Response $reponse L'objet Response qui contiendra la réponse envoyée à l'utilisateur
 * @return Response Retourne un objet Response modifié suivant ce qu'on a trouvé
 */
function http_collectionjson_get_collection_dist($requete, $reponse){
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$contexte = $requete->query->all();

	// Allons chercher un squelette de base qui génère le JSON de la
	// collection demandée. Le squelette prend en contexte les
	// paramètres du GET uniquement
	if ($json = recuperer_fond("http/$format/$collection", $contexte)){
		// On décode ce qu'on a trouvé
		$retour = json_decode($json, true);
	}
	// S'il existe une fonction dédiée à ce type de ressource, on
	// n'utilise que ça. Cette fonction doit renvoyer un tableau à
	// mettre dans la réponse
	else if ($fonction_collection = charger_fonction('get_collection', "http/$format/$collection/", true)){

		$retour = $fonction_collection($requete, $reponse);
	}
	// Sinon on essaie de s'appuyer sur l'API objet
	else {
		include_spip('base/abstract_sql');
		include_spip('base/objets');

		// Si la collection demandée ne correspond pas à une table
		// d'objet on arrête tout
		if ( ! in_array(table_objet_sql($collection),
						array_keys(lister_tables_objets_sql()))) {
			// On utilise la fonction d'erreur générique pour
			// renvoyer dans le bon format
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			return $fonction_erreur(404, $requete, $reponse);
		}

		$links = array();

		$pagination = 10;
		$offset = $contexte['offset'] ?: 0;
		$nb_objets = sql_countsel(table_objet_sql($collection));

		// On ajoute des liens de pagination
		if ($offset > 0) {
			$offset_precedant = max(0, $offset-$pagination);
			$links[] = array(
				'rel' => 'prev',
				'prompt' => _T('public:page_precedente'),
				'href' => url_absolue(
					parametre_url(self(), 'offset', $offset_precedant)),
			);
		}
		if (($offset + $pagination) < $nb_objets) {
			$offset_suivant = $offset + $pagination;
			$links[] = array(
				'rel' => 'prev',
				'prompt' => _T('public:page_suivante'),
				'href' => url_absolue(
					parametre_url(self(), 'offset', $offset_suivant)),
			);
		}

		$table_collection = table_objet_sql($collection);
		$description = lister_tables_objets_sql($table_collection);
		$objets = sql_allfetsel('*', $table_collection,'','','',"$offset,$pagination");

		$items = array();
		foreach ($objets as $objet) {
			$data = array();
			foreach ($description['champs_editables'] as $champ){
				$data[] = array(
					'name' => $champ,
					'value' => $objet[$champ],
				);
			}

			$items[] = array(
				'href' => url_absolue(parse_url(self(), PHP_URL_PATH) . $objet[id_table_objet($table_collection)]),
				'data' => $data,
			);
		}

		$retour = array(
			'href' => url_absolue(parse_url(self(), PHP_URL_PATH)),
			'links' => $links,
			'items' => $items,
		);
	}

	// On le passe tout ça dans un pipeline avant de retourner la réponse
	$retour = pipeline(
		'http_collectionjson_get_collection_contenu',
		array(
			'args' => array(
				'requete' => $requete,
				'reponse' => $reponse,
			),
			'data' => $retour,
		)
	);

	return http_collectionjson_reponse(200, $retour, $requete, $reponse);
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
	include_spip('base/objets');

	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');
	$ressource = $requete->attributes->get('ressource');

	// On essaie de remplir avec un squelette
	if (find_in_path("http/$format/$collection-ressource.html")) {

		$contexte = array_merge(
			$requete->query->all(),
			$requete->attributes->all(),
			array(
				id_table_objet($collection) => $ressource,
				'ressource' => $ressource,
			));

		$json = recuperer_fond("http/$format/$collection-ressource", $contexte);
		$retour = json_decode($json, true);
	}
	// S'il existe une fonction dédiée à ce type de ressource, on
	// n'utilise que ça. Cette fonction doit renvoyer un tableau à
	// mettre dans la réponse
	else if ($fonction_ressource = charger_fonction('get_ressource', "http/$format/$collection/", true)){

		$retour = $fonction_ressource($requete, $reponse);
	}
	// Sinon on essaye de déduire par un échafaudage générique
	else {

		$table_collection = table_objet_sql($collection);
		$objets = lister_tables_objets_sql();
		if (isset($objets[$table_collection])) {
			$description = $objets[$table_collection];
			$select = implode(', ', array_map('sql_quote', $description['champs_editables']));
			$where = id_table_objet($table_collection) . "=" . intval($ressource);
		}

		// Si la collection fait partie des objets SPIP et qu'on trouve la
		// ligne de l'objet en question. Sinon on renvoie une erreur.
		if ( ! ($select
				and $objet = sql_fetsel($select, $table_collection, $where))) {

			// On utilise la fonction d'erreur générique pour
			// renvoyer dans le bon format
			$fonction_erreur = charger_fonction('erreur', "http/$format/");
			return $fonction_erreur(404, $requete, $reponse);
		}

		include_spip('inc/filtres');

		// On ne montre par défaut que les champs *éditables*.
		foreach ($objet as $champ=>$valeur){
			$data[] = array('name' => $champ, 'value' => $valeur);
		}

		$retour = array(
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
		);
	}

	// On le passe tout ça dans un pipeline avant de retourner la réponse
	$retour = pipeline(
		'http_collectionjson_get_ressource_contenu',
		array(
			'args' => array(
				'requete' => $requete,
				'reponse' => $reponse,
			),
			'data' => $retour,
		)
	);

	return http_collectionjson_reponse(200, $retour, $requete, $reponse);
}

/**
 * POST sur une collection : création d'une nouvelle ressource
 * http://site/http.api/collectionjson/patates
 *
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function http_collectionjson_post_collection_dist($requete, $reponse){
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');

	// S'il existe une fonction dédiée à ce type de ressource, on
	// n'utilise que ça. Cette fonction doit renvoyer un tableau à
	// mettre dans la réponse
	if ($fonction_ressource = charger_fonction('post_collection', "http/$format/$collection/", true)){
		$retour = $fonction_ressource($requete, $reponse);
		return http_collectionjson_reponse(200, $retour, $requete, $reponse);
	}
	// Sinon on échafaude en utilisant l'API des objets
	include_spip('base/objets');
	$objet= objet_type($collection);

	return http_collectionjson_editer_objet($objet, 'new', $requete->getContent(), $requete, $reponse);
}

/**
 * PUT sur une ressource : modification d'une ressource existante
 * http://site/http.api/collectionjson/patates/1234
 *
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function http_collectionjson_put_ressource_dist($requete, $reponse){
	$format = $requete->attributes->get('format');
	$collection = $requete->attributes->get('collection');

	// S'il existe une fonction dédiée à ce type de ressource, on
	// n'utilise que ça. Cette fonction doit renvoyer un tableau à
	// mettre dans la réponse
	if ($fonction_ressource = charger_fonction('put_ressource', "http/$format/$collection/", true)){
		$retour = $fonction_ressource($requete, $reponse);
		return http_collectionjson_reponse(200, $retour, $requete, $reponse);
	}
	// Sinon on échafaude en utilisant l'API des objets
	include_spip('base/objets');
	$id_objet = intval($requete->attributes->get('ressource'));
	$objet= objet_type($collection);

	return http_collectionjson_editer_objet($objet, $id_objet, $requete->getContent(), $requete, $reponse);
}

/**
 * Édition générique d'un objet en JSON
 *
 * Cette fonction sert à mutualiser le code d'échafaudage entre le POST et le PUT pour créer ou modifier un objet.
 *
 * @param Request $requete
 * @param Response $reponse
 * @return Response
 */
function http_collectionjson_editer_objet($objet, $id_objet, $contenu, $requete, $reponse){

	// On vérifie que la requête a bien un contenu et qu'on a bien un
	// tableau PHP et qu'on a au moins le bon tableau "data"
	// Sinon on retourne une erreur
	if ( ! ($contenu
			and $json = json_decode($contenu, true)
			and is_array($json)
			and isset($json['template']['data'])
			and $data = $json['template']['data']
			and is_array($data))) {

		// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
		$fonction_erreur = charger_fonction('erreur', "http/collectionjson/");
		return $fonction_erreur(415, $requete, $reponse);
	}

	include_spip('inc/filtres');
	include_spip('base/objets');
	$cle_objet = id_table_objet($objet);
	$new = !intval($id_objet);

	// Pour chaque champ envoyé, on va faire un set_request() de SPIP
	foreach ($data as $champ){
		if (isset($champ['name']) and isset($champ['value'])){
			set_request($champ['name'], $champ['value']);
		}
	}

	// On va chercher la fonction de vérification de cet objet
	$erreurs = array();
	if ($fonction_verifier = charger_fonction('verifier', "formulaires/editer_$objet/", true)){
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
	if ($erreurs){
		$donnees_reponse = array(
			'href' => url_absolue(self()),
			'error' => array(
				'title' => _T('erreur'),
				'code' => 400,
			),
			'errors' => array(),
		);
		foreach ($erreurs as $nom => $erreur){
			$donnees_reponse['errors'][$nom] = array(
				'title' => $erreur,
				'code' => 400,
			);
		}
		return http_collectionjson_reponse(400, $donnees_reponse, $requete, $reponse);
	}

	// Sinon on continue le traitement
	$retours = array();
	if ($fonction_traiter = charger_fonction('traiter', "formulaires/editer_$objet/", true)){
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

	// S'il y a eu une erreur
	if (isset($retour['message_erreur']) and $retour['message_erreur']) {
		// On utilise la fonction d'erreur générique pour renvoyer dans le bon format
		$fonction_erreur = charger_fonction('erreur', "http/collectionjson/");
		return $fonction_erreur(404, $requete, $reponse);
	}

	// Si on a bien modifié l'objet sans erreur
	if ($id_objet = $retours[$cle_objet]){
		// On va cherche la fonction qui génère la vue d'une ressource
		if ($fonction_ressource = charger_fonction('get_ressource', 'http/collectionjson/', true)){
			// On ajoute à la requête, l'identitiant de la nouvelle ressource
			$requete->attributes->set('ressource', $id_objet);
			$retour = $fonction_ressource($requete, $reponse);
		} else {
			// TODO s'appuyer sur l'API objet pour faire un truc utile
			$retour = array();
		}
	}

	// Si c'était une création, on renvoie 201
	return http_collectionjson_reponse($new ? 201 : 200, $retour, $requete, $reponse);
}
