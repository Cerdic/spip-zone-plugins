<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin SMS
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin SMS
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function sms_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array( array('maj_tables', array('spip_sms_logs')));
	$maj['1.0.4'] = array( array('maj_tables', array('spip_sms_logs')));

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin Boutique Episur.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function sms_vider_tables($nom_meta_base_version) {

	effacer_meta($nom_meta_base_version);
}
