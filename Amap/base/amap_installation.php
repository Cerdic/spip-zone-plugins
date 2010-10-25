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
		if (version_compare($current_version,'0.0','<=')){
			// Creation des tables
			include_spip('base/create');
			include_spip('base/abstract_sql');
			creer_base();
			// Changement de la config des metas et creation mot clef
			amap_config_site();
			amap_config_motsclefs();
			spip_log("Amap s'installe V0.0", "amap_installation");
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		}
}
function amap_vider_tables($nom_meta_version_base){
	//supprimer toutes les tables
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table('spip_amap_banques');
	sql_drop_table('spip_amap_contrats');
	sql_drop_table('spip_amap_evenements');
	sql_drop_table('spip_amap_famille_varietes');
	sql_drop_table('spip_amap_lieux');
	sql_drop_table('spip_amap_paniers');
	sql_drop_table('spip_amap_participation_sorties');
	sql_drop_table('spip_amap_personnes');
	sql_drop_table('spip_amap_prix');
	sql_drop_table('spip_amap_produits');
	sql_drop_table('spip_amap_produits_distributions');
	sql_drop_table('spip_amap_reglements');
	sql_drop_table('spip_amap_saisons');
	sql_drop_table('spip_amap_sorties');
	sql_drop_table('spip_amap_types_contrats');
	sql_drop_table('spip_amap_vacances');
	sql_drop_table('spip_amap_varietes');
	effacer_meta($nom_meta_version_base);
}
?>
