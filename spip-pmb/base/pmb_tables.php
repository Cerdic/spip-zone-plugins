<?php
include_spip('base/serial');
include_spip('base/create');
include_spip('base/abstract_sql');


global $tables_principales;
global $tables_auxiliaires;
$spip_auteurs_pmb = array(
		"id_auteur_pmb"	=> "bigint(21) NOT NULL auto_increment",
		"id_auteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"pmb_session"	=> "VARCHAR(255) NOT NULL",
		"pmb_firstname"	=> "VARCHAR(255) NOT NULL",
		"pmb_lastname"	=> "VARCHAR(255) NOT NULL",
		"pmb_barcode"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_part1"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_part2"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_cp"	=> "VARCHAR(255) NOT NULL",
		"pmb_address_city"	=> "VARCHAR(255) NOT NULL",
		"pmb_phone_number1"	=> "VARCHAR(255) NOT NULL",
		"pmb_phone_number2"	=> "VARCHAR(255) NOT NULL",
		"pmb_email"	=> "VARCHAR(255) NOT NULL",
		"pmb_birthyear"	=> "VARCHAR(255) NOT NULL",
		"pmb_location_id"	=> "VARCHAR(255) NOT NULL",
		"pmb_location_caption"	=> "VARCHAR(255) NOT NULL",
		"pmb_adhesion_date"	=> "VARCHAR(255) NOT NULL",
		"pmb_expiration_date"	=> "VARCHAR(255) NOT NULL",
		"pmb_twitter"	=> "VARCHAR(255) NOT NULL");


$spip_auteurs_pmb_key = array(
		"PRIMARY KEY"	=> "id_auteur_pmb",
		"KEY id_syndic"	=> "id_auteur");

$spip_syndic_articles_pmb = array(
		"id_syndic_article"	=> "bigint(21) NOT NULL auto_increment",
		"id_syndic"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"titre"	=> "text NOT NULL",
		"url"	=> "VARCHAR(255) NOT NULL",
		"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"lesauteurs"	=> "text NOT NULL",
		"maj"	=> "TIMESTAMP",
		"statut"	=> "VARCHAR(10) NOT NULL",
		"descriptif"	=> "TEXT NOT NULL",
		"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"url_source" => "TINYTEXT DEFAULT '' NOT NULL",
		"source" => "TINYTEXT DEFAULT '' NOT NULL",
		"tags" => "TEXT DEFAULT '' NOT NULL",
		"pmb_type" => "TEXT DEFAULT '' NOT NULL",
		"pmb_photo_src" => "TEXT DEFAULT '' NOT NULL",
		"pmb_id_notice"	=> "bigint(21) NOT NULL",
		"pmb_url_base" => "VARCHAR(255) NOT NULL",
		"pmb_isbn" => "VARCHAR(30) NOT NULL",
		"pmb_auteurs" => "VARCHAR(255) NOT NULL",
		"pmb_editeur" => "VARCHAR(255) NOT NULL",
		"pmb_editeur_lieu" => "VARCHAR(255) NOT NULL",
		"pmb_format" => "VARCHAR(255) NOT NULL",
		"pmb_annee_de_publication" => "VARCHAR(255) NOT NULL",
		"pmb_importance" => "VARCHAR(255) NOT NULL",
		"pmb_presentation" => "VARCHAR(255) NOT NULL",
		"pmb_serie" => "VARCHAR(255) NOT NULL",
		"pmb_titre2" => "VARCHAR(255) NOT NULL",
		"pmb_titre3" => "VARCHAR(255) NOT NULL",
		"pmb_titre4" => "VARCHAR(255) NOT NULL");
					

$spip_syndic_articles_pmb_key = array(
		"PRIMARY KEY"	=> "id_syndic_article",
		"KEY id_syndic"	=> "id_syndic",
		"KEY statut"	=> "statut",
		"KEY url"	=> "url");


global $table_primary;
$table_primary['syndic_articles_pmb']="id_syndic_article";
$table_primary['auteurs_pmb']="id_auteur_pmb";

global $table_date;
$table_date['syndic_articles_pmb'] = 'date';


global $table_des_tables;
$table_des_tables['syndic_articles_pmb'] = 'syndic_articles_pmb';
$table_des_tables['auteurs_pmb'] = 'auteurs_pmb';

