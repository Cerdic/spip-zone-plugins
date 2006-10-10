<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_Installer_SpipClear_dist() {
	global $dir_base;
	global $lang, $langues, $idx_lang;
	define(
		'_URL_PAQUET_ZIP',
		'http://trac.rezo.net/files/spip-zone/SpipClear.zip'
	);
	define(
		'_SPIP_LOADER_URL_RETOUR',
		'ecrire/?exec=admin_plugin'
	);
	define(
		'_SPIP_LOADER_SCRIPT',
		'spip.php?action=installer_spipclear'
	);
	include_once('spip_loader.php');
	exit;
}

?>