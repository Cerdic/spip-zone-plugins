<?php
/**
 * Loguer les visites.
 *
 * Surcharge du plugin Statistiques de la dist pour prendre en compte tous les objets éditoriaux.
 * Créé les fichiers de visite dans tmp/visites.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 */

/**
 * Loguer une visite
 *
 * @plugin Statistiques pour SPIP
 * @license GNU/GPL
 * @package SPIP\Stats\Public
 **/

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Logue une visite sur une page
 *
 * Enregistre le passage d'un visiteur sur la page demandée
 * dans `tmp/visites/` qui seront ensuite traitées par une tache cron.
 *
 * Ne tient pas compte
 * - des visites de robots,
 * - des 404,
 * - des forum
 *
 * @see genie_visites_dist() Pour la tache cron qui traite les logs.
 *
 * @param array|null $contexte
 *     Contexte d'appel de la page ; retrouvé automatiquement sinon.
 * @param string|null $referer
 *     Referer de provenance ; retrouvé automatiquement sinon.
 * @return null|void
 **/
function public_stats_dist($contexte = null, $referer = null) {

	if (!is_array($contexte)) {
		$contexte = $GLOBALS['contexte'];
	}
	if (is_null($referer)) {
		// $_SERVER["HTTP_REFERER"] ne fonctionne pas partout
		if (isset($_SERVER['HTTP_REFERER'])) {
			$referer = $_SERVER['HTTP_REFERER'];
		} else {
			if (isset($GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"])) {
				$referer = $GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"];
			}
		}
	}

	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (_IS_BOT or (isset($referer) and strpbrk($referer, '<>"\''))) {
		return;
	}

	// Ne pas tenir compte des tentatives de spam des forums
	if ($_SERVER['REQUEST_METHOD'] !== 'GET'
		or (isset($contexte['page']) and $contexte['page'] == 'forum')
	) {
		return;
	}

	// rejet des pages 404
	if (isset($GLOBALS['page']['status'])
		and $GLOBALS['page']['status'] == 404
	) {
		return;
	}

	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '')
//		. $_SERVER['HTTP_ACCEPT'] # HTTP_ACCEPT peut etre present ou non selon que l'on est dans la requete initiale, ou dans les hits associes
		. (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '')
		. (isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '')
	), 0, 10);

	// Analyse du referer
	$log_referer = '';
	if (!isset($GLOBALS['meta']['activer_referers']) or $GLOBALS['meta']['activer_referers'] == "oui") {
		if (isset($referer)) {
			$url_site_spip = preg_replace(',/$,', '',
				preg_replace(',^(https?://)?(www\.)?,i', '',
					url_de_base()));
			if (!(($url_site_spip <> '')
				and strpos('-' . strtolower($referer), strtolower($url_site_spip))
				and strpos($referer, "recherche=") === false)
			) {
				$log_referer = $referer;
			}
		}
	}

	//
	// stockage sous forme de fichier tmp/visites/client_id
	//

	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$fichier = sous_repertoire(_DIR_TMP, 'visites') . $client_id;
	if (lire_fichier($fichier, $content)) {
		$content = @unserialize($content);
	}
	// fichier absent probablement (ou problème unserialize)
	if (!is_array($content)) {
		$content = array();
	}
	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

		// Identification de l'element
		// [MODIF] : prise en compte de tous les objets (pas que les articles).
		include_spip('inc/statistiques');
		if ($objet_contexte = identifier_objet_contexte($contexte)) {
			$log_type = $objet_contexte['objet']."\t".$objet_contexte['id_objet']; // patate 1
		} else {
			$log_type = "autre\t0"; // autre 0
		}
		$log_type .= "\t" . trim($log_referer);
		if ($log_type && isset($content[$log_type])) {
			$content[$log_type]++;
		} else {
			$content[$log_type] = 1;
		} // bienvenue au club

		ecrire_fichier($fichier, serialize($content));
	} else {
		$flood = sous_repertoire(_DIR_TMP, 'flood') . $GLOBALS['ip'];
		@touch($flood);
	}
}
