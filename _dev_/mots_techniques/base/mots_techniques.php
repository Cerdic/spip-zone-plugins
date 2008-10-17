<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mots_techniques_declarer_tables_principales($tables_principales){
	// ajout de la jointure pour {technique=...} sur boucle MOT
	$tables_principales['spip_mots']['join']["id_groupe"] = "id_groupe";
	$tables_principales['spip_mots']['join']["id_mot"] = "id_mot";
	// jout du champ technique
	$tables_principales['spip_groupes_mots']['field']["technique"] = "text DEFAULT '' NOT NULL";
	return $tables_principales;
}

function mots_techniques_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_mots'][] = 'groupes_mots';		
	return $interface;
}

?>
