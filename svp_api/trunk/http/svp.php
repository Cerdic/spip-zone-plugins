<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/*
 * Implémentation d'un serveur REST pour SVP
 */

/**
 * Rien, car en Atom il n'y a malheureusement pas de gestion des erreurs pour l'instant
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne une chaîne vide
 */
function http_svp_erreur_dist($code, $requete, $reponse){

	include_spip('inc/svpapi_reponse');

	// Construction du contenu de la réponse. Deux cas possibles:
	// -- erreur détectée par le serveur HTTP abstrait : le contenu n'est pas initialisé
	//    et on utilise les messages générique du serveur HTTP
	$contenu = reponse_initialiser_contenu($requete);

	$contenu['erreur']['statut'] = $code;
	$contenu['erreur']['type'] = '';
	$contenu['erreur']['title'] = _T('http:erreur_' . $contenu['erreur']['statut'] . '_titre');
	$contenu['erreur']['detail'] = _T('http:erreur_' . $contenu['erreur']['statut'] . '_message');

	// Finaliser la réponse
	$reponse = reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une collection de plugins.
 * La requête est du type /svp/plugins et renvoie les objets plugin contenu dans la base du serveur (hors les plugins
 * installés). Il est possible de filtrer cette requête par catégorie /svp/plugins&categorie=outil et/ou par compatibilité SPIP.
 *
 * @param object	$requete
 * 		Objet matérialisant la requête faite au serveur SVP.
 * @param object	$reponse
 * 		Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 * 		complétée avant d'être retourné par la fonction.
 *
 * @return object
 * 		Objet réponse complétée (status, contenu de la ressource...).
 * 		La fonction peut lever une erreur sur le format de sortie, la collection et sur les critères de filtre,
 * 		catégorie et compatibilité SPIP.
 */
function http_svp_get_collection_dist($requete, $reponse) {

	include_spip('inc/svpapi_requete');
	include_spip('inc/svpapi_reponse');

	// Initialisation du format de sortie du contenu de la réponse
	$contenu = reponse_initialiser_contenu($requete);
	$erreur = array();

	// Vérification du format de sortie demandé
	if (requete_verifier_format($contenu['requete']['format'], $erreur)) {
		// On positionne cette fois le format de sortie car on sait que celui demandé est valide
		$contenu['format'] = $contenu['requete']['format'];
		// Vérification du nom de la collection
		if (requete_verifier_collection($contenu['requete']['collection'], $erreur)) {
			// Récupération de la collection en fonction des critères appliqués
			$from = array('spip_plugins', 'spip_depots_plugins AS dp');
			$select = array('*');
			$where = array('dp.id_depot>0', 'dp.id_plugin=spip_plugins.id_plugin');
			$group_by = array('spip_plugins.id_plugin');
			// On vérifie les critères de filtre additionnels
			if (requete_verifier_criteres($contenu['requete']['criteres'], $erreur)) {
				// Si il y a des critères additionnels on complète le where en conséquence
				if ($contenu['requete']['criteres']) {
					foreach($contenu['requete']['criteres'] as $_critere => $_valeur) {
						if ($_critere == 'compatible_spip') {
							$f_critere = charger_fonction('where_compatible_spip', 'inc');
							$where[] = $f_critere($_valeur, 'spip_plugins', '>');
						} else {
							$where[] = "spip_plugins.${_critere}=" . sql_quote(${_valeur});
						}
					}
				}
				$plugins = sql_allfetsel($select, $from, $where, $group_by);
				$items = array();
				if ($plugins) {
					// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par les préfixes.
					foreach ($plugins as $_plugin) {
						unset($_plugin['id_plugin']);
						$items[$_plugin['prefixe']] = $_plugin;
					}
				}
				$contenu['items'] = $items;
				$contenu['nb_items'] = count($items);
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les information issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = array_merge($contenu['erreur'], reponse_expliquer_erreur($contenu['erreur']));
	}

	// Construction de la réponse finale
	$reponse = reponse_construire($reponse, $contenu);

	return $reponse;
}


/**
 * Fait un GET sur une ressource de type plugin identifié par son préfixe.
 *
 * @param object	$requete
 * 		Objet matérialisant la requête faite au serveur SVP.
 * @param object	$reponse
 * 		Objet matérialisant la réponse telle qu'initialisée par le serveur HTTP abstrait. Cet objet sera
 * 		complétée avant d'être retourné par la fonction.
 *
 * @return object
 * 		Objet réponse complétée (status, contenu de la ressource...).
 */
function http_svp_get_ressource_dist($requete, $reponse){
	// Pour l'instant on va simplement chercher un squelette du nom de la ressource
	// Le squelette prend en contexte les paramètres du GET + l'identifiant de la ressource en essayant de faire au mieux
	include_spip('base/objets');
	$collection = $requete->attributes->get('collection');
	$ressource = $requete->attributes->get('ressource');
	$cle = id_table_objet($collection);
	$contexte = array(
		$cle => $ressource,
		'ressource' => $ressource,
	);
	$contexte = array_merge($requete->query->all(), $contexte);
	
	if ($flux = recuperer_fond("http/atom/$collection-ressource", $contexte)){
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->headers->set('Content-Type', 'application/atom+xml');
		$reponse->setContent($flux);
	}
	// Si on ne trouve rien c'est que ça n'existe pas
	else{
		$reponse->setStatusCode(404);
	}
	
	return $reponse;
}
