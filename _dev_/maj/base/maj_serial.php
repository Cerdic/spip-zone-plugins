<?php

/*
titre = nom du paquet
url = adresse de la page d'info du paquet distant (si vide, c'est dans le fichier de conf)
date = date de la derniere mise a jour
date_verif = date de la derniere verification du paquet distant
date_reference = date du paquet distant
source = adresse du paquet distant (contient la methode de recuperation https?|svn ...autre:ftp?cvs? )
destination = repertoire ou le paquet est installe localement
id_auteur = identifiant du webmestre qui a effectue la derniere mise a jour
revision = parametre pour svn
user = parametre pour svn
methode = methode de mise a jour (spip_loader, svn, autre:ftp?cvs?)
categorie = champ libre pour preciser plugin, squelette, theme ou autre
*/

include_spip('base/serial');
global $tables_principales,$table_primary, $table_des_tables, $table_date;

$spip_paquets = array(
	"id_paquet"			=> "bigint(21) NOT NULL",
	"titre"				=> "text NOT NULL",
	"url"				=> "VARCHAR(255)",
	"date"				=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_verif"		=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"date_reference"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
	"source"			=> "TINYTEXT DEFAULT '' NOT NULL",
	"destination"		=> "TINYTEXT DEFAULT '' NOT NULL",
	"id_auteur"			=> "bigint(21) DEFAULT NULL",
	"revision"			=> "VARCHAR(255)",
	"user"				=> "VARCHAR(255)",
	"methode"			=> "VARCHAR(255)",
	"categorie"			=> "VARCHAR(255)"
);

$spip_paquets_key = array(
		"PRIMARY KEY"	=> "id_paquet",
		"KEY url"	=> "url");

$tables_principales['spip_paquets'] =
	array('field' => &$spip_paquets, 'key' => &$spip_paquets_key);

$table_primary['paquets'] = "id_paquet";

$table_date['paquets'] = 'date';

$table_des_tables['paquets'] = 'paquets';

?>