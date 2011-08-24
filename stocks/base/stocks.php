<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function stocks_declarer_tables_interfaces($interface){
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['stocks'] = 'stocks';
	
	return $interface;
}

function stocks_declarer_tables_principales($tables_principales){
	//-- Table stocks -----------------------------------------------------------
	$stocks = array(
		'id_stock' => 'bigint(21) not null',
		'id_objet' => 'bigint(21) not null default 0',
		'objet' => 'varchar(255) not null default ""',
		'quantite' => 'bigint(21) not null',
		'maj' => 'timestamp not null',
	);
	
	$stocks_cles = array(
		'PRIMARY KEY' => 'id_stock, id_objet, objet',
        'KEY id_objet' => 'id_objet, objet'
	);
	
	$tables_principales['spip_stocks'] = array(
		'field' => &$stocks,
		'key' => &$stocks_cles,
	);

	return $tables_principales;
}

?>
