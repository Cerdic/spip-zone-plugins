<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_tweak_input_dist() {
tweak_log("Début : action_tweak_input_dist()");
	if ($GLOBALS['spip_version_code']>=1.92) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		include_spip('inc/actions');
		$var_f = charger_fonction('controler_action_auteur', 'inc');
		$var_f();
		$arg = _request('arg');
	}

tweak_log(" -- arg = $arg");
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		 spip_log("action_tweak_input_dist $arg pas compris");
	} else action_tweak_input_post($r);
tweak_log("Fin   : action_tweak_input_dist()");
}

function action_tweak_input_post($r) {
tweak_log("Début : action_tweak_input_dist(Array($r[1], $r[2], ...))");

	lire_metas();
	global $metas_vars;
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_variables']);

	$variable = _request('variable');
	$final = corriger_caracteres(_request($variable));

	$metas_vars[$variable] = $final;
tweak_log(" -- tweak $r[1] : $variable devient $final, donc :");
tweak_log(" -- metas_vars = ".serialize($metas_vars));
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	ecrire_metas();
tweak_log("Fin   : action_tweak_input_dist(Array($r[1], $r[2], ...)) - Réinitialisation forcée :");

	// on reinitialise tout, au cas ou ...
	tweak_initialisation_totale();
}
?>