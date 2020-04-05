<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_aeres_verifier_zitem_charger_dist($id_zitem,$auteur){
	$valeurs = array(
		'id_zitem' => $id_zitem,
		'auteur' => $auteur,
		'texte' => '',
		'exemple' => ''
	);
	return $valeurs;
}

function formulaires_aeres_verifier_zitem_verifier_dist($id_zitem,$auteur){
	$erreurs=array();
	if (!_request('texte') || strlen(_request('texte'))<10)
		$erreurs['texte'] = 'Le texte doit faire au moins dix caractères.';
	
	//Si on joint un document
	$doc = &$_FILES['ajouter_document'];
	if (isset($_FILES['ajouter_document'])
	AND $_FILES['ajouter_document']['tmp_name']) {
		include_spip('inc/ajouter_documents');
		list($extension,$doc['name']) = fixer_extension_document($doc);
		$acceptes = explode(', ',lire_config('aeres/format_docs'));
		if (!in_array($extension, $acceptes)) {
			$erreurs['ajouter_document'] = "Ce format de document n'est pas accepté.";
			spip_unlink($_FILES['ajouter_document']['tmp_name']);
		}
	}
	return $erreurs;
}



function formulaires_aeres_verifier_zitem_traiter_dist($id_zitem,$auteur){
	if (_request('ajout')=='correction') {
		$ticket = array(
			'titre' => '[AERES] Corriger référence '.$id_zitem.(($auteur!='') ? (' pour '.$auteur) : ''),
			'statut' => 'ouvert',
			'type' => 2,
			'severite' => 3,
			'texte' => _request('texte'),
			'exemple' => _request('exemple'),
			'id_zitem' => $id_zitem,
			'date' => "NOW()"
		);
		
		include_spip('base/abstract_sql');
		if ($id_ticket = sql_insertq('spip_tickets',$ticket))
			$ret = array('message_ok'=>'Votre demande de correction sera intégrée dans la base de données prochainement.');
		else
			$ret = array('message_erreur'=>'Un problème est survenu.');
		
		// Ajouter un document
		if (isset($_FILES['ajouter_document'])
		AND $_FILES['ajouter_document']['tmp_name']) {
			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
			$ajouter_documents(
				$_FILES['ajouter_document']['tmp_name'],
				$_FILES['ajouter_document']['name'], 'ticket', $id_ticket,
				'document', 0, $documents_actifs);
			// supprimer le temporaire
			spip_unlink($_FILES['ajouter_document']['tmp_name']);
		}
	}
	if (_request('ajout')=='commentaire') {
		$commentaire = array(
			'id_ticket' => _request('id_ticket'),
			'texte' => _request('texte'),
			'date' => "NOW()"
		);
		
		include_spip('base/abstract_sql');
		if ($id_ticket_forum = sql_insertq('spip_tickets_forum',$commentaire))
			$ret = array('message_ok'=>'Votre commentaire a été ajouté.');
		else
			$ret = array('message_erreur'=>'Un problème est survenu.');
		
		// Ajouter un document
		if (isset($_FILES['ajouter_document'])
		AND $_FILES['ajouter_document']['tmp_name']) {
			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
			$ajouter_documents(
				$_FILES['ajouter_document']['tmp_name'],
				$_FILES['ajouter_document']['name'], 'ticket_forum', $id_ticket_forum,
				'document', 0, $documents_actifs);
			// supprimer le temporaire
			spip_unlink($_FILES['ajouter_document']['tmp_name']);
		}
	}
	
	return $ret;
}

?>