<?php


	/**
	 * SPIP-Lettres : plugin de gestion de lettres d'information
	 *
	 * Copyright (c) 2006
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	 * Pour plus de details voir le fichier COPYING.txt.
	 *  
	 **/


	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;



	$table_des_tables['abonnes'] = 'abonnes';
	$table_des_tables['archives'] = 'archives';
	$table_des_tables['archives_statistiques'] = 'archives_statistiques';
	$table_des_tables['lettres'] = 'lettres';
	$table_des_tables['lettres_statistiques'] = 'lettres_statistiques';



	$spip_abonnes = array(
						"id_abonne" => "bigint(21) NOT NULL",
						"email"		=> "varchar(255) NOT NULL default ''",
						"code"		=> "varchar(255) NOT NULL default ''",
						"format"	=> "enum('html','texte','mixte') NOT NULL default 'mixte'",
						"maj"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"extra"		=> "longblob NULL"
					);
	$spip_abonnes_key = array(
						"PRIMARY KEY" 	=> "id_abonne",
						"UNIQUE email"	=> "email",
						"UNIQUE code"	=> "code"
					);

	$spip_abonnes_archives = array(
						"id_abonne"		=> "bigint(21) NOT NULL default '0'",
						"id_archive"	=> "bigint(21) NOT NULL default '0'",
						"statut"		=> "enum('a_envoyer','envoye','echec') NOT NULL default 'a_envoyer'",
						"format"		=> "enum('mixte','html','texte') NOT NULL default 'mixte'",
						"maj"			=> "datetime NOT NULL default '0000-00-00 00:00:00'"
					);
	$spip_abonnes_archives_key = array(
						"PRIMARY KEY" => "id_abonne, id_archive"
					);

	$spip_abonnes_lettres = array(
						"id_abonne"			=> "bigint(21) NOT NULL default '0'",
						"id_lettre" 		=> "bigint(21) NOT NULL default '0'",
						"date_inscription"	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"statut"			=> "enum('a_valider','valide') NOT NULL default 'a_valider'"
					);
	$spip_abonnes_lettres_key = array(
						"PRIMARY KEY" => "id_abonne, id_lettre"
					);

	$spip_archives = array(
						"id_archive"			=> "bigint(21) NOT NULL",
						"id_lettre"				=> "bigint(21) NOT NULL default '0'",
						"titre"					=> "text NOT NULL",
						"message_html"			=> "longblob NOT NULL",
						"message_texte"			=> "longblob NOT NULL",
						"date"					=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"nb_emails_envoyes"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_non_envoyes"	=> "bigint(21) NOT NULL default '0'",
						"nb_emails_echec"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_html"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_texte"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_mixte"		=> "bigint(21) NOT NULL default '0'",
						"date_debut_envoi"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"date_fin_envoi"		=> "datetime NOT NULL default '0000-00-00 00:00:00'"
					);
	$spip_archives_key = array(
						"PRIMARY KEY" => "id_archive"
					);

	$spip_archives_statistiques = array(
						"id_archive"	=> "bigint(21) NOT NULL",
						"url"			=> "varchar(255) NOT NULL default ''",
						"hits"			=> "bigint(21) NOT NULL default '0'"
					);
	$spip_archives_statistiques_key = array(
						"KEY" => "id_archive"
					);

	$spip_auteurs_lettres = array(
						"id_auteur"		=> "bigint(21) NOT NULL",
						"id_lettre"		=> "bigint(21) NOT NULL"
					);
	$spip_auteurs_lettres_key = array(
						"PRIMARY KEY" => "id_auteur, id_lettre",
						"KEY id_mot"	=> "id_auteur",
						"KEY id_lettre"	=> "id_lettre"
					);

	$spip_documents_lettres = array(
						"id_document"	=> "BIGINT (21) DEFAULT '0' NOT NULL",
						"id_lettre"		=> "BIGINT (21) DEFAULT '0' NOT NULL"
					);

	$spip_documents_lettres_key = array(
						"KEY id_document"	=> "id_document",
						"KEY id_lettre"		=> "id_lettre"
					);

	$spip_lettres = array(
						"id_lettre"		=> "bigint(21) NOT NULL",
						"titre"			=> "text NOT NULL",
						"descriptif"	=> "text NOT NULL",
						"texte"			=> "longblob NOT NULL",
						"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"lang"			=> "varchar(10) NOT NULL",
						"maj"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"statut"		=> "enum('brouillon','publie','envoi_en_cours') NOT NULL default 'brouillon'",
						"extra"			=> "longblob NULL"
					);
	$spip_lettres_key = array(
						"PRIMARY KEY" => "id_lettre"
					);

	$spip_lettres_statistiques = array(
						"id_lettre"		=> "bigint(21) NOT NULL",
						"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"type"			=> "enum('inscription','desinscription','import','suppression') NOT NULL default 'inscription'"
					);
	$spip_lettres_statistiques_key = array(
						"KEY" => "id_lettre"
					);

	$spip_mots_lettres = array(
						"id_mot"		=> "bigint(21) NOT NULL",
						"id_lettre"		=> "bigint(21) NOT NULL"
					);
	$spip_mots_lettres_key = array(
						"PRIMARY KEY" => "id_mot, id_lettre",
						"KEY id_mot"	=> "id_mot",
						"KEY id_lettre"	=> "id_lettre"
					);



	$tables_principales['spip_abonnes'] =
		array('field' => &$spip_abonnes, 'key' => &$spip_abonnes_key);
	$tables_principales['spip_archives'] =
		array('field' => &$spip_archives, 'key' => &$spip_archives_key);
	$tables_principales['spip_archives_statistiques'] =
		array('field' => &$spip_archives_statistiques, 'key' => &$spip_archives_statistiques_key);
	$tables_principales['spip_lettres'] =
		array('field' => &$spip_lettres, 'key' => &$spip_lettres_key);
	$tables_principales['spip_lettres_statistiques'] =
		array('field' => &$spip_lettres_statistiques, 'key' => &$spip_lettres_statistiques_key);



	$tables_auxiliaires['spip_abonnes_archives'] = 
		array('field' => &$spip_abonnes_archives, 'key' => &$spip_abonnes_archives_key);
	$tables_auxiliaires['spip_abonnes_lettres'] = 
		array('field' => &$spip_abonnes_lettres, 'key' => &$spip_abonnes_lettres_key);
	$tables_auxiliaires['spip_auteurs_lettres'] = 
		array('field' => &$spip_auteurs_lettres, 'key' => &$spip_auteurs_lettres_key);
	$tables_auxiliaires['spip_documents_lettres'] = 
		array('field' => &$spip_documents_lettres, 'key' => &$spip_documents_lettres_key);
	$tables_auxiliaires['spip_mots_lettres'] = 
		array('field' => &$spip_mots_lettres, 'key' => &$spip_mots_lettres_key);



	$tables_jointures['spip_abonnes'][]= 'archives';
	$tables_jointures['spip_abonnes'][]= 'abonnes_archives';
	$tables_jointures['spip_abonnes'][]= 'abonnes_lettres';
	$tables_jointures['spip_abonnes'][]= 'lettres';

	$tables_jointures['spip_archives'][]= 'abonnes';
	$tables_jointures['spip_archives'][]= 'abonnes_archives';
	$tables_jointures['spip_archives'][]= 'archives_statistiques';
	$tables_jointures['spip_archives'][]= 'lettres';

	$tables_jointures['spip_lettres'][]= 'abonnes';
	$tables_jointures['spip_lettres'][]= 'abonnes_lettres';
	$tables_jointures['spip_lettres'][]= 'archives';
	$tables_jointures['spip_lettres'][]= 'auteurs_lettres';
	$tables_jointures['spip_lettres'][]= 'auteurs';
	$tables_jointures['spip_lettres'][]= 'lettres_statistiques';
	$tables_jointures['spip_lettres'][]= 'mots_lettres';
	$tables_jointures['spip_lettres'][]= 'mots';

	$tables_jointures['spip_mots'][]= 'mots_lettres';
	$tables_jointures['spip_mots'][]= 'lettres';

	$tables_jointures['spip_auteurs'][]= 'auteurs_lettres';
	$tables_jointures['spip_auteurs'][]= 'lettres';



	//
	// <BOUCLE(LETTRES)>
	//
	function boucle_LETTRES_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_lettres";  

			if (!$GLOBALS['var_preview']) {
				if (!$boucle->statut) {
					$boucle->where[]= array("'IN'", "'$id_table.statut'", "'(\"publie\",\"envoi_en_cours\")'");
				}
			}
	        return calculer_boucle($id_boucle, $boucles); 
	}


?>