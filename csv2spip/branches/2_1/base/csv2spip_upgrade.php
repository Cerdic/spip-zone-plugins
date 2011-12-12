<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip('inc/meta');
include_spip('base/create');

function csv2spip_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if (isset($GLOBALS['meta'][$nom_meta_base_version])) {
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	}

	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
/* exemple d'upgrade de la base
	if (version_compare($current_version,"1.1","<")){
		// ajout du champ "robe" et "infos"
		maj_tables('spip_chats');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
*/
}

function csv2spip_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_tmp_csv2spip");
	effacer_meta($nom_meta_base_version);
}


?>
