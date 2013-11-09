<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_offrir_souhait_saisies_dist($id_souhait, $retour=''){
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'nom',
				'label' => _T('souhait:offrir_nom_label'),
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'taille',
				'options' => array(
					'min' => 3,
				)
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'email',
				'label' => _T('souhait:offrir_email_label'),
			),
			'verifier' => array(
				'type' => 'email',
			)
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'message',
				'label' => _T('souhait:offrir_message_label'),
				'rows' => 4,
			),
		),
	);
	
	if (include_spip('base/abstract_sql') and $souhait = sql_fetsel('titre,statut,propositions,prix', 'spip_souhaits', 'id_souhait = '.$id_souhait)){
		$propositions = unserialize($souhait['propositions']);
		if ($souhait['statut'] == 'libre'){
			$explication = _T('souhait:offrir_explication_libre', array('souhait' => $souhait['titre']));
		}
		elseif ($souhait['statut'] == 'cagnotte'){
			$deja_propose = 0;
			if (is_array($propositions)){
				$deja_propose += array_sum($propositions);
			}
			$reste = $souhait['prix'] - $deja_propose;
			$reste_devise = "$reste €";
			
			$explication = _T('souhait:offrir_explication_cagnotte', array('souhait' => $souhait['titre'], 'reste' => $reste_devise));
			
			array_unshift($saisies, array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'contribution',
					'label' => _T('souhait:offrir_contribution_label'),
					'explication' => _T('souhait:offrir_contribution_explication'),
					'obligatoire' => 'oui',
				),
				'verifier' => array(
					'type' => 'decimal',
					'options' => array(
						'min' => 0.01,
						'max' => $reste
					)
				)
			));
		}
		
		array_unshift($saisies, array(
			'saisie' => 'explication',
			'options' => array(
				'nom' => 'explication',
				'texte' => $explication
			)
		));
	}
	
	return $saisies;
}

function formulaires_offrir_souhait_charger_dist($id_souhait, $retour=''){
	static $contexte = array();
	
	if (!empty($contexte)) return $contexte;
	
	if (
		!$id_souhait = intval($id_souhait)
		or !$statut = sql_getfetsel('statut', 'spip_souhaits', 'id_souhait = '.$id_souhait)
		or in_array($statut, array('propose', 'achete', 'poubelle'))
	){
		return false;
	}
	
	$contexte['saisies_texte_submit'] = _T('icone_envoyer_message');
	
	return $contexte;
}

function formulaires_offrir_souhait_verifier_dist($id_souhait, $retour=''){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_offrir_souhait_traiter_dist($id_souhait, $retour=''){
	if ($retour){ refuser_traiter_formulaire_ajax(); }
	include_spip('action/editer_objet');
	include_spip('inc/session');
	$retours = array();
	
	$souhait = sql_fetsel('titre,statut, propositions, prix', 'spip_souhaits', 'id_souhait = '.$id_souhait);
	
	// Méga crade : on émule le fait d'être un admin (spipCnul)
	$statut = session_get('statut');
	session_set('statut', '0minirezo');
	
	// Cadeau normal
	if ($souhait['statut'] == 'libre'){
		$modifs = array(
			'statut' => 'propose',
			'propositions' => _request('nom')
		);
		$erreur = objet_modifier('souhait', $id_souhait, $modifs);
	}
	// Cagnotte
	elseif ($souhait['statut'] == 'cagnotte'){
		$propositions = unserialize($souhait['propositions']);
		// Si les propositions n'étaient pas un tableau de plusieurs contribs, on met tout à zéro, tableau vide
		if (!is_array($propositions)){ $propositions = array(); }
		// On ajoute la proposition actuelle
		$propositions[_request('nom')] = _request('contribution');
		// On ajoute le tableau modifié aux changements
		$modifs = array('propositions' => serialize($propositions));
		// On regarde à combien en est la cagnotte
		$deja_propose = array_sum($propositions);
		// Si tout est payé on change aussi le statut
		if ($deja_propose == $souhait['prix']){
			$modifs['statut'] = 'propose';
		}
		$erreur = objet_modifier('souhait', $id_souhait, $modifs);
	}
	
	// On remet l'ancien statut
	session_set('statut', $statut);
	
	// S'il n'y a pas eu d'erreur, on envoie un courriel aux auteurs du souhait pour les prévenirs
	if (!$erreur){
		include_spip('action/editer_liens');
		
		// On récupère les auteurs
		$auteurs = objet_trouver_liens(array('auteur'=>'*'), array('souhait'=>$id_souhait));
		if ($auteurs){
			include_spip('inc/envoyer_mail');
			include_spip('inc/urls');
			
			$destinataires = array();
			foreach($auteurs as $auteur){
				$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.$auteur['id_auteur']);
				$destinataires[] = $email;
			}
			$destinataires = join(',', $destinataires);
			
			if ($souhait['statut'] == 'cagnotte') {
				$sujet = _T(
					'souhait:offrir_courriel_sujet_cagnotte',
					array(
						'souhait' => $souhait['titre'],
						'contribution' => _request('contribution').' €',
						'nom' => _request('nom')
					)
				);
			}
			else{
				$sujet = _T(
					'souhait:offrir_courriel_sujet_propose',
					array(
						'souhait' => $souhait['titre'],
						'nom' => _request('nom')
					)
				);
			}
			$message = _request('message');
			
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
			$envoyer_mail(	
				$destinataires,
				$sujet,
				array(
					'texte' => $message."\n\n--\n"._T('souhait:offrir_courriel_merci')."\n".generer_url_ecrire_objet('souhait', $id_souhait, '', '', false),
					'from' => _request('email'),
					'nom_envoyeur' => _request('nom')
				)
			);
		}
	}
	
	if ($erreur){
		$retours['message_erreur'] = $erreur;
	}
	else {
		if ($souhait['statut'] == 'cagnotte'){ $retours['message_ok'] = _T('souhait:offrir_message_ok_merci_cagnotte'); }
		else{ $retours['message_ok'] = _T('souhait:offrir_message_ok_merci'); } 
	}
	
	return $retours;
}

?>
