<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function orthotypo_ieconfig_metas($table){
	$table['orthotypo']['titre'] = _T('orthotypo:orthotypo_titre');
	$table['orthotypo']['icone'] = 'orthotypo-16.png';
	$table['orthotypo']['metas_serialize'] = 'orthotypo';
	
	return $table;
}