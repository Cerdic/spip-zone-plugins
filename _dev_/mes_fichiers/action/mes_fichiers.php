<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');

function action_mes_fichiers() {
	global $connect_statut, $connect_toutes_rubriques;
	$test = determine_upload();
	if(!($connect_statut == '0minirezo' and $connect_toutes_rubriques)) {
		include_spip('inc/headers');
		include_spip('inc/minipres');
		http_status('403');
		install_debut_html(_L('mes_fichiers.zip'));
		echo _T('ecrire:avis_non_acces_page');
		install_fin_html();
		exit;
	}
	include_spip('inc/pclzip');
	$mes_options = defined('_FILE_OPTIONS') ? _FILE_OPTIONS : 'ecrire/mes_options';
	$IMG = defined('_DIR_IMG') ? _DIR_IMG: 'IMG/';
	$liste = array();
	if(@is_dir($IMG)) $liste[] = $IMG;
	if(@is_dir('squelettes/')) $liste[] = 'squelettes/';
	if(@is_readable($mes_options)) $liste[] = $mes_options;
	$mes_fichiers = new PclZip('mes_fichiers.zip');
	$erreur = $mes_fichiers->create($liste, PCLZIP_OPT_ADD_PATH, "spip");
	if ($erreur == 0) {
		die("Erreur : ".$mes_fichiers->errorInfo(true));
	}
	@header('Location: ecrire/');
}

?>