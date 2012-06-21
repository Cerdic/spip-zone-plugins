<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2012 - Distribué sous licence GNU/GPL
 *
 * Définition des tables
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function getid3_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents')),
		array('getid3_verifier_binaires',array())
	);
	$maj['0.1'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.2'] = array(
		array('maj_tables',array('spip_documents')),
	);
	$maj['0.3.1'] = array(
		array('getid3_verifier_binaires',array())
	);
	
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function getid3_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
}

function getid3_verifier_binaires(){
	$getid3_binaires = charger_fonction('getid3_verifier_binaires','inc');
	$getid3_binaires(true);
}
?>