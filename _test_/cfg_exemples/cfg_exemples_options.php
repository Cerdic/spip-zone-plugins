<?php

function balise_CODE($p){
	$fichier = interprete_argument_balise(1,$p);
	$p->code = 'calculer_balise_CODE(' . $fichier .')';
	return $p;
}

function calculer_balise_CODE($fichier){
	if (!$f = find_in_path($fichier))
		return '';
	
	include_spip('inc/flock');

 	return propre("<cadre class='php'>\n"
 			. spip_file_get_contents($f)
 			. "</cadre>\n");	
}
?>
