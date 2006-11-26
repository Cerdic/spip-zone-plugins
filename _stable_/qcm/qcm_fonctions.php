<?php 

// filtre qui evite d'afficher le résultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->
// voir aussi : backend.html, backend-breves.html

if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!-- PLUGIN-DEBUT -->.*<!-- PLUGIN-FIN -->,UimsS", '', $texte);
 }
}
/*
function pas_de_plugin($texte) {
	$texte = preg_replace("/<qcm>[\s\n\t]*\nT\s+([^\n]*)/", "[\\1]\n<qcm>", $texte);
	$texte = preg_replace(',<(qcm)>(.*)<\/\1>,UimsS', '', $texte);
	return $texte;
} 
*/
?>