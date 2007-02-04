<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
define('_SPIP_LOADER_UPDATE_AUTEURS', _ID_WEBMESTRES);

function action_spip_loader() {
	global $dir_base;
	global $lang, $langues, $idx_lang;
	global $connect_id_auteur;
	include_spip('inc/mise_a_jour');
	$spip_loader_liste = spip_loader_liste(_SPIP_MAJ_FILE);
	$paquet = _request('paquet');
	
	define(
		'_NOM_PAQUET_ZIP',
		$paquet
	);
	define(
		'_URL_PAQUET_ZIP',
		$spip_loader_liste[$paquet][0]
	);
	define(
		'_DEST_PAQUET_ZIP',
		$spip_loader_liste[$paquet][1]
	);
	define(
		'_SPIP_LOADER_URL_RETOUR',
		'spip.php?action=mise_a_jour&paquet='.$paquet.'&redirect='.generer_url_ecrire('mise_a_jour')
	);
	define(
		'_SPIP_LOADER_SCRIPT',
		'spip.php?action=spip_loader'
	);

	//creation du sous repertoire ?
	if(_DEST_PAQUET_ZIP != '' AND !is_dir(_DEST_PAQUET_ZIP)){
		include_spip('inc/mise_a_jour');
		mkdir_r(_DEST_PAQUET_ZIP);
	}	

	include_once('spip_loader.php');
	exit;
}

?>