<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function mots_techniques_declarer_champs_extras($champs = array()){
	// ajout du champ technique
	$champs[] = new ChampExtra(array(
		'table' => 'groupes_mot', // sur quelle table ?
		'champ' => 'technique', // nom sql
		'label' => 'motstechniques:info_mots_cles_techniques', // chaine de langue 'prefix:cle'
		'precisions' => 'motstechniques:bouton_mots_cles_techniques', // chaine de langue 'prefix:cle'
		'type' => 'oui-non', // type de saisie
		'sql' => "varchar(15) NOT NULL DEFAULT ''", // declaration sql		
	));
	return $champs;
}

function mots_techniques_declarer_tables_principales($tables_principales){
	// ajout de la jointure pour {technique=...} sur boucle MOT
	$tables_principales['spip_mots']['join']["id_groupe"] = "id_groupe";
	$tables_principales['spip_mots']['join']["id_mot"] = "id_mot";
	return $tables_principales;
}

function mots_techniques_declarer_tables_interfaces($interface){
	$interface['tables_jointures']['spip_mots'][] = 'groupes_mots';		
	return $interface;
}

?>
