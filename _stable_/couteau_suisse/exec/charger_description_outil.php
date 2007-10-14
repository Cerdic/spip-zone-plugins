<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

//include_spip('inc/plugin');

// Un morceau d'ajax qui affiche le descriptif d'un outil a partir
// des listes d'outils a telecharger, dans exec=admin_couteau_suisse
function exec_charger_description_outil_dist() {
cs_log("Début : exec_charger_description_outil_dist() - Préparation du retour par Ajax sur div#cs_infos");

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}
//echo 'toto=',_request('outil');
	if ($outil_id=_request('outil')) {
		include_spip('inc/cs_outils');
		echo description_outil2($outil_id);
	}

cs_log("Fin   : exec_charger_description_outil_dist()");	
}

?>
