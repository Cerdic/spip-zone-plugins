<?php

function rang_upgrade($nom_meta_version_base, $version_cible){
	$version_actuelle = 0.0;
	if (
		(!isset($GLOBALS['meta'][$nom_meta_version_base]))
		|| (($version_actuelle = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		// On vérifie la présence d'un chang rang dans spip_articles et on le créer au besoin.
		$table_articles = sql_showtable('spip_articles');
		if (!isset($table_articles['field']['rang'])) {
			maj_tables('spip_articles');
			sql_update(
				'spip_articles',
				array(
					'rang' => "SUBSTRING_INDEX(titre,'.',1)",
					'titre' => "TRIM(SUBSTRING(titre, LOCATE('.', titre)+1))"
				),
				"titre REGEXP '^[0-9]+\..*$'"
			);
		}
		$table_rubriques = sql_showtable('spip_rubriques');
		if (!isset($table_rubriques['field']['rang'])) {
			maj_tables('spip_rubriques');
			sql_update(
				'spip_rubriques',
				array(
					'rang' => "SUBSTRING_INDEX(titre,'.',1)",
					'titre' => "TRIM(SUBSTRING(titre, LOCATE('.', titre)+1))"
				),
				"titre REGEXP '^[0-9]+\..*$'"
			);
		}
		ecrire_meta($nom_meta_version_base, $version_actuelle=$version_cible, 'non');
	}
}

function rang_vider_tables($nom_meta_version_base) {
	include_spip('base/abstract_sql');
	// On réaffecte les rangs dans les titres
	// On passe par un tableau car CONCAT pas dispo en SQLite
	$titres_rubriques = sql_allfetsel(array('id_rubrique','rang','titre'),'spip_rubriques','rang > 0');
	foreach($titres_rubriques as $cle => $titre) {
		$titres_rubriques[$cle]['titre'] = $titre['rang'].'. '.$titre['titre'];
		$titres_rubriques[$cle]['rang'] = '';
	}
	sql_replace_multi('spip_rubriques',$titres_rubriques);
	$titres_articles = sql_allfetsel(array('id_article','rang','titre'),'spip_articles','rang > 0');
	foreach($titres_articles as $cle => $titre) {
		$titres_articles[$cle]['titre'] = $titre['rang'].'. '.$titre['titre'];
		$titres_articles[$cle]['rang'] = '';
	}
	sql_replace_multi('spip_articles',$titres_articles);
	
	// Suppression du chang rang
	sql_alter("TABLE spip_rubriques DROP COLUMN rang"); 
	sql_alter("TABLE spip_articles DROP COLUMN rang"); 
	// On efface la version enregistrée
	effacer_meta($nom_meta_version_base);
}

?>
