<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');
function greve_active(){
	$date = date('Y-m-d H:i:s');
	$id_greve = sql_getfetsel('id_greve','spip_greves',array('`debut` <= "'.$date.'"','`fin` > "'.$date.'"'));
	return $id_greve;
}

if (greve_active()){
	define('_NO_CACHE',-1);	
}
?>