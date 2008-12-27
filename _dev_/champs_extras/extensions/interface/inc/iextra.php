<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function iextra_get_extras(){
	$extras = @unserialize($GLOBALS['meta']['iextras']);
	if (!is_array($extras)) $extras = array();
	return $extras;
}

function iextra_set_extras($extras){
	ecrire_meta('iextras',serialize($extras));
	return $extras;
}

// tableau des extras, mais classes par table SQL
function iextra_get_extras_par_table(){
	$extras = iextra_get_extras();
	$tables = array();
	foreach($extras as $i=>$e) {
		if (!isset($tables[$e['table']])) {
			$tables[$e['table']] = array();
		}
		$tables[$e['table']][$i] = $e;
	}
	return $tables;
}
?>
