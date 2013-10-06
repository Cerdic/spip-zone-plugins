<?php
/**
 * Plugin Grappes
 * Licence GPL (c) Matthieu Marcillaud
 * 
 * Fichier des fonctions d'installation, de mise à jour et de suppression
 * du plugin
 * 
 * @package Grappes\Installation
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param string $nom_meta_base_version
 * 	Le nom de la meta d'installation
 * @param float $version_cible
 * 	Version du shema d'installation de la base
 */
function grappes_upgrade($nom_meta_base_version,$version_cible){

	include_spip('base/create');

	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_grappes', 'spip_grappes_liens')));
	$maj['0.2.0']  = array(array('maj_tables', 'spip_grappes'));
	$maj['0.2.1']  = array(array('maj_tables', array('spip_grappes', 'spip_grappes_liens')));
	$maj['0.2.2']  = array(array('sql_alter', 'TABLE spip_grappes_liens CHANGE COLUMN rang rang bigint(21) NOT NULL DEFAULT 0'));
	$maj['0.2.3']  = array(array('sql_updateq',"spip_grappes_liens",array('objet'=>'site'),"objet='syndic'"));
	$maj['0.2.4']  = array(array('sql_alter',"TABLE spip_grappes ADD date datetime NOT NULL DEFAULT '0000-00-00 00:00:00'"));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation du plugin
 * 
 * Supprime les deux tables :
 * - spip_grappes
 * - spip_grappes_liens
 * Supprime la meta d'installation
 * 
 * @param string $nom_meta_base_version
 * 	Le nom de la meta d'installation
 */
function grappes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_grappes");
	sql_drop_table("spip_grappes_liens");
	effacer_meta($nom_meta_base_version);
}

?>
