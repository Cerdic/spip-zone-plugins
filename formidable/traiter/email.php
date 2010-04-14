<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_email_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$champs = saisies_lister_champs($saisies);
	
	// On récupère les destinataires
	if ($options['champ_destinataires']){
		$destinataires = _request($options['champ_destinataires']);
		if (is_array($destinataires)){
			// On récupère les mails des destinataires
			$destinataires = array_map('intval', $destinataires);
			$destinataires = sql_allfetsel(
				'email',
				'spip_auteurs',
				sql_in('id_auteur', $destinataires)
			);
			$destinataires = array_map('reset', $destinataires);
		}
	}
	if (!$destinataires)
		$destinataires = array();
	
	// On ajoute les destinataires en plus
	if ($options['destinataires_plus']){
		$destinataires_plus = explode(',', $options['destinataires_plus']);
		$destinataires_plus = array_map('trim', $destinataires_plus);
		$destinataires = array_merge($destinataires, $destinataires_plus);
		$destinataires = array_unique($destinataires);
	}
	
	// Si on a bien des destinataires, on peut continuer
	if ($destinataires){
		include_spip('inc/filtres');
		
		// On récupère le courriel de l'envoyeur
		$courriel_envoyeur = _request($options['champ_courriel']);
		
		// On récupère le nom de l'envoyeur
		$nom_envoyeur = $options['champ_nom'] ? _request($options['champ_nom']) : $courriel_envoyeur;
		
		// On récupère le sujet s'il existe sinon on le construit
		$sujet = $options['champ_sujet'] ? _request($options['champ_sujet']) : _T('formidable:traiter_email_sujet', array('nom'=>$nom_envoyeur));
		$sujet = filtrer_entites($sujet);
		
		// Maintenant on parcourt les champs pour générer le tableau des valeurs
		$valeurs = array();
		foreach ($champs as $champ){
			$valeurs[$champ] = _request($champ);
		}
		
		// On génère la vue HTML
		$html = recuperer_fond(
			'inclure/voir_saisies',
			array(
				'saisies' => $saisies,
				'valeurs' => $valeurs
			)
		);
		
		// On génère le texte brut
		include_spip('classes/facteur');
		$texte = Facteur::html2text($html);
		
		// horodatons
		$date = date("d/m/y");
		$heure = date("H:i:s");
		$contexte = "\n\n"
			._T('formidable:traiter_email_horodatage', array('formulaire'=>_T_ou_typo($formulaire['titre']), 'date'=>$date, 'heure'=>$heure))
			."\n"
			._T('formidable:traiter_email_page', array('url'=>self()))
			."\n\n";
		$texte = $contexte.$texte;
		
		// On finit par le nom du site
		$nom_site = supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']));
		$texte .= "\n\n-- "._T('envoi_via_le_site')." ".$nom_site." (".$GLOBALS['meta']['adresse_site']."/) --\n";
		
		// On envoie enfin le message
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$ok = $envoyer_mail($destinataires, $sujet, $texte, $courriel_envoyeur, "X-Originating-IP: ".$GLOBALS['ip']);
		
		if ($ok){
			$retours['message_ok'] .= "\n<br/>"._T('formidable:traiter_email_message_ok');
		}
		else{
			$retours['message_erreur'] .= "\n<br/>"._T('formidable:traiter_email_message_erreur');
		}
	}
	
	return $retours;
}

?>
