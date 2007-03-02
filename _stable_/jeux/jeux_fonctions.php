<?php
#------ filtres pas_de_plugin ----------------------#
#  Plugin  : jeux                                   #
#  Auteurs : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

// filtre qui evite d'afficher le resultat obtenu par certains plugins
// grace aux espions : <!-- PLUGIN-DEBUT --> et <!-- PLUGIN-FIN -->
// ou : <!-- PLUGIN-DEBUT-#xxxx --> et <!-- PLUGIN-FIN-#xxxx --> ou xxxx est le numero d'identification du plugin.

if (!function_exists("pas_de_plugin")) {	
 function pas_de_plugin($texte){
		return preg_replace(",<!--\s*PLUGIN-DEBUT(-#[0-9]*)?.*<!--\s*PLUGIN-FIN\\1?\s*-->,UimsS", '', $texte);
 }
}

?>