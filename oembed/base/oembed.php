<?php
	
function oembed_declarer_tables_interfaces($interface){
	
	$interface['table_des_tables']['oembed_providers']='oembed_providers';

	return $interface;
}

function oembed_declarer_tables_principales($tables_principales){
	$oembed_providers = array(
		"id_provider"	=> "bigint(21) NOT NULL",
		"scheme"		=> "text NOT NULL DEFAULT ''",
		"endpoint"		=> "text NOT NULL DEFAULT ''"
	);
	
	$oembed_providers_key = array(
		"PRIMARY KEY"	=> "id_provider"
	);
	
	$tables_principales['spip_oembed_providers'] = array(
		'field' => &$oembed_providers,
		'key' => &$oembed_providers_key
	);

	return $tables_principales;
}

?>