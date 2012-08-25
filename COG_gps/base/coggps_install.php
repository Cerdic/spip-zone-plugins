<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function coggps_upgrade($nom_meta_base_version,$version_cible){
	  $current_version = 0.0;

	  if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
	  || (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			  include_spip('base/coggps');
			  // cas d'une installation
			  if ($current_version=="0.0"){
					  maj_tables('spip_cog_communes');
					  ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
			  }
	  }
}





function coggps_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_cog_communes DROP lon,lat,zoom,elevation,elevation_moyenne,population,autre_nom");
	effacer_meta($nom_meta_base_version);
}


?>
