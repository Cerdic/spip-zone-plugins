<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

include_spip('formulaires/editer_ticket');

function formulaires_verifier_zitem_charger_dist($id_zitem,$auteur,$retour=NULL,$retourjava=NULL){
	$valeurs = formulaires_editer_objet_charger('ticket','new',0,0,'','tickets_edit_config',array(),'');
	$valeurs['id_zitem'] = $id_zitem;
	$valeurs['auteur'] = $auteur;
	return $valeurs;
}

function formulaires_verifier_zitem_verifier_dist($id_zitem,$auteur,$retour=NULL,$retourjava=NULL){
	return formulaires_editer_ticket_verifier();
}

function formulaires_verifier_zitem_traiter_dist($id_zitem,$auteur,$retour=NULL,$retourjava=NULL){
	$message = array();
	$id_auteur = isset($GLOBALS['auteur_session']['id_auteur']) ? $GLOBALS['auteur_session']['id_auteur'] : '';
	$ip = $id_auteur ? '' : $GLOBALS['ip'];
	if ($auteur)
		$titre = _T('bibliocheck:corriger_reference2',array('id'=>$id_zitem,'auteur'=>$auteur));
	else
		$titre = _T('bibliocheck:corriger_reference',array('id'=>$id_zitem));

	$champs = array(
		'statut' =>  'ouvert',
		'date' => date('Y-m-d H:i:s'),
		'date_modif' => date('Y-m-d H:i:s'),
		'ip' => $ip,
		'id_auteur' => $id_auteur,
		'id_assigne' => 0,
		'titre' => $titre,
		'texte' => _request('texte'),
		'exemple' => _request('exemple'),
		'id_zitem' => $id_zitem,
		'auteur' => $auteur
		);

	include_spip('base/abstract_sql');
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

	if ($id_ticket) {
		$message['message_ok'] = _T('bibliocheck:correction_enregistree');

		// Ajouter un document
		if (isset($_FILES['ajouter_document'])
		AND $_FILES['ajouter_document']['tmp_name']
		AND defined('_DIR_PLUGIN_MEDIAS')) {
			$ajouter_documents = charger_fonction('ajouter_documents', 'action');
			$ajouter_documents('',
				$_FILES, 'ticket', $id_ticket,
				'document');
			// supprimer le temporaire et ses meta donnees
			spip_unlink($_FILES['ajouter_document']['tmp_name']);
			spip_unlink(preg_replace(',\.bin$,',
				'.txt', $_FILES['ajouter_document']['tmp_name']));
		}

		// Invalider les caches
		include_spip('inc/invalideur');
		suivre_invalideur("id='id_ticket/$id_ticket'");

		if (_request("java") AND strncmp($retourjava,'javascript:',11)==0)
			$message['message_ok'] .= '<script type="text/javascript">/*<![CDATA[*/'.substr($retourjava,11).'/*]]>*/</script>';
		elseif ($retour) // sinon on utilise la redirection donnee.
			$message['redirect'] = $retour;

	} else
		$message['erreur'] = _T('erreur');

	return $message;
}

