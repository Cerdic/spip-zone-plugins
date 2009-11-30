<?php
#---------------------------------------------------#
#  Plugin  : Jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Gestion des scores : Maieul Rouquette, 2007      #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : http://www.spip-contrib.net/Des-jeux-dans-vos-articles  #
#--------------------------------------------------------------------------#


include_spip('base/jeux_tables');
include_spip('inc/jeux_autoriser');

// Declaration du pipeline "jeux_caracteristiques" qui permet de declarer au plugin des jeux tierces
$GLOBALS['spip_pipeline']['jeux_caracteristiques']=''; 

// raccourcis compatible SPIP 1.9x et 2.x -> il faudra voir ˆ le supprimer aprs la maj total vers la 2.0
function jeux_fetsel($sel, $t, $w=false, $o='date DESC', $l=1) {
	return sql_fetsel($sel, $t, $w,'', $o, $l);
}

?>