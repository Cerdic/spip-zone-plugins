<?php
	function blinks_declarer_tables_interfaces($interface){
		$interface['table_des_tables']['blinks'] = 'blinks';	
		$interface['table_des_traitements']['URL_BLINK']['blinks'] = _TRAITEMENT_TYPO;
		return $interface;
	}
	function blinks_declarer_tables_principales($tables_principales){
		//-- Table BLINKS ------------------------------------------
		$blinks = array(
			"id_blink"	=> "bigint(21) NOT NULL",
			"identifiant_blink"	=> "tinytext DEFAULT '' NOT NULL",
			"url_blink"	=> "tinytext DEFAULT '' NOT NULL",
			"keywords_blink"	=> "text DEFAULT '' NOT NULL",
			"maj_blink"   => "TIMESTAMP"
		);
	
		$blinks_key = array(
			"PRIMARY KEY"	=> "id_blink",
		);
	
		$tables_principales['spip_blinks'] =
			array('field' => &$blinks, 'key' => &$blinks_key);
		return $tables_principales;
	}
?>