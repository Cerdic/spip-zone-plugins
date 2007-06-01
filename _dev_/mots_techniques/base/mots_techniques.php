<?php

$spip_groupes_mots = array(
		"id_groupe"	=> "bigint(21) NOT NULL",
		"titre"	=> "text",
		"descriptif"	=> "text",
		"texte"	=> "longtext",
		"unseul"	=> "varchar(3)",
		"obligatoire"	=> "varchar(3)",
		"articles"	=> "varchar(3)",
		"breves"	=> "varchar(3)",
		"rubriques"	=> "varchar(3)",
		"syndic"	=> "varchar(3)",
		"minirezo"	=> "varchar(3)",
		"comite"	=> "varchar(3)",
		"forum"	=> "varchar(3)",
		"maj"	=> "TIMESTAMP",
		"technique"	=> "text",
		"affiche_formulaire"	=> "varchar(3) DEFAULT 'oui'");

$spip_groupes_mots_key = array(
		"PRIMARY KEY"	=> "id_groupe");


global $tables_principales;

$tables_principales['spip_groupes_mots'] =
	array('field' => &$spip_groupes_mots, 'key' => &$spip_groupes_mots_key);

?>