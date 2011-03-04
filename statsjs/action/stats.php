<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

// on recoit la demande de stats, on stocke la visite
// puis on fait un coup de cron
// => permet de supprimer les hits sur action=cron
function action_stats() {
	if ($GLOBALS['meta']["activer_statistiques"] != "non")
		do_stats();
	action_cron();
}

function do_stats() {
	// attention a un bug de piwik http://dev.piwik.org/trac/ticket/2115
	// (corrige en piwik 1.2)
	$obj = preg_replace(',[?].*,', '', _request('obj'));

	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (_IS_BOT) return;

	// Ne pas tenir compte des tentatives de spam des forums
	if ($_SERVER['REQUEST_METHOD'] !== 'GET'
	OR $_GET['page'] == 'forum')
		return;

	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . $_SERVER['HTTP_USER_AGENT']
//		. $_SERVER['HTTP_ACCEPT'] # HTTP_ACCEPT peut etre present ou non selon que l'on est dans la requete initiale, ou dans les hits associes
		. $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);

	// Analyse du referer
	$referer = $_GET['urlref']; // envoye par le js de piwik
	$log_referer = '';
	if (isset($referer)) {
		$url_site_spip = preg_replace(',/$,', '',
			preg_replace(',^(https?://)?(www\.)?,i', '',
			url_de_base()));
		if (!(($url_site_spip<>'')
		AND strpos('-'.strtolower($referer), strtolower($url_site_spip))
		AND strpos($referer,"recherche=")===false)) {
			$log_referer =$referer;
		}
	}

	//
	// stockage sous forme de fichier ecrire/data/stats/client_id
	//

	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$fichier = sous_repertoire(_DIR_TMP, 'visites') . $client_id;
	if (lire_fichier($fichier, $content))
		$content = @unserialize($content);

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

		// Identification de l'element
		if (preg_match(',^([a-z]+)(\d+)$,', $obj, $r))
			$log_type = $r[1]."\t" .$r[2];
		else
			$log_type = "autre\t0";

		$log_type .= "\t" . trim($log_referer);
		if (isset($content[$log_type]))
			$content[$log_type]++;
		else
			$content[$log_type] = 1; // bienvenue au club

		ecrire_fichier($fichier, serialize($content));
	}
}

?>
