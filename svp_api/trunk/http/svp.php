<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION'))
	return;

/*
 * Implémentation d'un serveur REST pour SVP
 */

/**
 * Traitement des erreurs directements détectées par le serveur HTTP abstrait.
 * Elles sont mises au format de l'API SVP et fournie au client en JSON.
 *
 * @param int $code Le code HTTP de l'erreur à générer
 * @return string Retourne une chaîne vide
 */
function http_svp_erreur_dist($code, $requete, $reponse){

	include_spip('inc/svpapi_reponse');

	// Construction du contenu de la réponse:
	// Comme l'erreur est détectée par le serveur HTTP abstrait, le contenu n'est pas initialisé.
	// Il faut donc l'initialiser selon la structure imposée par l'API.
	$contenu = reponse_initialiser_contenu($requete);

	// Description de l'erreur : pour les messages, on utilise ceux du plugin serveur HTTP abstrait.
	$contenu['erreur']['statut'] = $code;
	$contenu['erreur']['type'] = '';
	$contenu['erreur']['title'] = _T('http:erreur_' . $contenu['erreur']['statut'] . '_titre');
	$contenu['erreur']['detail'] = _T('http:erreur_' . $contenu['erreur']['statut'] . '_message');

	// Détermination du format de la réponse. Etant donné que l'on traite déjà une erreur, on ne se préoccupe pas
	// pas d'une éventuelle erreur sur le format, on utilisera dans ce cas le JSON.
	$format_reponse = 'json';
	if (requete_verifier_format($contenu['requete']['format'], $erreur)) {
		// On positionne le format de sortie car on sait que celui demandé est valide
		$format_reponse = $contenu['requete']['format'];
	}

	// Finaliser la réponse selon le format demandé.
	$reponse = reponse_construire($reponse, $contenu, $format_reponse);

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

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du format de sortie en JSON
	$contenu = reponse_initialiser_contenu($requete);
	$erreur = array();
	$format_reponse = 'json';

	// Vérification du format de sortie demandé
	if (requete_verifier_format($contenu['requete']['format'], $erreur)) {
		// On positionne cette fois le format de sortie car on sait que celui demandé est valide
		$format_reponse = $contenu['requete']['format'];
		// Vérification du nom de la collection
		if (requete_verifier_collection($contenu['requete']['collection'], $erreur)) {
			$items = array();
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
				if ($plugins) {
					// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par les préfixes.
					foreach ($plugins as $_plugin) {
						unset($_plugin['id_plugin']);
						unset($_plugin['id_depot']);
						$items[$_plugin['prefixe']] = $_plugin;
					}
				}
				$contenu['items'] = $items;
				$contenu['nb_plugins'] = count($items);
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = array_merge($contenu['erreur'], reponse_expliquer_erreur($contenu['erreur']));
	}

	// Construction de la réponse finale
	$reponse = reponse_construire($reponse, $contenu, $format_reponse);

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

	include_spip('inc/svpapi_requete');
	include_spip('inc/svpapi_reponse');

	// Initialisation du format de sortie du contenu de la réponse, du bloc d'erreur et du format de sortie en JSON
	$contenu = reponse_initialiser_contenu($requete);
	$erreur = array();
	$format_reponse = 'json';

	// Vérification du format de sortie demandé
	if (requete_verifier_format($contenu['requete']['format'], $erreur)) {
		// On positionne le format de sortie qui sera utilisé car on sait que celui demandé est valide
		$format_reponse = $contenu['requete']['format'];
		// Vérification du nom de la collection
		if (requete_verifier_ressource($contenu['requete']['collection'], $erreur)) {
			// Vérification du préfixe de la ressource
			if (requete_verifier_prefixe($contenu['requete']['ressource'], $erreur)) {
				$prefixe = strtoupper($contenu['requete']['ressource']);
				$items = array();
				// On recherche d'abord le plugin par son préfixe dans la table spip_plugins en vérifiant que
				// c'est bien un plugin fourni pas un dépôt et pas un plugin installé sur le serveur uniquement
				$from = array('spip_plugins', 'spip_depots_plugins AS dp');
				$select = array('*');
				$where = array(
					'prefixe=' . sql_quote($prefixe),
					'dp.id_depot>0',
					'dp.id_plugin=spip_plugins.id_plugin');
				$group_by = array('spip_plugins.id_plugin');
				$plugin = sql_fetsel($select, $from, $where, $group_by);
				if ($plugin) {
					// On refactore le tableau de sortie du fetsel en supprimant les colonnes id_depot et id_plugin qui ne
					// sont d'aucune utilité pour le service.
					unset($plugin['id_plugin']);
					unset($plugin['id_depot']);
					$items['plugin'] = $plugin;

					// On recherche maintenant les paquets du plugin
					$from = array('spip_paquets');
					$select = array('*');
					$where = array(
						'prefixe=' . sql_quote($prefixe),
						'id_depot>0');
					$paquets = sql_allfetsel($select, $from, $where);
					$items['paquets'] = array();
					if ($paquets) {
						// On refactore le tableau de sortie du allfetsel en un tableau associatif indexé par archives zip.
						$champs_inutiles = array(
							'id_paquet', 'id_plugin', 'id_depot',
							'actif', 'installe', 'recent', 'maj_version', 'superieur', 'obsolete', 'attente', 'constante', 'signature');
						foreach ($paquets as $_paquet) {
							foreach ($champs_inutiles as $_champ) {
								unset($_paquet[$_champ]);
							}
							$items['paquets'][$_paquet['nom_archive']] = $_paquet;
						}
					}
				} else {
					// On renvoie une erreur 404 pour indiquer que le plugin n'existe pas
					$erreur = array(
						'status'	=> 404,
						'type'		=> 'plugin_nok',
						'element'	=> 'plugin',
						'valeur'	=> $contenu['requete']['ressource']);
				}
				$contenu['items'] = $items;
			}
		}
	}

	// Si la réponse est une erreur, on complète le contenu avec les informations issues de la
	// vérification, le titre et le détail de l'erreur.
	if ($erreur) {
		$contenu['erreur'] = array_merge($contenu['erreur'], $erreur);
		$contenu['erreur'] = array_merge($contenu['erreur'], reponse_expliquer_erreur($contenu['erreur']));
	}

	// Construction de la réponse finale
	$reponse = reponse_construire($reponse, $contenu, $format_reponse);

	return $reponse;
}
