<?php
/**
 * Plugin Acces Restreint 3.0 pour Spip 2.0
 * Licence GPL (c) 2006-2008 Cedric Morin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
/**
 * Fonction d'installation, mise a jour de la base
 *
 * @param unknown_type $nom_meta_base_version
 * @param unknown_type $version_cible
 */
function accesrestreint_upgrade($nom_meta_base_version,$version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_zones','spip_zones_liens')),
	);

	$maj['0.1.0'] = array(
		array('maj_tables',array('spip_zones')), // publique, privee
	);
	$maj['0.2.0'] = array(
		array('maj_tables',array('spip_zones')), // publique, privee
	);
	$maj['0.3.0'] = array(
		array('sql_alter',"TABLE zones_auteurs DROP INDEX id_zone"),
		array('sql_alter',"TABLE zones_auteurs ADD PRIMARY KEY ( id_zone , id_auteur )"),
		array('sql_alter',"TABLE zones_rubriques DROP INDEX id_zone"),
		array('sql_alter',"TABLE zones_rubriques ADD PRIMARY KEY ( id_zone , id_rubrique )"),
	);
	$maj['0.3.1'] = array(
		array('sql_alter',"TABLE spip_zone ALTER titre SET DEFAULT ''"),
		array('sql_alter',"TABLE spip_zone ALTER descriptif SET DEFAULT ''"),
	);

	include_spip('maj/svn10000');
	$maj['0.4.0'] = array(
		array('maj_liens','zone'), // creer la table zones_liens
		array('maj_liens','zone','auteur'),
		array('sql_drop_table',"spip_zones_auteurs"),
		array('maj_liens','zone','rubrique'),
		array('sql_drop_table',"spip_zones_rubriques"),
	);
	$maj['0.4.1'] = array(
		array('sql_alter',"TABLE spip_zone CHANGE publique publique char(3) DEFAULT 'oui' NOT NULL"),
		array('sql_alter',"TABLE spip_zone CHANGE privee privee char(3) DEFAULT 'oui' NOT NULL"),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de desinstallation
 *
 * @param unknown_type $nom_meta_base_version
 */
function accesrestreint_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_zones");
	sql_drop_table("spip_zones_liens");
	effacer_meta('creer_htaccess');
	effacer_meta($nom_meta_base_version);
}

?>
