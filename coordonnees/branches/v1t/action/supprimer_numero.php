<?php
/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_numero_dist($arg=NULL) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'un numero et de toutes ses liaisons
	if ($arg[0] == 'numero' AND intval($arg[1])) {
		action_supprimer_numero_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de le numero avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'lien' AND intval($arg[1]) AND intval($arg[3])) {
		action_supprimer_numero_post($arg[1], $arg[2], $arg[3], $arg[4]);
	}

	else {
		spip_log("action_supprimer_numero_dist $arg pas compris");
	}
}

function action_supprimer_numero_post($id_numero, $objet='', $id_objet='', $type='') {

	// on passe objet et id_objet en plus de id_numero...
	// c'est que l'on souhaite faire attention aux liaisons
	$il_en_reste = FALSE;

	if ($objet AND $id_objet = intval($id_objet)) {
		// on supprime les liens entre l'objet et le numero...
		sql_delete('spip_numeros_liens', array(
			"id_numero=" . intval($id_numero),
			"objet=" . sql_quote($objet),
			"id_objet=" . intval($id_objet),
			"type=" . sql_quote($type),
		));
		$il_en_reste = sql_countsel('spip_numeros_liens', "id_numero=" . intval($id_numero));
	}

	if (!$il_en_reste) {
		// s'il reste des liens... on ne supprime pas le numero :
		// c'est qu'elle est encore utilisée quelque part
		sql_delete('spip_numeros', "id_numero=" . intval($id_numero));
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_numero/$id_numero'");
}

?>