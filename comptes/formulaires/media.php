<?php
function formulaires_media_charger_dist(){
	$valeurs = array(
		'signupNameField'=>'',
		'signupAddressField'=>'',
		'signupFNameField'=>'',
		'signupPCodeField'=>'',
		'signupCityField'=>'',
		'signupCountryField'=>'',
		'signupEmailField'=>'',
		'signupMobileField'=>'',
		'signupFunctionField'=>'',
		'signupEmailField2'=>'',
		'signupWebsiteField'=>'',
		'signupSupportField'=>'',
		'signupFIAField'=>'',
		'signupPRLangField'=>'',
		'condAgreementField'=>''
	);
	return $valeurs;
}


function formulaires_media_verifier_dist(){
	include_spip('inc/validations');
	$erreurs = array();
	
	/* VERIF SUR LES CHAMPS OBLIGATOIRES */
	$champs_obligatoires = array(
		'signupNameField'=>'',
		'signupAddressField'=>'',
		'signupFNameField'=>'',
		'signupPCodeField'=>'',
		'signupCityField'=>'',
		'signupCountryField'=>'',
		'signupEmailField'=>'',
		'signupMobileField'=>'',
		'signupFunctionField'=>'',
		'signupEmailField2'=>'',
		'signupSupportField'=>'',
		'signupFIAField'=>'',
		'signupPRLangField'=>''
	);
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('comptes:erreur_obligatoire');
	}
	
	/* AUTRES VERIFS SUR CHAMPS OBLIGATOIRES*/
	$email = _request('signupEmailField');
	$email2 = _request('signupEmailField2');
	if($email != $email2) $erreurs['emails_non_concordants'] = _T('comptes:erreur_emails_non_concordants');
	else{
		if(!$erreurs['signupEmailField'] && !verif_email_apsulis($email)) $erreurs['email_nonvalide'] = _T('comptes:erreur_email_nonvalide');
		if(!$erreurs['signupEmailField2'] && !verif_email_apsulis($email2)) $erreurs['email2_nonvalide'] = _T('comptes:erreur_email_nonvalide');
		if(!$erreurs['signupEmailField'] && !verif_email_dispo($email)) $erreurs['email_nondispo'] = _T('comptes:erreur_email_nondispo');
	}
	
	// Vérifier le nom et le prenom
	$nom = _request('signupNameField');
	$prenom = _request('signupFNameField');
	if(!$erreurs['signupNameField'] && !verif_nom($nom)) $erreurs['nom_nonvalide'] = _T('comptes:erreur_pasdechiffres');
	if(!$erreurs['signupFNameField'] && !verif_nom($prenom)) $erreurs['prenom_nonvalide'] = _T('comptes:erreur_pasdechiffres');
	
	/* VERFIS SUR CHAMPS NON OBLIGATOIRES */ 
	$url = _request('signupWebsiteField');
	if(_request('signupWebsiteField') && !verif_url($url) ) $erreurs['url_nonvalide'] = _T('comptes:erreur_adresse_nonvalide');
	

	if (count($erreurs))
		$erreurs['message_erreur'] = _T('comptes:erreur_message_erreur');
	
		
	return $erreurs;
}

