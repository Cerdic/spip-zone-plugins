<?php

function formulaires_contact_libre_charger_dist($adresse, $url='', $sujet=''){
	include_spip('inc/texte');
	
	$valeurs = array(
		'sujet_message'=>$sujet,
		'texte_message'=>'',
		'email_message'=>isset($GLOBALS['visiteur_session']['email']) ? $GLOBALS['visiteur_session']['email'] : ''
	);
	
	// id du formulaire (pour en avoir plusieurs sur une meme page)
	$valeurs['id'] = rand(1, 100);
	
	return $valeurs;
}

function formulaires_contact_libre_verifier_dist($adresse, $url='', $sujet=''){
	$erreurs = array();
	include_spip('inc/filtres');
	include_spip('inc/texte');
	
	if (!$adres = _request('email_message'))
		$erreurs['email_message'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_message'] = _T('form_prop_indiquer_email');

	if (!$sujet=_request('sujet_message'))
		$erreurs['sujet_message'] = _T("info_obligatoire");
	elseif(!(strlen($sujet)>3))
		$erreurs['sujet_message'] = _T('forum_attention_trois_caracteres','',array('force'=>false)) ? _T('forum_attention_trois_caracteres') : _T('forum:forum_attention_trois_caracteres');

	if (!$texte=_request('texte_message'))
		$erreurs['texte_message'] = _T("info_obligatoire");
	
	if(_request("nobot"))
		$erreurs['nobot'] = true;
	
	// on traite les spams
	if (include_spip('inc/nospam')) {
		$caracteres = compter_caracteres_utiles($texte);
		// moins de 10 caracteres sans les liens = spam !
		if ($caracteres < 10){
			$erreurs['texte_message'] = _T('forum_attention_dix_caracteres','',array('force'=>false)) ? _T('forum_attention_dix_caracteres') : _T('forum:forum_attention_dix_caracteres');
		}
		// on analyse le sujet
		$infos_sujet = analyser_spams($sujet);
		// si un lien dans le sujet = spam !
		if ($infos_sujet['nombre_liens'] > 0)
			$erreurs['sujet_message'] = _T('nospam:erreur_spam');

		// on analyse le texte
		$infos_texte = analyser_spams($texte);
		if ($infos_texte['nombre_liens'] > 0) {
			// si un lien a un titre de moins de 3 caracteres = spam !
			if ($infos_texte['caracteres_texte_lien_min'] < 3) {
				$erreurs['texte_message'] = _T('nospam:erreur_spam');
			}
			// si le texte contient plus de trois liens = spam !
			if ($infos_texte['nombre_liens'] >= 3)
				$erreurs['texte_message'] = _T('nospam:erreur_spam');
		}
	}

	if (!_request('confirmer') AND !count($erreurs))
		$erreurs['previsu']=' ';
	return $erreurs;
}

function formulaires_contact_libre_traiter_dist($adresse, $url='', $sujet=''){
	
	$adres = _request('email_message');
	$sujet = _request('sujet_message');
	$texte = _request('texte_message');
	
	$texte .= "\n\n-- "._T('envoi_via_le_site')." ".supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']))." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	if($url)
		$texte .= "\n\n-- Depuis la page : ".supprimer_tags($url)." --\n";
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($adresse, $sujet, $texte, $adres,
				"X-Originating-IP: ".$GLOBALS['ip']);
	$message = _T("form_prop_message_envoye");

	return array('message_ok'=>$message);
}

?>
