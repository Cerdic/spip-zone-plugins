<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_mes_fichiers() {

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
	
	include_spip('inc/pclzip');
	$mes_options = defined('_FILE_OPTIONS') ? _FILE_OPTIONS : 'ecrire/mes_options.php';
	$IMG = defined('_DIR_IMG') ? _DIR_IMG: 'IMG/';
	$tmp_dump = defined('_DIR_DUMP') ? _DIR_DUMP: 'tmp/dump/';
	$liste = array();
	if (@is_dir($IMG)) {
		$liste[] = $IMG;
	}
	if (@is_dir('squelettes/')) {
		$liste[] = 'squelettes/';
	}
	if (@is_readable($mes_options)) {
		$liste[] = $mes_options;
	}
	$dump = preg_files($tmp_dump);
	$fichier_dump = '';
	$mtime = 0;
	foreach ($dump as $_fichier_dump) {
		if (($_mtime = filemtime($_fichier_dump)) > $mtime) {
			$fichier_dump = $_fichier_dump;
			$mtime = $_mtime;
		}
	}
	if ($fichier_dump) {
		$liste[] = $fichier_dump;
	}
	spip_log('*** mes_fichiers ***');
	spip_log($liste);
	$mes_fichiers = new PclZip(_DIR_TMP . 'mes_fichiers_'.date("Ymd_Hi").'.zip');
	$erreur = $mes_fichiers->create($liste, PCLZIP_OPT_ADD_PATH, "spip");
	if ($erreur == 0) {
		die("Erreur : ".$mes_fichiers->errorInfo(true));
	}
	redirige_par_entete(generer_url_ecrire('admin_tech', 'mes_fichier=sauve_ok', true));
}

?>