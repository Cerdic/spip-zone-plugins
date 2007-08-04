<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_decompresser_mes_fichiers($nom_zip=NULL) {

	if (!isset($nom_zip)) {
		$liste_zip = preg_files('tmp/mes_fichiers_');
		$fichier_zip = '';
		$mtime_zip = 0;
		foreach ($liste_zip as $_fichier) {
			if (($_mtime = filemtime($_fichier)) > $mtime_zip) {
				$fichier_zip = $_fichier;
				$mtime_zip = $_mtime;
			}
		}
		$nom_zip = substr($fichier_zip, strpos($fichier_zip, '/')+1, strlen($fichier_zip)-strpos($fichier_zip, '/'));
	}
	
	define(
		'_NOM_PAQUET_ZIP',
		$nom_zip
	);
	define(
		'_URL_PAQUET_ZIP',
		'./'.$nom_zip
	);
	
	define(
		'_SPIP_LOADER_SCRIPT',
		'spip.php?action=decompresser_mes_fichiers'
	);
	define(
		'_SPIP_LOADER_URL_RETOUR',
		'ecrire/?exec=admin_tech&mes_fichiers=restauration_ok'
	);

	include_once('spip_loader.php');
	exit;
}

?>