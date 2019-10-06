<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin stocks.
 *
 * @plugin    stocks
 * @licence   GNU/GPL
 * @package   SPIP\stocks\Administrations
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin stocks.
 *
 * @param string $nom_meta_base_version
 *		 Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *		 Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
**/
function stocks_upgrade($nom_meta_base_version, $version_cible) {

	include_spip('inc/config');

	$maj = array();

	$maj['create'] = array(
		array('maj_tables', array('spip_stocks'))
	);

	$maj['1.0.0'] = array(
		array('maj_tables', array('spip_stocks'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation du plugin stocks.
 *
 * @param string $nom_meta_base_version
 *		 Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
**/
function stocks_vider_tables($nom_meta_base_version) {
	// tables
	sql_drop_table('spip_stocks');
	//Metas
	effacer_meta($nom_meta_base_version);
}
