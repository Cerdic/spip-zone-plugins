<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 *
 */

/**
 * editer un ticket (action apres creation/modif de ticket)
 *
 * @return array
 */
 
function action_editer_ticket() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_ticket n'est pas un nombre, c'est une creation 
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_ticket = intval($arg)) {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		if (!$id_auteur) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}
		$id_ticket = insert_ticket($id_auteur);
	} 

	// Enregistre l'envoi dans la BD
	if ($id_ticket > 0) $err = tickets_set($id_ticket);
	
	return array($id_ticket,$err);
}

/**
 * 
 * Mettre a jour une zone
 *
 * @return 
 * @param int $id_ticket
 * 
 */
function tickets_set($id_ticket) {
	spip_log('ticket_set');
	$err = '';

	$c = array();
	foreach (array(
		'titre', 'texte', 'severite', 'type', 'id_assigne', 'exemple', 'composant','jalon','version','projet'
	) as $champ)
		$c[$champ] = _request($champ);

	include_spip('inc/modifier');
	revision_ticket($id_ticket, $c);
 
	// Modification de statut. On ne peut passer par inc/modifier
	$c = array();
	foreach (array('statut') as $champ)
		$c[$champ] = _request($champ);
		$c['date'] = date('Y-m-d H:i:s');
	$err .= instituer_ticket($id_ticket, $c);

	return $err;
}

/**
 * Creer un nouveau ticket
 *
 * @return int
 */
function insert_ticket($id_auteur) {

	$id_ticket = sql_insertq("spip_tickets", array(
		'statut' => 'redac',
		'date' => date('Y-m-d H:i:s'),
		'date_modif' => date('Y-m-d H:i:s'),
		'id_auteur' => $id_auteur));

	return $id_ticket;
}

/**
 * Enregistre une revision de ticket
 * 
 * @return 
 * @param int $id_ticket
 * @param array $c[optional]
 */
function revision_ticket ($id_ticket, $c=false) {

	// Si l'article est publie, invalider les caches et demander sa reindexation
	$t = sql_getfetsel("statut", "spip_tickets", "id_ticket=$id_ticket");
	if ($t == 'publie') {
		$invalideur = "id='id_ticket/$id_ticket'";
		$indexation = true;
	}

	modifier_contenu('ticket', $id_ticket,
		array(
			'nonvide' => array('titre' => _T('info_sans_titre')),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			'date_modif' => 'date_modif' // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
		),
		$c);

	return ''; // pas d'erreur
}

/**
 * 
 * Gestion du statut d'un ticket
 * Tout changement de statut devrait passer par là
 * 
 * @return 
 * @param int $id_ticket
 * @param array $c
 */
function instituer_ticket($id_ticket, $c) {
	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$row = sql_fetsel("statut", "spip_tickets", "id_ticket=$id_ticket");
	$statut_ancien = $statut = $row['statut'];
	$champs = array();
	$date = $c['date'];
	
	$s = $c['statut'];

	// cf autorisations dans inc/instituer_article
	if ($s AND $s != $statut) {
		if (autoriser('creer', 'ticket'))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_ticket $id_ticket refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		
		if ($champs['statut'] == 'ouvert'
		OR ($champs['statut'] == 'ouvert'
			AND !in_array($statut_ancien, array('ouvert'))
		)) {
			if (!is_null($date))
				$champs['date'] = $date;
			else
				$champs['date'] = date('Y-m-d H:i:s');
		}
		// On met à jour la date_modif à chaque mise à jour de statut
		$champs['date_modif'] = date('Y-m-d H:i:s');
	}


	// Envoyer aux plugins
	$champs = pipeline('pre_edition',
		array(
			'args' => array(
				'table' => 'spip_tickets',
				'id_objet' => $id_ticket,
				'action'=>'instituer'
			),
			'data' => $champs
		)
	);

	if (!count($champs)) return;

	// Envoyer les modifs.
	sql_updateq('spip_tickets', $champs, "id_ticket=$id_ticket");

	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_ticket/$id_ticket'");

	// Pipeline
	pipeline('post_edition',
		array(
			'args' => array(
				'table' => 'spip_tickets',
				'id_objet' => $id_ticket
			),
			'data' => $champs
		)
	);

	// Notifications
	if ($notifications = charger_fonction('notifications', 'inc')) {
		$notifications('instituerticket', $id_ticket,
			array('statut' => $statut, 'statut_ancien' => $statut_ancien)
		);
	}

	return ''; // pas d'erreur
}
?>
