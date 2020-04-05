<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function spiplistes_declarer_tables_objets_sql($tables)
{

	$tables['spip_courriers'] = array(
		'principale' => 'oui',
		'page'=>'spiplistes_courrier',
		'field' => array(
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
				),
		'key' => array(
				"PRIMARY KEY"	=> "id_courrier",
				"KEY idx"		=> "idx"
				),
		'join' => array(
				"id_auteur"=>"id_auteur"
				),
		'date'=>array("date","date_debut_envoi","date_fin_envoi")
	);


	$tables['spip_listes'] = array(
		'principale' => 'oui',
		'table_objet'=>'listes',
		'page'=>'spiplistes_listes',
		'field' => array(
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
				),
		'key' => array(
					"PRIMARY KEY"	=> "id_liste",
					"KEY idx"		=> "idx"
				),
		'date'=>array("date","maj")
	);

if(!isset($tables['spip_auteurs_elargis'] ))
{
$tables['spip_auteurs_elargis'] = array(
		'principale' => 'non',
		'field' => array(
				"id"			=> "bigint(21) NOT NULL AUTO_INCREMENT",
				"id_auteur"		=> "bigint(21) NOT NULL"
				),
		'key' => array(
				"PRIMARY KEY"	=> "id",
				"KEY id_auteur"	=> "id_auteur"
				),
		'join' => array(
				"id_auteur"=>"id_auteur"
				)
	);
}
$tables['spip_auteurs_elargis']['field']['`spip_listes_format`']="VARCHAR( 8 ) DEFAULT 'non' NOT NULL";

return $tables;
}


function spiplistes_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['courriers'] = 'courriers';
	$interfaces['table_des_tables']['listes'] = 'listes';
	$interfaces['table_des_tables']['auteurs_listes'] = 'auteurs_listes';


	$interfaces['tables_jointures']['spip_courriers'][] = 'auteurs';
	$interfaces['tables_jointures']['spip_courriers'][] = 'auteurs_courriers';
	$interfaces['tables_jointures']['spip_courriers'][] = 'listes';


	$interfaces['tables_jointures']['spip_listes'][] = 'auteurs';
	$interfaces['tables_jointures']['spip_listes'][] = 'auteurs_listes';
	$interfaces['tables_jointures']['spip_listes'][] = 'courriers';
	$interfaces['tables_jointures']['spip_listes'][] = 'auteurs_mod_listes';


	$interfaces['tables_jointures']['spip_auteurs'][] = 'auteurs_listes';
	$interfaces['tables_jointures']['spip_auteurs'][] = 'courriers';
	$interfaces['tables_jointures']['spip_auteurs'][] = 'listes';
	$interfaces['tables_jointures']['spip_auteurs']['id_liste'] = 'auteurs_listes';



	
	return $interfaces;
}




function spiplistes_declarer_tables_auxiliaires($tables) {
	
	$tables['spip_auteurs_listes'] = array(
		'field' => array(
			"id_auteur"			=> "bigint(21) NOT NULL default '0'",
			"id_liste" 			=> "bigint(21) NOT NULL default '0'",
			"date_inscription"	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
			"statut"			=> "enum('a_valider','valide') NOT NULL default 'a_valider'",
			"format"			=> "enum('html','texte') NOT NULL default 'html'"
		),
		'key' => array(
			"PRIMARY KEY" => "id_auteur, id_liste"
		)
	);



$tables['spip_auteurs_courriers'] = array(
			
		'field' => array(
				"id_auteur"		=> "bigint(21) NOT NULL default '0'",
				"id_courrier"	=> "bigint(21) NOT NULL default '0'",
				"statut"		=> "enum('a_envoyer','envoye','echec') NOT NULL default 'a_envoyer'",
				"etat"			=> "varchar(5) NOT NULL default ''",
				"maj"			=> "datetime NOT NULL default '0000-00-00 00:00:00'"),
		'key' => array(
				"PRIMARY KEY" => "id_auteur, id_courrier")
		);

$tables['spip_auteurs_mod_listes'] = array(
			
		'field' => array(
						"id_auteur"		=> "bigint(21) NOT NULL",
						"id_liste"		=> "bigint(21) NOT NULL"),
		'key' => array(
				"PRIMARY KEY" => "id_auteur, id_liste")
		);


	
	return $tables;
}



?>