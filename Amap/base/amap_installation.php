<?php

/*
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Code repris sur Agenda 2.0
*/

include_spip('inc/meta');
include_spip('base/create');
include_spip('base/amap_tables');

function amap_upgrade($nom_meta_version_base, $version_cible){
	$current_version = 0.0;
		if ((!isset($GLOBALS['meta'][$nom_meta_version_base])) || (($current_version = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)){
		if (version_compare($current_version,'0.3','<=')){
			// Creation des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// Changement de la config des metas et creation mot clef
			amap_config_site();
			amap_config_motsclefs();
			// Creation des champs extras
			amap_declarer_champs_extras();
			sql_alter("TABLE spip_auteurs ADD adhesion text NULL");
			spip_log("Amap s'installe V0.3", "amap_installation");
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		if (version_compare($current_version,'0.4','<')){
			// On supprime la table personne
			sql_drop_table("spip_amap_banques");
			sql_drop_table("spip_amap_paniers");
			spip_log("Suppression des table banques et paniers V0.4", "amap_installation");
			}
		if (version_compare($current_version,'0.5','<')) {
			create_rubrique("000. Agenda de la saison", "0");
			$id_rubrique = id_rubrique("000. Agenda de la saison");
			if ($id_rubrique >0) {
				create_rubrique("001. Distribution", $id_rubrique);
				create_rubrique("002. Événements", $id_rubrique);
			}
			sql_alter("TABLE spip_auteurs ADD type_panier text NULL");
			spip_log("Création de rubrique et champ extra type_panier V0.5", "amap_installation");                
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		}
}
function amap_vider_tables($nom_meta_version_base){
	//supprimer toutes les tables
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table('spip_amap_contrats');
	sql_drop_table('spip_amap_evenements');
	sql_drop_table('spip_amap_famille_varietes');
	sql_drop_table('spip_amap_lieux');
	sql_drop_table('spip_amap_participation_sorties');
	sql_drop_table('spip_amap_prix');
	sql_drop_table('spip_amap_produits');
	sql_drop_table('spip_amap_produits_distributions');
	sql_drop_table('spip_amap_reglements');
	sql_drop_table('spip_amap_saisons');
	sql_drop_table('spip_amap_sorties');
	sql_drop_table('spip_amap_types_contrats');
	sql_drop_table('spip_amap_vacances');
	sql_drop_table('spip_amap_varietes');
	sql_drop_table('spip_paniers');
	//suppression des champs supplementaire
	$champs = amap_declarer_champs_extras();
	//supprimer les mots clef
	vider_groupe("_Amap_config");
	effacer_meta($nom_meta_version_base);
}
?>
