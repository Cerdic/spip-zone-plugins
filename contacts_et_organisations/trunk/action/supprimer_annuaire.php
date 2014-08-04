<?php

/**
 * Supprimer un annuaire
 *
 * @plugin Contacts & Organisations pour Spip 3.0
 * @license GPL (c) 2009 - 2014
 * @author Cyril Marion, Matthieu Marcillaud, RastaPopoulos
 *
 * @package SPIP\Contacts\Actions
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Action pour supprimer un annuaire
 * 
 * @param null|int $arg
 *     `id` de l'annuaire. En absence de `id`, utilise l'argument de l'action sécurisée.
**/
function action_supprimer_annuaire_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$id_annuaire = intval($arg);

	if ($id_annuaire) {
		sql_delete('spip_annuaires', 'id_annuaire = '.$id_annuaire);
	}
	else {
		spip_log("action_supprimer_annuaire_dist $arg pas compris");
	}
}
