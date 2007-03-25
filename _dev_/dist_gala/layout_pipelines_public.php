<?php

// Inserer le layout
function layout_insert_head ($texte) {
	$s = isset($GLOBALS['meta']['layout'])?$GLOBALS['meta']['layout']:'layout/dist.css';
	if (strlen($s) && $s = find_in_path("layout/$s"))
		$texte .= "<link rel='stylesheet' href='$s' type='text/css' media='all' />";
	return $texte;
}

?>