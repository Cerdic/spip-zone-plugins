<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_description_outil_dist() {
cs_log("Début : action_description_outil_dist() - Une modification de variable(s) a été demandée !");
	if ($GLOBALS['spip_version_code']>=1.92) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	} else {
		include_spip('inc/actions');
		$var_f = charger_fonction('controler_action_auteur', 'inc');
		$var_f();
		$arg = _request('arg');
	}

//cs_log(" -- arg = $arg (index de l'outil appelant)");
	if (preg_match(",^\W*(\d+)$,", $arg, $r))
		action_description_outil_post($r[1]);
	else spip_log("action_description_outil_dist $arg pas compris");
cs_log("Fin   : action_description_outil_dist($arg)");
}

function action_description_outil_post($index) {
	global $connect_id_auteur, $metas_vars;
cs_log("Début : action_description_outil_post($index) - On modifie la(les) variable(s) dans la base");

	// on lit les metas
	lire_metas();
	$metas_vars = unserialize($GLOBALS['meta']['tweaks_variables']);
	// on recupere dans le POST le nom des variables a modifier et le nom de l'outil correspondant
	$variables = unserialize(urldecode(corriger_caracteres(_request('variables'))));
	$outil = corriger_caracteres(_request('tweak'));
//cs_log($variables, '$variables = ');
cs_log($metas_vars, 'metas_vars :');
	// on traite chaque variable
	foreach($variables as $var) {
		// on recupere dans le POST la nouvelle valeur de la variable
		$final = corriger_caracteres(_request($var));
		if (in_array($var, $metas_vars['_nombres'])) $final = intval($final);
		// et on modifie les metas !
		$metas_vars[$var] = $final;
cs_log(" -- outil $index ($outil) : %$var% prend la valeur '$final'");
		spip_log("Outil du Couteau Suisse n°$index. Modification d'une variable par l'auteur id=$connect_id_auteur : %$var% = $final");
	}
	$serialized = serialize($metas_vars);
//cs_log($metas_vars, " -- metas_vars = ");
	ecrire_meta('tweaks_variables', $serialized);
	ecrire_metas();

cs_log(" -- donc, réinitialisation forcée !");
	// on reinitialise tout, au cas ou ...
	include_spip('cout_utils');
	cs_initialisation_totale();
cs_log("Fin   : action_description_outil_post(Array($index)) - Réinitialisation forcée terminée.");
}
?>