<?php
include_spip('inc/meta');
include_spip('base/create');

function boutique_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}

	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.1","<")){
		// ajout du champ "nom" et "texte"
		maj_tables('spip_produits');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
}

function boutique_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_produits");
	sql_drop_table("spip_avis_boutique");
	effacer_meta($nom_meta_base_version);
}

?>
