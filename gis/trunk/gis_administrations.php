<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function gis_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	
	// Première installation
	$maj['create'] = array(
		array('maj_tables', array('spip_gis')),
		array('maj_tables', array('spip_gis_liens')),
	);
	
	// Mise à jour depuis GIS 1
	$maj['2.0'] = array(
		// On ajoute la nouvelle table
		array('maj_tables', array('spip_gis_liens')),
		// On renomme le champ #LONX en #LON
		array('sql_alter', array('TABLE spip_gis CHANGE lonx lon float(21) NULL NULL')),
		// On déplace les liaisons articles, rubriques et mots
		array('gis_upgrade_2_0'),
		// Virer les champs id_article et id_rubrique
		array('sql_alter', array('TABLE spip_gis DROP id_article')),
		array('sql_alter', array('TABLE spip_gis DROP id_rubrique')),
		// Virer les index id_article et id_rubrique
		array('sql_alter', array('TABLE spip_gis DROP INDEX id_article')),
		array('sql_alter', array('TABLE spip_gis DROP INDEX id_rubrique')),
		// Virer la table pour les mots
		array('sql_drop_table', array('spip_gis_mots')),
	);
	
	// Des nouveaux champs
	$maj['2.0.1'] = array(
		array('maj_tables', array('spip_gis')),
	);
	
	// Augmenter la précision des champs de coordonnées
	$maj['2.0.2'] = array(
		array('sql_alter', array('TABLE spip_gis CHANGE lat lat DOUBLE NULL NULL')),
		array('sql_alter', array('TABLE spip_gis CHANGE lon lon DOUBLE NULL NULL')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function gis_upgrade_2_0(){
	include_spip('action/editer_gis');
	
	// On déplace les liaisons articles et rubriques
	$res = sql_select('*','spip_gis');
	while ($row = sql_fetch($res)) {
		if($row['id_article'] != 0)
			lier_gis($row['id_gis'], 'article', $row['id_article']);
		if($row['id_rubrique'] != 0)
			lier_gis($row['id_gis'], 'article', $row['id_rubrique']);
	}
	
	// On déplace les liaisons mots
	$res = sql_select('*','spip_gis_mots');
	while ($row = sql_fetch($res)) {
		$titre_mot = sql_getfetsel('titre','spip_mots','id_mot='.$row['id_mot']);
		$c = array(
			'titre' => $titre_mot,
			'lat'=> $row['lat'],
			'lon' => $row['lonx'],
			'zoom' => $row['zoom']
		);
		$id_gis = insert_gis();
		revisions_gis($id_gis,$c);
		lier_gis($id_gis, 'mot', $row['id_mot']);
	}
}

/**
 * Desinstallation/suppression des tables gis
 *
 * @param string $nom_meta_base_version
 */
function gis_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_gis");
	sql_drop_table("spip_gis_liens");
	effacer_meta($nom_meta_base_version);
	// Effacer la config
	effacer_meta('gis');
}

?>
