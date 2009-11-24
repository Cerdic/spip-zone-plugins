<?php

#   +----------------------------------+
#    Nom du Filtre : licence   
#   +----------------------------------+
#    date : 11/04/2007
#    auteur :  fanouch - lesguppies@free.fr
#    version: 0.1
#    licence: GPL
#   +-------------------------------------+
#    Fonctions de ce filtre :
#	permet de lier une licence à un article 
#   +-------------------------------------+
# Pour toute suggestion, remarque, proposition d ajout
# reportez-vous au forum de l article :
# http://www.spip-contrib.net/fr_article2147.html
#   +-------------------------------------+

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees
global $tables_principales;

// Extension de la table des articles
$spip_articles = array(
		"id_article"	=> "bigint(21) NOT NULL");

if (isset($GLOBALS['meta']['licence_base_version'])) 
	$spip_articles = array_merge($spip_articles,array("id_licence"	=> "bigint(21) NOT NULL"));

$spip_articles = array_merge($spip_articles,array(
		"surtitre"	=> "text NOT NULL",
		"titre"	=> "text NOT NULL",
		"soustitre"	=> "text NOT NULL",
		"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"descriptif"	=> "text NOT NULL",
		"chapo"	=> "mediumtext NOT NULL",
		"texte"	=> "longblob NOT NULL",
		"ps"	=> "mediumtext NOT NULL",
		"date"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"statut"	=> "varchar(10) DEFAULT '0' NOT NULL",
		"id_secteur"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"maj"	=> "TIMESTAMP",
		"export"	=> "VARCHAR(10) DEFAULT 'oui'",
		"date_redac"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"visites"	=> "INTEGER DEFAULT '0' NOT NULL",
		"referers"	=> "INTEGER DEFAULT '0' NOT NULL",
		"popularite"	=> "DOUBLE DEFAULT '0' NOT NULL",
		"accepter_forum"	=> "CHAR(3) NOT NULL",
		"date_modif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"lang"		=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
		"id_trad"	=> "bigint(21) DEFAULT '0' NOT NULL",
		"extra"		=> "longblob NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"id_version"	=> "int unsigned DEFAULT '0' NOT NULL",
		"nom_site"	=> "tinytext NOT NULL",
		"url_site"	=> "VARCHAR(255) NOT NULL",
		"url_propre" => "VARCHAR(255) NOT NULL"));
		

$spip_articles_key = array(
		"PRIMARY KEY"		=> "id_article");
		
if (isset($GLOBALS['meta']['licence_base_version']))
		$spip_articles_key = array_merge($spip_articles_key,array("KEY id_licence"	=> "id_licence"));
 
$spip_articles_key = array_merge($spip_articles_key, array(
		"KEY id_rubrique"	=> "id_rubrique",
		"KEY id_secteur"	=> "id_secteur",
		"KEY id_trad"		=> "id_trad",
		"KEY lang"			=> "lang",
		"KEY statut"		=> "statut, date",
		"KEY url_site"		=> "url_site",
		"KEY date_modif"	=> "date_modif",
		"KEY idx"			=> "idx",
		"KEY url_propre"	=> "url_propre"));

$tables_principales['spip_articles'] =
	array('field' => &$spip_articles, 'key' => &$spip_articles_key);


?>