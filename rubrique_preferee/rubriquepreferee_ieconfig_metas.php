<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function rubriquepreferee_ieconfig_metas($table){
	$table['rubriquepreferee']['titre'] = _T('rubriquepreferee:titre');
	$table['rubriquepreferee']['icone'] = 'images/rubriquepreferee-24.png';
	$table['rubriquepreferee']['metas_serialize'] = 'rubriquepreferee';
	return $table;
}

?>