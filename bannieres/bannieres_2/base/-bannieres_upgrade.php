<?php

	/**
	* Plugin Bannières
	*
	* Copyright (c) 2009
	* François de Montlivault - Jeannot
	* Mise à jour Inspirée du plugin chats
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/


include_spip('inc/meta');
include_spip('base/create');

function bannieres_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";

	if ($current_version=="0.0") {
		creer_base();
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	if (version_compare($current_version,"1.1","<")){
		// ToDo : traiter les mises à jour anterieures
		maj_tables('spip_bannieres');
		ecrire_meta($nom_meta_base_version,$current_version="0.2");
	}
}

function bannieres_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_bannieres");
	sql_drop_table("spip_bannieres_suivi");
	effacer_meta($nom_meta_base_version);
}

?>
