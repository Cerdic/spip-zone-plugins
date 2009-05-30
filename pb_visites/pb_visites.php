<?php

function pb_visites_logs_visites () {

	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// Ne pas compter les visiteurs sur les flux rss (qui sont pourtant
	// des pages web comme les autres) [hack pourri en attendant de trouver
	// une meilleure idee ?]
	if (preg_match(',^backend,', $GLOBALS['fond']))
		return;


	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . $_SERVER['HTTP_USER_AGENT']
		. $_SERVER['HTTP_ACCEPT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);

	// Analyse du referer
	// Ici on s'en fiche
	$log_referer = '';

	//
	// stockage sous forme de fichier ecrire/data/stats/client_id
	//


	$fond = $GLOBALS["page"]["contexte"]["page"];
            
            
	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$fichier = sous_repertoire(_DIR_TMP, 'pb_visites') . $client_id;
	if (lire_fichier($fichier, $content))
		$content = @unserialize($content);
		
		$content["ip"] = $GLOBALS['ip'];
		$content["fond"]["$fond"] ++;
		
		$content["fin"] = date("U");
		if (!isset($content["debut"])) $content["debut"] = date("U");

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {

	// Identification de l'element
	// Attention il s'agit bien des $GLOBALS, regles (dans le cas des urls
	// personnalises), par la carte d'identite de la page... ne pas utiliser
	// _request() ici !
		$log_type = $_SERVER["REQUEST_URI"];

		if (isset($content[$log_type]))
			$content[$log_type]++;
		else	$content[$log_type] = 1; // bienvenue au club

		ecrire_fichier($fichier, serialize($content));
	}


}


function pb_visites_sauver_infos ($texte) {
	if ( strstr($texte, "<html") ) {
		if($GLOBALS["auteur_session"]["statut"] != "0minirezo") { // Ne pas compter les admins (sinon, durees de visites enormes...)
			pb_visites_logs_visites();
		}
	}
	return $texte;
}

function pb_visites_ajouter_cron ($taches_generales) {
	$taches_generales['pb_visites_traiter'] = 300; 
	return $taches_generales;
}


function pb_visites_pb_traiter_visites ($vars) {
//	include_spip("inc/pb_visites_traiter"); 
//	pb_traiter_les_visites();

	return $vars;
}


?>
