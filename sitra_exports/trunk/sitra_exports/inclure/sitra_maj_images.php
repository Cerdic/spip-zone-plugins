<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// *********
// Config
// *********

// stockage des images dans IMG : SITRA_CHEMIN_IMAGES;
// dossier sitra : SITRA_DIR

$nl = "\n";
$br = '<br />';
$hr = '<hr />';

// *********
// traitement des fichiers images et archive zip
// *********

$fichier_images = trouver_fichier_prefixe(SITRA_DIR,'('.SITRA_ID_SITE.')_ImagesOI_');

if (!$fichier_images) {
	message($nl.'Pas de fichier image zip', 'erreur');
	return;
}

$fichier_images = SITRA_DIR.$fichier_images;

message($nl.'/// Fichier image zip '.$fichier_images.' ///');
// on decompresse
include_spip('ecrire/inc/pclzip');
$archive = new PclZip($fichier_images);
$list = $archive -> extract(PCLZIP_OPT_PATH, SITRA_CHEMIN_IMAGES, PCLZIP_OPT_REMOVE_ALL_PATH);
if ($list != 0){
	message ('Decompression fichier image zip '.$fichier_images);
	foreach ($list as $row){
		message('Import image : '.$row['stored_filename']);
		rename(SITRA_CHEMIN_IMAGES.$row['stored_filename'], SITRA_CHEMIN_IMAGES.strtolower($row['stored_filename']));
	}
} else {
	message ('Erreur decompression fichier image zip '.$fichier_images, 'erreur');
}

// si pas en mode debug on supprime le fichier importé
if (!SITRA_DEBUG) {
	unlink($fichier_images);
	message('Suppression fichier '.$fichier_images);
}

message('/// Fin traitement fichier images zip ///');

?>