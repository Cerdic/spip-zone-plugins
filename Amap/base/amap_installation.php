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
			amap_config_site();
			amap_config_motsclefs();
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
			}
		if (version_compare($current_version,'0.1.1','<')) {
			ecrire_meta('config_precise_groupes', 'oui','non');
			ecrire_meta('articles_mots', 'oui','non');
			create_groupe("modalites_affichage_article", "L'article correspondant aux distributions d'une saison donnée sera associé au mot clé « amap_agenda »", "", 'oui', 'non', 'non', 'non', 'non', 'oui', 'non', 'oui', 'non', 'non');
				create_mot("modalites_affichage_article", "amap_contrat", "Affecter ce mot clef à l'article concernant les contrats.", "");
				create_mot("modalites_affichage_article", "amap_distribution", "Affecter ce mot clef à l'article concernant la distribution.", "");
				create_mot("modalites_affichage_article", "amap_responsable", "Affecter ce mot clef à l'article concernant le responsable.", "");
				create_mot("modalites_affichage_article", "amap_sortie", "Affecter ce mot clef à l'article concernant la sorties.", "");
				create_mot("modalites_affichage_article", "amap_vacance", "Affecter ce mot clef à l'article concernant les vacances.", "");
			ecrire_meta($nom_meta_base_version,$current_version='0.1.1','non');
			spip_log("Amap rajoute des mots clef V0.1.1", "amap_installation");
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
	supprimer_mot_groupe("modalites_affichage_article", "amap_contrat");
	supprimer_mot_groupe("modalites_affichage_article", "amap_distribution");
	supprimer_mot_groupe("modalites_affichage_article", "amap_responsable");
	supprimer_mot_groupe("modalites_affichage_article", "amap_sortie");
	supprimer_mot_groupe("modalites_affichage_article", "amap_vacance");
	vider_groupe("modalites_affichage_article");
	effacer_meta($nom_meta_version_base);
}
?>
