<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// etre certain d'avoir la classe ChampExtra de connue
include_spip('inc/cextras');

function iextras_get_extras(){
	$extras = @unserialize($GLOBALS['meta']['iextras']);
	if (!is_array($extras)) $extras = array();
	return $extras;
}

function iextras_set_extras($extras){
	ecrire_meta('iextras',serialize($extras));
	return $extras;
}

// tableau des extras, mais classes par table SQL
// et sous forme de tableau PHP pour pouvoir boucler dessus.
function iextras_get_extras_par_table(){
	$extras = iextras_get_extras();
	$tables = array();
	foreach($extras as $i=>$e) {
		if (!isset($tables[$e->table])) {
			$tables[$e->table] = array();
		}
		$tables[$e->table][$i] = $e->toArray();
	}
	return $tables;
}
?>
