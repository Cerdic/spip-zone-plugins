<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 */

include_spip('inc/meta');

include_spip('base/gmap_tables');


function gmap_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	
	// Création "from scratch"
	$maj['create'] = array(
		array('maj_tables', array('spip_geopoints', 'spip_geopoints_liens', 'spip_types_geopoints')),
		array('gmap_cree_types_defaut'),
		array('gmap_verif_types_documents'),
		array('gmap_initialize_configuration'),
	);
	
	// Mise à jour pour version 1.0.1 (ajout du champs nom dans les libellés)
	$maj['1.0.1'] = array(
	
		array('sql_alter', "TABLE spip_gmap_points RENAME TO spip_geopoints"),
		array('sql_alter', "TABLE spip_gmap_points_liens RENAME TO spip_geopoints_liens"),
		array('sql_alter', "TABLE spip_gmap_types RENAME TO spip_types_geopoints"),
		
		array('sql_drop_table', "spip_gmap_labels"),
		
		array('sql_alter', "TABLE spip_geopoints CHANGE id_point id_geopoint bigint(21) NOT NULL AUTO_INCREMENT"),
		array('sql_alter', "TABLE spip_geopoints ADD id_parent bigint(21) DEFAULT '0' NOT NULL"),
		array('sql_alter', "TABLE spip_geopoints ADD nom text DEFAULT '' NOT NULL"),
		array('sql_alter', "TABLE spip_geopoints ADD descriptif text DEFAULT '' NOT NULL"),
		array('sql_alter', "TABLE spip_geopoints ADD date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL"),
		array('sql_alter', "TABLE spip_geopoints CHANGE id_type_point id_type_geopoint bigint(21) DEFAULT '0' NOT NULL"),
		array('sql_alter', "TABLE spip_geopoints ADD tile char(20) DEFAULT '' NOT NULL"),
		
		array('sql_alter', "TABLE spip_geopoints_liens CHANGE id_point id_geopoint bigint(21) DEFAULT '0' NOT NULL"),
		
		array('sql_alter', "TABLE spip_types_geopoints CHANGE id_type_point id_type_geopoint bigint(21) NOT NULL AUTO_INCREMENT"),
		
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
	
}


function gmap_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_geopoints");
	sql_drop_table("spip_geopoints_liens");
	sql_drop_table("spip_types_geopoints");
	
	effacer_meta($nom_meta_base_version);
	
}

?>