<?php
/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_adresse_dist($arg=NULL) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'une adresse et de toutes ses liaisons
	if ($arg[0] == 'adresse' AND intval($arg[1])) {
		action_supprimer_adresse_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de l'adresse avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'lien' AND intval($arg[1]) AND intval($arg[3])) {
		action_supprimer_adresse_post($arg[1], $arg[2], $arg[3], $arg[4]);
	}

	else {
		spip_log("action_supprimer_adresse_dist $arg pas compris");
	}
}

function action_supprimer_adresse_post($id_adresse, $objet='', $id_objet='', $type='') {

	// on passe objet et id_objet en plus de id_adresse...
	// c'est que l'on souhaite faire attention aux liaisons
	$il_en_reste = FALSE;

	if ($objet AND $id_objet = intval($id_objet)) {
		// on supprime les liens entre l'objet et l'adresse...
		sql_delete('spip_adresses_liens', array(
			"id_adresse=" . intval($id_adresse),
			"objet=" . sql_quote($objet),
			"id_objet=" . intval($id_objet),
			"type=" . sql_quote($type),
		));
		$il_en_reste = sql_countsel('spip_adresses_liens', "id_adresse=" . intval($id_adresse));
	}

	if (!$il_en_reste) {
		// s'il reste des liens... on ne supprime pas l'adresse :
		// c'est qu'elle est encore utilisée quelque part
		sql_delete('spip_adresses', "id_adresse=" . intval($id_adresse));
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_adresse/$id_adresse'");
}

?>