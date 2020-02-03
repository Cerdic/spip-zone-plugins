<?php
/**
 * Action permettant de vérifier si une traduction de la base
 * contient les mêmes éléments que la langue mère
 * Si le compte est différent, on ajoute les éléments en NEW dans la langue non mère
 * ou on supprime l'élément s'il n'existe pas dans la langue mère.
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_tradlang_verifier_langue_base_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match(',^(\d+)\/(\w+)$,', $arg, $r)) {
		spip_log("action_tradlang_verifier_langue_base $arg pas compris", 'tradlang');
	} else {
		$id_tradlang_module = $r[1];
		$lang = $r[2];
		$tradlang_verifier_langue_base = charger_fonction('tradlang_verifier_langue_base', 'inc');
		$tradlang_verifier_langue_base($id_tradlang_module,$lang);
	}

	return;
}
