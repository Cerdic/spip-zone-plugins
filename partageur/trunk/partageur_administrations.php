<?php
/**
 * Plugin Partageur
 * 
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation du plugin et de mise à jour.
**/
function partageur_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
 
  $maj['create'] = array(
	   array('maj_tables', array('spip_partageurs')),
     array('sql_alter','TABLE spip_articles ADD s2s_url VARCHAR(255) DEFAULT \'\' NOT NULL'),
	   array('sql_alter','TABLE spip_articles ADD s2s_url_trad VARCHAR(255) DEFAULT \'\' NOT NULL'),
	);
  
  // pour la migration venant de SPIP 2 : ajout du statut
  $maj['1.1'] = array( 		
    array('sql_alter',"TABLE spip_partageurs ADD `statut` varchar(20) NOT NULL DEFAULT 'publie'")
	); 

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
**/
function partageur_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_partageurs");
  
  # Nettoyer les colonnes en extra
  sql_alter("TABLE spip_articles DROP COLUMN s2s_url");
	sql_alter("TABLE spip_articles DROP COLUMN s2s_url_trad");	

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions",              sql_in("objet", array('partageur')));
	sql_delete("spip_versions_fragments",    sql_in("objet", array('partageur')));
	sql_delete("spip_forum",                 sql_in("objet", array('partageur')));

	effacer_meta($nom_meta_base_version);
}

?>