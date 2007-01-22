<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_tweak_input_dist() {

	global $metas_vars;

	$script = _request('script');
	$index = intval(_request('index'));
	$variable = _request('variable');
	$valeur = _request('valeur');
	$label = urldecode(_request('label'));
	$actif = _request('actif');

	$final = $metas_vars[$variable];	
tweak_log("exec_tweak_input_dist : $variable est devenu $final");
tweak_log("  metas_vars = ".serialize($metas_vars));
	
	if (!preg_match('/^\w+$/', $script))
	      {include_spip('minipres');
		echo minipres();
		exit;
	      }

	$tweak_input = charger_fonction('tweak_input', 'inc');
	ajax_retour($tweak_input($index, $variable, $final, $label, $actif, $script));
}
?>
