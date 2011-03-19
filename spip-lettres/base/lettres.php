<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/

	global $table_des_abonnes;
	$table_des_abonnes['abonnes'] = array(
										'table'				=> 'abonnes',
										'url_prive'			=> 'abonnes_edit',
										'url_prive_titre'	=> _T('lettresprive:modifier_abonne'),
										'champ_id'			=> 'id_abonne',
										'champ_email'		=> 'email',
										'champ_nom'			=> 'nom'
										);
	$table_des_abonnes['auteurs'] = array(
										'table'				=> 'auteurs',
										'url_prive'			=> 'auteur_infos',
										'url_prive_titre'	=> _T('lettresprive:voir_fiche_auteur'),
										'champ_id'			=> 'id_auteur',
										'champ_email'		=> 'email',
										'champ_nom'			=> 'nom'
										);



	function lettres_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['abonnes'] = 'abonnes';
		$interface['table_des_tables']['abonnes_statistiques'] = 'abonnes_statistiques';
		$interface['table_des_tables']['lettres'] = 'lettres';
		$interface['table_des_tables']['lettres_statistiques'] = 'lettres_statistiques';
		$interface['table_des_tables']['themes'] = 'themes';
		$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_lettres';
		$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_rubriques';
		$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_statistiques';
		$interface['tables_jointures']['spip_abonnes'][] = 'rubriques';
		$interface['tables_jointures']['spip_abonnes'][] = 'abonnes_clics';
		$interface['tables_jointures']['spip_abonnes'][] = 'clics';
		$interface['tables_jointures']['spip_articles'][] = 'articles_lettres';
		$interface['tables_jointures']['spip_articles'][] = 'lettres';
		$interface['tables_jointures']['spip_lettres'][] = 'articles_lettres';
		$interface['tables_jointures']['spip_lettres'][] = 'articles';
		$interface['tables_jointures']['spip_lettres'][] = 'lettres_statistiques';
		$interface['tables_jointures']['spip_lettres'][] = 'mots_lettres';
		$interface['tables_jointures']['spip_lettres'][] = 'mots';
		$interface['tables_jointures']['spip_lettres'][] = 'rubriques';
		$interface['tables_jointures']['spip_lettres'][] = 'abonnes_lettres';
		$interface['tables_jointures']['spip_lettres']['id_auteur'] = 'auteurs_lettres';
		$interface['tables_jointures']['spip_lettres'][] = 'auteurs_lettres';
		$interface['tables_jointures']['spip_lettres'][] = 'documents_liens';
		$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_lettres';
		$interface['tables_jointures']['spip_mots'][] = 'mots_lettres';
		$interface['tables_jointures']['spip_themes'][] = 'rubriques';
		$interface['table_date']['abonnes']	= 'maj';
		$interface['table_date']['lettres']	= 'date';
		$interface['table_des_traitements']['URL_FORMULAIRE_LETTRES'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_LETTRE'][] = 'quote_amp(%s)';
		return $interface;
	}


	function lettres_declarer_tables_principales($tables_principales) {
		$spip_abonnes = array(
							"id_abonne"	=> "BIGINT(21) NOT NULL",
							"objet"		=> "VARCHAR(255) NOT NULL DEFAULT 'abonnes'",
							"id_objet"	=> "BIGINT(21) NOT NULL",
							"email"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
							"code"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
							"nom"		=> "VARCHAR(255) NOT NULL DEFAULT ''",
							"format"	=> "ENUM('html','texte','mixte') NOT NULL DEFAULT 'mixte'",
							"maj"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
							"extra"		=> "LONGBLOB NULL"
						);
		$spip_abonnes_key = array(
							"PRIMARY KEY" 	=> "id_abonne",
							"UNIQUE code"	=> "code"
						);
		$spip_clics = array(
							"id_clic"		=> "BIGINT(21) NOT NULL",
							"id_lettre"		=> "BIGINT(21) NOT NULL",
							"url"			=> "VARCHAR(255) NOT NULL"
						);
		$spip_clics_key = array(
							"PRIMARY KEY"	=> "id_clic",
							"UNIQUE lettre"	=> "id_lettre, url"
						);
		$spip_desabonnes = array(
							"id_desabonne"	=> "BIGINT(21) NOT NULL",
							"email"			=> "VARCHAR(255) NOT NULL DEFAULT ''"
						);
		$spip_desabonnes_key = array(
							"PRIMARY KEY" 	=> "id_desabonne",
							"UNIQUE email"	=> "email"
						);
		$spip_lettres = array(
							"id_lettre"				=> "BIGINT(21) NOT NULL",
							"id_rubrique"			=> "BIGINT(21) NOT NULL",
							"id_secteur"			=> "BIGINT(21) NOT NULL",
							"titre"					=> "TEXT NOT NULL",
							"descriptif"			=> "TEXT NOT NULL",
							"chapo"					=> "MEDIUMTEXT NOT NULL",
							"texte"					=> "longtext DEFAULT '' NOT NULL",
							"ps"					=> "TEXT NOT NULL",
							"date"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"lang"					=> "VARCHAR(10) NOT NULL",
							"langue_choisie"		=> "VARCHAR(3) DEFAULT 'non'",
							"maj"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"message_html"			=> "longtext DEFAULT '' NOT NULL",
							"message_texte"			=> "longtext DEFAULT '' NOT NULL",
							"date_debut_envoi"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
							"date_fin_envoi"		=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
							"statut"				=> "VARCHAR(15) NOT NULL DEFAULT 'brouillon'",
							"extra"					=> "longtext NULL"
						);
		$spip_lettres_key = array(
							"PRIMARY KEY"	=> "id_lettre"
						);
		$spip_rubriques_crontabs = array(
							"id_rubrique"			=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"titre"					=> "TEXT NOT NULL"
						);
		$spip_rubriques_crontabs_key = array(
							"UNIQUE id_rubrique"	=> "id_rubrique"
						);
		$spip_themes = array(
							"id_theme"		=> "BIGINT(21) NOT NULL",
							"id_rubrique"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"titre"			=> "TEXT NOT NULL",
							"lang"			=> "VARCHAR(10) NOT NULL"
						);
		$spip_themes_key = array(
							"PRIMARY KEY"			=> "id_theme",
							"UNIQUE id_rubrique"	=> "id_rubrique"
						);
		$tables_principales['spip_abonnes'] =
			array('field' => &$spip_abonnes, 'key' => &$spip_abonnes_key);
		$tables_principales['spip_clics'] =
			array('field' => &$spip_clics, 'key' => &$spip_clics_key);
		$tables_principales['spip_desabonnes'] =
			array('field' => &$spip_desabonnes, 'key' => &$spip_desabonnes_key);
		$tables_principales['spip_lettres'] =
			array('field' => &$spip_lettres, 'key' => &$spip_lettres_key);
		$tables_principales['spip_rubriques_crontabs'] =
			array('field' => &$spip_rubriques_crontabs, 'key' => &$spip_rubriques_crontabs_key);
		$tables_principales['spip_themes'] =
			array('field' => &$spip_themes, 'key' => &$spip_themes_key);
		return $tables_principales;
	}


	function lettres_declarer_tables_auxiliaires($tables_auxiliaires) {
		$spip_abonnes_clics = array(
							"id_abonne"		=> "BIGINT(21) NOT NULL",
							"id_clic"		=> "BIGINT(21) NOT NULL",
							"id_lettre"		=> "BIGINT(21) NOT NULL"
						);
		$spip_abonnes_clics_key = array();
		$spip_abonnes_lettres = array(
							"id_abonne"		=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"id_lettre" 	=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"statut"		=> "ENUM('a_envoyer','envoye','echec','annule') NOT NULL DEFAULT 'a_envoyer'",
							"format"		=> "ENUM('mixte','html','texte') NOT NULL DEFAULT 'mixte'",
							"verrou"		=> "TINYINT NOT NULL DEFAULT '0'",
							"maj"			=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'"
						);
		$spip_abonnes_lettres_key = array(
							"PRIMARY KEY"	=> "id_abonne, id_lettre"
						);
		$spip_abonnes_rubriques = array(
							"id_abonne"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"id_rubrique" 		=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"date_abonnement"	=> "DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'",
							"statut"			=> "ENUM('a_valider','valide') NOT NULL DEFAULT 'a_valider'"
						);
		$spip_abonnes_rubriques_key = array(
							"PRIMARY KEY" => "id_abonne, id_rubrique"
						);
		$spip_abonnes_statistiques = array(
							"periode"				=> "VARCHAR(7) NOT NULL",
							"nb_inscriptions"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"nb_desinscriptions"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_abonnes_statistiques_key = array(
							"PRIMARY KEY"	=> "periode"
						);
		$spip_articles_lettres = array(
							"id_article"	=> "BIGINT(21) NOT NULL",
							"id_lettre"		=> "BIGINT(21) NOT NULL"
						);
		$spip_articles_lettres_key = array(
							"PRIMARY KEY" 		=> "id_article, id_lettre",
							"KEY id_article"	=> "id_article",
							"KEY id_lettre"		=> "id_lettre"
						);
		$spip_auteurs_lettres = array(
							"id_auteur"		=> "BIGINT(21) NOT NULL",
							"id_lettre"		=> "BIGINT(21) NOT NULL"
						);
		$spip_auteurs_lettres_key = array(
							"PRIMARY KEY" 	=> "id_auteur, id_lettre",
							"KEY id_auteur"	=> "id_auteur",
							"KEY id_lettre"	=> "id_lettre"
						);
		$spip_lettres_statistiques = array(
							"periode"		=> "VARCHAR(7) NOT NULL",
							"nb_envois"		=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_lettres_statistiques_key = array(
							"PRIMARY KEY"	=> "periode"
						);
		$spip_mots_lettres = array(
							"id_mot"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"id_lettre"		=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_mots_lettres_key = array(
							"PRIMARY KEY"	=> "id_lettre, id_mot",
							"KEY id_mot"	=> "id_mot"
						);
		$tables_auxiliaires['spip_abonnes_clics'] = 
			array('field' => &$spip_abonnes_clics, 'key' => &$spip_abonnes_clics_key);
		$tables_auxiliaires['spip_abonnes_lettres'] = 
			array('field' => &$spip_abonnes_lettres, 'key' => &$spip_abonnes_lettres_key);
		$tables_auxiliaires['spip_abonnes_rubriques'] = 
			array('field' => &$spip_abonnes_rubriques, 'key' => &$spip_abonnes_rubriques_key);
		$tables_auxiliaires['spip_abonnes_statistiques'] = 
			array('field' => &$spip_abonnes_statistiques, 'key' => &$spip_abonnes_statistiques_key);
		$tables_auxiliaires['spip_articles_lettres'] = 
			array('field' => &$spip_articles_lettres, 'key' => &$spip_articles_lettres_key);
		$tables_auxiliaires['spip_auteurs_lettres'] = 
			array('field' => &$spip_auteurs_lettres, 'key' => &$spip_auteurs_lettres_key);
		$tables_auxiliaires['spip_lettres_statistiques'] = 
			array('field' => &$spip_lettres_statistiques, 'key' => &$spip_lettres_statistiques_key);
		$tables_auxiliaires['spip_mots_lettres'] = 
			array('field' => &$spip_mots_lettres, 'key' => &$spip_mots_lettres_key);
		return $tables_auxiliaires;
	}

	function lettres_upgrade($nom_meta_base_version,$version_cible){
		include_spip('inc/meta');
		// migration depuis l'ancien systeme de maj
		if (isset($GLOBALS['meta']['spip_lettres_version'])
		  AND !isset($GLOBALS['meta'][$nom_meta_base_version])){
			ecrire_meta($nom_meta_base_version,$GLOBALS['meta']['spip_lettres_version'],'non');
			effacer_meta('spip_lettres_version');
		}

		$current_version = 0.0;
		if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
				|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
			include_spip('base/create');
			include_spip('base/abstract_sql');
			if (version_compare($current_version,'0.1','<')){
				creer_base();
				ecrire_meta('spip_lettres_version', $version_plugin);
				ecrire_meta('spip_lettres_fond_formulaire_lettres', 'lettres');
				ecrire_meta('spip_lettres_fond_lettre_titre', 'emails/lettre_titre');
				ecrire_meta('spip_lettres_fond_lettre_html', 'emails/lettre_html');
				ecrire_meta('spip_lettres_fond_lettre_texte', 'emails/lettre_texte');
				ecrire_meta('spip_lettres_notifier_suppression_abonne', 'non');
				ecrire_meta('spip_lettres_utiliser_articles', 'non');
				ecrire_meta('spip_lettres_utiliser_descriptif', 'non');
				ecrire_meta('spip_lettres_utiliser_chapo', 'non');
				ecrire_meta('spip_lettres_utiliser_ps', 'non');
				ecrire_meta('spip_lettres_envois_recurrents', 'non');
				ecrire_meta('spip_lettres_cron', md5(uniqid(rand())));
				ecrire_meta('spip_lettres_cliquer_anonyme', 'oui');
				ecrire_meta('spip_lettres_admin_abo_toutes_rubriques', 'non');
				if (!strpos($GLOBALS['meta']['preview'],',0minirezo,'))
					ecrire_meta('preview',',0minirezo,');
				ecrire_metas();
				include_spip('inc/getdocument');
				creer_repertoire_documents('lettres');
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
			}
			if (version_compare($current_version,'3.0','<')){
				creer_base();
				ecrire_meta('spip_lettres_notifier_suppression_abonne', 'non');
				ecrire_meta('spip_lettres_utiliser_articles', 'non');
				ecrire_meta($nom_meta_base_version,$current_version='3.0','non');
			}
			if (version_compare($current_version,'3.1','<')){
				maj_tables('spip_lettres');
				ecrire_meta('spip_lettres_utiliser_ps', 'non');
				ecrire_meta($nom_meta_base_version,$current_version='3.1','non');
			}
			if (version_compare($current_version,'3.2','<')){
				$INDEX_elements_objet = unserialize($GLOBALS['meta']['INDEX_elements_objet']);
				unset($INDEX_elements_objet['spip_lettres']);
				ecrire_meta('INDEX_elements_objet',serialize($INDEX_elements_objet));
				ecrire_meta($nom_meta_base_version,$current_version='3.2','non');
			}
			if (version_compare($current_version,'3.3','<')){
				creer_base(); // table spip_desabonnes
				ecrire_meta($nom_meta_base_version,$current_version='3.3','non');
			}
			if (version_compare($current_version,'3.4','<')){
				include_spip('inc/getdocument');
				creer_repertoire_documents('lettres');
				ecrire_meta($nom_meta_base_version,$current_version='3.4','non');
			}
			if (version_compare($current_version,'3.5','<')){
				ecrire_meta($nom_meta_base_version,$current_version='3.5','non');
			}
			if (version_compare($current_version,'3.6','<')){
				ecrire_meta('spip_lettres_cron', md5(uniqid(rand())));
				ecrire_meta($nom_meta_base_version,$current_version='3.6','non');
			}
			if (version_compare($current_version,'3.7','<')){
				creer_base(); // table spip_rubriques_crontabs
				ecrire_meta($nom_meta_base_version,$current_version='3.7','non');
			}
			if (version_compare($current_version,'3.8','<')){
				maj_tables('spip_lettres');
				sql_alter("TABLE spip_lettres DROP idx");
				sql_drop_table('spip_documents_lettres', true);
				ecrire_meta('spip_lettres_utiliser_descriptif', 'non');
				ecrire_meta('spip_lettres_utiliser_chapo', 'non');
				ecrire_meta('spip_lettres_fond_lettre_titre', 'emails/lettre_titre');
				ecrire_meta('spip_lettres_envois_recurrents', 'non');
				ecrire_meta($nom_meta_base_version,$current_version='3.8','non');
			}
			if (version_compare($current_version,'4.0.0','<')){
				echo "SPIP-Lettres MAJ 4.0.0<br />";
				if ($GLOBALS['meta']['spip_lettres_fond_lettre_titre']=='lettre_titre'
				  AND !find_in_path('lettre_titre.html'))
					ecrire_meta('spip_lettres_fond_lettre_titre', 'emails/lettre_titre');
				if ($GLOBALS['meta']['spip_lettres_fond_lettre_texte']=='lettre_texte'
				  AND !find_in_path('lettre_texte.html'))
					ecrire_meta('spip_lettres_fond_lettre_texte', 'emails/lettre_texte');
				if ($GLOBALS['meta']['spip_lettres_fond_lettre_html']=='lettre_html'
				  AND !find_in_path('lettre_html.html'))
					ecrire_meta('spip_lettres_fond_lettre_html', 'emails/lettre_html');
				ecrire_meta($nom_meta_base_version,$current_version='4.0.0','non');
			}
			if (version_compare($current_version,'4.0.1','<')){
				echo "SPIP-Lettres MAJ 4.0.1<br />";
				sql_alter("TABLE spip_lettres CHANGE statut statut VARCHAR(15) NOT NULL DEFAULT 'brouillon'");
				ecrire_meta($nom_meta_base_version,$current_version='4.0.1','non');
			}
			if (version_compare($current_version,'4.0.2','<')){
				echo "SPIP-Lettres MAJ 4.0.2<br />";
				sql_alter("TABLE spip_lettres CHANGE texte texte longtext DEFAULT '' NOT NULL");
				sql_alter("TABLE spip_lettres CHANGE message_html message_html longtext DEFAULT '' NOT NULL");
				sql_alter("TABLE spip_lettres CHANGE message_texte message_texte longtext DEFAULT '' NOT NULL");
				sql_alter("TABLE spip_lettres CHANGE extra extra longtext NULL");
				ecrire_meta($nom_meta_base_version,$current_version='4.0.2','non');
			}
			if (version_compare($current_version,'4.1','<'))
				ecrire_meta('spip_lettres_cliquer_anonyme', 'oui');
				ecrire_meta('spip_lettres_admin_abo_toutes_rubriques', 'non');
				ecrire_meta($nom_meta_base_version,$current_version='4.1','non');
		}
	}

	function lettres_vider_tables($nom_meta_base_version) {
		include_spip('inc/meta');
		include_spip('base/abstract_sql');
		include_spip('classes/lettre');

		$res = sql_select('id_lettre', 'spip_lettres');
		while ($arr = sql_fetch($res)) {
			$lettre = new lettre($arr['id_lettre']);
			$lettre->supprimer();
		}
		include_spip('base/abstract_sql');
		sql_drop_table('spip_abonnes', true);
		sql_drop_table('spip_clics', true);
		sql_drop_table('spip_desabonnes', true);
		sql_drop_table('spip_lettres', true);
		sql_drop_table('spip_rubriques_crontabs', true);
		sql_drop_table('spip_themes', true);
		sql_drop_table('spip_abonnes_clics', true);
		sql_drop_table('spip_abonnes_lettres', true);
		sql_drop_table('spip_abonnes_rubriques', true);
		sql_drop_table('spip_abonnes_statistiques', true);
		sql_drop_table('spip_articles_lettres', true);
		sql_drop_table('spip_auteurs_lettres', true);
		sql_drop_table('spip_documents_lettres', true);
		sql_drop_table('spip_lettres_statistiques', true);
		sql_drop_table('spip_mots_lettres', true);
		effacer_meta('spip_lettres_version');
		effacer_meta('spip_lettres_fond_formulaire_lettres');
		effacer_meta('spip_lettres_fond_lettre_titre');
		effacer_meta('spip_lettres_fond_lettre_html');
		effacer_meta('spip_lettres_fond_lettre_texte');
		effacer_meta('spip_lettres_notifier_suppression_abonne');
		effacer_meta('spip_lettres_utiliser_articles');
		effacer_meta('spip_lettres_utiliser_descriptif');
		effacer_meta('spip_lettres_utiliser_chapo');
		effacer_meta('spip_lettres_utiliser_ps');
		effacer_meta('spip_lettres_envois_recurrents');
		effacer_meta('derniere_modif_lettre');
		effacer_meta('spip_lettres_cron');
		effacer_meta('spip_lettres_abonnement_par_defaut');
		effacer_meta('spip_lettres_cliquer_anonyme');
		effacer_meta('spip_lettres_admin_abo_toutes_rubriques');
		include_spip('inc/getdocument');
		effacer_repertoire_temporaire(_DIR_LETTRES);
		effacer_meta($nom_meta_base_version);
	}

?>