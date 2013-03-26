<?php
/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_site_dist($arg=NULL) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'un site et de toutes ses liaisons
	if ($arg[0] == 'site' AND intval($arg[1])) {
		action_supprimer_site_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de l'adresse avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'lien' AND intval($arg[1]) AND intval($arg[3])) {
		action_supprimer_site_post($arg[1], $arg[2], $arg[3], $arg[4]);
	}

	else {
		spip_log("action_supprimer_site_dist $arg pas compris");
	}
}

function action_supprimer_site_post($id_site, $objet='', $id_objet='', $type='') {

	// on passe objet et id_objet en plus de id_syndic...
	// c'est que l'on souhaite faire attention aux liaisons
	$il_en_reste = FALSE;

	if ($objet AND $id_objet = intval($id_objet)) {
		// on supprime les liens entre l'objet et le site...
		sql_delete('spip_syndic_liens', array(
			"id_syndic=" . intval($id_site),
			"objet=" . sql_quote($objet),
			"id_objet=" . intval($id_objet),
			"type=" . sql_quote($type),
		));
		$il_en_reste = sql_countsel('spip_syndic_liens', "id_syndic=" . intval($id_site));
	}

	if (!$il_en_reste) {
		// s'il reste des liens... on ne supprime pas le site :
		// c'est qu'il est encore utilisée quelque part
		sql_delete('spip_syndic', "id_syndic=" . intval($id_site) . " AND statut NOT IN ('publie','prop') " );
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_syndic/$id_site'");
}

?>