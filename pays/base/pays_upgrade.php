<?php
/**
 * Plugin Pays pour Spip 2.0
 * Licence GPL
 * Auteur Organisation Internationale de Normalisation http://www.iso.org/iso/fr/country_codes/iso_3166_code_lists.htm
 * Cedric Morin et Collectif SPIP pour version spip_geo_pays
 * Portage sous SPIP par Cyril MARION - Ateliers CYM http://www.cym.fr
 *
 */

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/pays_peupler_base');

function pays_upgrade($nom_meta_base_version, $version_cible){

	$current_version = "0.0";
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		peupler_base_pays();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	
	if (version_compare($current_version,"1.1","<")){
		sql_drop_table("spip_pays");
		creer_base();
		peupler_base_pays();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}

}
function pays_vider_tables($nom_meta_base_version) {

	sql_drop_table("spip_pays");
	effacer_meta($nom_meta_base_version);
}


?>