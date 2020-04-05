<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// *********
// Config
// *********


$nl = "\n";
$br = '<br />';
$hr = '<hr />';

// *********
// traitement du fichier export
// *********

$fichier_export = trouver_fichier_prefixe(SITRA_DIR,'('.SITRA_ID_SITE.')_export_');

if (!$fichier_export) {
	message($nl.'Pas de fichier _export_', 'erreur');
	return;
}

$fichier_export = SITRA_DIR.$fichier_export;

message($nl.'/// Fichier export '.$fichier_export.' ///');
// on decomprime
include_spip('ecrire/inc/pclzip');
$archive = new PclZip($fichier_export);
$list = $archive -> extract(PCLZIP_OPT_PATH, SITRA_DIR, PCLZIP_OPT_REMOVE_ALL_PATH);
if ($list != 0){
	message ('Decompression fichier export '.$fichier_export);
	foreach ($list as $row){
		message('Decompression fichier : '.$row['stored_filename']);
	}
} else {
	message ('Erreur decompression fichier export '.$fichier_export, 'erreur');
}
// si pas en mode debug on supprime le fichier importé
if (!SITRA_DEBUG) {
	unlink($fichier_export);
	message('Suppression fichier '.$fichier_export);
}

message('/// Fin traitement fichier _export_ ///');

?>