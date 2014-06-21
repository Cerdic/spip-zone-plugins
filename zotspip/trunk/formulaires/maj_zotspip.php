<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_maj_zotspip_charger_dist(){
	$zotspip_maj = isset($GLOBALS['meta']['zotspip_maj']) ? unserialize($GLOBALS['meta']['zotspip_maj']) : array('forcer' => false);
	$avancement = '';
	if (isset($zotspip_maj['step']) & $zotspip_maj['step']=='items') {
		$zotspip_maj_items = unserialize($GLOBALS['meta']['zotspip_maj_items']);
		$avancement = _T('zotspip:plusieurs_references_sync',array('nb'=>isset($zotspip_maj_items['start'])?$zotspip_maj_items['start']:0));
	}
	if (isset($zotspip_maj['step']) & $zotspip_maj['step']=='collections') {
		$zotspip_maj_collections = unserialize($GLOBALS['meta']['zotspip_maj_collections']);
		$avancement = _T('zotspip:plusieurs_collections_sync',array('nb'=>isset($zotspip_maj_collections['start'])?$zotspip_maj_collections['start']:0));
	}
	if (isset($zotspip_maj['step']) & $zotspip_maj['step']=='nettoyage') {
		$avancement = _T('zotspip:nettoyage');
	}
	
	$contexte = array(
		'forcer'=> $zotspip_maj['forcer'] ? 'on' : '',
		'sync' => isset($zotspip_maj['step']) ? 'on' : '',
		'avancement' => $avancement
	);
	return $contexte;
}

function formulaires_maj_zotspip_traiter_dist(){
	include_spip('inc/zotspip');
	$forcer = (_request('sync_complete')) ? true : false;
	
	$cont = zotspip_maj($forcer);
	
	if ($cont==0)
		return array('message_erreur' => _T('zotspip:erreur_connexion'));
	if ($cont>0) {
		return array('message_ok' => _T('zotspip:synchronisation_effectuee'));
	}
}

?>