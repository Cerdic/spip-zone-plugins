<?php

function formulaires_contact_avance_charger_dist(){	
	$valeurs = array();
	
	$valeurs['email_contact'] = '';
	$valeurs['sujet_contact'] = '';
	$valeurs['texte_contact'] = '';
	$valeurs['destinataire'] = '';
	$valeurs['choix_destinataires'] = '';
	
	$choix_destinataires = lire_config('contactavance/choix_destinataires');
	// Rien n'a été défini, on utilise l'auteur 1
	if (count($choix_destinataires) == 0){
		$valeurs['destinataire'] = 1;
	}
	// S'il n'y a qu'un seul choix, on l'utilise directement
	else if (count($choix_destinataires) == 1){
		$valeurs['destinataire'] = intval($choix_destinataires[0]);
	}
	// S'il y a plusieurs choix, on s'assure que ce sont tous des entiers
	else{
		$valeurs['choix_destinataires'] = array_map('intval', $choix_destinataires);
	}
	
	if (lire_config('contactavance/plusieurs_destinataires') == 'oui')
		$valeurs['plusieurs_destinataires'] = true;
	else
		$valeurs['plusieurs_destinataires'] = false;
	
	return $valeurs;
}

function formulaires_contact_avance_verifier_dist(){
	$erreurs = array();
	include_spip('inc/filtres');
	
	if (!$adres = _request('email_contact'))
		$erreurs['email_contact'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_contact'] = _T('form_prop_indiquer_email');

	if (!$sujet=_request('sujet_contact'))
		$erreurs['sujet_contact'] = _T("info_obligatoire");
	elseif(!(strlen($sujet)>3))
		$erreurs['sujet_contact'] = _T('forum_attention_trois_caracteres');

	if (!$texte=_request('texte_contact'))
		$erreurs['texte_contact'] = _T("info_obligatoire");
	elseif(!(strlen($texte)>10))
		$erreurs['texte_contact'] = _T('forum_attention_dix_caracteres');
	
	if ($nobot=_request('nobot'))
		$erreurs['nobot'] = 'Vous êtes un robot. Méchant robot.';
	
	if (!_request('confirmer') AND !count($erreurs))
		$erreurs['previsu']=' ';
	
	return $erreurs;
}

function formulaires_contact_avance_traiter_dist(){
	
	$adres = _request('email_contact');
	$sujet=_request('sujet_contact');
	$texte=_request('texte_contact');
	
	// On récupère à qui ça va être envoyé
	$destinataire = _request('destinataire');
	if (!is_array($destinataire))
		$destinataire = array($destinataire);
	$destinataire = array_map('intval', $destinataire);
	$mail = sql_allfetsel(
		'email',
		'spip_auteurs',
		'id_auteur IN ('.join(', ', $destinataire).')'
	);
	$mail = array_map('reset', $mail);
	$mail = join(', ', $mail);
	
	$texte .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $sujet, $texte, $adres,
				"X-Originating-IP: ".$GLOBALS['ip']);
	$message = _T("form_prop_message_envoye");

	return array('message_ok'=>$message);
}

?>
