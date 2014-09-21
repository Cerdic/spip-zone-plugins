<?php

/**
 * Supprimer un projet
 *
 * @plugin Projets
 * @license GPL (c) 2009 - 2014
 * @author Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Projets\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un projet
 *
 * @param null|int $id
 *     `id` : son identifiant. En absence de `id` utilise l'argument de l'action sécurisée.
**/
function action_supprimer_projet_dist($id=null) {
	if (is_null($id)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id = $securiser_action();
	}
	$id_projet = intval($id);

	if ($id_projet) {
		sql_delete('spip_projets', 'id_projet='.$id_projet);
	}
	else {
		spip_log(__FUNCTION__ . " $id pas compris");
	}
}
