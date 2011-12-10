<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function stp_declarer_tables_objets_sql($tables){

	// Ajout des champs necessaires a STP dans la table paquets
	$fields = array(
			// est local ? (id_depot = 0)
			"actif"			=> "varchar(3) DEFAULT 'non' NOT NULL", // est actif ? oui / non
			"installe"		=> "varchar(3) DEFAULT 'non' NOT NULL", // est desinstallable ? oui / non
			"recent"		=> "int(2) DEFAULT 0 NOT NULL", // a ete utilise recemment ? > 0 : oui
			
			"maj_version"	=> "VARCHAR(255) DEFAULT '' NOT NULL", // version superieure existante (mise a jour possible)
			"superieur"		=> "varchar(3) DEFAULT 'non' NOT NULL", // superieur : version plus recente disponible (distant) d'un plugin (actif?) existant
			"obsolete"		=> "varchar(3) DEFAULT 'non' NOT NULL", // obsolete : version plus ancienne (locale) disponible d'un plugin local existant

			"constante"		=> "VARCHAR(30) DEFAULT '' NOT NULL", // nom de la constante _DIR_(PLUGINS|EXTENSIONS|PLUGINS_SUPP)
	);

	$tables['spip_paquets']['field'] = array_merge($tables['spip_paquets']['field'], $fields);
	
	return $tables;
}

?>
