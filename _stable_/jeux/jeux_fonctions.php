<?php
#------ filtres pas_de_plugin ----------------------#
#  Plugin  : jeux                                   #
#  Auteurs : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->
// ou : <!-- PLUGIN-DEBUT-xx --> et <!-- PLUGIN-FIN-xx --> ou xx est un numero.

if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!--\s*PLUGIN-DEBUT(-[0-9]*)?\s*-->.*<!--\s*PLUGIN-FIN(-[0-9]*)?\s*-->,UimsS", '', $texte);
 }
}

?>