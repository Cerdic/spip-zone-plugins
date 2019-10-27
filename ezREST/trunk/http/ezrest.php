<?php
/**
 * Le fichier contient l'ensemble des constantes et fonctions implémentant l'API REST ezREST qui
 * permet de définir une couche applicative standard pour développer des API REST selon le modèle imposé par
 * le serveur HTTP abstrait.
 *
 * @package SPIP\EZREST\HTTP
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Traite les erreurs directement détectées par le serveur HTTP abstrait uniquement.
 * Celles-ci sont mises au format de l'API REST ezREST et fournies au client systématiquement en JSON.
 *
 * @api
 *
 * @param int                                       $code
 *      Le code HTTP de l'erreur à générer
 * @param Symfony\Component\HttpFoundation\Request  $requete
 *      Objet matérialisant la requête faite au serveur SVP.
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 *      complétée avant d'être retourné par la fonction.
 *
 * @return Symfony\Component\HttpFoundation\Response
 *      Retourne l'objet réponse dont le contenu est mis à jour avec les éléments du bloc d'erreur.
 */
function http_ezrest_erreur_dist($code, $requete, $reponse) {

	// Construction du contenu de la réponse:
	// Comme l'erreur est détectée par le serveur HTTP abstrait, le contenu n'est pas initialisé.
	// Il faut donc l'initialiser selon la structure imposée par l'API.
	include_spip('ezrest/ezrest');
	$contenu = ezrest_reponse_initialiser_contenu($requete);

	// Description de l'erreur : pour les messages, on utilise ceux du plugin serveur HTTP abstrait.
	$contenu['erreur'] = ezrest_erreur_initialiser('http', $code, '');
	$contenu['erreur']['titre'] = _T('http:erreur_' . $contenu['erreur']['status'] . '_titre');
	$contenu['erreur']['detail'] = _T('http:erreur_' . $contenu['erreur']['status'] . '_message');

	// Finaliser la réponse selon le format demandé.
	$reponse = ezrest_reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur l'API ezREST seule et renvoie la liste des collections disponibles et les possibilités associées.
 * Il ne peut pas y avoir d'erreur à ce niveau de l'API ezREST.
 *
 * @api
 *
 * @param Symfony\Component\HttpFoundation\Request  $requete
 *      Objet matérialisant la requête faite au serveur SVP.
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 *      complétée avant d'être retourné par la fonction.
 *
 * @return Symfony\Component\HttpFoundation\Response
 *      Retourne l'objet réponse dont le contenu est mis à jour avec les éléments du bloc d'erreur.
 */
function http_ezrest_get_index_dist($requete, $reponse) {

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du plugin utilisateur qui
	// n'est pas encore connu à ce stade.
	include_spip('ezrest/ezrest');
	$contenu = ezrest_reponse_initialiser_contenu($requete);

	// Récupération de la liste des collections disponibles.
	$declarer = charger_fonction('ezrest_declarer_collections', 'inc');
	$collections = $declarer();

	// On construit l'index des collections disponibles.
	$contenu['donnees'] = ezrest_indexer($collections);

	// Construction de la réponse finale
	$reponse = ezrest_reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une collection gérée par l'API ezREST.
 * La requête est du type `/ezrest/xxx` et renvoie les objets associées contenus
 * dans la base du serveur.
 * Il est possible de filtrer la collection et de compléter la colelction en utilisant le pipeline `post_ezcollection`.
 *
 * @param Symfony\Component\HttpFoundation\Request  $requete
 *      Objet matérialisant la requête faite au serveur SVP.
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 *      complétée avant d'être retourné par la fonction.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse complétée (status, contenu de la ressource...).
 *      La fonction peut lever une erreur sur le contexte lors de l'appel, la collection ou sur les critères
 *      de filtre.
 *@api
 *
 */
function http_ezrest_get_collection_dist($requete, $reponse) {

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du plugin utilisateur qui
	// n'est pas encore connu à ce stade.
	include_spip('ezrest/ezrest');
	$contenu = ezrest_reponse_initialiser_contenu($requete);
	$plugin = '';
	$erreur = array();

	// Récupération de la liste des collections disponibles.
	$declarer = charger_fonction('ezrest_declarer_collections', 'inc');
	$collections = $declarer();

	// Vérification du nom de la collection.
	$collection = $contenu['requete']['collection'];
	if (ezrest_requete_verifier_collection($collection, $collections, $plugin, $erreur)) {
		// La collection étant correcte on extrait sa configuration.
		$configuration = $collections[$collection];

		// On complète l'initialisation du contenu de la réponse avec des informations sur le plugin utilisateur.
		// -- Par défaut, le schéma et la version mais le plugin utilisateur peut compléter ces informations.
		$contenu = ezrest_reponse_informer_plugin($plugin, $contenu);

		// On utilise son préfixe pour appeler une fonction spécifique au plugin pour vérifier si le contexte
		// permet l'utilisation de l'API.
		if (ezrest_api_verifier_contexte($plugin, $erreur)) {
			// Le contexte autorise l'utilisation de l'API.
			// -> Vérification des filtres éventuels.
			if (ezrest_requete_verifier_filtres($plugin, $contenu['requete']['filtres'], $collection, $configuration, $erreur)) {
				// -- on construit le contenu de la collection.
				$contenu['donnees'] = ezrest_collectionner(
					$plugin,
					$collection,
					$contenu['requete']['filtres'],
					$configuration
				);

				// -- on complète éventuellement le contenu de la collection.
				if ($contenu['donnees']) {
					$flux = array(
						'args' => array(
							'plugin'        => $plugin,
							'configuration' => $configuration,
							'collection'    => $collection
						),
						'data' => $contenu['donnees']
					);
					$contenu['donnees'] = pipeline('post_ezcollection', $flux);
				}
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = ezrest_reponse_expliquer_erreur($plugin, $contenu['erreur'], $collection);
	}

	// Construction de la réponse finale
	$reponse = ezrest_reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une ressource d'une collection gérée par l'API ezREST.
 * La requête est du type `/ezrest/xxx` et renvoie l'objet de la base désigné.
 *
 * Il est possible de rajouter des informations en utilisant le pipeline `post_ezressource`.
 *
 * @api
 *
 * @param Symfony\Component\HttpFoundation\Request  $requete
 *      Objet matérialisant la requête faite au serveur SVP.
 * @param Symfony\Component\HttpFoundation\Response $reponse
 *      Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 *      complétée avant d'être retourné par la fonction.
 *
 * @return Symfony\Component\HttpFoundation\Response $reponse
 *      Objet réponse complété (status, contenu de la ressource...).
 *      La fonction peut lever une erreur sur l'état du serveur, le format de sortie, le type de ressouce et
 *      sur l'existence de la ressource demandée.
 */
function http_ezrest_get_ressource_dist($requete, $reponse) {

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du format de sortie en JSON
	include_spip('ezrest/ezrest');
	$contenu = ezrest_reponse_initialiser_contenu($requete);
	$erreur = array();

	// Récupération de la liste des collections disponibles.
	$declarer = charger_fonction('ezrest_declarer_collections', 'inc');
	$collections = $declarer();

	// Vérification du nom de la collection.
	$collection = $contenu['requete']['collection'];
	if (ezrest_requete_verifier_collection($collection, $collections, $plugin, $erreur)) {
		// La collection étant correcte on extrait sa configuration.
		$configuration = $collections[$collection];

		// On complète l'initialisation du contenu de la réponse avec des informations sur le plugin utilisateur.
		// -- Par défaut, le schéma et la version mais le plugin utilisateur peut compléter ces informations.
		$contenu = ezrest_reponse_informer_plugin($plugin, $contenu);

		// On utilise son préfixe pour appeler une fonction spécifique au plugin pour vérifier si le contexte
		// permet l'utilisation de l'API.
		if (ezrest_api_verifier_contexte($plugin, $erreur)) {
			// Le contexte autorise l'utilisation de l'API.
			// Vérification de la ressource
			$ressource = $contenu['requete']['ressource'];
			if (ezrest_requete_verifier_ressource($plugin, $ressource, $collection, $configuration, $erreur)) {
				// -- on construit le contenu de la collection.
				$contenu['donnees'] = ezrest_ressourcer(
					$plugin,
					$collection,
					$ressource
				);

				// -- on complète éventuellement le contenu de la collection.
				if ($contenu['donnees']) {
					$flux = array(
						'args' => array(
							'plugin'        => $plugin,
							'configuration' => $configuration,
							'collection'    => $collection,
							'ressource'     => $ressource
						),
						'data' => $contenu['donnees']);
					$contenu['donnees'] = pipeline('post_ezressource', $flux);
				}
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = ezrest_reponse_expliquer_erreur($plugin, $contenu['erreur'], $collection);
	}

	// Construction de la réponse finale
	$reponse = ezrest_reponse_construire($reponse, $contenu);

	return $reponse;
}
