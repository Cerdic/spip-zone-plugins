<?php
#------ filtres pas_de_plugin ----------------------#
#  Filtres : jeux                                   #
#  Auteurs : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->

if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!-- PLUGIN-DEBUT -->.*<!-- PLUGIN-FIN -->,UimsS", '', $texte);
 }
}

?>