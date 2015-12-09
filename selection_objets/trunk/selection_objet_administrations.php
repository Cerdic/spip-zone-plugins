<?php
/**
 * Plugin Sélection d'objets
 * (c) 2012 Rainer Müller
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function selection_objet_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(array('maj_tables', array('spip_selection_objets')));
	$maj['0.1.0'] = array(array('maj_tables', array('spip_selection_objets')));
	$maj['0.2.0'] = array(array('maj_tables', array('spip_selection_objets')));
	$maj['0.2.1'] = array(array('maj_tables', array('spip_selection_objets')));    
	$maj['0.2.2'] = array(array('maj_tables', array('spip_selection_objets')));  
	$maj['0.2.3'] = array(array('maj_tables', array('spip_selection_objets'))); 
	$maj['0.2.4'] = array(array('maj_tables', array('spip_selection_objets')));   
	$maj['0.3.0'] = array(array('maj_tables', array('spip_selection_objets'))); 
	$maj['0.4.0'] = array(array('maj_tables', array('spip_selection_objets'))); 
	$maj['0.5.0'] = array(array('maj_tables', array('spip_selection_objets')));  
	$maj['0.5.1'] = array(
		array('sql_alter', "TABLE spip_selection_objets ADD KEY (id_objet)"),
		array('sql_alter', "TABLE spip_selection_objets ADD KEY (id_objet_dest)"),
		array('sql_alter', "TABLE spip_selection_objets ADD KEY (objet)"),
		array('sql_alter', "TABLE spip_selection_objets ADD KEY (objet_dest)"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function selection_objet_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_selection_objets");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('selection_objet')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('selection_objet')));
	sql_delete("spip_forum",                 sql_in("objet", array('selection_objet')));

	effacer_meta($nom_meta_base_version);
}

