<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function cog_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_cog_communes',
									'spip_cog_communes_liens',
									'spip_cog_cantons',
									'spip_cog_arrondissements',
									'spip_cog_departements',
									'spip_cog_regions',
									'spip_cog_epcis',
									'spip_cog_epcis_natures',
									/*'spip_cog_zauers',
									'spip_cog_zauers_espace',
									'spip_cog_zauers_categories'*/)),
		array('cog_peupler_base')
	);
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function cog_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_cog_communes");
	sql_drop_table("spip_cog_communes_liens");
	sql_drop_table("spip_cog_cantons");
	sql_drop_table("spip_cog_arrondissements");
	sql_drop_table("spip_cog_departements");
	sql_drop_table("spip_cog_regions");
	sql_drop_table("spip_cog_epcis");
	sql_drop_table("spip_cog_epci_natures");
	/*sql_drop_table("spip_cog_zauers");
	sql_drop_table("spip_cog_zauer_espace");
	sql_drop_table("spip_cog_zauer_categories");*/

	effacer_meta($nom_meta_base_version);
}



function cog_peupler_base()
{
	include_spip('inc/config');
	ecrire_config('cog/chemin_donnee','cog_donnees/');
}

?>
