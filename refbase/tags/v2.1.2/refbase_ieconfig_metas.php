<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function refbase_ieconfig_metas($table){
	$table['refbase']['titre'] = 'RefBase';
	$table['refbase']['icone'] = 'refbase-16.png';
	$table['refbase']['metas_serialize'] = 'refbase';
	return $table;
}

?>