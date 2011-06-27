<?php

/**
 * Insertion dans le pipeline header_prive_css
 * Inclure les css prive de la svp (formulaire d'ajout de plugin)
 *
 * @param object $flux
 * @return $flux
 */
function svp_header_prive_css($flux){
	$css = find_in_path('prive/themes/svp_prive.css');
	$flux .= "\n<link rel='stylesheet' href='$css' type='text/css' />\n";
	return $flux;
}

?>
