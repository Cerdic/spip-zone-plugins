<?php

/*
* Plugin AMAP pour Spip 2.0
* Pierre KUHN
* Code repris sur Agenda 2.0
*/

include_spip('inc/meta');
include_spip('base/create');

function amap_upgrade($nom_meta_version_base, $version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_version_base]))
			|| (($current_version = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
		){
			if (version_compare($current_version,'0.0','<=')){
				// Creation des tables
				include_spip('base/create');
				include_spip('base/abstract_sql');
				creer_base();
				ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
				}
	}
}
function amap_vider_tables($nom_meta_version_base){
	//supprimer toutes les tables
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	sql_drop_table('spip_amap_banque');
	sql_drop_table('spip_amap_contrat');
	sql_drop_table('spip_amap_evenements');
	sql_drop_table('spip_amap_famille_variete');
	sql_drop_table('spip_amap_lieu');
	sql_drop_table('spip_amap_panier');
	sql_drop_table('spip_amap_participation_sortie');
	sql_drop_table('spip_amap_personne');
	sql_drop_table('spip_amap_prix');
	sql_drop_table('spip_amap_produit');
	sql_drop_table('spip_amap_produit_distribution');
	sql_drop_table('spip_amap_reglement');
	sql_drop_table('spip_amap_saison');
	sql_drop_table('spip_amap_sortie');
	sql_drop_table('spip_amap_type_contrat');
	sql_drop_table('spip_amap_vacance');
	sql_drop_table('spip_amap_variete');
	effacer_meta($nom_meta_version_base);
}
?>
