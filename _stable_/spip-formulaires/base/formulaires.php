<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/



	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;
	global $choses_possibles;
	global $table_des_traitements;
	global $table_des_abonnes;



	$table_des_tables['applicants'] = 'applicants';
	$table_des_tables['applications'] = 'applications';
	$table_des_tables['blocs'] = 'blocs';
	$table_des_tables['choix_question'] = 'choix_question';
	$table_des_tables['formulaires'] = 'formulaires';
	$table_des_tables['questions'] = 'questions';
	$table_des_tables['reponses'] = 'reponses';



	$spip_applicants = array(
						"id_applicant"	=> "BIGINT(21) NOT NULL",
						"iv"			=> "VARCHAR(32) NOT NULL",
						"email"			=> "VARCHAR(255) NOT NULL DEFAULT ''",
						"nom"			=> "VARCHAR(255) NOT NULL DEFAULT ''",
						"mdp"			=> "TINYTEXT NOT NULL",
						"cookie"		=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"maj"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"idx"			=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_applicants_key = array(
						"PRIMARY KEY"		=> "id_applicant",
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

	$spip_auteurs_formulaires = array(
						"id_auteur"			=> "BIGINT(21) NOT NULL",
						"id_formulaire"		=> "BIGINT(21) NOT NULL"
					);
	$spip_auteurs_formulaires_key = array(
						"PRIMARY KEY" 		=> "id_auteur, id_formulaire",
						"KEY id_mot"		=> "id_auteur",
						"KEY id_formulaire"	=> "id_formulaire"
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
						"id_auteur"			=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"idx"				=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_choix_question_key = array(
						"PRIMARY KEY"		=> "id_choix_question",
						"KEY id_question"	=> "id_question"
					);

	$spip_documents_applications = array(
						"id_document"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"id_application"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
					);
	$spip_documents_applications_key = array(
						"PRIMARY KEY"		=> "id_document, id_application",
						"KEY id_document"	=> "id_document"
					);

	$spip_documents_formulaires = array(
						"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"id_formulaire"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
					);
	$spip_documents_formulaires_key = array(
						"PRIMARY KEY"		=> "id_document, id_formulaire",
						"KEY id_document"	=> "id_document"
					);

	$spip_formulaires = array(
						"id_formulaire" 		=> "BIGINT(21) NOT NULL",
						"id_rubrique"	 		=> "BIGINT(21) NOT NULL",
						"id_secteur"	 		=> "BIGINT(21) NOT NULL",
						"titre"					=> "TEXT NOT NULL",
						"descriptif"			=> "TEXT NOT NULL",
						"chapo"					=> "TEXT NOT NULL",
						"texte"					=> "TEXT NOT NULL",
						"ps"					=> "MEDIUMTEXT NOT NULL",
						"lang"					=> "VARCHAR(10) NOT NULL",
						"langue_choisie"		=> "VARCHAR(3) DEFAULT 'non'",
						"date_debut"			=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"date_fin"				=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"maj"					=> "DATETIME DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"type"					=> "ENUM('une_seule_page','plusieurs_pages') NOT NULL DEFAULT 'plusieurs_pages'",
						"limiter_temps"			=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"limiter_invitation"	=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"limiter_applicant"		=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"notifier_applicant"	=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"notifier_auteurs"		=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"en_ligne"				=> "ENUM('oui','non') NOT NULL DEFAULT 'non'",
						"statut"				=> "ENUM('en_attente','publie','termine') NOT NULL DEFAULT 'en_attente'",
						"idx"					=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_formulaires_key = array(
						"PRIMARY KEY" 	=> "id_formulaire"
					);

	$spip_documents_formulaires = array(
						"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"id_formulaire"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
					);
	$spip_documents_formulaires_key = array(
						"PRIMARY KEY"		=> "id_document, id_formulaire",
						"KEY id_document"	=> "id_document"
					);

	$spip_mots_formulaires = array(
						"id_mot"		=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"id_formulaire"	=> "BIGINT (21) DEFAULT '0' NOT NULL"
					);
	$spip_mots_formulaires_key = array(
						"PRIMARY KEY"	=> "id_formulaire, id_mot",
						"KEY id_mot"	=> "id_mot"
					);

	$spip_questions = array(
						"id_question"	=> "BIGINT(21) NOT NULL",
						"id_bloc"		=> "BIGINT(21) NOT NULL",
						"ordre"			=> "BIGINT(21) NOT NULL DEFAULT '0'",
						"titre"			=> "TEXT NOT NULL",
						"descriptif"	=> "TEXT NOT NULL",
						"type"			=> "ENUM('champ_texte','zone_texte','boutons_radio','cases_a_cocher','liste','liste_multiple','email_applicant','nom_applicant','abonnements','fichier','auteurs') NOT NULL DEFAULT 'champ_texte'",
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
						"valeur"			=> "TEXT NOT NULL",
						"idx"				=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
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



	$tables_auxiliaires['spip_auteurs_formulaires'] = 
		array('field' => &$spip_auteurs_formulaires, 'key' => &$spip_auteurs_formulaires_key);
	$tables_auxiliaires['spip_documents_applications'] = 
		array('field' => &$spip_documents_applications, 'key' => &$spip_documents_applications_key);
	$tables_auxiliaires['spip_documents_formulaires'] = 
		array('field' => &$spip_documents_formulaires, 'key' => &$spip_documents_formulaires_key);
	$tables_auxiliaires['spip_mots_formulaires'] = 
		array('field' => &$spip_mots_formulaires, 'key' => &$spip_mots_formulaires_key);



	$tables_jointures['spip_applicants'][]= 'applications';

	$tables_jointures['spip_applications'][]= 'applicants';
	$tables_jointures['spip_applications'][]= 'documents_applications';
	$tables_jointures['spip_applications'][]= 'formulaires';
	$tables_jointures['spip_applications'][]= 'reponses';

	$tables_jointures['spip_auteurs'][]= 'auteurs_formulaires';

	$tables_jointures['spip_blocs'][]= 'formulaires';
	$tables_jointures['spip_blocs'][]= 'questions';

	$tables_jointures['spip_choix_question'][]= 'question';

	$tables_jointures['spip_documents'][]= 'documents_applications';
	$tables_jointures['spip_documents'][]= 'documents_formulaires';

	$tables_jointures['spip_formulaires'][]= 'applications';
	$tables_jointures['spip_formulaires'][]= 'auteurs_formulaires';
	$tables_jointures['spip_formulaires'][]= 'blocs';
	$tables_jointures['spip_formulaires'][]= 'choix_question';
	$tables_jointures['spip_formulaires'][]= 'documents_formulaires';
	$tables_jointures['spip_formulaires'][]= 'mots_formulaires';
	$tables_jointures['spip_formulaires'][]= 'mots';
	$tables_jointures['spip_formulaires'][]= 'questions';
	$tables_jointures['spip_formulaires'][]= 'reponses';
	$tables_jointures['spip_formulaires'][]= 'rubriques';

	$tables_jointures['mots'][]= 'mots_formulaires';

	$tables_jointures['spip_questions'][]= 'blocs';
	$tables_jointures['spip_questions'][]= 'choix_question';
	$tables_jointures['spip_questions'][]= 'reponses';

	$tables_jointures['spip_reponses'][]= 'questions';
	$tables_jointures['spip_reponses'][]= 'applications';



	$choses_possibles['formulaires'] = array(
										  'titre_chose' => 'formulaires',
										  'id_chose' => 'id_formulaire',
										  'table_principale' => 'spip_formulaires',
									  	  'url_base' => 'formulaires',
										  'tables_limite' => array(
																   'formulaires' => array(
																					   'table' => 'spip_formulaires',
																					   'nom_id' => 'id_formulaire'),
																   'rubriques' => array(
																						'table' => 'spip_formulaires',
																						'nom_id' =>  'id_rubrique'),
																   'documents' => array(
																						'table' => 'spip_documents_formulaires',
																						'nom_id' =>  'id_document'),
																   'auteurs' => array(
																						'table' => 'spip_auteurs_formulaires',
																						'nom_id' =>  'id_auteur')
																   )
										  );



	$table_des_traitements['URL_FORMULAIRE'][]					= 'quote_amp(%s)';
	$table_des_traitements['URL_BLOC'][]						= 'quote_amp(%s)';
	$table_des_traitements['URL_ACTION_LOGIN_FORMULAIRE'][]		= 'quote_amp(%s)';
	$table_des_traitements['URL_ACTION_LOGOUT_FORMULAIRE'][]	= 'quote_amp(%s)';
	$table_des_traitements['URL_FORMULAIRE_OUBLI_FORMULAIRE'][]	= 'quote_amp(%s)';



	//
	// <BOUCLE(FORMULAIRES)>
	//
	function boucle_FORMULAIRES_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_formulaires";  
			if (!$GLOBALS['var_preview']) {
				if (!$boucle->statut) {
					$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
				}
			}
	        return calculer_boucle($id_boucle, $boucles); 
	}

	//
	// <BOUCLE(BLOCS)>
	//
	function boucle_BLOCS_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_blocs";  
			$boucle->default_order[] = "'ordre'" ;
	        return calculer_boucle($id_boucle, $boucles); 
	}

	//
	// <BOUCLE(QUESTIONS)>
	//
	function boucle_QUESTIONS_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_questions";  
			$boucle->default_order[] = "'ordre'" ;
	        return calculer_boucle($id_boucle, $boucles); 
	}

	//
	// <BOUCLE(CHOIX_QUESTION)>
	//
	function boucle_CHOIX_QUESTION_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_choix_question";
			$boucle->default_order[] = "'ordre'" ;
	        return calculer_boucle($id_boucle, $boucles); 
	}



	$table_des_abonnes['applicants'] = array(
										'table'				=> 'applicants',
										'url_prive'			=> 'applicants',
										'url_prive_titre'	=> _T('formulairesprive:modifier_applicant'),
										'champ_id'			=> 'id_applicant',
										'champ_email'		=> 'email',
										'champ_nom'			=> 'nom'
										);


?>