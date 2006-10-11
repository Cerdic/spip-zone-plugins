<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_decompresser_mes_fichiers() {
	global $dir_base;
	global $lang, $langues, $idx_lang;
	define(
		'_URL_PAQUET_ZIP',
		'./mes_fichiers.zip'
	);
	define(
		'_SPIP_LOADER_SCRIPT',
		'spip.php?action=decompresser_mes_fichiers'
	);
	include_once('spip_loader.php');
	exit;
}

?>