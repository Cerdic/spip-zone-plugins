<?php
/**
 * Plugin Licence
 *
 * (c) 2007-2013 fanouch
 * Distribue sous licence GPL
 *
 * Modification des tables
 * 
 * @package SPIP\Licence\Administration
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Installation ou mise à jour du plugin
 * 
 * Ajoute un champ id_licence sur les tables spip_articles et spip_documents
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la meta d'installation
 * @param float $version_cible
 * 		La version vers laquelle installer
 * @return void
 */
function licence_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_articles')),
		array('maj_tables',array('spip_documents'))
	);
	
	$maj['0.2.0'] = array('maj_tables',array('spip_documents'));
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Désinstallation du plugin
 * 
 * On supprime : 
 * -* La meta de configuration
 * -* La meta d'installation
 * 
 * On laisse :
 * -* Les nouveaux champs sur les tables spip_documents et spip_articles
 * 
 * @param string $nom_meta_base_version
 * 		Le nom de la meta d'installation
 * @return void
 */
function licence_vider_tables($nom_meta_base_version) {
	effacer_meta('licence');
	effacer_meta($nom_meta_base_version);
}

?>