<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_tweak_input_dist() {
tweak_log("Début : action_tweak_input_dist() - Une modification de variable(s) a été demandée !");
	if ($GLOBALS['spip_version_code']>=1.92) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		include_spip('inc/actions');
		$var_f = charger_fonction('controler_action_auteur', 'inc');
		$var_f();
		$arg = _request('arg');
	}

//tweak_log(" -- arg = $arg (index du tweak appelant)");
	if (preg_match(",^\W*(\d+)$,", $arg, $r))
		action_tweak_input_post($r[1]);
	else spip_log("action_tweak_input_dist $arg pas compris");
tweak_log("Fin   : action_tweak_input_dist($arg)");
}

function action_tweak_input_post($index) {
	global $connect_id_auteur, $metas_vars;
tweak_log("Début : action_tweak_input_post($index) - On modifie la(les) variable(s) dans la base");

	// on lit les metas
	lire_metas();
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_variables']);
	// on recupere dans le POST le nom des variables a modifier et le nom du tweak correspondant
	$variables = unserialize(urldecode(corriger_caracteres(_request('variables'))));
	$tweak = corriger_caracteres(_request('tweak'));
//tweak_log($variables, '$variables = ');
tweak_log($metas_vars, 'metas_vars :');
	// on traite chaque variable
	foreach($variables as $var) {
		// on recupere dans le POST la nouvelle valeur de la variable
		$final = corriger_caracteres(_request($var));
		if (in_array($var, $metas_vars['_nombres'])) $final = intval($final);
		// et on modifie les metas !
		$metas_vars[$var] = $final;
tweak_log(" -- tweak $index ($tweak) : %$var% prend la valeur '$final'");
		spip_log("Tweak $index. Modification d'une variable par l'auteur id=$connect_id_auteur : %$var% = $final");
	}
	$serialized = serialize($metas_vars);
//tweak_log($metas_vars, " -- metas_vars = ");
	ecrire_meta('tweaks_variables', $serialized);
	ecrire_metas();

tweak_log(" -- donc, réinitialisation forcée !");
	// on reinitialise tout, au cas ou ...
	include_spip('tweak_spip');
	tweak_initialisation_totale();
tweak_log("Fin   : action_tweak_input_post(Array($index)) - Réinitialisation forcée terminée.");
}
?>