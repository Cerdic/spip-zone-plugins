<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/meta');

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function gisgeom_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	$maj['create'] = array(
		// ajout des champs geo et type à la table gis (procéder en 2 temps, dans le cas où la table n'est pas vide)
		array('sql_alter', 'TABLE spip_gis ADD geo GEOMETRY AFTER lon'),
		// renseigner spip_gis.geo avec spip_gis.lat et spip_gis.lon pour les objets existants
		array('sql_update', 'spip_gis', array('geo' => "GeomFromText(CONCAT('POINT(',lon,' ',lat,')'))")),
		// NOT NULL pour pouvoir avec index !
		array('sql_alter', 'TABLE spip_gis CHANGE COLUMN geo geo GEOMETRY NOT NULL'),
		array('sql_alter', "TABLE spip_gis ADD type VARCHAR (25) DEFAULT '' NOT NULL AFTER zoom"),
		// renseigner spip_gis.type = point pour les objets existants
		array('sql_updateq', 'spip_gis', array('type' => 'Point')),
		// ajouter un index sur le champ geo
		array('sql_alter', 'TABLE spip_gis ADD SPATIAL INDEX (geo)'),
		// purger le cache js
		array('gisgeom_purger_cache_js'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function gisgeom_purger_cache_js() {
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_VAR.'cache-js');
}

/**
 * Desinstallation/suppression des tables gis
 *
 * @param string $nom_meta_base_version
 */
function gisgeom_vider_tables($nom_meta_base_version) {
	sql_alter('TABLE spip_gis DROP geo');
	sql_alter('TABLE spip_gis DROP type');
	sql_alter('TABLE spip_gis DROP INDEX geo');
	effacer_meta($nom_meta_base_version);
}
