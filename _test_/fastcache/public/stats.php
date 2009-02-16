<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


function public_stats() {
	// $_SERVER["HTTP_REFERER"] ne fonctionne pas partout
	if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
	else if (isset($GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"])) $referer = $GLOBALS["HTTP_SERVER_VARS"]["HTTP_REFERER"];
	
	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// rejet des pages 404
	if (isset($GLOBALS['page']['status'])
	AND $GLOBALS['page']['status'] == 404)
		return;

	// Identification du client
	$client_id = substr(md5(
		$_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']
//		. $_SERVER['HTTP_ACCEPT'] # HTTP_ACCEPT peut etre present ou non selon que l'on est dans la requete initiale, ou dans les hits associes
		. $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);

	// Analyse du referer
	$log_referer = '';
	if (isset($referer)) {
		$url_site_spip = preg_replace(',/$,', '',
			preg_replace(',^(https?://)?(www\.)?,i', '',
			$_SERVER['HTTP_HOST']));
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
	$fichier = _DIR_TMP . 'visites/' . $client_id;
	if ($content = @file_get_contents($fichier))
		$content = @unserialize($content);

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

		// Identification de l'element
		if (isset($GLOBALS['contexte']['id_article']))
			$log_type = "article";
		else if (isset($GLOBALS['contexte']['id_breve']))
			$log_type = "breve";
		else if (isset($GLOBALS['contexte']['id_rubrique']))
			$log_type = "rubrique";
		else
			$log_type = "";

		if ($log_type)
			$log_type .= "\t" . intval($GLOBALS['contexte']["id_$log_type"]);
		else    $log_type = "autre\t0";

		$log_type .= "\t" . trim($log_referer);
		if (isset($content[$log_type]))
			$content[$log_type]++;
		else	$content[$log_type] = 1; // bienvenue au club

		if ($tmp = tempnam(dirname($fichier), 'tmp')
		AND $fw = fopen($tmp, 'w')
		AND fwrite($fw, serialize($content))) {
			fclose($fw);
			if (is_file($fichier)) unlink($fichier);
			rename($tmp,$fichier);
		}
	}
}

?>
