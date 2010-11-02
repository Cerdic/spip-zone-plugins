<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_header_prive($flux){
	$css = find_in_path('css/boussole_prive.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}

?>
