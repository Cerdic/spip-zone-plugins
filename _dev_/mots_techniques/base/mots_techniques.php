<?php

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

$spip_mots = array(
		"id_mot"	=> "bigint(21) NOT NULL",
		"titre"	=> "text DEFAULT '' NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"texte"	=> "longtext DEFAULT '' NOT NULL",
		"id_groupe"	=> "bigint(21) NOT NULL",
		"type"	=> "text DEFAULT '' NOT NULL",
		"extra"	=> "longtext NULL",
#		"url_propre" => "VARCHAR(255) DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP");

$spip_mots_key = array(
		"PRIMARY KEY"	=> "id_mot",
#		"KEY url_propre"	=> "url_propre"
);
// ajout de la jointure pour {technique=...} sur boucle MOT
$spip_mots_join = array(
		"id_mot"=>"id_mot",
		"id_groupe"=>"id_groupe");
		
$spip_groupes_mots = array(
		"id_groupe"	=> "bigint(21) NOT NULL",
		"titre"	=> "text DEFAULT '' NOT NULL",
		"descriptif"	=> "text DEFAULT '' NOT NULL",
		"texte"	=> "longtext DEFAULT '' NOT NULL",
		"unseul"	=> "varchar(3) DEFAULT '' NOT NULL",
		"obligatoire"	=> "varchar(3) DEFAULT '' NOT NULL",
		"articles"	=> "varchar(3) DEFAULT '' NOT NULL",
		"breves"	=> "varchar(3) DEFAULT '' NOT NULL",
		"rubriques"	=> "varchar(3) DEFAULT '' NOT NULL",
		"syndic"	=> "varchar(3) DEFAULT '' NOT NULL",
		"minirezo"	=> "varchar(3) DEFAULT '' NOT NULL",
		"comite"	=> "varchar(3) DEFAULT '' NOT NULL",
		"forum"	=> "varchar(3) DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP",
		"technique"	=> "text DEFAULT '' NOT NULL",
		"affiche_formulaire"	=> "varchar(3) DEFAULT 'oui'");

$spip_groupes_mots_key = array(
		"PRIMARY KEY"	=> "id_groupe");

global $tables_principales;
$tables_principales['spip_mots']     =
	array('field' => &$spip_mots, 'key' => &$spip_mots_key ,'join' => &$spip_mots_join);
$tables_principales['spip_groupes_mots'] =
	array('field' => &$spip_groupes_mots, 'key' => &$spip_groupes_mots_key );	

// dans public/interfaces
global $tables_jointures;
$tables_jointures['spip_mots'][]= 'groupes_mots';


?>
