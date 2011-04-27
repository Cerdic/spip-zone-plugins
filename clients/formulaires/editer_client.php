<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_client_saisies_dist($id_auteur, $retour=''){
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prenom',
				'label' => _T('contacts:label_prenom'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'nom',
				'label' => _T('contacts:label_nom'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'email_rien',
				'label' => _T('contacts:label_email'),
				'disable' => 'oui',
			),
			'verifier' => array(
				'type' => 'email'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'voie',
				'label' => _T('coordonnees:label_voie'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'complement',
				'label' => _T('coordonnees:label_complement'),
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'code_postal',
				'label' => _T('coordonnees:label_code_postal'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'ville',
				'label' => _T('coordonnees:label_ville'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'pays',
			'options' => array(
				'nom' => 'pays',
				'code_pays' => 'oui',
				'label' => _T('coordonnees:label_pays'),
				'obligatoire' => 'oui'
			)
		),
	);
}

function formulaires_editer_client_charger_dist($id_auteur, $retour=''){
	include_spip('inc/session');
	$contexte = array();
	
	// On vérifie qu'il y a un client correct (auteur+contact+adresse) quelque part
	if (
		$id_auteur > 0
		and $email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.intval($id_auteur))
		and $contact = sql_fetsel(
			'*',
			'spip_contacts_liens LEFT JOIN spip_contacts USING(id_contact)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur)
			)
		)
	){
		$contexte['email_rien'] = $email;
		foreach ($contact as $cle=>$valeur) {
			$contexte[$cle] = $valeur;
		}
		
		// S'il y a une adresse principale, on charge les infos
		if ($adresse = sql_fetsel(
			'*',
			'spip_adresses_liens LEFT JOIN spip_adresses USING(id_adresse)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur),
				'type = '.sql_quote('principale')
			)
		))
			$contexte = array_merge($contexte, $adresse);
	}
	// Sinon rien
	else{
		$contexte['editable'] = false;
	}
	
	return $contexte;
}

function formulaires_editer_client_verifier_dist($id_auteur, $retour=''){
	$erreurs = array();
	
	return $erreurs;
}

function formulaires_editer_client_traiter_dist($id_auteur, $retour=''){
	// Si redirection demandée, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	
	$retours = array();
	
	// On modifie le contact
	$id_contact = sql_getfetsel(
		'id_contact',
		'spip_contacts_liens',
		'objet = '.sql_quote('auteur').' and id_objet = '.$id_auteur
	);
	$editer_contact = charger_fonction('editer_contact', 'action/');
	$editer_contact($id_contact);
	
	// Le pseudo SPIP est construit
	set_request('nom', _request('prenom').' '._request('nom'));
	
	// On modifie l'auteur
	$editer_auteur = charger_fonction('editer_auteur', 'action/');
	$editer_auteur($id_auteur);
	
	// On modifie l'adresse
	$id_adresse = sql_getfetsel(
		'id_adresse',
		'spip_adresses_liens',
		array(
			'objet = '.sql_quote('auteur'),
			'id_objet = '.$id_auteur,
			'type = '.sql_quote('principale')
		)
	);
	// S'il n'y a pas d'adresse principale, on la crée
	if (!$id_adresse){
		$id_adresse = 'oui';
		set_request('objet', 'auteur');
		set_request('id_objet', $id_auteur);
		set_request('type', 'principale');
	}
	
	$editer_adresse = charger_fonction('editer_adresse', 'action/');
	$editer_adresse($id_adresse);
	
	// Quand on reste sur la même page, on peut toujours éditer après
	$retours['editable'] = true;
	
	// Si on demande une redirection
	if ($retour) $retours['redirect'] = $retour;
	
	return $retours;
}

?>
