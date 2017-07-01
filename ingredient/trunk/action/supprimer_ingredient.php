<?php
/**
 * Action pour supprimer un ingrédient
 *
 * Vérifier l'autorisation avant d'appeler l'action.
 *
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence de id utilise l'argument de l'action sécurisée.
 **/
function action_supprimer_ingredient_dist($arg = null) {
	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression
	if ($arg) {
		sql_delete('spip_ingredients', 'id_ingredient='.sql_quote($arg));
	} else {
		spip_log("action_supprimer_ingredient_dist $arg pas compris");
	}
}
