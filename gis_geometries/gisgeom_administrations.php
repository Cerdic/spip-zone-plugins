<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function gisgeom_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		// installation
		if (version_compare($current_version, '0.0','<=')){
			include_spip('base/abstract_sql');
			// ajout des champs geo et type à la table gis
			sql_alter("TABLE spip_gis ADD geo GEOMETRY DEFAULT '' NOT NULL AFTER lon");
			sql_alter("TABLE spip_gis ADD type VARCHAR (25) DEFAULT '' NOT NULL AFTER zoom");
			// renseigner spip_gis.geo avec spip_gis.lat et spip_gis.lon pour les objets existants
			sql_update("spip_gis", array("geo"=>"GeomFromText(CONCAT('POINT(',lon,' ',lat,')'))"));
			// renseigner spip_gis.type = point pour les objets existants
			sql_updateq("spip_gis", array("type"=>"Point"));
			// ajouter un index sur le champ geo
			sql_alter("TABLE spip_gis ADD SPATIAL INDEX (geo)");
			include_spip('inc/invalideur');
			purger_repertoire(_DIR_VAR.'cache-js');
			ecrire_meta($nom_meta_base_version,$current_version="1.0.0",'non');
		}
	}
}

/**
 * Desinstallation/suppression des tables gis
 *
 * @param string $nom_meta_base_version
 */
function gisgeom_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_gis DROP geo");
	sql_alter("TABLE spip_gis DROP type");
	sql_alter("TABLE spip_gis DROP INDEX geo");
	effacer_meta($nom_meta_base_version);
}

?>