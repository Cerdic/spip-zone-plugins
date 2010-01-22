<?php
function formulaires_part_pros_charger_dist(){
	$valeurs = array(
		'signuporganismeField'=>'',
		'signupEmailField'=>'',
		'signupbusinessField'=>''
	);
	return $valeurs;
}


function formulaires_part_pros_verifier_dist(){
	include_spip('inc/validations');
	$erreurs = array();
	
	/* VERIF SUR LES CHAMPS OBLIGATOIRES */
	$champs_obligatoires = array(
		'signuporganismeField'=>'',
		'signupEmailField'=>'',
		'signupbusinessField'=>''
	);
	foreach($champs_obligatoires as $obligatoire => $valeur){
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('comptes:erreur_obligatoire');
	}
	
	/* AUTRES VERIFS SUR CHAMPS OBLIGATOIRES*/
	$email = _request('signupEmailField');
	if(!$erreurs['signupEmailField'] && !verif_email_apsulis($email)) $erreurs['email_nonvalide'] = _T('comptes:erreur_email_nonvalide');
	if(!$erreurs['signupEmailField'] && !verif_email_dispo($email)) $erreurs['email_nondispo'] = _T('comptes:erreur_email_nondispo');
	
	
	if (count($erreurs))
		$erreurs['message_erreur'] = _T('comptes:erreur_message_erreur');	
		
	return $erreurs;
}

function formulaires_part_pros_traiter_dist($type){
	include_spip('base/abstract_sql');
		
	$name=corriger_caracteres(textebrut(_request('signuporganismeField')));
	$business=corriger_caracteres(textebrut(_request('signupbusinessField')));
	$email=corriger_caracteres(textebrut(_request('signupEmailField')));
	$date=date("Y-m-d H:i:s",time());
	$login_spip=strtolower(translitteration($name."_".$email));
	$zone='2';
					
	// On ajoute l'auteur à la table auteur de SPIP
	include_spip('inc/acces');
	$auteur_comptes = array(
		'nom'=>$name,
		'email'=>$email,
		'login'=>$login_spip,
		'pass'=>md5("montest"),								// Pass pour le moment, pour phase de test
		'htpass'=>generer_htpass("montest"),
		// 'pass'=>md5($alea_actuel.$alea_futur),			// On ne le connait pas, du coup, le compte n'est pas "actif"
		// 'htpass'=>generer_htpass($alea_actuel.$alea_futur), // TODO remettre un mot de pass aléatoire, qui ne servira jamais, c'est la validation du compte qui sql_updatera le champ pass et l'enverra au client (ou lui enverra un mail pour lui demander d'en choisir un, c'est encoremieux)
		'alea_actuel'=>'',
		'alea_futur'=>creer_uniqid(),
		'low_sec'=>'',
		'lang'=>'en',
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
			'organisme'=>$name,
			'business'=>$business,
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
	$informations[] = "<span>Organisme :</span> ".$name;
	$informations[] = "<span>Business :</span> ".$business;
	$informations[] = "<span>Courriel :</span> ".$email;
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