<?php

include_spip('base/serial'); // pour eviter une reinit posterieure des tables modifiees

global $tables_principales;
global $tables_auxiliaires;

$spip_liens = array(
		"id_lien"	=> "bigint(21) NOT NULL",
		"url"	=> "VARCHAR(255) NOT NULL",
		"titre"	=> "text NOT NULL",
		"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
		"maj"	=> "TIMESTAMP",
		"statut"	=> "VARCHAR(10) NOT NULL",
		"verification"	=> "VARCHAR(3) NOT NULL",
		"date_verif"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
		"obsolete"	=> "ENUM('oui', 'non') NOT NULL DEFAULT 'non'",
		"titre_auto"	=> "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
		"lang_auto"	=> "ENUM('oui', 'non') NOT NULL DEFAULT 'oui'",
		"id_objet"	=> "INT UNSIGNED NOT NULL",
		"id_table"	=> "TINYINT UNSIGNED NOT NULL"	
		);

$spip_liens_key = array(
		"PRIMARY KEY"	=> "id_lien",
		"KEY url"	=> "url",
		"KEY id_table"	=> "id_table",
		"KEY id_objet"	=> "id_objet"
		);

$tables_principales['spip_liens'] =
	array('field' => &$spip_liens, 'key' => &$spip_liens_key);

?>