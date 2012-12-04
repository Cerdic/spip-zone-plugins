<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function cog_upgrade($nom_meta_base_version,$version_cible){
	  $current_version = 0.0;

	  if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	  || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		  include_spip('base/cog');
		  // cas d'une installation
		  if ($current_version=="0.0"){
				include_spip('base/create');
				creer_base();
				maj_tables('spip_cog_communes');
				cog_peupler_base();
				ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
			}
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
	  }
}


function cog_peupler_base()
{
include_spip('inc/config');
ecrire_config('cog/chemin_donnee','donnees/');

}







/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function cog_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_cog_communes");
	sql_drop_table("spip_cog_cantons");
	sql_drop_table("spip_cog_arrondissements");
	sql_drop_table("spip_cog_departements");
	sql_drop_table("spip_cog_regions");
	sql_drop_table("spip_cog_epcis");
	sql_drop_table("spip_cog_epci_natures");
	sql_drop_table("spip_cog_zaeurs");
	sql_drop_table("spip_cog_zaeur_categories");
	sql_drop_table("spip_cog_zone_emplois");
	sql_drop_table("spip_cog_communes_liens");
	effacer_meta($nom_meta_base_version);
	effacer_meta('cog');
}



?>
