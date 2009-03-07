<?php

function formulaires_contact_charger_dist(){
	$valeurs = array();
	foreach(lire_config('contact/present') as $val)
	$valeurs[$val] = '';
	
	if ($GLOBALS['visiteur_session']['email']){
		$valeurs['email'] = $GLOBALS['visiteur_session']['email'];
		}
	
	return $valeurs;
}

function formulaires_contact_verifier_dist(){
	if (!_request('nobot')){
		$erreurs = array();
		// verifier que les champs obligatoires sont bien la :
		foreach(lire_config('contact/oblig') as $obligatoire)
			if (!_request($obligatoire)) $erreurs[$obligatoire] = _T("info_obligatoire");

		// verifier que si un email a ete saisi, il est bien valide :
		include_spip('inc/filtres');
		if (_request('email') AND !email_valide(_request('email')))
			$erreurs['email'] = 'Cet email n\'est pas valide';
		
		if(!(strlen(_request('message'))>10))
		$erreurs['message'] = _T('forum_attention_dix_caracteres');
		
		if (count($erreurs))
			$erreurs['message_erreur'] = 'Votre saisie contient des erreurs !';
		return $erreurs;
	}else{
		return false;
		}
}

function formulaires_contact_traiter_dist(){
	$date = date("d / m / y à H:i:s") ;
	$depuis_ip = $GLOBALS['ip'];
	$depuis_page = $GLOBALS['meta']['adresse_site'].$_SERVER['REQUEST_URI'];
	$email_posteur = _request('email');
	
	$ids_dest = array();
	$ids_dest = lire_config('contact/to');
	if (count($ids_dest) == 1){ 
		$ids_dest = intval($ids_dest[0]); 
		$where='id_auteur = '.$ids_dest.'';
		}
	else if (count($ids_dest) > 1){ 
		$ids_dest = array_map('intval', $ids_dest); 
		$where='id_auteur IN ('.implode(',',$ids_dest).')';
		}
		$emails_dests =array();
		
		$res = sql_select('email','spip_auteurs',$where);
		while($row = sql_fetch($res)) {
			$emails_dests[] = $row['email'];
		}
	 
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$email_from = ($GLOBALS['meta']['email_envoi']) ? $GLOBALS['meta']['email_envoi'] : $GLOBALS['meta']['email_webmaster'];
	$nom_site = $GLOBALS['meta']['nom_site'];
	$sujet = 'Formulaire de contact du site '.$nom_site.'';
	$infolettre = (_request('infolettre')) ? _T('contact:informez_moi') : _T('contact:pas_infolettre');
	$raison_sociale = _request('raison_sociale');
	$civilite = _request('civilite');
	$adresse = _request('adresse');
	$motif = '=';
	
	$message = _request('message');
	$message .= "\n\n\n";
	$message .= str_pad($motif, 60 , "====");
	$message .= "\n\n";
	$message .= "Nature du contact : ";
	$message .= _request('qui');
	$message .= "\n";
	if ($raison_sociale){$message .= "Raison sociale : {$raison_sociale}\n";}
	if ($civilite){$message .= "Civilité : {$civilite}\n";}
	$message .= "Prénom : ";
	$message .= _request('prenom');
	$message .= "\n";
	$message .= "Nom : ";
	$message .= _request('nom');
	$message .= "\n";
	$message .= "Email : ";
	$message .= "{$email_posteur}\n";
	$message .= "Téléphone : ";
	$message .= _request('telephone');
	$message .= "\n";
	if ($adresse){$message .= "Adresse : {$adresse}\n";}
	$message .= "CP et ville : ";
	$message .= _request('cp');
	$message .= " ";
	$message .= _request('ville');
	$message .= "\n";
	$message .= "{$infolettre}\n";
	$message .= _T('contact:date_envoi');
	$message .= "{$date}\n";
	$message .= _T('contact:depuis_ip');
	$message .= "{$depuis_ip}\n";
	$message .= _T('contact:depuis_page')."\n{$depuis_page}\n";
	if (!$ids_dest){
		$email_dest = $email_from;
		$envoyer_mail($email_dest,$sujet,$message,$email_from,
				"X-Originating-IP: ".$GLOBALS['ip']);
	}else{
	foreach($emails_dests as $email_dest){
		$envoyer_mail($email_dest,$sujet,$message,$email_from,
				"X-Originating-IP: ".$GLOBALS['ip']);
	}
	}
		return array('message_ok'=> _T('contact:succes', array("equipe_site" => $nom_site)));
}

?>