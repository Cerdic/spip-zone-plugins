<?php

function titre_logo_install($nom_meta_base_version, $version_cible=0){
	include_spip('inc/meta');


	// On traite le cas de la premiere version de Tickets sans version_base
		
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
		
		
	if ($current_version=="0.0") {
		$version_cible = "0.1";
		include_spip('base/titre_logo_install');
		
		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}


	$version_cible = "0.3";
	if (version_compare($current_version,$version_cible,"<")){

		sql_alter("TABLE spip_articles ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_articles ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");

		sql_alter("TABLE spip_rubriques ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_rubriques ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");
	

		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}

	$version_cible = "0.4";
	if (version_compare($current_version,$version_cible,"<")){

		sql_alter("TABLE spip_auteurs ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_auteurs ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");

		sql_alter("TABLE spip_breves ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_breves ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");

		sql_alter("TABLE spip_syndic ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_syndic ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");

		sql_alter("TABLE spip_mots ADD COLUMN titre_logo text DEFAULT '' NOT NULL");
		sql_alter("TABLE spip_mots ADD COLUMN descriptif_logo text DEFAULT '' NOT NULL");
	

		ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
	}

	return true;
		
	ecrire_metas();
}

?>