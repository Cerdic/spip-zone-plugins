<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Identifiants
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     C.R
 * @licence    GNU/GPL
 * @package    SPIP\Identifiants\Installation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Fonction d'installation et de mise à jour du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function identifiants_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_identifiants')),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Identifiants.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function identifiants_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_identifiants");

	effacer_meta($nom_meta_base_version);
}

?>
