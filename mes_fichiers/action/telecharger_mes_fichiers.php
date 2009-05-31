<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_telecharger_mes_fichiers() {

	//securite
	if(@function_exists('autoriser')) {
		if(!autoriser('sauvegarder')) {
			$paspermis = true;
		}
	}
	//vieille methode pour compatiblite ascendante
	else {	
		global $connect_statut;
		global $connect_toutes_rubriques;
		if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
			$paspermis = true;
		}
	}

	if($paspermis) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	if (!isset($nom_zip)) {
		$liste_zip = preg_files(_DIR_TMP . 'mes_fichiers_');
		$fichier_zip = '';
		$mtime_zip = 0;
		foreach ($liste_zip as $_fichier) {
			if (($_mtime = filemtime($_fichier)) > $mtime_zip) {
				$fichier_zip = $_fichier;
				$mtime_zip = $_mtime;
			}
		}
	}

	header("Content-type: application/force-download;");
	header("Content-Transfer-Encoding: application/zip");	header("Content-Length: ".filesize($fichier_zip));	header("Content-Disposition: attachment; filename=\"".basename($fichier_zip)."\"");	header("Pragma: no-cache");	header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public");	header("Expires: 0");	readfile($fichier_zip);
	exit;
}

?>