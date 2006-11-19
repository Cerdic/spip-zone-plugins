<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_spip_loader() {
	global $dir_base;
	global $lang, $langues, $idx_lang;
	include_spip('inc/spip_loader_update');
	$spip_loader_liste = spip_loader_liste();
	
	define(
		'_NOM_PAQUET_ZIP',
		strtoupper(_request('paquet'))
	);
	define(
		'_URL_PAQUET_ZIP',
		$spip_loader_liste[_request('paquet')]
	);
	define(
		'_SPIP_LOADER_URL_RETOUR',
		'ecrire/?exec=spip_loader_update&paquet='._request('paquet')
	);
	define(
		'_SPIP_LOADER_SCRIPT',
		'spip.php?action=spip_loader'
	);
	include_once('spip_loader.php');
	exit;
}

?>