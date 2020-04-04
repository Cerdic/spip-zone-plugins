<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin spipiris
 *
 * @plugin     spipiris
 * @copyright  2012-2015
 * @author     Phil
 * @licence    GNU/GPL
 * @package    SPIP\SPIPiris\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin spipiris.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function spipiris_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin spipiris.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function spipiris_vider_tables($nom_meta_base_version) {
	effacer_meta('spipiris');
	// Les metas du plugin sont stockés avec 'cooliris'
	effacer_meta('cooliris');
	effacer_meta($nom_meta_base_version);
}

?>