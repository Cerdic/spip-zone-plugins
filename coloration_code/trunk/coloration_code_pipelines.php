<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline header_prive (SPIP)
 * Ajout d'une feuille de style CSS dans l'espace privé pour l'affichage des codes et cadres
 */
function coloration_code_header_prive_css($flux){
	$css2=find_in_path('prive/themes/spip/coloration_code.css');
	$flux .= "\n<link rel='stylesheet' type='text/css' href='$css2' id='csscoloration_code'> \n";
	return $flux;
}
?>