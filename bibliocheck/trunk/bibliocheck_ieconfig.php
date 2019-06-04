<?php
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function bibliocheck_ieconfig_metas($table){
	$table['bibliocheck']['titre'] = _T('bibliocheck:bibliocheck');
	$table['bibliocheck']['icone'] = 'bibliocheck-16.png';
	$table['bibliocheck']['metas_serialize'] = 'bibliocheck';
	return $table;
}

