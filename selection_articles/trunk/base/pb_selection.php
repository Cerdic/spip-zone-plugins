<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

spip_log("hit A","___xxx");

function pb_selection_declarer_tables_interfaces($interface){
    spip_log("hit B","___xxx");
	$interface['table_des_tables']['pb_selection'] = 'pb_selection';
	return $interface;
}


/**
 * Table auxilaire spip_pb_selection
 *
 * @param array $tables_auxiliaires
 * @return array
 */
function pb_selection_declarer_tables_auxiliaires($tables_auxiliaires){
    spip_log("hit C","___xxx");
	$spip_pb_selection = array(
		"id_rubrique" 	=> "bigint(21) NOT NULL",
		"id_article" 	=> "bigint(21) NOT NULL",
		"ordre" 		=> "bigint(21) NOT NULL",
		"maj" 			=> "TIMESTAMP"
	);

	$spip_pb_selection_key = array(
		"PRIMARY KEY" 	=> "id_rubrique, id_article"
	);

	$tables_auxiliaires['spip_pb_selection'] =
		array(
			'field' => &$spip_pb_selection,
			'key' => &$spip_pb_selection_key,
		);

	return $tables_auxiliaires;
}