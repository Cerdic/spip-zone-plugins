<?php

/**
 * Gestion de l'action `supprimer_evaluations_critere` 
 *
 * @plugin Evaluations pour Spip 3.0
 * @license GPL (c) 2013
 * @author Cyril Marion, Matthieu Marcillaud
 *
 * @package SPIP\Evaluations\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un critère d'évaluation
 * 
 * @param null|int $arg
 *     Identifiant à supprimer.
 *     En absence utilise l'argument de l'action sécurisée.
**/
function action_supprimer_evaluations_critere_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = intval($arg);

	// cas suppression d'une adresse et de toutes ses liaisons
	if ($arg) {
		action_supprimer_evaluations_critere_post($arg);
	}
	else {
		spip_log("action_supprimer_evaluations_critere_dist \$arg pas compris");
	}
}

/**
 * Supprime un critère d'évaluation
 *
 * @param int $id_evaluations_critere
 *     Identifiant du critère d'évaluation
**/
function action_supprimer_evaluations_critere_post($id_evaluations_critere) {
	$id_evaluations_critere = intval($id_evaluations_critere);

	sql_delete("spip_evaluations_criteres",  "id_evaluations_critere=" . sql_quote($id_evaluations_critere));
	sql_delete("spip_evaluations_critiques", "id_evaluations_critere=" . sql_quote($id_evaluations_critere));

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_evaluations_critere/$id_evaluations_critere'");
}

?>
