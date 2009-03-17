<?php

function formulaires_contact_avance_charger_dist($id_auteur=''){	
	$valeurs = array();
	
	$valeurs['email_contact'] = '';
	$valeurs['sujet_contact'] = '';
	$valeurs['texte_contact'] = '';
	$valeurs['destinataire'] = array();
	$valeurs['choix_destinataires'] = '';
	
	// La liste dans laquelle on pourra éventuellement choisir
	$choix_destinataires = lire_config('contactavance/choix_destinataires');
	// Le type de choix
	$valeurs['type_choix'] = $type_choix = lire_config('contactavance/type_choix');
	
	// Rien n'a été défini, on utilise l'auteur 1
	if (count($choix_destinataires) == 0){
		$valeurs['destinataire'][] = 1;
	}
	// S'il n'y a qu'un seul choix OU que le type est "tous", on l'utilise directement
	else if ((count($choix_destinataires) == 1) or ($type_choix == 'tous')){
		$valeurs['destinataire'] = $choix_destinataires;
	}
	// S'il y a plusieurs choix, on s'assure que ce sont tous des entiers
	else{
		$valeurs['choix_destinataires'] = array_map('intval', $choix_destinataires);
		// Et on met le paramètre éventuel en choix par défaut
		$valeurs['destinataire'] = array($id_auteur);
	}
	
	// Les infos supplémentaires
	$champs_possibles = contactavance_infos_supplementaires();
	if (!is_array($champs_choisis = lire_config('contactavance/champs')))
		$valeurs['champs'] = false;
	else{
		// On envoie un talbeau contenant tous les champs choisis et leur titre
		// DANS L'ORDRE de ce qu'on a récupéré de CFG
		$champs_choisis = array_flip($champs_choisis);
		foreach ($champs_choisis as $cle => $valeur){
			$champs_choisis[$cle] = $champs_possibles[$cle];
		}
		$valeurs['champs'] = $champs_choisis;
		// Mais aussi tous les champs un par un
		$valeurs = array_merge(
			$valeurs,
			array_map(
				create_function('', 'return "";'),
				$champs_choisis
			)
		);
	}
	
	if (!is_array($champs_obligatoires = lire_config('contactavance/obligatoires')))
		$valeurs['obligatoires'] = false;
	else
		$valeurs['obligatoires'] = $champs_obligatoires;
	
	return $valeurs;
}

function formulaires_contact_avance_verifier_dist($id_auteur=''){
	$erreurs = array();
	include_spip('inc/filtres');
	
	if (!_request('destinataire'))
		$erreurs['destinataire'] = _T("info_obligatoire");
	if (!$adres = _request('email_contact'))
		$erreurs['email_contact'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_contact'] = _T('form_prop_indiquer_email');
	
	$champs_choisis = lire_config('contactavance/champs');
	$champs_obligatoires = lire_config('contactavance/obligatoires');
	if (is_array($champs_choisis) and is_array($champs_obligatoires)){
		foreach($champs_choisis as $champ){
			if (!_request($champ) and in_array($champ, $champs_obligatoires))
				$erreurs[$champ] = _T("info_obligatoire");
		}
	}
	
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

function formulaires_contact_avance_traiter_dist($id_auteur=''){
	
	include_spip('base/abstract_sql');
	
	$adres = _request('email_contact');
	$sujet = _request('sujet_contact');
	$texte = "\n\n"._request('texte_contact');
	$infos = '';
	
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
	
	// Les infos supplémentaires
	$champs_possibles = contactavance_infos_supplementaires();
	$champs_choisis = lire_config('contactavance/champs');
	if (is_array($champs_choisis)){
		foreach($champs_choisis as $champ){
			$infos .= "\n".$champs_possibles[$champ]." : "._request($champ);
		}
	}
	$texte = $infos.$texte;
	$texte .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	
	spip_log($texte);
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $sujet, $texte, $adres,
				"X-Originating-IP: ".$GLOBALS['ip']);
	$message = _T("form_prop_message_envoye");

	return array('message_ok'=>$message);
}

?>
