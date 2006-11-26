<?php
#------ filtres mot-croisés ----------#
# Filtres : grille de mot croisés     #
# Auteurs : Maïeul Rouquette, 2006    #
#           Patrice Vanneufville      #
# Licence : GPL                       #
# Contact : maieulrouquette@tele2.fr  #
#-------------------------------------#

// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->
// voir par ex. : backend.html, backend-breves.html

if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!-- PLUGIN-DEBUT -->.*<!-- PLUGIN-FIN -->,UimsS", '', $texte);
 }
}

// vieux filtres a supprimer...
// on les garde pour compatibilite
function grille($texte, $page=''){ return $texte; } 
function pas_de_grille($texte){
	include_spip('inc/gestion_grille');
	$texte = explode('/'._GRILLE_.'/',$texte);
	$j = 0;
	foreach ($texte as $i) if ($j++%2==1) $texte[$j]='';
	return implode($texte);
}

?>