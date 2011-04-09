<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/filtres');

function formulaires_inscription_client_saisies_dist(){
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
		)
	);
}

function formulaires_inscription_client_charger_dist(){
	// On récupère le formulaire classique d'inscription
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('charger', 'formulaires/inscription');
	$contexte = $inscription_dist($mode,'');
	
	return $contexte;
}

function formulaires_inscription_client_verifier_dist(){
	// On crée un faux positif pour le nom car on le construira nous-même plus tard
	set_request('nom_inscription', 'glop');
	
	// On récupère les erreurs du formulaire d'inscription classique
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('verifier', 'formulaires/inscription');
	$erreurs = $inscription_dist($mode,'');
	
	return $erreurs;
}

function formulaires_inscription_client_traiter_dist(){
	// Le pseudo SPIP est construit
	set_request('nom_inscription', _request('co__prenom').' '._request('co__nom'));
	
	// On active le traitement du formulaire d'inscription classique, donc on crée un nouvel utilisateur
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('traiter', 'formulaires/inscription');
	$retours = $inscription_dist($mode,'');
	
	// On récupère l'auteur qu'on vient de créer avec l'email du form
	if ($id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email = '.sql_quote(_request('mail_inscription')))){
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
	}
	
	return $retours;
}

?>
