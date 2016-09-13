<?php
/**
 * Plugin PDF_VERSION pour Spip 3.x
 * Licence GPL (c) 2016 Cedric
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function pdf_version_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('pdf_version_protection_documents'),
	);

	$maj['0.1.0'] = array(
		array('pdf_version_protection_documents'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function pdf_version_protection_documents(){
	include_spip("inc/pdf_version");
	if (!is_dir(_DIR_PDF_VERSION)){
		sous_repertoire(_DIR_PDF_VERSION);
	}
	pdf_version_gerer_htaccess(true);
}

/**
 * Fonction de desinstallation
 *
 * @param string $nom_meta_base_version
 */
function pdf_version_vider_tables($nom_meta_base_version) {
	include_spip("inc/pdf_version");
	// vider le repertoire _DIR_PDF_VERSION
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_PDF_VERSION);
	pdf_version_gerer_htaccess(false);
	spip_unlink(_DIR_PDF_VERSION);
	effacer_meta($nom_meta_base_version);
}