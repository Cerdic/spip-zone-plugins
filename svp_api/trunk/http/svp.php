<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant une API REST pour SVP
 * selon le modèle imposé par le serveur HTTP abstrait.
 *
 * @package SPIP\SVPAPI\API
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Traite les erreurs directement détectées par le serveur HTTP abstrait uniquement.
 * Celles-ci sont mises au format de l'API SVP et fournies au client systématiquement en JSON.
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
function http_svp_erreur_dist($code, $requete, $reponse) {

	// Construction du contenu de la réponse:
	// Comme l'erreur est détectée par le serveur HTTP abstrait, le contenu n'est pas initialisé.
	// Il faut donc l'initialiser selon la structure imposée par l'API.
	include_spip('inc/repondre_svp');
	$contenu = reponse_initialiser_contenu($requete);

	// Description de l'erreur : pour les messages, on utilise ceux du plugin serveur HTTP abstrait.
	$contenu['erreur']['status'] = $code;
	$contenu['erreur']['type'] = '';
	$contenu['erreur']['title'] = _T('http:erreur_' . $contenu['erreur']['status'] . '_titre');
	$contenu['erreur']['detail'] = _T('http:erreur_' . $contenu['erreur']['status'] . '_message');

	// Finaliser la réponse selon le format demandé.
	$reponse = reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une collection de plugins ou de dépôts.
 * La requête est du type `/svp/plugins` ou `/svp/depots` et renvoie les objets plugin contenus dans la base du serveur
 * (hors les plugins installés) ou les objets dépôt hébergés par le serveur. Il est possible de filtrer la collection
 * des plugins par compatibilité SPIP `/svp/plugins&compatible_spip=2.1`.
 *
 * Il est possible pour des plugins de modifier ou de  rajouter des collections en utilisant le pipeline
 * `declarer_collections_svp` et en fournissant les fonctions de service associées.
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
 *      Objet réponse complétée (status, contenu de la ressource...).
 *      La fonction peut lever une erreur sur l'état du serveur, le format de sortie, la collection et sur les critères
 *      de filtre, à savoir, catégorie et compatibilité SPIP.
 */
function http_svp_get_collection_dist($requete, $reponse) {

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et de la collection.
	include_spip('inc/repondre_svp');
	$contenu = reponse_initialiser_contenu($requete);
	$erreur = array();
	$collection = '';

	// Vérification du mode SVP du serveur : celui-ci ne doit pas être en mode runtime pour
	// renvoyer des données complètes.
	include_spip('inc/verifier_requete_svp');
	if (requete_verifier_serveur($erreur)) {
		// Récupération de la liste des collections disponibles.
		$declarer = charger_fonction('declarer_collections_svp', 'inc');
		$collections = $declarer();

		// Vérification du nom de la collection.
		$collection = $contenu['requete']['collection'];
		if (requete_verifier_collection($collection, $collections, $erreur)) {
			// La collection étant correcte on extrait sa configuration.
			$configuration = $collections[$collection];

			// Vérification des filtres, si demandés.
			if (requete_verifier_filtres($contenu['requete']['filtres'], $collection, $configuration, $erreur)) {
				// Détermination de la fonction de service permettant de récupérer la collection spécifiée
				// filtrée sur les critères éventuellement fournis.
				// -- la fonction de service est contenue dans un fichier du répertoire svpapi/ et est supposée
				//    être toujours présente.
				$module = $configuration['module'];
				include_spip("svpapi/${module}");
				$collectionner = "${collection}_collectionner";

				// -- on construit le contenu de la collection.
				$contenu['donnees'] = $collectionner($contenu['requete']['filtres'], $configuration);
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = reponse_expliquer_erreur($contenu['erreur'], $collection);
	}

	// Construction de la réponse finale
	$reponse = reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une ressource de type plugin identifiée par son préfixe.
 * La requête est du type `/svp/plugins/prefixe` et renvoie l'objet plugin et les objets paquets associés.
 *
 * Il est possible de rajouter des ressources en utilisant le pipeline `declarer_ressources_svp`.
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
function http_svp_get_ressource_dist($requete, $reponse) {

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du format de sortie en JSON
	include_spip('inc/repondre_svp');
	$contenu = reponse_initialiser_contenu($requete);
	$erreur = array();
	$collection = '';

	// Vérification du mode SVP du serveur : celui-ci ne doit pas être en mode runtime pour
	// renvoyer des données complètes.
	include_spip('inc/verifier_requete_svp');
	if (requete_verifier_serveur($erreur)) {
		// Récupération de la liste des collections disponibles.
		$declarer = charger_fonction('declarer_collections_svp', 'inc');
		$collections = $declarer();

		// Vérification du nom de la collection.
		$collection = $contenu['requete']['collection'];
		if (requete_verifier_collection($collection, $collections, $erreur)) {
			// La collection étant correcte on extrait sa configuration.
			$configuration = $collections[$collection];

			// Vérification de la ressource
			$ressource = $contenu['requete']['ressource'];
			if (requete_verifier_ressource($ressource, $collection, $configuration, $erreur)) {
				// Détermination de la fonction de service permettant de récupérer la ressource spécifiée.
				// -- la fonction de service est contenue dans un fichier du répertoire svpapi/ et est supposée
				//    être toujours présente.
				$module = $configuration['module'];
				include_spip("svpapi/${module}");
				$ressourcer = "${collection}_ressourcer";

				// -- on construit le contenu de la collection.
				$contenu['donnees'] = $ressourcer($ressource);
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = reponse_expliquer_erreur($contenu['erreur'], $collection);
	}

	// Construction de la réponse finale
	$reponse = reponse_construire($reponse, $contenu);

	return $reponse;
}
