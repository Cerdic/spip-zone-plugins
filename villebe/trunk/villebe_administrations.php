<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Ville de belgique
 *
 * @plugin     Ville de belgique
 * @copyright  2015
 * @author     Phenix
 * @licence    GNU/GPL
 * @package    SPIP\Villebe\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin Ville de belgique.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function villebe_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
        array('maj_tables', array('spip_villes_belges', 'spip_villes_belges_liens')),
        array('peupler_base_villebe')
    );

	$maj['1.0.1'] = array(
        array('maj_tables', array('spip_villes_belges_liens')),
	);

	include_spip('base/upgrade');
    include_spip('base/villebe_peupler_base');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Ville de belgique.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function villebe_vider_tables($nom_meta_base_version) {

	sql_drop_table('spip_villes_belges');
	sql_drop_table('spip_villes_belges_liens');
	effacer_meta($nom_meta_base_version);
}
