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

// filtre qui retire le code source des jeux du texte original
function pas_de_balise_jeux($texte){
	return preg_replace(",<jeux>.*?</jeux>,UimsS", '', $texte);
}

// aide le Couteau Suisse a calculer la balise #INTRODUCTION
$GLOBALS['cs_introduire'][] = 'pas_de_balise_jeux';

// ajoute une identifiant dans le forme, correspondant au jeu
function ajoute_id_jeu($texte,$id_jeu){
	$texte = str_replace('</form>',"<input type='hidden' name='id_jeu' value='".$id_jeu."'/>\n</form>",$texte);
	return $texte;
;}

?>