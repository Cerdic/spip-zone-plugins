<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function doc2img_declarer_tables_principales($tables_principales){
	$tables_principales['spip_documents']['field']['page'] = "bigint DEFAULT '0' NOT NULL";
	return $tables_principales;

}
?>