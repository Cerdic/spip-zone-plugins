<?php

function rang_declarer_tables_principales($tables_principales){
	$tables_principales['spip_rubriques']['field']['rang'] = "BIGINT(21) NOT NULL";
	$tables_principales['spip_articles']['field']['rang'] = "BIGINT(21) NOT NULL";
	
	return $tables_principales;
}
?>
