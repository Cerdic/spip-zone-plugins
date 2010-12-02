<?php
include_spip('inc/meta');
//include_spip('base/create');

function statut_auteurs_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}

	if ($current_version=="0.0") {
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
		ecrire_meta('statut_auteurs:autre_statut_auteur',''); //nous n'avons pas encore d'autres statuts
	}
	
	/* A ACTIVER POUR LA PROCHAINE VERSION
	 * if (version_compare($current_version,"1.1","<")){
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}*/
}

function statut_auteurs_vider_tables($nom_meta_base_version) { 
	
	effacer_meta("statut_auteurs:autre_statut_auteur");
	effacer_meta($nom_meta_base_version);
}

?>