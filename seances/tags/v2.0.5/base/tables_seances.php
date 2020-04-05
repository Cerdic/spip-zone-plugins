<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function seances_declarer_tables_objets_sql($tables){
	// la table seances_endroits
	$tables['spip_seances_endroits'] = array(
		'principale' => 'oui',
		'field' => array(
			'id_endroit' => 'bigint(21) NOT NULL AUTO_INCREMENT',
			'id_article' => 'bigint(21) NOT NULL',
			'nom_endroit' => 'text NOT NULL',
			'descriptif_endroit' => 'text NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_endroit',
			'KEY id_article' => 'id_article',
		),
		'join' => array(
			'id_article' => 'id_article',
			'id_endroit' => 'id_endroit',
		),
		'tables_jointures' => array(
			'articles',
			'seances',
		),
		'titre' => "nom_endroit AS titre, '' AS lang",
	);
		
	// table seances
	$tables['spip_seances'] = array(
		'principale' => 'oui',
		'field' => array(
			'id_seance' => 'bigint(21) NOT NULL AUTO_INCREMENT',
			'id_article' => 'bigint(21) NOT NULL',
			'date_seance' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
			'id_endroit' => 'bigint(21) NOT NULL',
			'remarque_seance' => 'text NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_seance',
			'KEY id_article' => 'id_article',
			'KEY id_endroit' => 'id_endroit',
		),
		'join' => array(
			'id_endroit' => 'id_endroit',
			'id_article' => 'id_article',
		),
		'tables_jointures' => array(
			'articles',
			'seances_endroits',
		),
		'date' => 'date_seance',
	);
	
	$tables['spip_rubriques']['field']['seance'] = 'tinyint(1) DEFAULT 0 NOT NULL';

	return $tables;
} // fin declarer_tables_principales


function seances_declarer_tables_interfaces($interface){
	// les noms des tables dans les boucles
	$interface['table_des_tables']['seances'] = 'seances';
	$interface['table_des_tables']['seances_endroits'] = 'seances_endroits';
	
	return $interface;
}

?>