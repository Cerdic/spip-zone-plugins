<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_tweak_input_dist() {
tweak_log("Début : action_tweak_input_dist() - Une modification de variable a été demandée !");
	if ($GLOBALS['spip_version_code']>=1.92) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		include_spip('inc/actions');
		$var_f = charger_fonction('controler_action_auteur', 'inc');
		$var_f();
		$arg = _request('arg');
	}

tweak_log(" -- arg = $arg (index de la variable à changer)");
	if (!preg_match(",^\W*(\d+)$,", $arg, $r)) {
		 spip_log("action_tweak_input_dist $arg pas compris");
	} else action_tweak_input_post($r);
tweak_log("Fin   : action_tweak_input_dist()");
}

function action_tweak_input_post($r) {
tweak_log("Début : action_tweak_input_post(Array($r[1], $r[2], ...)) - On modifie la variable dans la base !");

	// on lit les metas
	lire_metas();
	global $metas_vars;
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_variables']);

	// on recupere dans le POST le nom de la variable a modifier
	$variable = _request('variable');
	// on recupere dans le POST la nouvelle valeur de la variable
	$final = corriger_caracteres(_request($variable));

	// et on modifie les metas !
	$metas_vars[$variable] = $final;
	$serialized = serialize($metas_vars);
tweak_log(" -- tweak $r[1] : $variable devient $final");
tweak_log(" -- donc, metas_vars = ".$serialized);
	ecrire_meta('tweaks_variables', $serialized);
	ecrire_metas();
	global $connect_id_auteur, $connect_login;
	spip_log("Changement de valeur sur la variable '$variable' du tweak $index par l'auteur id=$connect_id_auteur : $final");

tweak_log(" -- donc, réinitialisation forcée !");

	// on reinitialise tout, au cas ou ...
	include_spip('tweak_spip');
	tweak_initialisation_totale();
tweak_log("Fin   : action_tweak_input_post(Array($r[1], $r[2], ...)) - Réinitialisation forcée terminée.");
}
?>