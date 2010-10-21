<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/



	function formulaires_declarer_tables_interfaces($interface) {
		$interface['table_des_tables']['applicants'] = 'applicants';
		$interface['table_des_tables']['applications'] = 'applications';
		$interface['table_des_tables']['blocs'] = 'blocs';
		$interface['table_des_tables']['choix_question'] = 'choix_question';
		$interface['table_des_tables']['formulaires'] = 'formulaires';
		$interface['table_des_tables']['questions'] = 'questions';
		$interface['table_des_tables']['reponses'] = 'reponses';
		$interface['tables_jointures']['spip_applicants'][] = 'applications';
		$interface['tables_jointures']['spip_applications'][] = 'applicants';
		$interface['tables_jointures']['spip_applications'][] = 'formulaires';
		$interface['tables_jointures']['spip_applications'][] = 'reponses';
		$interface['tables_jointures']['spip_auteurs'][] = 'auteurs_formulaires';
		$interface['tables_jointures']['spip_blocs'][] = 'formulaires';
		$interface['tables_jointures']['spip_blocs'][] = 'questions';
		$interface['tables_jointures']['spip_choix_question'][] = 'question';
		$interface['tables_jointures']['spip_formulaires'][] = 'applications';
		$interface['tables_jointures']['spip_formulaires'][] = 'auteurs_formulaires';
		$interface['tables_jointures']['spip_formulaires'][] = 'blocs';
		$interface['tables_jointures']['spip_formulaires'][] = 'choix_question';
		$interface['tables_jointures']['spip_formulaires'][] = 'mots_formulaires';
		$interface['tables_jointures']['spip_formulaires'][] = 'mots';
		$interface['tables_jointures']['spip_formulaires'][] = 'questions';
		$interface['tables_jointures']['spip_formulaires'][] = 'reponses';
		$interface['tables_jointures']['spip_formulaires'][] = 'rubriques';
		$interface['tables_jointures']['mots'][] = 'mots_formulaires';
		$interface['tables_jointures']['spip_questions'][] = 'blocs';
		$interface['tables_jointures']['spip_questions'][] = 'choix_question';
		$interface['tables_jointures']['spip_questions'][] = 'reponses';
		$interface['tables_jointures']['spip_reponses'][] = 'questions';
		$interface['tables_jointures']['spip_reponses'][] = 'applications';
		$interface['table_des_traitements']['MERCI'][] = 'propre(%s)';
		$interface['table_des_traitements']['URL_FORMULAIRE'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_BLOC'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_ACTION_LOGIN_FORMULAIRE'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_ACTION_LOGOUT_FORMULAIRE'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_FORMULAIRE_ESPACE_FORMULAIRE'][] = 'quote_amp(%s)';
		$interface['table_des_traitements']['URL_FORMULAIRE_OUBLI_FORMULAIRE'][] = 'quote_amp(%s)';
		return $interface;
	}


	function formulaires_declarer_tables_principales($tables_principales) {
		$spip_applicants = array(
							"id_applicant"	=> "BIGINT(21) NOT NULL",
							"iv"			=> "VARCHAR(32) NOT NULL",
							"email"			=> "VARCHAR(255) NOT NULL DEFAULT ''",
							"nom"			=> "VARCHAR(255) NOT NULL DEFAULT ''",
							"mdp"			=> "TINYTEXT NOT NULL",
							"cookie"		=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
						);
		$spip_applicants_key = array(
							"PRIMARY KEY"		=> "id_applicant",
							"INDEX email"		=> "email",
							"UNIQUE"			=> "iv"
						);
		$spip_applications = array(
							"id_application"	=> "BIGINT(21) NOT NULL",
							"id_applicant"		=> "BIGINT(21) NOT NULL",
							"id_formulaire"		=> "BIGINT(21) NOT NULL",
							"statut"			=> "ENUM('temporaire','valide','traite') NOT NULL DEFAULT 'temporaire'",
							"note"				=> "TEXT NOT NULL",
							"maj"				=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL"
						);
		$spip_applications_key = array(
							"PRIMARY KEY"		=> "id_application"
						);
		$spip_blocs = array(
							"id_bloc"		=> "BIGINT(21) NOT NULL",
							"id_formulaire"	=> "BIGINT(21) NOT NULL",
							"ordre"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"titre"			=> "TEXT NOT NULL",
							"descriptif"	=> "TEXT NOT NULL",
							"texte"			=> "LONGBLOB NOT NULL"
						);
		$spip_blocs_key = array(
							"PRIMARY KEY"		=> "id_bloc",
							"KEY id_formulaire"	=> "id_formulaire"
						);
		$spip_choix_question = array(
							"id_choix_question"	=> "BIGINT(21) NOT NULL",
							"id_question"		=> "BIGINT(21) NOT NULL",
							"ordre"				=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"titre"				=> "TEXT NOT NULL",
							"id_rubrique"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"id_auteur"			=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_choix_question_key = array(
							"PRIMARY KEY"		=> "id_choix_question",
							"KEY id_question"	=> "id_question"
						);
		$spip_formulaires = array(
							"id_formulaire" 		=> "BIGINT(21) NOT NULL",
							"id_rubrique"	 		=> "BIGINT(21) NOT NULL",
							"id_secteur"	 		=> "BIGINT(21) NOT NULL",
							"titre"					=> "TEXT NOT NULL",
							"descriptif"			=> "TEXT NOT NULL",
							"chapo"					=> "TEXT NOT NULL",
							"texte"					=> "TEXT NOT NULL",
							"merci"					=> "TEXT NOT NULL",
							"ps"					=> "MEDIUMTEXT NOT NULL",
							"lang"					=> "VARCHAR(10) NOT NULL",
							"langue_choisie"		=> "VARCHAR(3) DEFAULT 'non'",
							"date"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"maj"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
							"type"					=> "ENUM('une_seule_page','plusieurs_pages') NOT NULL DEFAULT 'plusieurs_pages'",
							"limiter_invitation"	=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
							"limiter_applicant"		=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
							"notifier_applicant"	=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
							"notifier_auteurs"		=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
							"statut"				=> "ENUM('hors_ligne','en_ligne') NOT NULL DEFAULT 'hors_ligne'"
						);
		$spip_formulaires_key = array(
							"PRIMARY KEY" 	=> "id_formulaire"
						);
		$spip_questions = array(
							"id_question"	=> "BIGINT(21) NOT NULL",
							"id_bloc"		=> "BIGINT(21) NOT NULL",
							"ordre"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
							"titre"			=> "TEXT NOT NULL",
							"descriptif"	=> "TEXT NOT NULL",
							"type"			=> "ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier','auteurs') NOT NULL DEFAULT 'champ_texte'",
							"mime"			=> "TEXT NOT NULL",
							"obligatoire"	=> "TINYINT(1) NOT NULL DEFAULT '0'",
							"controle"		=> "ENUM('non_vide','email','email_applicant','url','nombre','date') NOT NULL DEFAULT 'non_vide'"
						);
		$spip_questions_key = array(
							"PRIMARY KEY"		=> "id_question",
							"KEY id_bloc"		=> "id_bloc"
						);
		$spip_reponses = array(
							"id_reponse"		=> "BIGINT(21) NOT NULL",
							"id_question"		=> "BIGINT(21) NOT NULL",
							"id_application"	=> "BIGINT(21) NOT NULL",
							"valeur"			=> "TEXT NOT NULL"
						);
		$spip_reponses_key = array(
							"PRIMARY KEY"		=> "id_reponse",
							"KEY id_question"	=> "id_question"
						);
		$tables_principales['spip_applicants'] =
			array('field' => &$spip_applicants, 'key' => &$spip_applicants_key);
		$tables_principales['spip_applications'] =
			array('field' => &$spip_applications, 'key' => &$spip_applications_key);
		$tables_principales['spip_blocs'] =
			array('field' => &$spip_blocs, 'key' => &$spip_blocs_key);
		$tables_principales['spip_choix_question'] =
			array('field' => &$spip_choix_question, 'key' => &$spip_choix_question_key);
		$tables_principales['spip_formulaires'] =
			array('field' => &$spip_formulaires, 'key' => &$spip_formulaires_key);
		$tables_principales['spip_questions'] =
			array('field' => &$spip_questions, 'key' => &$spip_questions_key);
		$tables_principales['spip_reponses'] =
			array('field' => &$spip_reponses, 'key' => &$spip_reponses_key);
		return $tables_principales;
	}


	function formulaires_declarer_tables_auxiliaires($tables_auxiliaires) {
		$spip_auteurs_formulaires = array(
							"id_auteur"			=> "BIGINT(21) NOT NULL",
							"id_formulaire"		=> "BIGINT(21) NOT NULL"
						);
		$spip_auteurs_formulaires_key = array(
							"PRIMARY KEY" 		=> "id_auteur, id_formulaire",
							"KEY id_mot"		=> "id_auteur",
							"KEY id_formulaire"	=> "id_formulaire"
						);
		$spip_mots_formulaires = array(
							"id_mot"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
							"id_formulaire"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
						);
		$spip_mots_formulaires_key = array(
							"PRIMARY KEY"	=> "id_formulaire, id_mot",
							"KEY id_mot"	=> "id_mot"
						);
		$tables_auxiliaires['spip_auteurs_formulaires'] = 
			array('field' => &$spip_auteurs_formulaires, 'key' => &$spip_auteurs_formulaires_key);
		$tables_auxiliaires['spip_mots_formulaires'] = 
			array('field' => &$spip_mots_formulaires, 'key' => &$spip_mots_formulaires_key);
		return $tables_auxiliaires;
	}


	function formulaires_install($action) {
		include_spip('inc/plugin');
		if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
			$get_infos = charger_fonction('get_infos','plugins');
			$info_plugin_formulaires = $get_infos(_DIR_PLUGIN_SPIPBB);
		}
		else {
			$info_plugin_formulaires = plugin_get_infos(_DIR_PLUGIN_SPIPBB);
		}
		$version_plugin = $info_plugin_formulaires['version'];
		switch ($action) {
			case 'test':
				return (isset($GLOBALS['meta']['spip_formulaires_version']) AND ($GLOBALS['meta']['spip_formulaires_version'] >= $version_plugin));
				break;
			case 'install':
				include_spip('base/create');
				include_spip('base/abstract_sql');
				if (!isset($GLOBALS['meta']['spip_formulaires_version'])) {
					creer_base();
					include_spip('inc/getdocument');
					creer_repertoire_documents('formulaires');
					$secret = formulaires_generer_nouveau_mdp(16);
					ecrire_meta('spip_formulaires_blowfish', $secret);
					ecrire_meta('spip_formulaires_version', $version_plugin);
					ecrire_meta('spip_formulaires_fond_formulaire_espace_formulaire', 'espace_formulaire');
					ecrire_meta('spip_formulaires_fond_formulaire_oubli_formulaire', 'oubli_formulaire');
					ecrire_meta('spip_formulaires_utiliser_descriptif', 'oui');
					ecrire_meta('spip_formulaires_utiliser_chapo', 'oui');
					ecrire_meta('spip_formulaires_utiliser_ps', 'oui');
					ecrire_metas();
				} else {
					$version_base = $GLOBALS['meta']['spip_formulaires_version'];
					if ($version_base < 0.2) {
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','date','email_applicant') NOT NULL DEFAULT 'champ_texte'");
						ecrire_meta('spip_formulaires_version', $version_base = 0.2);
						ecrire_metas();
					}
					if ($version_base < 0.3) {
						ecrire_meta('spip_formulaires_fond_formulaire_espace_applicant', 'espace_applicant');
						ecrire_meta('spip_formulaires_fond_formulaire_oubli_formulaire', 'oubli_formulaire');
						sql_drop_table('spip_lettres_formulaires', true);
						sql_alter("TABLE spip_formulaires ADD limiter_invitation ENUM('oui','non') NOT NULL DEFAULT 'non' AFTER limiter_temps");
						sql_alter("TABLE spip_formulaires DROP inscrire_applicant");
						sql_alter("TABLE spip_applications ADD maj DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
						sql_alter("TABLE spip_applicants DROP code");
						sql_alter("TABLE spip_applicants ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER cookie");
						sql_alter("TABLE spip_reponses ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER valeur");
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','date','email_applicant','lettres') NOT NULL DEFAULT 'champ_texte'");
						sql_alter("TABLE spip_questions CHANGE controle controle ENUM('non_vide','email','email_applicant','url','nombre') NOT NULL DEFAULT 'non_vide'");
						sql_updateq('spip_questions', array('controle' => 'email_applicant'), "type='email_applicant'");
						sql_alter("TABLE spip_choix_question ADD id_lettre BIGINT (21) DEFAULT '0' NOT NULL AFTER titre");
						ecrire_meta('spip_formulaires_version', $version_base = 0.3);
						ecrire_metas();
					}
					if ($version_base < 0.4) {
						sql_alter("TABLE spip_applicants DROP idx");
						sql_alter("TABLE spip_choix_question DROP idx");
						ecrire_meta('spip_formulaires_version', $version_base = 0.4);
						ecrire_metas();
					}
					if ($version_base < 0.5) {
						sql_alter("TABLE spip_applicants DROP INDEX email");
						ecrire_meta('spip_formulaires_version', $version_base = 0.5);
						ecrire_metas();
					}
					if ($version_base < 0.6) {
						sql_alter("TABLE spip_applicants CHANGE iv iv VARCHAR(32) NOT NULL;");
						$res = sql_select('*', 'spip_applicants');
						while ($arr = sql_fetch($res)) {
							$verification = false;
							$i = 0;
							while (!$verification) {
								if ($i == 0)
							    	$iv = $arr['iv'];
								else
									$iv = formulaires_generer_vecteur_initialisation();
								$res2 = sql_select('id_applicant', 'spip_applicants', 'iv="'.base64_encode($iv).'"');
								if (sql_count($res2) == 0)
									$verification = true;
								$i++;
							}
							sql_updateq('spip_applicants', array('iv' => base64_encode($iv)), 'id_applicant='.intval($arr['id_applicant']));
						}
						ecrire_meta('spip_formulaires_version', $version_base = 0.6);
						ecrire_metas();
					}
					if ($version_base < 0.7) {
						sql_alter("TABLE spip_applicants ADD UNIQUE (`iv`);");
						ecrire_meta('spip_formulaires_version', $version_base = 0.7);
						ecrire_metas();
					}
					if ($version_base < 0.8) {
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','lettres') NOT NULL DEFAULT 'champ_texte'");
						sql_alter("TABLE spip_questions CHANGE controle controle ENUM('non_vide','email','email_applicant','url','nombre','date') NOT NULL DEFAULT 'non_vide'");
						ecrire_meta('spip_formulaires_version', $version_base = 0.8);
						ecrire_metas();
					}
					if ($version_base < 0.9) {
						include_spip('inc/getdocument');
						creer_repertoire_documents('formulaires');
						creer_base();
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','lettres','fichier') NOT NULL DEFAULT 'champ_texte'");
						ecrire_meta('spip_formulaires_version', $version_base = 0.9);
						ecrire_metas();
					}
					if ($version_base < 1.0) {
						sql_alter("TABLE spip_applicants ADD nom VARCHAR(255) NOT NULL DEFAULT '' AFTER email");
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','lettres','abonnements','fichier') NOT NULL DEFAULT 'champ_texte'");
						// id_lettre -> id_rubrique
						$blocs = sql_select('*', 'spip_blocs');
						while ($b = sql_fetch($blocs)) {
							$questions = sql_select('*', 'spip_questions', 'type="lettres" AND id_bloc='.intval($b['id_bloc']));
							while ($q = sql_fetch($questions)) {
								$choix_questions = sql_select('*', 'spip_choix_question', 'id_question='.intval($q['id_question']));
								while ($c = sql_fetch($choix_questions)) {
									$lettre = sql_select('id_rubrique', 'spip_lettres', 'id_lettre='.intval($c['id_lettre']));
									if (sql_count($lettre)) {
										$tab = sql_fetch($lettre);
										sql_updateq('spip_choix_question', array('id_lettre' => intval($tab['id_rubrique'])), 'id_choix_question='.intval($c['id_choix_question']));
									} else {
										// la lettre a été supprimée
										$choix_question = new choix_question($b['id_formulaire'], $b['id_bloc'], $q['id_question'], $c['id_choix_question']);
										$choix_question->supprimer();
									}
								}
							}
						}
						sql_updateq('spip_questions', array('type' => 'abonnements'), "type='lettres'");
						sql_alter("TABLE spip_choix_question CHANGE id_lettre id_rubrique BIGINT(21) DEFAULT '0' NOT NULL");
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier') NOT NULL DEFAULT 'champ_texte'");
						ecrire_meta('spip_formulaires_version', $version_base = 1.0);
						ecrire_metas();
					}
					if ($version_base < 1.1) {
						sql_alter("TABLE spip_applicants CHANGE nom nom VARCHAR(255) NOT NULL DEFAULT ''");
						sql_alter("TABLE spip_applicants DROP INDEX iv_2");
						sql_alter("TABLE spip_applicants ADD maj DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL AFTER cookie");
						sql_alter("TABLE spip_applicants ADD idx ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL AFTER maj");
						ecrire_meta('spip_formulaires_version', $version_base = 1.1);
						ecrire_metas();
					}
					if ($version_base < 1.2) {
						sql_alter("TABLE spip_questions CHANGE type type ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier','auteurs') NOT NULL DEFAULT 'champ_texte'");
						sql_alter("TABLE spip_choix_question ADD id_auteur BIGINT (21) DEFAULT '0' NOT NULL AFTER id_rubrique");
						ecrire_meta('spip_formulaires_version', $version_base = 1.2);
						ecrire_metas();
					}
					if ($version_base < 1.3) {
						sql_alter("TABLE spip_choix_question ADD id_auteur BIGINT (21) DEFAULT '0' NOT NULL AFTER id_rubrique");
						ecrire_meta('spip_formulaires_version', $version_base = 1.3);
						ecrire_metas();
					}
					if ($version_base < 1.4) {
						sql_alter("TABLE spip_formulaires ADD chapo TEXT NOT NULL AFTER descriptif");
						ecrire_meta('spip_formulaires_version', $version_base = 1.4);
						ecrire_metas();		
					}
					if ($version_base < 2.0) {
						maj_tables('spip_formulaires');
						sql_alter("TABLE spip_questions ADD mime TEXT NOT NULL AFTER type");
						sql_updateq('spip_questions', array('mime' => serialize(array('image/jpeg', 'image/png', 'image/gif'))), 'type="fichier"');
						sql_alter("TABLE spip_applicants ADD INDEX email (email)");
						sql_alter("TABLE spip_applicants DROP idx");
						sql_alter("TABLE spip_choix_question DROP idx");
						sql_alter("TABLE spip_formulaires DROP idx");
						sql_alter("TABLE spip_reponses DROP idx");
						sql_alter("TABLE spip_formulaires DROP limiter_temps");
						sql_alter("TABLE spip_formulaires DROP date_fin");
						sql_alter("TABLE spip_formulaires CHANGE date_debut date DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL");
						sql_alter("TABLE spip_formulaires CHANGE statut statut ENUM('en_attente','prepa','publie','hors_ligne','en_ligne') NOT NULL DEFAULT 'en_attente'");
						sql_updateq('spip_formulaires', array('statut' => 'hors_ligne'), 'en_ligne="non"');
						sql_updateq('spip_formulaires', array('statut' => 'en_ligne'), 'en_ligne="oui"');
						sql_alter("TABLE spip_formulaires CHANGE statut statut ENUM('hors_ligne','en_ligne') NOT NULL DEFAULT 'hors_ligne'");
						sql_alter("TABLE spip_formulaires DROP en_ligne");
						effacer_meta('spip_formulaires_fond_formulaire_espace_applicant');
						ecrire_meta('spip_formulaires_fond_formulaire_espace_formulaire', 'espace_formulaire');
						ecrire_meta('spip_formulaires_utiliser_descriptif', 'oui');
						ecrire_meta('spip_formulaires_utiliser_chapo', 'oui');
						ecrire_meta('spip_formulaires_utiliser_ps', 'oui');
						ecrire_meta('spip_formulaires_version', $version_base = 2.0);
						ecrire_metas();		
					}
				}
				break;
			case 'uninstall':
				include_spip('base/abstract_sql');
				include_spip('inc/cookie');
				spip_setcookie('spip_formulaires_mcrypt_iv');
				spip_setcookie('spip_formulaires_id_applicant');
				sql_drop_table('spip_applicants', true);
				sql_drop_table('spip_applications', true);
				sql_drop_table('spip_blocs', true);
				sql_drop_table('spip_choix_question', true);
				sql_drop_table('spip_formulaires', true);
				sql_drop_table('spip_questions', true);
				sql_drop_table('spip_reponses', true);
				sql_drop_table('spip_auteurs_formulaires', true);
				sql_drop_table('spip_mots_formulaires', true);
				effacer_meta('spip_formulaires_blowfish');
				effacer_meta('spip_formulaires_version');
				effacer_meta('spip_formulaires_fond_formulaire_espace_formulaire');
				effacer_meta('spip_formulaires_fond_formulaire_oubli_formulaire');
				effacer_meta('spip_formulaires_utiliser_descriptif');
				effacer_meta('spip_formulaires_utiliser_chapo');
				effacer_meta('spip_formulaires_utiliser_ps');
				$res = sql_select('id_formulaire', 'spip_formulaires');
				while ($arr = sql_fetch($res)) {
					$formulaire = new formulaire($arr['id_formulaire']);
					$formulaire->supprimer();
				}
				include_spip('inc/getdocument');
				effacer_repertoire_temporaire(_DIR_FORMULAIRES);
				break;
		}
	}


	global $table_des_abonnes;
	$table_des_abonnes['applicants'] = array(
										'table'				=> 'applicants',
										'url_prive'			=> 'applicants',
										'url_prive_titre'	=> _T('formulairesprive:modifier_applicant'),
										'champ_id'			=> 'id_applicant',
										'champ_email'		=> 'email',
										'champ_nom'			=> 'nom'
										);


?>