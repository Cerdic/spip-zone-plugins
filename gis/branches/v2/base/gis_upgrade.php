<?php

include_spip('inc/meta');

/**
 * Installation/maj des tables gis
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function gis_upgrade($nom_meta_base_version,$version_cible){
	$current_version = '0.0';
	if ( (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		// installation
		if (version_compare($current_version, '0.0','<=')){
			include_spip('base/gis');
			include_spip('base/create');
			// creer les tables
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		// maj depuis gis v1
		if (version_compare($current_version, '2.0','<')){
			include_spip('base/gis');
			include_spip('base/create');
			creer_base();
			// maj de la table gis
			include_spip('base/abstract_sql');
			// renommer le champ lonx en lon
			sql_alter("TABLE spip_gis CHANGE lonx lon float(21) NULL NULL");
			// creer les liens
			include_spip('action/editer_gis');
			$res = sql_select('*','spip_gis');
			while ($row = sql_fetch($res)) {
				if($row['id_article'] != 0)
					lier_gis($row['id_gis'], 'article', $row['id_article']);
				if($row['id_rubrique'] != 0)
					lier_gis($row['id_gis'], 'article', $row['id_rubrique']);
			}
			// virer les champs id_article et id_rubrique
			sql_alter("TABLE spip_gis DROP id_article");
			sql_alter("TABLE spip_gis DROP id_rubrique");
			// virer les index id_article et id_rubrique
			sql_alter("TABLE spip_gis DROP INDEX id_article");
			sql_alter("TABLE spip_gis DROP INDEX id_rubrique");
			// migrer le contenu de la table gis_mots
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
			// et virer la table gis_mots
			sql_drop_table('spip_gis_mots');
			ecrire_meta($nom_meta_base_version,$current_version="2.0",'non');
		}
		if (version_compare($current_version, '2.0.1','<')){
			include_spip('base/gis');
			include_spip('base/create');
			// creer les tables
			maj_tables(array('spip_gis'));
			ecrire_meta($nom_meta_base_version,$current_version="2.0.1",'non');
		}
		if (version_compare($current_version, '2.0.2','<')){
			// augmenter la précision des coords lat/lon
			include_spip('base/abstract_sql');
			sql_alter("TABLE spip_gis CHANGE lat lat DOUBLE NULL NULL");
			sql_alter("TABLE spip_gis CHANGE lon lon DOUBLE NULL NULL");
			ecrire_meta($nom_meta_base_version,$current_version="2.0.2",'non');
		}
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
}

?>