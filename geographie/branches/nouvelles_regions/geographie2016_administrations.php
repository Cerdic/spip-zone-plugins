<?php
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Géographie
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 *
 * @return void
 **/
function geographie2016_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Installation du plugin, deux cas possibles : on importe à neuf, ou bien on reprend l'ancien plugins geographie
	$maj['create'] = array(
		array('creer_base')
	);
	// Cas 1 : il y avait le plugin. Dans ce cas là, on considère simplement qu'il n'existe plus
	if (lire_config('geographie_base_version')) {
		effacer_config('geographie_base_version');
	} else {//Cas 2 : le plugin n'existe pas encore, dans ce cas on considère qu'il faut créer les tables
		$maj['create'][] = array('geographie_upgrade_importer_geographie'); // importation de presque toute la géo sauf arrondissements
		$maj['create'][] = array('geographie_upgrade_importer_arrondissements'); // importation des arrondissements
	}

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function geographie_upgrade_importer_geographie() {
	if ($importer_geographie = charger_fonction('geographie', 'imports')) {
		$importer_geographie();
	}
}

function geographie_upgrade_importer_arrondissements() {
	if ($importer_arrondissements = charger_fonction('arrondissements', 'imports')) {
		$importer_arrondissements();
	}
}

function geographie_upgrade_importer_pays() {
	include_spip('imports/pays');
	sql_insertq_multi('spip_geo_pays', $GLOBALS['liste_pays']);
}

/**
 * Fonction de désinstallation du plugin Géographie
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 *
 * @return void
 **/
function geographie2016_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_geo_pays');
	sql_drop_table('spip_geo_regions');
	sql_drop_table('spip_geo_departements');
	sql_drop_table('spip_geo_arrondissements');
	sql_drop_table('spip_geo_communes');

	sql_drop_table('spip_geo_pays_liens');
	sql_drop_table('spip_geo_regions_liens');
	sql_drop_table('spip_geo_departements_liens');
	sql_drop_table('spip_geo_arrondissements_liens');
	sql_drop_table('spip_geo_communes_liens');

	effacer_meta($nom_meta_base_version);
}
