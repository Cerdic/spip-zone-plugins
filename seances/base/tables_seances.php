<?php
function seances_declarer_tables_principales($tables_principales){
	// la table seances_endroits
	$champs_seances_endroits = array(
		'id_endroit' => 'bigint(21) NOT NULL AUTO_INCREMENT',
		'id_article' => 'bigint(21) NOT NULL',
		'nom_endroit' => 'text NOT NULL',
		'descriptif_endroit' => 'text NOT NULL',
	);
	
	$cles_seances_endroits = array(
		'PRIMARY KEY' => 'id_endroit',
		'KEY id_article' => 'id_article',
	);
	
	$jointures_seances_endroits = array(
		'id_article' => 'id_article',
		'id_endroit' => 'id_endroit',
	);
	
	$tables_principales['spip_seances_endroits'] = array(
		'field' => &$champs_seances_endroits,
		'key' => &$cles_seances_endroits,
		'join' => &$jointures_seances_endroits,
	);
	
	// table seances
	$champs_seances = array(
		'id_seance' => 'bigint(21) NOT NULL AUTO_INCREMENT',
		'id_article' => 'bigint(21) NOT NULL',
		'date_seance' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
		'id_endroit' => 'bigint(21) NOT NULL',
		'remarque_seance' => 'text NOT NULL',
	);
	
	$cles_seances = array(
		'PRIMARY KEY' => 'id_seance',
		'KEY id_article' => 'id_article',
		'KEY id_endroit' => 'id_endroit',
	);
	
	$jointures_seances = array(
		'id_endroit' => 'id_endroit',
		'id_article' => 'id_article',
	);
		
	$tables_principales['spip_seances'] = array(
		'field' => &$champs_seances,
		'key' => &$cles_seances,
		'join' => &$jointures_seances,
	);
	
	$tables_principales['spip_rubriques']['field']['seance'] = 'tinyint(1) DEFAULT 0 NOT NULL';

	return $tables_principales;
} // fin declarer_tables_principales

function seances_declarer_tables_interfaces($interface){
	// les noms des tables dans les boucles
	$interface['table_des_tables']['seances'] = 'seances';
	$interface['table_des_tables']['seances_endroits'] = 'seances_endroits';

	// les jointures
	// sur seances_endroits
	$interface['tables_jointures']['spip_seances_endroits']['id_article'] = 'articles';
	$interface['tables_jointures']['spip_seances_endroits']['id_endroit'] = 'seances';
	
	// sur seances
	$interface['tables_jointures']['spip_seances']['id_seance'] = 'seances_endroits';
	$interface['tables_jointures']['spip_seances']['id_article'] = 'articles';
	
	// traitement
	$interface['table_des_traitements']['DESCRIPTIF_ENDROIT'][]= 'propre(%s)';
	$interface['table_des_traitements']['REMARQUE_SEANCE'][]= 'propre(%s)';
	
	// dates
	$interface['table_date']['seances'] = 'date_seance';
	
	return $interface;
}

?>