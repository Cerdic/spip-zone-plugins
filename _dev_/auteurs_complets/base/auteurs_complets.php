<?php

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

if (!defined("_ECRIRE_INC_VERSION")) return;

// Extension de la table des auteurs
$spip_auteurs = array(
		"id_auteur"	=> "bigint(21) NOT NULL",
		"nom"	=> "text NOT NULL",
		"bio"	=> "text NOT NULL",
		"email"	=> "tinytext NOT NULL",
		"nom_famille" => "text NOT NULL",
		"prenom" => "text NOT NULL",
		"organisation"	=> "text NOT NULL",
		"url_organisation" => "text NOT NULL",
		"telephone"	=> "text NOT NULL",
		"fax"	=> "text NOT NULL",
		"skype"	=> "text NOT NULL",
		"adresse"	=> "text NOT NULL",
		"codepostal"	=> "text NOT NULL",
		"ville"	=> "text NOT NULL",
		"pays"	=> "text NOT NULL",
		"latitude"	=> "text NOT NULL",
		"longitude"	=> "text NOT NULL",
		"nom_site"	=> "tinytext NOT NULL",
		"url_site"	=> "text NOT NULL",
		"login"	=> "VARCHAR(255) BINARY NOT NULL",
		"pass"	=> "tinytext NOT NULL",
		"low_sec"	=> "tinytext NOT NULL",
		"statut"	=> "VARCHAR(255) NOT NULL",
		"maj"	=> "TIMESTAMP",
		"pgp"	=> "BLOB NOT NULL",
		"htpass"	=> "tinyblob NOT NULL",
		"en_ligne"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"imessage"	=> "VARCHAR(3) NOT NULL",
		"messagerie"	=> "VARCHAR(3) NOT NULL",
		"alea_actuel"	=> "tinytext NOT NULL",
		"alea_futur"	=> "tinytext NOT NULL",
		"prefs"	=> "tinytext NOT NULL",
		"cookie_oubli"	=> "tinytext NOT NULL",
		"source"	=> "VARCHAR(10) DEFAULT 'spip' NOT NULL",
		"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL",
		"url_propre" => "VARCHAR(255) NOT NULL",
		"extra"	=> "longblob NULL");

$spip_auteurs_key = array(
		"PRIMARY KEY"	=> "id_auteur",
		"KEY login"	=> "login",
		"KEY statut"	=> "statut",
		"KEY lang"	=> "lang",
		"KEY idx"	=> "idx",
		"KEY en_ligne"	=> "en_ligne",
		"KEY url_propre"	=> "url_propre");

$tables_principales['spip_auteurs']  =
	array('field' => &$spip_auteurs, 'key' => &$spip_auteurs_key);

global  $table_des_traitements;

$table_des_traitements['ADRESSE'][] = 'propre(%s)';
$table_des_traitements['VILLE'][] = 'propre(%s)';
$table_des_traitements['PAYS'][] = 'propre(%s)';
$table_des_traitements['ORGANISATION'][] = 'propre(%s)';
$table_des_traitements['CODEPOSTAL'][] = 'propre(%s)';

?>