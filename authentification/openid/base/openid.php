<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function openid_declarer_tables_principales($tables_principales){
	// Extension de la table auteurs
	$tables_principales['spip_auteurs']['field']['openid'] = "text DEFAULT '' NOT NULL";
	$tables_principales['spip_auteurs']['key']['KEY openid'] = "openid";
		
	return $tables_principales;
}

?>
