<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function sjcycle_ieconfig_metas($table){
	$table['sjcycle']['titre'] = _T('sjcycle:titre_menu');
	$table['sjcycle']['icone'] = 'sjcycle-16.png';
	$table['sjcycle']['metas_serialize'] = 'sjcycle';
	return $table;
}