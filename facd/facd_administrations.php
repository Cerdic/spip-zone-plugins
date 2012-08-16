<?php
/**
 * Fichier d'installation du plugin
 *
 * @plugin FACD pour SPIP
 * @author b_b
 * @author kent1 (http://www.kent1.info - kent1@arscenic.info)
 * @license GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation et mises à jour du plugin
 * 
 * Crée la table SQL du plugin (spip_facd_conversions)
 * 
 * @param string $nom_meta_base_version
 *   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *   Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function facd_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_facd_conversions'))
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin
 * 
 * Supprime la table SQL du plugin (spip_facd_conversions)
 * 
 * @param string $nom_meta_base_version
 *   Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function facd_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_facd_conversions');
	effacer_meta($nom_meta_base_version);
}
?>