<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/config');

function formulaires_editer_client_saisies_dist($id_auteur, $retour=''){
	$conf=lire_config('clients/elm',array());
	
	$civilite=array();
	$type_c = lire_config('clients/type_civ','i');
	
	if($type_c == 'c'){		
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
	}else{
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
                'obligatoire' =>  in_array("obli_portable", $conf) ? 'oui' : '',
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
				'obligatoire' => in_array("obli_fax", $conf) ? 'oui' : ''
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
				'nom' => 'email_rien',
				'label' => _T('contacts:label_email'),
				'disable' => 'oui',
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
			
		// S'il y a un numero principal, on charge les infos
		if ($numero = sql_fetsel(
			'*',
			'spip_numeros_liens LEFT JOIN spip_numeros USING(id_numero)',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.intval($id_auteur),
				'type = '.sql_quote('principal')
			)
		))
			$contexte = array_merge($contexte, $numero);
			
		$conf=lire_config('clients/elm',array());
		if (in_array('portable', $conf)){	
			// S'il y a un numero portable, on charge les infos
			if ($portable = sql_fetsel(
				'*',
				'spip_numeros_liens LEFT JOIN spip_numeros USING(id_numero)',
				array(
					'objet = '.sql_quote('auteur'),
					'id_objet = '.intval($id_auteur),
					'type = '.sql_quote('portable')
				)
			)){
				foreach($portable as $c => $v){
					if ($c == 'numero'){
							$c = 'portable'; 
							$_portable[$c] = $v;
							}
					}				
				$contexte = array_merge($contexte, $_portable);
			}
		}
		if (in_array('fax', $conf)){	
			// S'il y a un numero fax, on charge les infos
			if ($fax = sql_fetsel(
				'*',
				'spip_numeros_liens LEFT JOIN spip_numeros USING(id_numero)',
				array(
					'objet = '.sql_quote('auteur'),
					'id_objet = '.intval($id_auteur),
					'type = '.sql_quote('fax')
				)
			)){
				foreach($fax as $c => $v){
					if ($c == 'numero'){
							$c = 'fax'; 
							$_fax[$c] = $v;
							}
					}				
				$contexte = array_merge($contexte, $_fax);
			}
		}
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

    //Si le contact n'existe pas encore, on doit le créer (cas d'un auteur prexistant à son statut de client)
    if (is_null($id_contact)) {
        $inscrire_client = charger_fonction('traiter','formulaires/inscription_client');
        $inscrire_client();

	    $id_contact = sql_getfetsel(
		    'id_contact',
		    'spip_contacts_liens',
		    'objet = '.sql_quote('auteur').' and id_objet = '.$id_auteur
	    );
    }

    $editer_contact = charger_fonction('editer_contact', 'action/'); 
    $editer_contact($id_contact);

	// Le pseudo SPIP est construit
	$nom_save = _request('nom') ;
	set_request('nom', trim(_request('prenom').' '._request('nom'))); 
	
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
	
	// On modifie le numero
	$id_numero = sql_getfetsel(
		'id_numero',
		'spip_numeros_liens',
		array(
			'objet = '.sql_quote('auteur'),
			'id_objet = '.$id_auteur,
			'type = '.sql_quote('principal')
		)
	);
	
	// S'il n'y a pas de numero de telephone principal, on le crée
	if (!$id_numero){
		$id_numero = 'oui';
		set_request('objet', 'auteur');
		set_request('id_objet', $id_auteur);
		set_request('type', 'principal');
	}
	
	$editer_numero = charger_fonction('editer_numero', 'action/');
	$editer_numero($id_numero);
	
	// On modifie le portable s'il existe dans l'environnement
	if(_request('portable')){
		// on stocke cette donnee
		$numero = _request('numero');
		set_request('numero', _request('portable'));
		$id_portable = sql_getfetsel(
			'id_numero',
			'spip_numeros_liens',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.$id_auteur,
				'type = '.sql_quote('portable')
			)
		);
	
		// S'il n'y a pas de numero de portable, on le crée
		if (!$id_portable){
			$id_portable = 'oui';
			set_request('objet', 'auteur');
			set_request('id_objet', $id_auteur);
			set_request('type', 'portable');
		}
		
		$editer_portable = charger_fonction('editer_numero', 'action/');
		$editer_portable($id_portable);
		
	}
	
	// On modifie le fax s'il existe dans l'environnement
	if(_request('fax')){
		// on stocke cette donnee si elle ne l'est pas deja
		$numero ? '' : $numero = _request('numero');
		set_request('numero', _request('fax'));
		$id_fax = sql_getfetsel(
			'id_numero',
			'spip_numeros_liens',
			array(
				'objet = '.sql_quote('auteur'),
				'id_objet = '.$id_auteur,
				'type = '.sql_quote('fax')
			)
		);
	
		// S'il n'y a pas de numero de fax, on le crée
		if (!$id_fax){
			$id_fax = 'oui';
			set_request('objet', 'auteur');
			set_request('id_objet', $id_auteur);
			set_request('type', 'fax');
		}
		
		$editer_fax = charger_fonction('editer_numero', 'action/');
		$editer_fax($id_fax);
		
	}
	
	// Quand on reste sur la même page, on peut toujours éditer après
	$retours['editable'] = true;
	// si necessaire on replace la bonne donnee dans l'environnement
	$numero ? set_request('numero', $numero) : '';
	
	// Si on demande une redirection
	if ($retour) $retours['redirect'] = $retour;

	set_request('nom', $nom_save); 

	return $retours;
}

?>
