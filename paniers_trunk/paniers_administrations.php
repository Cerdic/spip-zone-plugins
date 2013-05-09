<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin paniers
 *
 * @plugin     Paniers
 * @copyright  2013
 * @author     Les Développements Durables, cédric Morin
 * @licence    GPL v3
 * @package    SPIP\Paniers\Installation
 */
 
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin paniers.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function paniers_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	include_spip('base/abstract_sql');
	include_spip('inc/config');

	// Première installation
	// création des tables + options de configuration
	$maj['create'] = array(
		array('maj_tables', array('spip_paniers', 'spip_paniers_liens')),
		array('ecrire_config', 'paniers', array(
			'limite_ephemere' => '24',
			'limite_enregistres' => '168')
		)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin paniers.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function paniers_vider_tables($nom_meta_base_version){

	include_spip('base/abstract_sql');
	include_spip('inc/config');

	// On efface les tables du plugin
	sql_drop_table('spip_paniers');
	sql_drop_table('spip_paniers_liens');

	// On efface la version entregistrée
	effacer_meta($nom_meta_base_version);
	effacer_config('paniers');

}

?>
