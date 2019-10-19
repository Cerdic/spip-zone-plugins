<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dissocier un pays d'un objet editorial
 *
 * @example
 *     ```
 *     #URL_ACTION_AUTEUR{dissocier_pays, #ID_PAYS/#OBJET/#ID_OBJET, #SELF}
 *     ```
 *
 * @param $arg string
 *     arguments séparés par un charactère non alphanumérique
 *
 *     - id_pays : identifiant de l'pays
 *     - objet : type d'objet à dissocier
 *     - id_objet : identifiant de l'objet à dissocier
 */
function action_dissocier_pays_dist($arg = null) {

	// Si $arg n'est pas donné directement, le récupérer via _POST ou _GET
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	if (
		list($id_pays, $objet, $id_objet) = preg_split('/\W/', $arg)
		and intval($id_pays) > 0 and intval($id_objet) > 0
		and autoriser('modifier', $objet, $id_objet)
	){
		include_spip('action/editer_liens');
		objet_dissocier(array('pays' => $id_pays), array($objet => $id_objet));
	}
}

