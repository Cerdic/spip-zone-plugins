<?php
/**
 *
 * Trad-lang v2
 * Plugin SPIP de traduction de fichiers de langue
 * © Florent Jugla, Fil, kent1
 *
 * Action permettant de récupérer un fichier de langue
 *
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_tradlang_bon_a_pousser_dist(){
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_tradlang_module = intval($arg);

	if (!$id_tradlang_module){
		spip_log("action_tradlang_bon_a_pousser $arg pas compris", 'tradlang.' . _LOG_ERREUR);
		return false;
	}

	sql_updateq("spip_tradlang_modules", array('bon_a_pousser' => 1), 'id_tradlang_module=' . intval($id_tradlang_module));

}
