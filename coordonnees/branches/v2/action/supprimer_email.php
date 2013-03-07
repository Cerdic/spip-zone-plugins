<?php
/**
 * Plugin Coordonnées
 * Licence GPL (c) 2010 Matthieu Marcillaud
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_email_dist($arg=null) {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}
	$arg = explode('/', $arg);

	// cas suppression d'un email et de toutes ses liaisons
	if ($arg[0] == 'email' and intval($arg[1])) {
		action_supprimer_email_post($arg[1]);
	}

	// cas de suppression d'un lien donne
	// (et de l'email avec s'il n'existe plus de liaison ensuite)
	elseif ($arg[0] == 'lien' and intval($arg[1]) and intval($arg[3])) {
		action_supprimer_email_post($arg[1], $arg[2], $arg[3], $arg[4]);
	}

	else {
		spip_log("action_supprimer_email_dist $arg pas compris");
	}
}

function action_supprimer_email_post($id_email, $objet='', $id_objet='', $type='') {

	// on passe objet et id_objet en plus de id_email...
	// c'est que l'on souhaite faire attention aux liaisons
	$il_en_reste = false;

	if ($objet and $id_objet = intval($id_objet)) {
		// on supprime les liens entre l'objet et l'email...
		// s'il reste des liens... on ne supprime pas l'email :
		// c'est qu'elle est encore utilisée quelque part
		sql_delete("spip_emails_liens", array(
			"id_email=" . sql_quote($id_email),
			"objet=" . sql_quote($objet),
			"id_objet=" . sql_quote($id_objet),
			"type=" . sql_quote($type),
		));
		$il_en_reste = sql_countsel('spip_emails_liens', "id_email=" . sql_quote($id_email));
	}

	if (!$il_en_reste) {
		sql_delete("spip_emails", "id_email=" . sql_quote($id_email));
	}

	include_spip('inc/invalideur');
	suivre_invalideur("id='id_email/$id_email'");
}
?>
