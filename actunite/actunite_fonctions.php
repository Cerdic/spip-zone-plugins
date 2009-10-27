<?php
/**
 * Plugin Actuite pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */

function actunite_insert_head($flux){

	// à remplacer par une css active dont les paramètres sont définis avec CFG
	$flux .= '<!-- insertion de la css actunite--><link rel="stylesheet" type="text/css" href="'.find_in_path('actunite.css').'" media="all" />';
	
	$jsFile = generer_url_public('actunite.js');
	$flux .= "<!-- insertion du js actunite --><script src='$jsFile' type='text/javascript'></script>
	<!-- fin du js actunite -->";
	
	return $flux;
}

?>