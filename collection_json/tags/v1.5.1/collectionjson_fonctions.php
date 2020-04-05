<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Produit le contenu du JSON d'une collection
 * - par un squelette
 * - par un échafaudage générique
 * 
 * @param string $collection Nom de la collection à générer
 * @param array $contexte Tableau associatif de l'environnement (à priori venant du GET)
 * @return array Retourne un tableau associatif représentant la collection suivant la grammaire Collection+JSON ou un tableau vide si erreur (générera une 404)
 **/
function collectionjson_get_collection($collection, $contexte) {
	// Allons chercher un squelette de base qui génère le JSON de la collection demandée
	// Le squelette prend en contexte les paramètres du GET uniquement
	if ($json = recuperer_fond("http/collectionjson/$collection", $contexte)) {
		// On décode ce qu'on a trouvé
		$json = json_decode($json, true);
	}
	// Si on ne trouve rien on essaie de s'appuyer sur l'API objet pour générer un JSON
	else  {
		include_spip('base/abstract_sql');
		include_spip('base/objets');
		
		// Si la collection demandée ne correspond pas à une table
		// d'objet on arrête tout
		if (!in_array(
			table_objet_sql($collection),
			array_keys(lister_tables_objets_sql())
		)) {
			// On ne renvoit rien, et ça devrait générer une erreur
			return array();
		}
		
		// On génère la pagination si besoin
		$links = array();
		$pagination = 10;
		$offset = isset($contexte['offset']) ? $contexte['offset'] : 0;
		$nb_objets = sql_countsel(table_objet_sql($collection));
		
		// On ajoute des liens de pagination
		if ($offset > 0) {
			$offset_precedant = max(0, $offset-$pagination);
			$links[] = array(
				'rel' => 'prev',
				'prompt' => _T('public:page_precedente'),
				'href' => url_absolue(
					parametre_url(self('&'), 'offset', $offset_precedant)),
			);
		}
		if (($offset + $pagination) < $nb_objets) {
			$offset_suivant = $offset + $pagination;
			$links[] = array(
				'rel' => 'prev',
				'prompt' => _T('public:page_suivante'),
				'href' => url_absolue(
					parametre_url(self('&'), 'offset', $offset_suivant)),
			);
		}
		
		// On requête l'ensemble de cette page d'un coup
		$table_collection = table_objet_sql($collection);
		$cle_objet = id_table_objet($table_collection);
		$description = lister_tables_objets_sql($table_collection);
		$select = isset($description['champs_editables']) ? array_merge($description['champs_editables'], array($cle_objet)) : '*';
		$lignes = sql_allfetsel($select, $table_collection,'','','',"$offset,$pagination");
		
		$items = array();
		foreach ($lignes as $champs) {
			$items[] = collectionjson_get_objet(objet_type($table_collection), $champs[$cle_objet], $champs);
		}
		
		$json = array(
			'collection' => array(
				'version' => '1.0',
				'href' => url_absolue(parse_url(self('&'), PHP_URL_PATH)),
				'links' => $links,
				'items' => $items,
			),
		);
	}
	
	// Et on le passe dans un pipeline
	$json = pipeline(
		'collectionjson_get_collection',
		array(
			'args' => array(
				'collection' => $collection,
				'contexte' => $contexte,
			),
			'data' => $json,
		)
	);
	
	return $json;
}

/**
 * Produit le contenu du JSON d'une ressource
 * - par une fonction dédiée au JSON
 * - par un squelette
 * - par un échafaudage générique
 * 
 * @param string $collection Nom de la collection à générer
 * @param array $contexte Tableau associatif de l'environnement (à priori venant du GET)
 * @return array Retourne un tableau associatif représentant la ressource suivant la grammaire Collection+JSON ou un tableau vide si erreur (générera une 404)
 **/
function collectionjson_get_ressource($collection, $contexte) {
	
}
