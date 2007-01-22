<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


function action_tweak_input_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

tweak_log("action_tweak_input_dist : arg = $arg");
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		 spip_log("action_tweak_input_dist $arg pas compris");
	} else action_tweak_input_post($r);

}

function action_tweak_input_post($r) {
tweak_log("action_tweak_input_post : $r[1] $r[2]");

//	lire_metas();
	global $metas_vars;
//	$metas_vars = unserialize($GLOBALS['meta']['tweaks_vars']);

	$variable = _request('variable');
	$final = corriger_caracteres(_request($variable));

tweak_log("  tweak $r[1] : $variable devient $final");
tweak_log("  tweak $r[1] : metas_vars = ".serialize($metas_vars));
	$metas_vars[$variable] = $final;
tweak_log("  tweak $r[1] : metas_vars = ".serialize($metas_vars));
	ecrire_meta('tweaks_vars', serialize($metas_vars));
	ecrire_metas();

}
?>