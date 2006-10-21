<?php

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_jointures;
global $table_des_tables;

if (!defined("_ECRIRE_INC_VERSION")) return;

$spip_auteurs_supp = array(
		"id_auteur"	=> "bigint(21) NOT NULL",
		"nom"	=> "text NOT NULL",
		"organisation"	=> "text NOT NULL",
		"telephone"	=> "text NOT NULL",
		"fax"	=> "text NOT NULL",
		"skype"	=> "text NOT NULL",
		"adresse"	=> "text NOT NULL",
		"codepostal"	=> "text NOT NULL",
		"ville"	=> "text NOT NULL",
		"pays"	=> "text NOT NULL",
		"latitude"	=> "text NOT NULL",
		"longitude"	=> "text NOT NULL",
		"idx"		=> "ENUM('', '1', 'non', 'oui', 'idx') DEFAULT '' NOT NULL");

$spip_auteurs_supp_key = array(
		"PRIMARY KEY"	=> "id_auteur",
		"KEY idx"	=> "idx");

$tables_principales['spip_auteurs_supp']  =
	array('field' => &$spip_auteurs_supp, 'key' => &$spip_auteurs_supp_key);
	
$tables_jointures['spip_auteurs'][] = 'spip_auteurs_supp';

$table_des_tables['spip_auteurs_supp']='auteurs_supp';
?>