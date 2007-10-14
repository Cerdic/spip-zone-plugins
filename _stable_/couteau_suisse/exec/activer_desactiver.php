<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;


// Un morceau d'ajax qui affiche le descriptif d'un outil a partir
// des listes d'outils a telecharger, dans exec=admin_couteau_suisse
function exec_activer_desactiver_dist() {
cs_log("Début : exec_activer_desactiver_dist() - Préparation du retour par Ajax sur div#cs_outils");

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}


cs_log("Fin   : exec_activer_desactiver_dist()");	
}

?>
