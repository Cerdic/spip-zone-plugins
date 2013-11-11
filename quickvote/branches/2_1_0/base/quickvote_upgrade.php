<?php
/**
 * Plugin Quickvote pour Spip 2.1
 * Licence GPL
 * 
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');
include_spip('base/create');

function quickvote_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
    include_spip('base/quickvote');
		
    if ($current_version==0.0){			
			include_spip('base/create');
			include_spip('base/abstract_sql');
      creer_base();			
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
    
    // si maj  ulterieure 
    // if (version_compare($current_version,"0.5.0","<")){ }



	}
}

function quickvote_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_quickvotes");
	sql_drop_table("spip_quickvotes_reponses");
  sql_drop_table("spip_quickvotes_votes");
	effacer_meta($nom_meta_base_version);
}

?>