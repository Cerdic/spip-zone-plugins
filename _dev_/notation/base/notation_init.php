<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Installer la base si pas deja fait 
*  
**/

include_spip('inc/meta');
include_spip('base/create');
include_spip('inc/vieilles_defs');

function notation_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		
		if ($current_version==0.0){
			include_spip('base/notation');
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
		ecrire_metas();
	}
}

function notation_vider_tables($nom_meta_base_version) {
	spip_query("DROP TABLE spip_notations");
	spip_query("DROP TABLE spip_notations_articles");
	effacer_meta($nom_meta_base_version);
	ecrire_metas();
}
	
?>