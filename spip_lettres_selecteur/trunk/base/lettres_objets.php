<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

	function lettres_objets_declarer_tables_auxiliaires($tables_auxiliaires) {
		$spip_lettres_liens = array(
							"id_lettre"		=> "BIGINT(21) NOT NULL",
							"id_objet"		=> "BIGINT(21) NOT NULL",
							"objet"		    => "VARCHAR(25) NOT NULL"
						);
		$spip_lettres_liens_key = array(
    		"PRIMARY KEY" => "id_lettre,id_objet,objet",
    		"INDEX id_lettre" => "id_lettre"
		);

		$tables_auxiliaires['spip_lettres_liens'] = 
			array('field' => &$spip_lettres_liens, 'key' => &$spip_lettres_liens_key);

		return $tables_auxiliaires;
	}
	
?>
