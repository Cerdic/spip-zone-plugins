<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_parrainage_charger(){
	include_spip('inc/session');
	
	// S'il n'y a pas d'auteur connecté, pas de formulaire
	if (!($id_auteur = session_get('id_auteur')) > 0)
		return false;
	else{
		$contexte = array(
			'filleuls' => array(),
			'message' => '',
			'inviter_tous' => '',
			'supprimer_filleul' => '',
			'_id_parrain' => $id_auteur
		);
		return $contexte;
	}
}

function formulaires_parrainage_verifier(){
	$erreurs = array();
	
	if (!_request('supprimer_filleul') and !_request('filleuls')){
		$erreurs['filleuls'] = _T('parrainage:erreur_aucun_filleul');
	}
	
	return $erreurs;
}

function formulaires_parrainage_traiter(){
	// On revient toujours en éditable
	$retours = array('editable' => true);
	
	// Si c'est une supression d'un seul filleul
	if ($id_filleul = intval(_request('supprimer_filleul'))){
		sql_delete('spip_filleuls', 'id_filleul = '.$id_filleul);
	}
	// Sinon ce sont des envois d'invitations
	elseif ($filleuls = _request('filleuls') and is_array($filleuls)){
		$retours = array_merge($retours,traiter_inviter_filleuls($filleuls));
	}
	
	return $retours;
}

function traiter_inviter_filleuls($filleuls){
	$filleuls = array_map('intval',$filleuls);
	// On envoie pas à n'importe qui, on filtre ceux qui peuvent être invités
	$filleuls = sql_allfetsel(
		'id_filleul',
		'spip_filleuls',
		array(
			sql_in('id_filleul', $filleuls),
			sql_in('statut', array('contact', 'sans_nouvelles'))
		)
	);
	$filleuls = array_map('reset', $filleuls);

	// S'il en reste dans la liste
	if ($filleuls){
		$nombre = 0;
		$ok = true;

		// L'éventuel message perso
		$message = _request('message');

		foreach ($filleuls as $id_filleul){
			// On programme l'invitation au plus tôt
			$id_job = job_queue_add('inviter_filleul', "Inviter le filleul $id_filleul", array($id_filleul, $message), 'action/', true);
			// Si c'est bon
			if ($id_job){
				// On lie l'invitation au filleul
				job_queue_link($id_job, array('objet'=>'filleul', 'id_objet'=>$id_filleul));
				// On change le statut
				sql_updateq(
					'spip_filleuls',
					array(
						'statut' => 'en_cours'
					),
					'id_filleul = '.$id_filleul
				);
				// On incrémente le nombre d'invitation envoyée
				$nombre+=1;
			}
			// Si c'est pas bon
			else{
				$ok = false;
			}
		}

		// Si au moins un message a bien été envoyé on le dit
		if ($nombre)
			$retours['message_ok'] = $nombre > 1 ? _T('parrainage:parrainage_message_ok_pluriel', array('nombre'=>$nombre)) : _T('parrainage:parrainage_message_ok_singulier');
		// Si au moins un message a merdé on le dit
		if (!$ok)
			$retours['message_erreur'] = _T('parrainage:parrainage_message_erreur');
	}
	// Si on invite personne dans la sélection
	else{
		if (count($filleuls)>1)
			$retours['message_ok'] = _T('parrainage:parrainage_message_aucun');
		else
			$retours['message_ok'] = _T('parrainage:parrainage_message_aucun_1');
	}
	return $retours;
}
?>
