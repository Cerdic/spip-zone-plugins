<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function prefixfree_insert_head($flux) {
	$flux .= '<script src="'.find_in_path('js/prefixfree.js').'" type="text/javascript"></script>';
	return $flux;
}

?>