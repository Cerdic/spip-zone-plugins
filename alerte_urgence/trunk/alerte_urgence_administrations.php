<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Alerte d'urgence
 *
 * @plugin     Alerte d'urgence
 * @copyright  2014
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Alerte d'urgence\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Alerte d'urgence.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function alerte_urgence_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('base/upgrade');
	
	$maj = array();
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Alerte d'urgence.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function alerte_urgence_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	effacer_meta('alerte_urgence');
}

?>
