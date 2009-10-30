<?php
/**
 * Plugin Infobox pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */


//
// Ajout de la feuille de style et du script javascript
//
function infobox_insert_head($flux){
	$flux .= '<!-- insertion de la css infobox --><link rel="stylesheet" type="text/css" href="'.find_in_path('infobox.css').'" media="all" />';
	
	$jsFile = generer_url_public('infobox.js');
	$flux .= "<!-- insertion du js infobox --><script src='$jsFile' type='text/javascript'></script>";
	
	return $flux;
}


?>