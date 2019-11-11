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
function geographie_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	// Installation du plugin
	$maj['create'] = array(
		array('creer_base'),
		array('geographie_importer'), // importation des pays, régions et continents à partir du serveur
	);

	// On refait la base des pays
	$maj['0.2.0'] = array(
		array('sql_drop_table', 'spip_geo_pays'),
		array('maj_tables', 'spip_geo_pays'),
		array('geographie_upgrade_importer_pays'),
	);

	// On ajoute les arrondissements
	$maj['0.3.0'] = array(
		array('maj_tables', 'spip_geo_arrondissements'),
		array('geographie_upgrade_importer_arrondissements'),
	);

	// On refait encore les pays
	$maj['0.4.0'] = array(
		array('sql_drop_table', 'spip_geo_pays'),
		array('maj_tables', 'spip_geo_pays'),
		array('geographie_upgrade_importer_pays'),
	);

	// Coquille dans la description de midi-pyrénnées
	$maj['0.4.2'] = array(
		array('sql_delete', 'spip_geo_departements', 'nom=' . sql_quote('09')),
	);

	// Toutes les tables de liens
	$maj['1.0.0'] = array(
		array(
			'maj_tables',
			array(
				'spip_geo_pays_liens',
				'spip_geo_regions_liens',
				'spip_geo_departements_liens',
				'spip_geo_arrondissements_liens',
				'spip_geo_communes_liens',
			),
		),
	);
	$maj['1.3.0'] = array(
		array('sql_updateq','spip_geo_regions',array('nom'=>'Centre-Val de Loire'),"nom=".sql_quote('Centre'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function geographie_importer() {
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
function geographie_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_geo_pays');
	sql_drop_table('spip_geo_regions');
	sql_drop_table('spip_geo_continents');
//	sql_drop_table('spip_geo_subdivisions');
//	sql_drop_table('spip_geo_communes');

	sql_drop_table('spip_geo_pays_liens');
//	sql_drop_table('spip_geo_subdivisions_liens');
//	sql_drop_table('spip_geo_communes_liens');

	effacer_meta($nom_meta_base_version);
}