$tables_principales['spip_syndic_articles_pmb'] =
array('field' => &$spip_syndic_articles_pmb, 'key' => &$spip_syndic_articles_pmb_key);
$tables_principales['spip_auteurs_pmb'] =
array('field' => &$spip_auteurs_pmb, 'key' => &$spip_auteurs_pmb_key);

function boucle_SYNDIC_ARTICLES_PMB_dist($id_boucle, &$boucles) {
	        $boucle = &$boucles[$id_boucle];
	        $id_table = $boucle->id_table;
	        $boucle->from[$id_table] =  "spip_syndic_articles_pmb";  

			if (!$boucle->statut) {
				$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
			}
			
	        return calculer_boucle($id_boucle, $boucles); 
	}




function pmb_install($action){
  switch ($action){
	case 'test':
	  
	  break;
	case 'install':
		spip_query("CREATE TABLE IF NOT EXISTS ".$GLOBALS['table_prefix']."_syndic_articles_pmb (
			id_syndic_article bigint(21) NOT NULL auto_increment, 
			id_syndic bigint(21) DEFAULT '0' NOT NULL,
			titre	text NOT NULL,
			url	 VARCHAR(255) NOT NULL,
			date	 datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			lesauteurs	 text NOT NULL,
			maj	 TIMESTAMP,
			statut	 VARCHAR(10) NOT NULL,
			descriptif	 TEXT NOT NULL,
			lang	 VARCHAR(10) DEFAULT '' NOT NULL,
			url_source  TINYTEXT DEFAULT '' NOT NULL,
			source  TINYTEXT DEFAULT '' NOT NULL,
			tags  TEXT DEFAULT '' NOT NULL,
			pmb_type  TEXT DEFAULT '' NOT NULL,
			pmb_photo_src  TEXT DEFAULT '' NOT NULL,
			pmb_id_notice	 bigint(21) NOT NULL,
			pmb_url_base  VARCHAR(255) NOT NULL,
			pmb_isbn  VARCHAR(30) NOT NULL,
			pmb_auteurs  VARCHAR(255) NOT NULL,
			pmb_editeur  VARCHAR(255) NOT NULL,
			pmb_editeur_lieu  VARCHAR(255) NOT NULL,
			pmb_format  VARCHAR(255) NOT NULL,
			pmb_annee_de_publication  VARCHAR(255) NOT NULL,
			pmb_importance  VARCHAR(255) NOT NULL,
			pmb_presentation  VARCHAR(255) NOT NULL,
			pmb_serie  VARCHAR(255) NOT NULL,
			pmb_titre2  VARCHAR(255) NOT NULL,
			pmb_titre3  VARCHAR(255) NOT NULL,
			pmb_titre4  VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id_syndic_article),
			KEY id_syndic (id_syndic),
			KEY statut (statut),
			KEY url (url)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=513 ");

		spip_query("CREATE TABLE IF NOT EXISTS ".$GLOBALS['table_prefix']."_auteurs_pmb (
			id_auteur_pmb bigint(21) NOT NULL auto_increment, 
			id_auteur bigint(21) DEFAULT '0' NOT NULL,
			pmb_session  VARCHAR(255) NOT NULL,
			pmb_firstname  VARCHAR(255) NOT NULL,
			pmb_lastname  VARCHAR(255) NOT NULL,
			pmb_barcode  VARCHAR(255) NOT NULL,
			pmb_address_part1  VARCHAR(255) NOT NULL,
			pmb_address_part2  VARCHAR(255) NOT NULL,
			pmb_address_cp  VARCHAR(255) NOT NULL,
			pmb_address_city  VARCHAR(255) NOT NULL,
			pmb_phone_number1  VARCHAR(255) NOT NULL,
			pmb_phone_number2  VARCHAR(255) NOT NULL,
			pmb_email  VARCHAR(255) NOT NULL,
			pmb_birthyear  VARCHAR(255) NOT NULL,
			pmb_location_id  VARCHAR(255) NOT NULL,
			pmb_location_caption  VARCHAR(255) NOT NULL,
			pmb_adhesion_date  VARCHAR(255) NOT NULL,
			pmb_expiration_date  VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id_auteur_pmb),
			KEY id_syndic (id_auteur)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=513 ");

		spip_query("ALTER TABLE `".$GLOBALS['table_prefix']."_auteurs` ADD `twitter_user` VARCHAR( 255 ) NOT NULL");

		break;
	case 'uninstall':
	  
		break;
  }
}





?>