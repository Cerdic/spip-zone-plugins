<?php
/**
* Plugin abonnement
*
* Copyright (c) 2013
* Collectif SPIP
* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
* Pour plus de details voir le fichier COPYING.txt
*  
**/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/meta');
include_spip('base/create');

function relance_upgrade($nom_meta_base_version,$version_cible){
	
	$current_version = 0.0; //suppose que le plugin n'a jamais ete installe
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	//jamais installe
	if ($current_version==0.0){
		creer_base();
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}

}

function relance_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_relances');
	sql_drop_table('spip_relances_archives');
	effacer_meta($nom_meta_base_version);
}
		

?>
