<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres');

function action_description_outil_dist() {
cs_log("INIT : action_description_outil_dist() - Une modification de variable(s) a ete demandee !");
	if (defined('_SPIP19200')) {
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
cs_log(" FIN : action_description_outil_dist($arg)");
}

function action_description_outil_post($index) {
	global $metas_vars;
	if(defined('_SPIP19300')) $connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		else global $connect_id_auteur;
cs_log("Debut : action_description_outil_post($index) - On modifie la(les) variable(s) dans la base");

	// on recupere dans le POST le nom des variables a modifier et le nom de l'outil correspondant
	$variables = unserialize(urldecode(corriger_caracteres(_request('variables'))));
	$outil = corriger_caracteres(_request('outil'));
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
//cs_log($metas_vars, " -- metas_vars = ");
	ecrire_meta('tweaks_variables', serialize($metas_vars));
	ecrire_metas();

cs_log(" -- donc, reinitialisation forcee !");
	// on reinitialise tout, au cas ou ...
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_SKELS);
	purger_repertoire(_DIR_CACHE);
	include_spip('cout_utils');
	cs_initialisation(true);
cs_log(" FIN : action_description_outil_post(Array($index)) - Reinitialisation forcee terminee.");
}
?>