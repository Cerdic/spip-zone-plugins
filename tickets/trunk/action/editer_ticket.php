<?php

/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2012
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Editer un ticket (action apres creation/modif de ticket)
 *
 * @return array
 * 	Un array contenant l'identifiant numérique du ticket et l'erreur s'il y a lieu
 */
function action_editer_ticket() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	// si id_ticket n'est pas un nombre, c'est une creation
	// mais on verifie qu'on a toutes les donnees qu'il faut.
	if (!$id_ticket = intval($arg)) {
		$id_auteur = $GLOBALS['visiteur_session']['id_auteur'];
		$id_ticket = ticket_inserer($id_auteur);
	}

	// Enregistre l'envoi dans la BD
	if ($id_ticket > 0) $err = ticket_modifier($id_ticket,null);

	return array($id_ticket,$err);
}

/**
 * Création d'un nouveau ticket
 *
 * Lorsqu'on cree un ticket anonyme,
 * on stocke l'adresse ip ; cela peut servir pour filtrer des spam
 * 
 * @param int $id_auteur
 * 	Identifiant numérique de l'auteur qui crée le ticket
 * @return int $id_ticket
 * 	Identifiant numérique du nouveau ticlet
 */
function ticket_inserer($id_auteur=null) {
	$ip = $id_auteur ? '' : $GLOBALS['ip'];
	
	$champs = array(
		'statut' =>  'redac',
		'date' => date('Y-m-d H:i:s'),
		'date_modif' => date('Y-m-d H:i:s'),
		'ip' => $ip,
		'id_auteur' => $id_auteur,
		'id_assigne' => 0
		);
		
	// Envoyer aux plugins
	$champs = pipeline('pre_insertion',
		array(
			'args' => array(
				'table' => 'spip_tickets',
			),
			'data' => $champs
		)
	);
	
	$id_ticket = sql_insertq("spip_tickets", $champs);

	pipeline('post_insertion',
		array(
			'args' => array(
				'table' => 'spip_tickets',
				'id_objet' => $id_ticket
			),
			'data' => $champs
		)
	);
	
	return $id_ticket;
}

/**
 * Mettre a jour un ticket
 *
 * 
 * @param int $id_ticket
 * 	L'identifiant numérique du ticket à modifier
 * @param array|null $set
 * 	Un array des valeurs par défaut si appel direct de la fonction
 * @return string $err
 * 	L'erreur retournée sinon ''
 */
function ticket_modifier($id_ticket, $set=null) {
	$err = '';

	include_spip('inc/modifier');
	include_spip('inc/filtres');
	$c = collecter_requests(
		// white list
		objet_info('ticket','champs_editables'),
		// black list
		array('date','statut','id_auteur'),
		// donnees eventuellement fournies
		$set
	);

	$invalideur = "id='ticket/$id_ticket'";
	$indexation = true;
		
	if ($err = objet_modifier_champs('ticket', $id_ticket,
		array(
			'data' => $set,
			'nonvide' => array('titre' => _T('ticket:nouveau_ticket')." "._T('info_numero_abbreviation').$id_ticket),
			'invalideur' => $invalideur,
			'indexation' => $indexation,
			'date_modif' => 'date_modif' // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
		),
		$c))
		return $err;
	
	/**
	 * Ajout d'un document
	 */
	if (($files = ($_FILES ? $_FILES : $HTTP_POST_FILES))
		&& isset($_FILES['ajouter_document'])
		&& $_FILES['ajouter_document']['tmp_name']
		&& defined('_DIR_PLUGIN_MEDIAS')) {
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		
		/**
		 * On enlève le titre du post du ticket pour éviter d'avoir des conflits d'édition
		 * si une fonction de métadata essaie d'écrire le titre
		 */
		$titre_ticket = _request('titre');
		$ctr_titre_ticket = _request('ctr_titre');
		set_request('titre',false);
		set_request('ctr_titre',false);
		$id_document = $ajouter_documents($id_document,$files,'ticket',$id_ticket,'document');
		if(intval(reset($id_document)))
			$id_document = reset($id_document);
		
		/**
		 * On remet le titre
		 */
		set_request('titre',$titre_ticket);
		set_request('ctr_titre',$ctr_titre_ticket);
	}
	
	// Invalider les caches
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_ticket/$id_ticket'");
	
	// Modification de statut. On ne peut passer par inc/modifier
	$c = collecter_requests(array('date', 'statut'),array(),$set);
	$err = ticket_instituer($id_ticket, $c);
	
	return $err;
}

/**
 *
 * Gestion du statut d'un ticket
 * Tout changement de statut devrait passer par là
 *
 * @param int $id_ticket
 * @param array $c Les valeurs passées en paramètre
 * @return string Erreurs, '' si pas d'erreur
 */
function ticket_instituer($id_ticket, $c) {
	include_spip('inc/autoriser');
	include_spip('inc/modifier');

	$row = sql_fetsel("statut", "spip_tickets", "id_ticket=".intval($id_ticket));	
	$statut_ancien = $statut = $row['statut'];

	$champs = array();
	$date = $c['date'];

	$s = $c['statut'];
	
	if(!$s && $statut_ancien == 'redac')
		$s = $c['statut'] = 'ouvert';
	
	if ($s AND $s != $statut) {
		if (autoriser('instituer', 'ticket', $id_ticket,$GLOBALS['visiteur_session'],array('statut'=>$s)))
			$statut = $champs['statut'] = $s;
		else
			spip_log("editer_ticket $id_ticket refus " . join(' ', $c),'test.'._LOG_ERREUR);

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
	sql_updateq('spip_tickets', $champs, "id_ticket=".intval($id_ticket));

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

// Obsolete
function revision_ticket($id_ticket, $c=false) {
	return ticket_modifier($id_ticket,$c);
}
?>
