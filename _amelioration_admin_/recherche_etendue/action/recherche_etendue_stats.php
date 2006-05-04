<?php

function action_recherche_etendue_stats(){
	envoie_image_vide();
	
	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// Compter les recherches unitaires	
	
	// nettoyons tout cela
	$recherche = _request('recherche');
	$recherche = attribut_html($recherche);
	$debut = intval(_request('debut'));
	
	// Identification du client
	$client_id = substr(md5(
		$GLOBALS['ip'] . $_SERVER['HTTP_USER_AGENT']
		. $_SERVER['HTTP_ACCEPT'] . $_SERVER['HTTP_ACCEPT_LANGUAGE']
		. $_SERVER['HTTP_ACCEPT_ENCODING']
	), 0,10);
	
	//
	// stockage sous forme de fichier ecrire/data/recherches/client_id
	//

	// 1. Chercher s'il existe deja une session pour ce numero IP.
	$content = array();
	$session = sous_repertoire(_DIR_SESSIONS, 'recherches') . $client_id;
	if (lire_fichier($session, $content))
		$content = @unserialize($content);

	// 2. Plafonner le nombre de hits pris en compte pour un IP (robots etc.)
	// et ecrire la session
	if (count($content) < 200) {
		$content[$recherche][$debut] ++;
		ecrire_fichier($session, serialize($content));
	}

	// Agreger les recherches dans une table SQL
	
}
?>