<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function cp_upgrade($nom_meta_base_version,$version_cible){
	  $current_version = 0.0;

	  if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	  || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		  include_spip('base/cp');
		  // cas d'une installation
		  if ($current_version=="0.0"){
						include_spip('base/create');
						creer_base();
						cp_peupler_base();
						ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		  }
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');

	  }
}
function cp_peupler_base()
{
include_spip('inc/config');
ecrire_config('cp/chemin_donnee','donnees/');
}

/**
 * Desinstallation
 *
 * @param string $nom_meta_base_version
 */
function cp_vider_tables($nom_meta_base_version) {
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table("spip_code_postals");
	effacer_meta($nom_meta_base_version);
}



?>
