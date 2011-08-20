<?php

/*
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Code repris sur Agenda 2.0
*/

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/amap_tables');
include_spip('inc/cextras_gerer');

function amap_upgrade($nom_meta_version_base, $version_cible){
	$current_version = 0.0;
		if ((!isset($GLOBALS['meta'][$nom_meta_version_base])) || (($current_version = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)){
		if (version_compare($current_version,'0.7','<=')){
			// Creation des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// Creation des champs extras
			amap_declarer_champs_extras();
			sql_alter("TABLE spip_auteurs ADD adhesion text NULL");
			sql_alter("TABLE spip_auteurs ADD type_panier text NULL");
			create_rubrique("000. Agenda de la saison", "0");
			$id_rubrique = id_rubrique("000. Agenda de la saison");
			if ($id_rubrique >0) {
				create_rubrique("001. Distribution", $id_rubrique);
				create_rubrique("002. Événements", $id_rubrique);
			}
			create_rubrique("001. Archives", "0");
			spip_log("Amap s'installe V0.7", "amap_installation");
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		if (version_compare($current_version,'0.8','<')) {
			maj_tables("spip_amap_livraisons");
			maj_tables("spip_amap_paniers");
			sql_drop_table('spip_paniers');
			create_rubrique("001. Archives", "0");
			spip_log("Creation de la table amap_livraisons et amap_paniers V0.8", "amap_installation");
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		}
}
function amap_vider_tables($nom_meta_version_base){
	//supprimer toutes les tables
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table('spip_amap_livraisons');
	sql_drop_table('spip_amap_paniers');
	//suppression des champs supplementaire
	$champs = amap_declarer_champs_extras();
	desinstaller_champs_extras($champs, $nom_meta_base_version);
	effacer_meta($nom_meta_version_base);
}
?>
