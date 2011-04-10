<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/filtres');

function formulaires_inscription_client_saisies_dist($retour=''){
	$mode = tester_config(0);
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'co__prenom',
				'label' => _T('contacts:label_prenom'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'co__nom',
				'label' => _T('contacts:label_nom'),
				'obligatoire' => 'oui'
			)
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'mail_inscription',
				'label' => _T('contacts:label_email'),
				'obligatoire' => 'oui'
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

function formulaires_inscription_client_charger_dist($retour=''){
	// On récupère le formulaire classique d'inscription
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('charger', 'formulaires/inscription');
	$contexte = $inscription_dist($mode,'');
	
	return $contexte;
}

function formulaires_inscription_client_verifier_dist($retour=''){
	// On crée un faux positif pour le nom car on le construira nous-même plus tard
	set_request('nom_inscription', 'glop');
	
	// On récupère les erreurs du formulaire d'inscription classique
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('verifier', 'formulaires/inscription');
	$erreurs = $inscription_dist($mode,'');
	
	return $erreurs;
}

function formulaires_inscription_client_traiter_dist($retour=''){
	// Le pseudo SPIP est construit
	set_request('nom_inscription', _request('co__prenom').' '._request('co__nom'));
	
	// On active le traitement du formulaire d'inscription classique, donc on crée un nouvel utilisateur
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('traiter', 'formulaires/inscription');
	$retours = $inscription_dist($mode,'');
	
	// On récupère l'auteur qu'on vient de créer avec l'email du form
	if ($id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email = '.sql_quote(_request('mail_inscription')))){
		// On ajoute des infos au contexte
		set_request('objet', 'auteur');
		set_request('id_objet', $id_auteur);
		set_request('type', 'principale');
		
		// On crée un contact pour cet utilisateur
		$definir_contact = charger_fonction('definir_contact', 'action/');
		$definir_contact("contact/$id_auteur");
		
		// On récupère tous les champs d'un contact
		$champs = sql_fetsel('*', "spip_contacts_liens LEFT JOIN spip_contacts USING(id_contact)", 'id_objet='.$id_auteur." AND objet = 'auteur'");
		$id_contact = $champs['id_contact'];
		
		// Pour chaque champ, on regarde si on l'a modifié dans ce formulaire-là
		foreach ($champs as $cle=>$null){
			if (isset($_REQUEST['co__'.$cle])) {
				$c[$cle] = _request('co__' . $cle);
			}
		}
		modifier_contenu('contact', $id_contact, array('invalideur' => "id='id_contact/$id_contact'"), $c);
		
		// On crée l'adresse
		$editer_adresse = charger_fonction('editer_adresse', 'action/');
		$editer_adresse('oui');
	}
	
	// si redirection demandee, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();
	
	return $retours;
}

?>
