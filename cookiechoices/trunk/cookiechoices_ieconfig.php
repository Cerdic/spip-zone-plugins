<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cookiechoices_ieconfig_metas($table){
	$table['cookiechoices']['titre'] = _T('cookiechoices:cookiechoices_titre');
	$table['cookiechoices']['icone'] = 'cookiechoices-16.png';
	$table['cookiechoices']['metas_serialize'] = 'cookiechoices';
	
	return $table;
}

?>