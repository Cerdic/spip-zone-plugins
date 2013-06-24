<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');
include_spip('inc/filtres');

function formulaires_inscription_client_saisies_dist($retour=''){
	$mode = tester_config(0);

	$conf=lire_config('clients/elm',array());

	$civilite=array();	
	$type_c = lire_config('clients/type_civ','i');

	if ($type_c == 'c'){
		$civ=lire_config('clients/elm_civ',array('madame', 'monsieur'));
		$civ_t=array();
		if (in_array("civilite", $conf)) {		
			foreach($civ as $v){
				array_push($civ_t, "<:clients:label_$v:>");
			}
			$civ_t = array_combine($civ, $civ_t);
			$civilite=array(
				'saisie' => 'radio',
				'options' => array(
					'nom' => 'civilite',
					'label' => _T('contacts:label_civilite'),
					'obligatoire' => in_array("obli_civilite", $conf) ? 'oui' : '',
					'datas' => $civ_t
				)
			);
		}
	} else {
		if (in_array("civilite", $conf)) {
			$civilite=array(
				'saisie' => 'input',
				'options' => array(
					'nom' => 'civilite',
					'label' => _T('contacts:label_civilite'),
					'obligatoire' => in_array("obli_civilite", $conf) ? 'oui' : '',
				)
			);
		}
	}
	
	$numero=array();
	if (in_array("numero", $conf)) {
		$numero=array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'numero',
				'label' => _T('clients:label_tel'),
				'obligatoire' => in_array("obli_numero", $conf) ? 'oui' : '',
			)
		);
	}
	
	$portable=array();
	if (in_array("portable", $conf)) {
		$portable=array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'portable',
				'label' => _T('clients:label_portable'),
				'obligatoire' => in_array("obli_portable", $conf) ? 'oui' : '',
			)
		);
	}

	$fax=array();
	if (in_array("fax", $conf)) {
		$fax=array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'fax',
				'label' => _T('clients:label_fax'),
				'obligatoire' => in_array("obli_fax", $conf) ? 'oui' : '',
			)
		);
	}

	$complement=array();
	if (in_array("complement", $conf)) {
		$complement=array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'complement',
				'label' => _T('coordonnees:label_complement'),
				'obligatoire' => in_array("obli_complement", $conf) ? 'oui' : '',
			)
		);
	}
	
	$pays=array();
	if (in_array("pays", $conf)) {
		$pays=array(
			'saisie' => 'pays',
			'options' => array(
				'nom' => 'pays',				
				'code_pays' => 'oui',
				'label' => _T('coordonnees:label_pays'),
				'obligatoire' => in_array("obli_pays", $conf) ? 'oui' : '',
			)
		);
	}
	
	return array(
		$civilite,
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
				'nom' => 'mail_inscription',
				'label' => _T('contacts:label_email'),
				'obligatoire' => 'oui'
			),
			'verifier' => array(
				'type' => 'email'
			)
		),
		$numero,
		$portable,
		$fax,
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'voie',
				'label' => _T('coordonnees:label_voie'),
				'obligatoire' => 'oui'
			)
		),
		$complement,
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
		$pays
	);
}

function formulaires_inscription_client_charger_dist($retour=''){
	$erreurs = array();
	// On récupère le formulaire classique d'inscription
	$mode = tester_config(0);
	$inscription_dist = charger_fonction('charger', 'formulaires/inscription');
	$contexte = $inscription_dist($mode,'');

	if(isset($contexte['editable']) && $contexte['editable']!=true){
		$contexte['message_erreur'] = _T('clients:erreur_inscription_visiteur');
	}

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
	// Si redirection demandée, on refuse le traitement en ajax
	if ($retour) refuser_traiter_formulaire_ajax();

	// Le pseudo SPIP est construit
	set_request('nom_inscription', trim(_request('prenom').' '._request('nom')));

	// On active le traitement du formulaire d'inscription classique, donc on crée un nouvel utilisateur
	if (!($id_auteur = verifier_session())) {
		$mode = tester_config(0);
		$inscription_dist = charger_fonction('traiter', 'formulaires/inscription');
		$retours = $inscription_dist($mode,'');

		$id_auteur = sql_getfetsel('id_auteur', 'spip_auteurs', 'email = '.sql_quote(_request('mail_inscription')));
	}

	// On récupère l'auteur qu'on vient de créer avec l'email du form
	if ($id_auteur){
		// On ajoute des infos au contexte
		set_request('objet', 'auteur');
		set_request('id_objet', $id_auteur);
		set_request('type', 'principale');

		// On crée un contact pour cet utilisateur
		$editer_contact = charger_fonction('editer_contact', 'action/');
		list($id_contact, $err) = $editer_contact('nouveau');
		//On lie le contact à l'auteur
		sql_insertq('spip_contacts_liens',array('id_objet' => $id_auteur,'objet' => 'auteur',"id_contact"=>$id_contact));
		//assurer la compatibilite
		sql_updateq('spip_contacts',array('id_auteur' => $id_auteur),"id_contact=".intval($id_contact));


		// On crée l'adresse
		$editer_adresse = charger_fonction('editer_adresse', 'action/');
		$editer_adresse('oui');

		// On crée le numero de tel
		if (_request('numero')) {
			set_request('type', 'principal');
			$editer_numero = charger_fonction('editer_numero', 'action/');
			$editer_numero('oui');
		}

		// On crée le portable
		if (_request('portable')){
			// on stocke cette donnee
			$numero = _request('numero');
			set_request('numero', _request('portable'));
			set_request('type', 'portable');
			set_request('titre', 'Portable');
			
			$editer_portable = charger_fonction('editer_numero', 'action/');
			$editer_portable('oui');
		}

		// On crée le fax
		if (_request('fax')){
			// on stocke cette donnee si elle ne l'est pas deja
			$numero ? '' : $numero = _request('numero');
			set_request('numero', _request('fax'));
			set_request('type', 'fax');
			set_request('titre', 'Fax');

			$editer_fax = charger_fonction('editer_numero', 'action/');
			$editer_fax('oui');
		}
	}

	// si necessaire on replace la bonne donnee dans l'environnement
	$numero ? set_request('numero', $numero) : '';

	// Comme conseillé dans la documentation on informe de l'id auteur inscrit
	$retours['id_auteur'] = $id_auteur;

	if ($retour) $retours['redirect'] = $retour;

	return $retours;
}

?>
