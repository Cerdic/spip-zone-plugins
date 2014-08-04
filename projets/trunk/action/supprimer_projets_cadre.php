<?php

/**
 * Supprimer un cadre de projets
 *
 * @plugin Projets
 * @license GPL (c) 2009 - 2014
 * @author Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Projets\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un cadre de projets
 *
 * @param null|int $id
 *     `id` : son identifiant. En absence de `id` utilise l'argument de l'action sécurisée.
**/
function action_supprimer_projets_cadre_dist($id = null) {
	if (is_null($id)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id = $securiser_action();
	}
	$id_projets_cadre = intval($id);

	if ($id_projets_cadre) {
		sql_delete('spip_projets_cadres', 'id_projets_cadre='. $id_projets_cadre);
	} else {
		spip_log(__FUNCTION__ . " $id pas compris");
	}
}
