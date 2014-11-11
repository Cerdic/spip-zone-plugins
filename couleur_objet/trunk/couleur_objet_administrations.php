<?php
/**
 * Fonctions d'installation et de désinstallation du plugin couleur_objet
 *
 * @plugin     couleur_objet
 * @copyright  2014
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\couleur_objet\Administrations
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 */
function couleur_objet_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();

	// Création des tables + options de configuration
	$maj['create'] = array(
		array('maj_tables', array('spip_couleur_objet_liens'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 */
function couleur_objet_vider_tables($nom_meta_base_version) {

	# Supression des tables
	sql_drop_table("spip_couleur_objet_liens");

	# Suppression meta
	effacer_meta($nom_meta_base_version);
	effacer_meta('couleur_objet');


}
