<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

// compatibilite spip 1.9
if(!function_exists(ajax_retour)) { 
	function ajax_retour($corps) {
		$c = $GLOBALS['meta']["charset"];
		header('Content-Type: text/html; charset='. $c);
		$c = '<' . "?xml version='1.0' encoding='" . $c . "'?" . ">\n";
		echo $c, $corps;
		exit;
	}
}

function exec_description_outil_dist() {
cs_log("Début : exec_description_outil_dist() - Préparation du retour par Ajax (données transmises par GET)");

	$script = _request('script');
	$outil = _request('tweak');
cs_log(" -- tweak = $outil - script = $script");
	if (!preg_match('/^\w+$/', $script)) { echo minipres(); exit;	}
	// ici on commence l'initialisation de tous les outils
	global $outils, $metas_vars, $metas_tweaks;
	include_spip('cout_utils');
	// remplir $outils (et aussi $cout_variables qu'on n'utilise pas ici);
	include_spip('config_outils');
cs_log(" -- exec_description_outil_dist() - Appel de config_outils.php : nb_outils = ".count($outils));
	// charger les metas
	$metas_tweaks = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();

cs_log(" -- appel de charger_fonction('description_outil', 'inc') et de description_outil($outil, $script) :");
	$description_outil = charger_fonction('description_outil', 'inc');
	cs_initialisation_d_un_outil($outil, $description_outil, true);
cs_log("Fin   : exec_description_outil_dist() - Appel maintenant de ajax_retour() pour afficher la ligne de configuration de l'outil");	

	include_spip('inc/texte');
	ajax_retour(propre($outils[$outil]['description']));
}
?>
