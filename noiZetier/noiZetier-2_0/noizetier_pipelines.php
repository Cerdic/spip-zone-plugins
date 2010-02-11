<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function noizetier_header_prive($flux){
	$css = find_in_path('css/noizetier.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}

?>
