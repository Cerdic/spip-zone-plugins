<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_aeres_ajouter_zitem_charger_dist($auteur){
	$contexte = array(
		'auteur' => ($auteur == '') ? '---' : $auteur,
		'texte' => '',
		'exemple' => '',
		'id_ticket' => ''
	);
	include_spip('inc/zotspip');
	$contexte = array_merge($contexte,form_item_zotero_charger());
	return $contexte;
}

function formulaires_aeres_ajouter_zitem_verifier_dist($auteur){
	include_spip('inc/config');
	$erreurs=array();
	if (_request('ajout') == 'reference' && _request('itemType')=='') // Seulement si on n'a pas saisi de référence
		if (!_request('texte') || strlen(_request('texte'))<10)
			$erreurs['texte'] = 'Le texte doit faire au moins dix caractères.';
	
	if (_request('ajout') == 'commentaire')
		if (!_request('texte_forum') || strlen(_request('texte_forum'))<10)
			$erreurs['texte_forum'] = 'Le texte doit faire au moins dix caractères.';
	
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
	$doc = &$_FILES['ajouter_document_forum'];
	if (isset($_FILES['ajouter_document_forum'])
	AND $_FILES['ajouter_document_forum']['tmp_name']) {
		include_spip('inc/ajouter_documents');
		list($extension,$doc['name']) = fixer_extension_document($doc);
		$acceptes = explode(', ',lire_config('aeres/format_docs'));
		if (!in_array($extension, $acceptes)) {
			$erreurs['ajouter_document_forum'] = "Ce format de document n'est pas accepté.";
			spip_unlink($_FILES['ajouter_document_forum']['tmp_name']);
		}
	}
	return $erreurs;
}



function formulaires_aeres_ajouter_zitem_traiter_dist($auteur){
	if (_request('ajout') == 'reference') {
		$ticket = array(
			'titre' => '[AERES] Ajouter une référence'. (($auteur!='') ? (' pour '.$auteur) : ''),
			'statut' => 'ouvert',
			'type' => 2,
			'severite' => 3,
			'texte' => _request('texte'),
			'exemple' => _request('exemple'),
			'auteur' => (($auteur=='') ? '---' :$auteur),
			'date' => "NOW()"
		);
		
		if (_request('itemType')!='') {
			include_spip('inc/zotspip');
			$zitem_json = form_item_zotero_traiter();
			$ticket['zitem_json'] = $zitem_json;
		}
		
		include_spip('base/abstract_sql');
		if ($id_ticket = sql_insertq('spip_tickets',$ticket))
			$ret = array('message_ok'=>'Votre demande d\'ajout sera intégrée dans la base de données prochainement.');
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
			'texte' => _request('texte_forum'),
			'date' => "NOW()"
		);
		
		include_spip('base/abstract_sql');
		if ($id_ticket_forum = sql_insertq('spip_tickets_forum',$commentaire))
			$ret = array('message_ok'=>'Votre commentaire a été ajouté.');
		else
			$ret = array('message_erreur'=>'Un problème est survenu.');
		
		// Ajouter un document
		if (isset($_FILES['ajouter_document_forum'])
		AND $_FILES['ajouter_document_forum']['tmp_name']) {
			$ajouter_documents = charger_fonction('ajouter_documents', 'inc');
			$ajouter_documents(
				$_FILES['ajouter_document_forum']['tmp_name'],
				$_FILES['ajouter_document_forum']['name'], 'ticket_forum', $id_ticket_forum,
				'document', 0, $documents_actifs);
			// supprimer le temporaire
			spip_unlink($_FILES['ajouter_document_forum']['tmp_name']);
		}
	}
	
	return $ret;
}

?>