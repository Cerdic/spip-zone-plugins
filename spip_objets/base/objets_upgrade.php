<?php
include_spip('inc/meta');
include_spip('base/create');

function objets_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}

	if ($current_version=="0.0") {
		
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
		ecrire_meta('objets_installes',''); //nous n'avons pas encore d'objets installé
	}
	
	/* A ACTIVER POUR LA PROCHAINE VERSION
	 * if (version_compare($current_version,"1.1","<")){
		maj_tables('spip_actus');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}*/
}

function objets_vider_tables($nom_meta_base_version) { 
	$objets_installes=liste_objets_meta();
	
	foreach ($objets_installes as $objet) {
		sql_drop_table("spip_".$objet);
		sql_drop_table("spip_".$objet."_liens");
		
	}
	effacer_meta("objets_installes");
	effacer_meta($nom_meta_base_version);
}

?>