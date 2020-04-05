<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('inc/meta');

// Installation et mise à jour
function bibliocheck_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_tickets'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Désinstallation
function bibliocheck_vider_tables($nom_meta_version_base){
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
	
	// Effacer les colonnes ajoutees a spip_tickets
	sql_alter("TABLE spip_tickets DROP COLUMN id_zitem");
	sql_alter("TABLE spip_tickets DROP COLUMN auteur");
	sql_alter("TABLE spip_tickets DROP COLUMN zitem_json");

	// On efface la configuration
	effacer_meta('bibliocheck');
}

