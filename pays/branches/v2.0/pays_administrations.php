<?php
/**
 * Plugin Pays pour Spip 3.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function pays_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_pays')),
		array('peupler_base_pays')
		);
	$maj['1.1.0'] = array(
		array('sql_drop_table', array('spip_pays')),
		array('maj_tables', array('spip_pays')),
		array('peupler_base_pays')
	);
	$maj['1.2.0'] = array(
		array('sql_update', array('spip_pays',array("code" => "0"),array("code='IR'", "id_pays=109", ))),
		array('sql_update', array('spip_pays',array("code" => "IR"),array("code='IQ'","id_pays=110", ))),
		array('sql_update', array('spip_pays',array("code" => "IQ"),array("code='0'", "id_pays=109", ))),
	);
	$maj['1.2.1'] = array(
		array('sql_update', array('spip_pays',array("code" => "0"),array("code='KR'", "id_pays=52", ))),
		array('sql_update', array('spip_pays',array("code" => "KP"),array("code='KR'","id_pays=51", ))),
		array('sql_update', array('spip_pays',array("code" => "KP"),array("code='0'", "id_pays=52", ))),
	);

	include_spip('base/upgrade');
	include_spip('base/pays_peupler_base');

	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}



function pays_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_pays");
	effacer_meta($nom_meta_base_version);
}


?>
