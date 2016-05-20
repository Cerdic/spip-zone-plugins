<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// On déclare ici la config du core
function compositions_ieconfig_metas($table){
	$table['compositions']['titre'] = _T('compositions:compositions');
	$table['compositions']['icone'] = 'composition-16.png';
	$table['compositions']['metas_serialize'] = 'compositions';
	
	return $table;
}

?>