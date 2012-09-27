<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function ezcss_insert_head($texte) {
	$texte .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('plugins/auto/ez-css/ez-plug.css').'" media="all" />'."\n";
	return $texte;
}

?>
