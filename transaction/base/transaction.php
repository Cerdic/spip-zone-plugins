<?php
/**
 * Declarer les interfaces des tables pour le compilateur de spip
 *
 * @param array $interface
 * @return array
 */
function transaction_declarer_tables_interfaces($interface){
	$interface['table_des_tables']['transaction'] = 'transaction';
	return $interface;
}

/**
 * Declaration des tables principales du plugin
 *
 * @param array $tables_principales
 * @return array
 */
function transaction_declarer_tables_principales($tables_principales){
	$spip_formulaires_transactions = array(
			"id_formulaires_reponse"	=> "bigint(21) NOT NULL",
			"ref_transaction"	=> "varchar(20) DEFAULT '' NOT NULL",
			"statut_transaction"	=> "tinyint(4) NOT NULL",
			"maj" => "timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP");
	
	$spip_formulaires_transactions_key = array(
			"PRIMARY KEY"	=> "id_formulaires_reponse");
	
	$tables_principales['spip_formulaires_transactions'] = array('field'=>&$spip_formulaires_transactions,'key'=>$spip_formulaires_transactions_key);
	return $tables_principales;
}


?>
