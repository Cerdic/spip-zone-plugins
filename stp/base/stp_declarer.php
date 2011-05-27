<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function stp_declarer_tables_principales($tables_principales){

	// Ajout des champs necessaires a STP dans la table paquets
	$GLOBALS['stp_paquets'] = array('field' => array(), 'key' => array(), 'join' => array());
	$GLOBALS['stp_paquets']['field'] = array(
			"present"		=> "varchar(3) DEFAULT 'non' NOT NULL", // est present ? oui / non (duplique l'info id_zone un peu)
			"actif"			=> "varchar(3) DEFAULT 'non' NOT NULL", // est actif ? oui / non
			"installe"		=> "varchar(3) DEFAULT 'non' NOT NULL", // est desinstallable ? oui / non
			"recent"		=> "int(2) DEFAULT 0 NOT NULL", // a ete utilise recemment ? > 0 : oui
			
			"maj_version"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // version superieure existante (mise a jour possible)
			"superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL", // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
			"obsolete"		=> "varchar(3) DEFAULT 'non' NOT NULL", // obsolete : version plus ancienne (locale) disponible d'un plugin local existant

			"constante"		=> "VARCHAR(30) DEFAULT '' NOT NULL", // nom de la constante _DIR_(PLUGINS|EXTENSIONS|PLUGINS_SUPP)
			"dossier"		=> "VARCHAR(255) DEFAULT '' NOT NULL", // chemin du dossier depuis la constante
	);

	$tables_principales['spip_paquets']['field'] = $GLOBALS['stp_paquets']['field'];

	return $tables_principales;
}

?>
