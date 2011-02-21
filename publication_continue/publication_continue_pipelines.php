<?php
/**
 * Plugin Publication continue pour Spip 2.1
 * Licence GPL (c) 2011
 * Cyril Marion - Ateliers CYM
 */


function publication_continue_insert_head($flux){

	// à remplacer par une css active dont les paramètres sont définis avec CFG
	$flux .= '<!-- insertion de la css publication continue--><link rel="stylesheet" type="text/css" href="'.find_in_path('css/publication_continue.css').'" media="all" />';
	
	$jsFile = generer_url_public('sripts/publication_continue.js');
	$flux .= "<!-- insertion du js publication_continue --><script src='$jsFile' type='text/javascript'></script>";
	
	return $flux;
}

?>