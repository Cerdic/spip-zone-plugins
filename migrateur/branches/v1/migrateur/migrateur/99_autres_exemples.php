<?php


/**
 * Autres fonctions d'exemples de migrations
 *
 * Attention, elles ne sont pas migrées dans le nouveau format de configuration
 * (constantes non adaptées, utilisation de step et pas svp…)
 *
 * TODO: codes d'exemples à adapter au fur et à mesure de leur utilisation
 * ailleurs
**/

if (!defined("_ECRIRE_INC_VERSION")) return;




function migration_concurrences_rapatrier() {

	$dest 		= MIG_C_DIR_DEST . 'tmp/dump/';
	$dir_source = MIG_C_DIR_SOURCE;
	$source_sql = MIG_C_SOURCE_SQL;

	// si url, utiliser wget, sinon copie
	if (preg_match('/http/', $dir_source)) {
		$copie = "wget " . $dir_source . $source_sql;
	} else {
		$copie = "cp " . $dir_source . $source_sql . " $source_sql";
	}

	$output = "";
	exec("
		cd $dest;
		rm $source_sql;
		$copie;
		ls ./
		", $output);
}





function migration_concurrences_supprimer_tables_inutiles() {
	$tables = sql_alltable('%');
	sort($tables);
	foreach($tables as $table) {
		if (substr($table, 0, strlen('contacts_')) == 'contacts_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('etiquettes_')) == 'etiquettes_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('emails_')) == 'emails_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('desabonnements_')) == 'desabonnements_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('spip_index_')) == 'spip_index_') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('spip_auteurs_elargis')) == 'spip_auteurs_elargis') {
			$x = sql_drop_table($table);
		}
		if (substr($table, 0, strlen('wa_')) == 'wa_') {
			$x = sql_drop_table($table);
		}
		// l'ancien acces restreint est supprimé aussi
		if (substr($table, 0, strlen('spip_zones')) == 'spip_zones') {
			$x = sql_drop_table($table);
		}
		if (in_array($table, array(
			'TABLE 143', 'temp_cle', 'test_md5',
			'spip_mots_articles_bk', 'spip_mots_bk',
			'reperes', 'spip_articles_bak', 'spip_articles_test'))) {
			$x = sql_drop_table($table);
		}
	}
}



function migration_concurrences_supprimer_vignettes_logos() {
	$ori = MIG_C_DIR_DEST . 'IMG/';
	foreach (scandir($ori) as $filename) {
		if (preg_match('/.*-[0-9]+x[0-9]+\.(jpg|gif|png)/i', $filename)) {
			unlink($ori . $filename);
		}
	}
}



// quelques plugins pour ne pas tout faire d'un coup
function migration_concurrences_activer_plugins_un() {
	include_spip('inc/step');

	step_install(array(
		'compositions',
		'accesrestreint',
		'date_inscription',
		'crayons',
		'mediabox',
		'medias',
		'contacts',
	),str_replace('&amp;','&', _request('redirect')) );
}





function migration_concurrences_configurer_site() {
	#ecrire_meta('nom_site', "");
	ecrire_meta('adresse_site', "http://www2.concurrences.com");
	ecrire_meta('image_process', "gd2");
	ecrire_meta('type_urls', "arbo");
	ecrire_meta('charset_sql_connexion', "utf-8"); // utf-8
	#ecrire_meta('charset_sql_connexion', "iso-8859-1"); // utf-8

    ecrire_meta('articles_redirection', 'oui');
    ecrire_meta('activer_breves', 'non');
    ecrire_meta('activer_logos_survol', 'oui');
}




function migration_concurrences_composer() {
	// 5. Dans cette fonction, mettre à jour la table spip_rubrique en fonction des champs de la table temporaire 'spip_migration_compositions'
	// dans la fonction 'migration_concurrences_recuperer_compos'
	$correspondances_tables = array(
		'article'	=> array(
			'table'		=> 'spip_articles',
			'nom_id'	=> 'id_article'
		),

		'auteur'	=> array(
			'table'		=> 'spip_auteurs',
			'nom_id'	=> 'id_auteur'
		),

		'rubrique'	=> array(
			'table'		=> 'spip_rubriques',
			'nom_id'	=> 'id_rubrique'
		)
	);

	// 6. on récupère les compositions qui ont été stockées dans la table temporaire 'spip_migration_compositions';
	$all = sql_allfetsel('objet, id_objet, composition', 'spip_migration_compositions');

	$succes = 0;
	foreach ($all as $k => $v) {
		$succes += sql_updateq(
				$correspondances_tables[$v['objet']]['table'],
				array('composition' => $v['composition']),
				$correspondances_tables[$v['objet']]['nom_id'].'='. $v['id_objet']
		);
	}
	spip_log($succes . ' compositions migrees', 'migration_concurrences');

	// On active le thème "concurrences"
	migration_concurrences_activer_theme_idc();
}

// Activer le thème "concurrences"
function migration_concurrences_activer_theme_idc() {
	include_spip('inc/meta');
	ecrire_meta("zengarden_theme", "theme_concurrences");
	return;
}



function migration_concurrences_nettoyage_sql() {
	sql_drop_table('categories');
	sql_drop_table('contacts');
	sql_drop_table('xp_groups');
	sql_drop_table('xp_rubriques'); // pas servie
	sql_drop_table('xp_sessions');  // pas servie
	sql_drop_table('xp_info');      // pas servie
	sql_drop_table('xp_users');     // pas servie
	sql_drop_table('xp_groups_rubriques');
	sql_drop_table('xp_users_groups');
	sql_drop_table('xp_users_articles');
	sql_drop_table('xp_users_sessions'); // pas servie
	sql_drop_table('commande');
	sql_drop_table('idc_produits');
	sql_drop_table('idc_categories_produit');
	sql_drop_table('maj_categories');
	sql_drop_table('requetes');
	sql_drop_table('pays');
	sql_drop_table('desabonnements');
	sql_drop_table('desabonnements_2009-10-08');
	sql_drop_table('desabonnements_2011-04-12');
	sql_drop_table('spip_ortho_cache');
	sql_drop_table('spip_ortho_dico');
	sql_drop_table('spip_geo_pays');
	sql_alter('TABLE spip_auteurs DROP COLUMN id_user');
}



