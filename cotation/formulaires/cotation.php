<?php

function formulaires_contact_charger_dist($id_auteur=''){	
	$valeurs = array();
	
	$valeurs['email_cotation'] = '';
	$valeurs['sujet_cotation'] = '';
	$valeurs['commentaire_cotation'] = '';
	$valeurs['destinataire'] = array();
	$valeurs['choix_destinataires'] = '';
	
	// La liste dans laquelle on pourra éventuellement choisir
	$choix_destinataires = lire_config('cotation/choix_destinataires');
	// Le type de choix
	$valeurs['type_choix'] = $type_choix = lire_config('cotation/type_choix');
	
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
	$champs_contact_infos = champs_contact_infos();
	$champs_cotation_infos = champs_cotation_infos();
	// MED ADD
	$champs_possibles = array_merge($champs_contact_infos,$champs_cotation_infos);
	
	if (!is_array($champs_choisis = lire_config('cotation/champs')))
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
		$valeurs = array_merge($valeurs,array_map(create_function('', 'return "";'),$champs_choisis));
		
	}
	if (!is_array($champs_obligatoires = lire_config('cotation/obligatoires')))
		$valeurs['obligatoires'] = false;
	else
		$valeurs['obligatoires'] = $champs_obligatoires;
	
	
	
	return $valeurs;
}

function formulaires_cotation_verifier_dist($id_auteur=''){
	$erreurs = array();
	
	include_spip('inc/filtres');
	include_spip('inc/charsets');
	
	if (!_request('destinataire'))
		$erreurs['destinataire'] = _T("info_obligatoire");
	if (!$adres = _request('email_cotation'))
		$erreurs['email_cotation'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_cotation'] = _T('form_prop_indiquer_email');
	
	$champs_choisis = lire_config('cotation/champs');
	$champs_obligatoires = lire_config('cotation/obligatoires');
	if (is_array($champs_choisis) and is_array($champs_obligatoires)){
		foreach($champs_choisis as $champ){
			if (!_request($champ) and in_array($champ, $champs_obligatoires))
				$erreurs[$champ] = _T("info_obligatoire");
		}
	}
	
	if (!$sujet=_request('sujet_cotation'))
		$erreurs['sujet_cotation'] = _T("info_obligatoire");
	elseif(!(strlen($sujet)>3))
		$erreurs['sujet_cotation'] = _T('forum_attention_trois_caracteres');

	if (!$texte=_request('commentaire_cotation'))
		$erreurs['commentaire_cotation'] = _T("info_obligatoire");
	elseif(!(strlen($texte)>10))
		$erreurs['commentaire_cotation'] = _T('forum_attention_dix_caracteres');
	
	if ($nobot=_request('nobot'))
		$erreurs['nobot'] = 'Vous êtes un robot. Méchant robot.';

	
	return $erreurs;
}

function formulaires_cotation_traiter_dist($id_auteur=''){
	
	include_spip('base/abstract_sql');
	
	$adres = _request('email_cotation');
	$sujet = _request('sujet_cotation');
	$texte = "\n\n"._request('commentaire_cotation');
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
	// S'il n'y a pas le plugin facteur, on met l'(es) adresse(s) sous forme de chaine de caractères.
	if (!defined("_DIR_PLUGIN_FACTEUR"))
		$mail = join(', ', $mail);
	
	// Les infos supplémentaires
	//$champs_possibles = contact_infos_supplementaires();
	// Les infos supplémentaires
	$champs_contact_infos = champs_contact_infos();
	$champs_cotation_infos = champs_cotation_infos();
	// MED ADD
	$champs_possibles = array_merge($champs_contact_infos,$champs_cotation_infos);
	
	$champs_choisis = lire_config('cotation/champs');
	if (is_array($champs_choisis)){
		foreach($champs_choisis as $champ){
			if ($reponse_champ = _request($champ))
				$infos .= "\n".$champs_possibles[$champ]." : ".$reponse_champ;
		}
	}
	
	
	$texte = $infos.$texte;
	$texte .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	
	// On formate pour les accents
	$texte = filtrer_entites($texte);
	
	spip_log($texte);
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $sujet, $texte, $adres, "X-Originating-IP: ".$GLOBALS['ip']);
	
	
	$message = _T("form_prop_message_envoye");
	return array('message_ok'=>$message);
}

?>
