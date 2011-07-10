<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// etre certain d'avoir la classe ChampExtra de connue
include_spip('inc/cextras');

function iextras_get_extras(){
	static $extras = null;
	if ($extras === null) {
		$extras = @unserialize($GLOBALS['meta']['iextras']);
		if (!is_array($extras)) $extras = array();
	}
	return $extras;
}


/* retourne l'extra ayant l'id demande */
function iextra_get_extra($extra_id){
		$extras = iextras_get_extras();
		foreach($extras as $extra) {
			if ($extra->get_id() == $extra_id) {
				return $extra;
			}
		}
		return false;
}

function iextras_set_extras($extras){
	ecrire_meta('iextras',serialize($extras));
	return $extras;
}

// tableau des extras, mais classes par table SQL
// et sous forme de tableau PHP pour pouvoir boucler dessus.
function iextras_get_extras_par_table($appliquer_typo = false){
	$extras = iextras_get_extras();
	if ($appliquer_typo) {
		$extras = _extras_typo($extras);
	}
	$tables = array();
	foreach($extras as $e) {
		if (!isset($tables[$e->table])) {
			$tables[$e->table] = array();
		}
		$tables[$e->table][] = $e->toArray();
	}

	return $tables;
}

// tableau des extras, tries par table SQL
function iextras_get_extras_tries_par_table(){
	$extras = iextras_get_extras();
	$tables = $extras_tries = array();
	foreach($extras as $e) {
		if (!isset($tables[$e->table])) {
			$tables[$e->table] = array();
		}
		$tables[$e->table][] = $e;
	}
	sort($tables);
	foreach ($tables as $table) {
		foreach ($table as $extra) {
			$extras_tries[] = $extra;
		}
	}
	return $extras_tries;
}

/**
 * Compter les saisies extras d'un objet
 *
 * @param 
 * @return 
**/
function compter_champs_extras($objet) {
	static $objets = array();
	if (isset($objets[$objet])) {
		return $objets[$objet];
	}
	
	include_spip('inc/saisies');
	if ($s = unserialize( $GLOBALS['meta'][ 'champs_extras_'.$objet ] )) {
		$s = saisies_lister_par_nom($s);
		return $objets[$objet] = count($s);
	}
	
	return $objets[$objet] = 0;
}
?>
