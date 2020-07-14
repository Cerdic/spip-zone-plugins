<?php
/**
 * Fichier gérant l'installation et la désinstallation du plugin
 *
 * @package    SPIP\ISOCODE\ADMINISTRATION
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'installation et de mise à jour du plugin.
 * Le schéma du plugin est composé des tables `spip_iso639xxxx` et d'une configuration.
 *
 * @param string $nom_meta_base_version
 *        Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *        Version du schéma de données (déclaré dans paquet.xml)
 *
 * @return void
 **/
function isocode_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Liste des tables créées par le plugin
	include_spip('inc/isocode');
	$tables = array();
	foreach (isocode_lister_types_service() as $_type) {
		foreach (isocode_lister_tables($_type) as $_table) {
			$tables[] = "spip_${_table}";
		}
	}

	$maj['create'] = array(
		array(
			'maj_tables',
			$tables
		),
	);

	$maj['2'] = array(
		array(
			'maj_tables',
			'spip_geoboundaries'
		),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}


/**
 * Fonction de désinstallation du plugin.
 *
 * @param string $nom_meta_base_version
 *        Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP.
 *
 * @return void
 **/
function isocode_vider_tables($nom_meta_base_version) {

	// Supprimer les tables ISO créées par le plugin
	include_spip('inc/isocode');
	foreach (isocode_lister_types_service() as $_type) {
		foreach (isocode_lister_tables($_type) as $_table) {
			sql_drop_table("spip_${_table}");
		}
	}

	// Effacer la meta de configuration et de stockage du plugin
	effacer_meta('isocode');

	// Effacer la meta du schéma de la base
	effacer_meta($nom_meta_base_version);
}
