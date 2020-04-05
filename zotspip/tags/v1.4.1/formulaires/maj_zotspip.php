<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_maj_zotspip_charger_dist(){
	$zotspip_maj_items = isset($GLOBALS['meta']['zotspip_maj_items']) ? unserialize($GLOBALS['meta']['zotspip_maj_items']) : array('forcer' => false);
	$contexte = array(
		'forcer'=> $zotspip_maj_items['forcer'] ? 'on' : '',
		'sync' => isset($zotspip_maj_items['start']) ? 'on' : ''
	);
	return $contexte;
}

function formulaires_maj_zotspip_traiter_dist(){
	include_spip('inc/zotspip');
	$forcer = (_request('sync_complete')) ? true : false;
	if (_request('nettoyer')) {
		zotspip_nettoyer(); // Eviter de nettoyer à chaque tour, la première fois suffit
		zotspip_maj_collections($forcer); // De meme, pour les collections, un seul appel suffit 
	}
	
	$cont = zotspip_maj_items($forcer);
	
	if ($cont==0)
		return array('message_erreur' => _T('zotspip:erreur_connexion'));
	if ($cont>0)
		return array('message_ok' => _T('zotspip:synchronisation_effectuee'));
}

?>