function formulaires_media_traiter_dist(){
	include_spip('base/abstract_sql');
	
	/*###
	## Equivalences champs formulaire / table SQL
	
	'signupNameField'=>name,
	'signupAddressField'=>address,
	'signupFNameField'=>firstname,
	'signupPCodeField'=>postcode,
	'signupCityField'=>city,
	'signupCountryField'=>country,
	'signupEmailField'=>email,
	'signupMobileField'=>mobile,
	'signupFunctionField'=>function,
	'signupEmailField2'=>(confirmation email),
	'signupWebsiteField'=>website,
	'signupSupportField'=>support,
	'signupFIAField'=>fia_acc,
	'signupPRLangField'=>lang,
	'condAgreementField'=>terms
	*/
	
	$type='media';
	$name=corriger_caracteres(textebrut(_request('signupNameField')));
	$firstname=corriger_caracteres(textebrut(_request('signupFNameField')));
	$address=corriger_caracteres(textebrut(_request('signupAddressField')));
	$postcode=corriger_caracteres(textebrut(_request('signupPCodeField')));
	$city=corriger_caracteres(textebrut(_request('signupCityField')));
	$country=corriger_caracteres(textebrut(_request('signupCountryField')));
	$email=corriger_caracteres(textebrut(_request('signupEmailField')));
	$mobile=corriger_caracteres(textebrut(_request('signupMobileField')));
	$function=corriger_caracteres(textebrut(_request('signupFunctionField')));
	$website=corriger_caracteres(textebrut(_request('signupWebsiteField')));
	$support=corriger_caracteres(textebrut(_request('signupSupportField')));
	$fia_acc=_request('signupFIAField');
	$lang=_request('signupPRLangField');
	$terms='Y';
	$date=date("Y-m-d H:i:s",time());
	$login_spip=strtolower(translitteration($name."_".$email));
	$zone='1';
					
	// On ajoute l'auteur à la table auteur de SPIP
	include_spip('inc/acces');
	$auteur_comptes = array(
		'nom'=>$name,
		'email'=>$email,
		'url_site'=>$website,
		'login'=>$login_spip,
		'pass'=>md5("montest"),								// Pass pour le moment, pour phase de test
		'htpass'=>generer_htpass("montest"),
		// 'pass'=>md5($alea_actuel.$alea_futur),			// On ne le connait pas, du coup, le compte n'est pas "actif"
		// 'htpass'=>generer_htpass($alea_actuel.$alea_futur), // TODO remettre un mot de pass aléatoire, qui ne servira jamais, c'est la validation du compte qui sql_updatera le champ pass et l'enverra au client (ou lui enverra un mail pour lui demander d'en choisir un, c'est encoremieux)
		'alea_actuel'=>'',
		'alea_futur'=>creer_uniqid(),
		'low_sec'=>'',
		'lang'=>$lang,
		'statut'=>"comptes"
	);
	$n = sql_insertq(
		'spip_auteurs',
		$auteur_comptes
	);
	// if (!$n) return _T('titre_probleme_technique');
	$auteur = $n;

	// On donne les bons droits sur les zones
	$lazone = sql_insertq(
		'spip_zones_auteurs',
		array(
			'id_zone'=>$zone,
			'id_auteur'=>$auteur
		)
	);
			
	// On complète la table spécifique comptes
	$nouveau_comptes = sql_insertq(
		'spip_auteurs_comptes_specifique',
		array(
			'id_auteur'=>$auteur,
			'type'=>$type,
			'name'=>$name,
			'firstname'=>$firstname,
			'mobile'=>$mobile,
			'organisme'=>$organisme,
			'address'=>$address,
			'postcode'=>$postcode,
			'city'=>$city,
			'country'=>$country,
			'function'=>$function,
			'support'=>$support,
			'fia_acc'=>$fia_acc,
			'date'=> $date
		)
	);

	// On adresse le mail d'inscription pour validation par comptes
	include_spip('inc/envoi_mail');
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$email_comptes = $GLOBALS['meta']['email_webmaster'];
	$email_from = $email;
	$reply = $email;
	
	/* Informations pour mail */
	$informations[] = "<span>Date de la demande :</span> ".$date;
	$informations[] = "<span>Nom :</span> ".$name;
	$informations[] = "<span>Prénom :</span> ".$firstname;
	$informations[] = "<span>Adresse :</span> ".$address;
	$informations[] = "<span>Code postal :</span> ".$postcode;
	$informations[] = "<span>Ville :</span> ".$city;
	$informations[] = "<span>Pays :</span> ".$country;
	$informations[] = "<span>Courriel :</span> ".$email;
	$informations[] = "<span>Téléphone :</span> ".$mobile;
	$informations[] = "<span>Fonction :</span> ".$function;
	$informations[] = "<span>URL :</span> ".$website;
	$informations[] = "<span>Support :</span> ".$support;
	$informations[] = "<span>FIA :</span> ".$fia_acc;
	$informations[] = "<span>Langue :</span> ".$lang;
	
	$email_demandeur = $email;
	$sujet_demandeur = _T('comptes:mail_signin_demandeur_sujet_prive');
	$message_demandeur = _T('comptes:mail_signin_demandeur_texte_prive')."<ul>";
	foreach($informations as $champ => $valeur){
		if($valeur != '') $message_demandeur .= "<li>".$valeur."</li>\n";
	}
	$message_demandeur .= "</ul>";

	/*
		TODO A qui on envoit ce mail ??
	*/
	envoyer_mail_html($email_demandeur,$sujet_demandeur,$email_from,$reply,$message_demandeur);

	// On envoit un mail pour générer le mot de pass
	include_spip('formulaires/oubli');
	$message = message_oubli($email,'p');
	
	$message_ok = _T('gestion:confirmation_signin_prive');
	return array('message_ok'=>$message_ok);
}

?>