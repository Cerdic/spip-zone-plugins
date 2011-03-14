<?php
include_spip('base/create');
function sel_upgrade($nom_meta_base_version, $version_cible){
	$current_version = "0.0";
	
	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
	$current_version = $GLOBALS['meta'][$nom_meta_base_version];
	
	if ($current_version=="0.0") {
		creer_base();
		$vue_offreurs = sql_get_select(
			$champs = array(
			"id_auteur AS id_offreur",
			"nom"
			),
			"spip_auteurs"
		);
		$vue_demandeurs = sql_get_select(
			$champs = array(
			"id_auteur AS id_demandeur",
			"nom"
			),
			"spip_auteurs"
		);
		$vue_valideurs = sql_get_select(
			$champs = array(
			"id_auteur AS id_valideur",
			"nom"
			),
			"spip_auteurs"
		);
		sql_create_view(spip_offreurs,$vue_offreurs);
		sql_create_view(spip_demandeurs,$vue_demandeurs);
		sql_create_view(spip_valideurs,$vue_valideurs);
		ecrire_meta($nom_meta_base_version, $current_version=$version_cible);
	}
	// if (version_compare($current_version,"0.2","<")) {
		// mise à jour de la structure des tables
		// maj_tables('spip_xxx');
		// ecrire_meta($nom_meta_base_version,$current_version="0.2");
	// }
}

function sel_vider_tables($nom_meta_base_version) {
	// sql_alter("TABLE spip_xxx DROP champ");
	// pour supprimer un champ du plugin sur une table de base de spip, auparaveant ajouté par ce plugin

	sql_drop_table("spip_auteurs_extension");	
	sql_drop_table("spip_sels");
	sql_drop_table("spip_annonces");
	sql_drop_table("spip_echanges");
	sql_drop_table("spip_themes");
	sql_drop_table("spip_parametres");
	sql_drop_table("spip_themes_annonces");
	// pour supprimer une table entière appartenant au plugin qu'on supprime
	
	sql_drop_view("spip_offreurs");
	sql_drop_view("spip_demandeurs");
	sql_drop_view("spip_valideurs");
	// pour supprimer une vue appartenant au plugin qu'on supprime
	
	effacer_meta($nom_meta_base_version);
	// suppression des informations du plugin dans la table meta
}


?>