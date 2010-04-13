<?php

/**
 * Plugin Quiz pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 */

include_spip('inc/meta');
include_spip('base/create');
include_spip('inc/vieilles_defs');

function quiz_upgrade($nom_meta_base_version,$version_cible){

	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/quiz_tables');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
			
		}
	}
}




function quiz_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_questions");
	sql_drop_table("spip_reponses");
	sql_drop_table("spip_corrections");
	effacer_meta($nom_meta_base_version);
}


?>