<?php
/**
 * Plugin Signaler des abus
 * (c) 2012 My Chacra
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
 * Vous pouvez :
 * - créer la structure SQL,
 * - insérer du pre-contenu,
 * - installer des valeurs de configuration,
 * - mettre à jour la structure SQL 
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
        
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 * Vous devez :
 * - nettoyer toutes les données ajoutées par le plugin et selection_objetn utilisation
 * - supprimer les tables et les champs créés par le plugin. 
**/
function selection_objet_vider_tables($nom_meta_base_version) {
	# quelques exemples
	# (que vous pouvez supprimer !)
	# sql_drop_table("spip_xx");
	# sql_drop_table("spip_xx_liens");

	sql_drop_table("spip_selection_objets");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('abuselection_objetbjet')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('abuselection_objetbjet')));
	sql_delete("spip_forum",                 sql_in("objet", array('abuselection_objetbjet')));

	effacer_meta($nom_meta_base_version);
}

?>
