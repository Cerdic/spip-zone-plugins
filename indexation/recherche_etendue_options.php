<?php
/*
 * Recherche entendue
 * plug-in d'outils pour la recherche et l'indexation
 * Panneaux de controle admin_index et index_tous
 * Boucle INDEX
 * filtre google_like
 *
 *
 * Auteur :
 * cedric.morin@yterium.com
 * pdepaepe et Nicolas Steinmetz pour google_like
 * � 2005 - Distribue sous licence GNU/GPL
 *
 */
if (!defined('_DIR_PLUGIN_INDEXATION')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_INDEXATION',(_DIR_PLUGINS.end($p)));
}

if (_request('recherche')&&_DIR_RACINE=="")
	RechercheEtendue_stats();

function RechercheEtendue_taches_generales_cron($taches_generales){
	$taches_generales['recherche_etendue_stats'] = 60;
	return $taches_generales;
}

// #SPIP_RECHERCHE_STAT
// insere un <div> avec un lien background-image vers la tache de fond 
// de collecte des requetes 'recherche'
// Cette balise doit etre presente sur la/les pages d'affichage des resultats
// de la recherche
function balise_SPIP_RECHERCHE_STAT_dist ($p) {
	$code = '<div style="background-image: url(\'' . generer_url_action('recherche_etendue_stats','recherche='._request('recherche').'&debut='._request('debut')) .
	'\');"></div>';
	$p->code = '"' . str_replace('"', '\"', $code) . '"';
	$p->interdire_scripts = false;
	return $p;
}

function RechercheEtendue_prepare_index_recherche($recherche, $cond=false){
	include_spip('inc/indexation');
spip_log('RechercheEtendue_prepare_index_recherche '.date('H:i:s'));
	static $cache = array();
	static $fcache = array();
	// traiter le cas {recherche?}
	if ($cond AND !strlen($recherche))
		return array("''" /* as points */, /* where */ '1');

	// Premier passage : chercher eventuel un cache des donnees sur le disque
	if (!$cache[$recherche]['hash']) {
		$dircache = _DIR_CACHE.creer_repertoire(_DIR_CACHE,'rech');
		$fcache[$recherche] =
			$dircache.'rech_'.substr(md5($recherche),0,10).'.txt';
		if (lire_fichier($fcache[$recherche], $contenu))
			$cache[$recherche] = @unserialize($contenu);
	}

	// si on n'a pas encore traite les donnees dans une boucle precedente
	if (!$cache[$recherche]['index']) {
		if (!$cache[$recherche]['hash'])
			$cache[$recherche]['hash'] = requete_hash($recherche);
		list($hash_recherche, $hash_recherche_strict)
			= $cache[$recherche]['hash'];

		$select = "SUM( index.points * ( 1 +99 * ( index.hash
IN ($hash_recherche_strict) ) ) )";
		$where = calcul_mysql_in('hash',$hash_recherche);

		$cache[$recherche]['index'] = array($select,$where);

		// ecrire le cache de la recherche sur le disque
		ecrire_fichier($fcache[$recherche], serialize($cache[$recherche]));
		// purger le petit cache
		nettoyer_petit_cache('rech', 300);
	}
	return $cache[$recherche]['index'];
}

function RechercheEtendue_stats(){
	// Rejet des robots (qui sont pourtant des humains comme les autres)
	if (preg_match(
	',google|yahoo|msnbot|crawl|lycos|voila|slurp|jeeves|teoma,i',
	$_SERVER['HTTP_USER_AGENT']))
		return;

	// Compter les recherches unitaires	
	
	// nettoyons tout cela
	$recherche = _request('recherche');
	$recherche = preg_replace(",<[^>]*>,U", "", $recherche);
	// ne pas oublier un < final non ferme
	$recherche = str_replace('<', ' ', $recherche);
	
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
	
}
?>