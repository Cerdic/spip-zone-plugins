<?php

// Ici on declare la structure des tables au compilo
// Inspiré de spip-lettres, Pierre Basson

	global $table_des_tables;
	global $tables_principales;
	global $tables_auxiliaires;
	global $tables_jointures;



	//$table_des_tables['abonnes'] = 'abonnes';
	$table_des_tables['courriers'] = 'courriers';
	$table_des_tables['listes'] = 'listes';

	//creer la table auteurs_elargis si besoin
	$spip_auteurs_elargis['id_auteur'] = "bigint(21) NOT NULL";
	$spip_auteurs_elargis['`spip_listes_format`'] = "VARCHAR( 8 ) DEFAULT 'non' NOT NULL";
	$spip_auteurs_elargis_key = array("PRIMARY KEY"	=> "id_auteur");
	$tables_principales['spip_auteurs_elargis']  =	array('field' => &$spip_auteurs_elargis, 'key' => &$spip_auteurs_elargis_key);
	
	
	
	$spip_auteurs_courriers = array(
						"id_auteur"		=> "bigint(21) NOT NULL default '0'",
						"id_courrier"	=> "bigint(21) NOT NULL default '0'",
						"statut"		=> "enum('a_envoyer','envoye','echec') NOT NULL default 'a_envoyer'",
						"etat"	=> "varchar(5) NOT NULL default ''",
						"maj"			=> "datetime NOT NULL default '0000-00-00 00:00:00'"
					);
					
	$spip_auteurs_courriers_key = array(
						"PRIMARY KEY" => "id_auteur, id_courrier"
					);

	$spip_auteurs_listes = array(
						"id_auteur"			=> "bigint(21) NOT NULL default '0'",
						"id_liste" 			=> "bigint(21) NOT NULL default '0'",
						"date_inscription"	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"statut"			=> "enum('a_valider','valide') NOT NULL default 'a_valider'",
						"format"			=> "enum('html','texte') NOT NULL default 'html'"

					);
	$spip_auteurs_listes_key = array(
						"PRIMARY KEY" => "id_auteur, id_liste"
					);

	$spip_courriers = array(
						"id_courrier"			=> "bigint(21) NOT NULL",
						"id_auteur"				=> "bigint(21) NOT NULL",
						"id_liste"				=> "bigint(21) NOT NULL default '0'",
						"titre"					=> "text NOT NULL",
						"texte"					=> "longblob NOT NULL",
						"message_texte"			=> "longblob NOT NULL",
						"date"					=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"statut"				=> "varchar(10) NOT NULL",
						"type" 					=> "varchar(10) NOT NULL",
						"email_test"			=> "varchar(255) NOT NULL default ''",
						"total_abonnes"			=> "bigint(21) NOT NULL default '0'",
						"nb_emails_envoyes"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_non_envoyes"	=> "bigint(21) NOT NULL default '0'",
						"nb_emails_echec"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_html"		=> "bigint(21) NOT NULL default '0'",
						"nb_emails_texte"		=> "bigint(21) NOT NULL default '0'",
						"date_debut_envoi"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"date_fin_envoi"		=> "datetime NOT NULL default '0000-00-00 00:00:00'",
						"idx"					=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_courriers_key = array(
						"PRIMARY KEY"	=> "id_courrier",
						"KEY idx"		=> "idx"
					);

	//moderateurs
	$spip_auteurs_mod_listes = array(
						"id_auteur"		=> "bigint(21) NOT NULL",
						"id_liste"		=> "bigint(21) NOT NULL"
					);
	$spip_auteurs_mod_listes_key = array(
						"PRIMARY KEY" => "id_auteur, id_liste",
					);

	$spip_listes = array(
						"id_liste"		=> "bigint(21) NOT NULL",
						"titre"			=> "text NOT NULL",
						"descriptif"	=> "text NOT NULL",
						"texte"			=> "longblob NOT NULL",
						"pied_page"		=> "longblob NOT NULL",
						"date"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"titre_message"	=> "varchar(255) NOT NULL default ''",
						"patron"		=> "varchar(255) NOT NULL default ''",
						"periode"		=> "bigint(21) NOT NULL",
						"lang"			=> "varchar(10) NOT NULL",
						"maj"			=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
						"statut"		=> "varchar(10) NOT NULL",
						"email_envoi"	=> "tinytext NOT NULL",
						"message_auto"	=> "varchar(10) NOT NULL",
						"extra"			=> "longblob NULL",
						"idx"			=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL"
					);
	$spip_listes_key = array(
						"PRIMARY KEY"	=> "id_liste",
						"KEY idx"		=> "idx"
					);

	$tables_principales['spip_courriers'] =
		array('field' => &$spip_courriers, 'key' => &$spip_courriers_key);
	$tables_principales['spip_listes'] =
		array('field' => &$spip_listes, 'key' => &$spip_listes_key);
	

	$tables_auxiliaires['spip_auteurs_courriers'] = 
		array('field' => &$spip_auteurs_courriers, 'key' => &$spip_auteurs_courriers_key);
	$tables_auxiliaires['spip_auteurs_listes'] = 
		array('field' => &$spip_auteurs_listes, 'key' => &$spip_auteurs_listes_key);
	$tables_auxiliaires['spip_auteurs_mod_listes'] = 
		array('field' => &$spip_auteurs_mod_listes, 'key' => &$spip_auteurs_mod_listes_key);
	


	//$tables_jointures['spip_abonnes'][]= 'abonnes_courriers';
	//$tables_jointures['spip_abonnes'][]= 'abonnes_listes';
	//$tables_jointures['spip_abonnes'][]= 'listes';

	$tables_jointures['spip_courriers'][]= 'auteurs';
	$tables_jointures['spip_courriers'][]= 'auteurs_courriers';
	$tables_jointures['spip_courriers'][]= 'listes';

	$tables_jointures['spip_listes'][]= 'auteurs';
	$tables_jointures['spip_listes'][]= 'auteurs_listes';
	$tables_jointures['spip_listes'][]= 'courriers';
	$tables_jointures['spip_listes'][]= 'auteurs_mod_listes';
	

	//$tables_jointures['spip_auteurs'][]= 'auteurs_mod_listes';
	$tables_jointures['spip_auteurs'][]= 'auteurs_listes';
	$tables_jointures['spip_auteurs'][]= 'listes';
	$tables_jointures['spip_auteurs'][]= 'courriers';
	

?>