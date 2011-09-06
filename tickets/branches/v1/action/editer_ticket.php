<?php

/**
 * Plugin Tickets pour Spip 2.0
 * Licence GPL (c) 2008-2009
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

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
		/*if (!$id_auteur) {
			include_spip('inc/headers');
			redirige_url_ecrire();
		}*/
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
	$err = '';

	$c = array();
	foreach (array(
		'titre', 'texte', 'severite', 'type', 'id_assigne', 'exemple', 'composant','jalon','version','projet','navigateur'
	) as $champ)
		$c[$champ] = trim(_request($champ));

	include_spip('inc/modifier');
	revision_ticket($id_ticket, $c);

	// Ajouter un document
	if (isset($_FILES['ajouter_document'])
	AND $_FILES['ajouter_document']['tmp_name']) {
		$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
		$ajouter_documents(
			$_FILES['ajouter_document']['tmp_name'],
			$_FILES['ajouter_document']['name'], 'ticket', $id_ticket,
			'document', 0, $documents_actifs);
		// supprimer le temporaire et ses meta donnees
		spip_unlink($_FILES['ajouter_document']['tmp_name']);
		spip_unlink(preg_replace(',\.bin$,',
			'.txt', $_FILES['ajouter_document']['tmp_name']));
	}
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_ticket/$id_ticket'");
	
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
	/* Si anonyme, on ne propose pas le ticket en redaction : on ouvre aussitot en lecture
	 * vu que l'autorisation de modifier de ticket dans instituer_ticket()
	 * risque d'interdire l'edition ensuite si l'autorisation de creation et de modification
	 * ne concernent pas les memes personnes.
	 * Ceci n'est pas encore ideal :
	 * si autoriser creer renvoie toujours true, et modifier false, pour un anonyme,
	 * un statut different de 'ouvert' ne sera pas pris en compte tout simplement.
	 * Mais a la creation, on met rarement "resolu" !
	 *
	 * Cependant, lorsqu'on cree un ticket anonyme,
	 * on stocke l'adresse ip ; cela peut servir pour filtrer des spam
	 */
	$ip = $id_auteur ? '' : $GLOBALS['ip'];
	$statut = intval($id_auteur) ? 'redac' : 'ouvert';
	$id_ticket = sql_insertq("spip_tickets", array(
		'statut' => $statut,
		'date' => date('Y-m-d H:i:s'),
		'date_modif' => date('Y-m-d H:i:s'),
		'ip' => $ip,
		'id_auteur' => $id_auteur));

	return $id_ticket;
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
		if (autoriser('ecrire', 'ticket', $id_ticket))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_ticket $id_ticket refus " . join(' ', $c));

		// En cas de publication, fixer la date a "maintenant"
		// sauf si $c commande autre chose
		if ($champs['statut'] == 'ouvert' AND !in_array($statut_ancien, array('ouvert'))) {
			if (!is_null($date))
				$champs['date'] = $date;
			else
				$champs['date'] = date('Y-m-d H:i:s');
			
			// On publie les documents du ticket
			$documents = sql_select('id_document','spip_documents_liens','objet="ticket" AND id_objet='.intval($id_ticket));
			while($document = sql_fetch($documents)){
				spip_log("On update le doc ".$document['id_document'],'tickets');
				$champs = array(
					'statut'=>'publie',
					'date_publication'=>date('Y-m-d H:i:s'));
				$id_document=$document['id_document'];
				sql_updateq('spip_documents',$champs,"id_document=$id_document AND statut='prepa'");
			}
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
