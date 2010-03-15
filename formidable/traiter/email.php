<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traiter_email_dist($saisies, $options, $retours){
	$saisies = saisies_lister_par_nom($saisies);
	
	// On récupère les destinataires
	$destinataires = _request($options['champ_destinataires']);
	if (is_array($destinataires)){
		include_spip('inc/saisies');
		include_spip('inc/filtres');
		
		// On récupère les mails des destinataires
		$destinataires = array_map('intval', $destinataires);
		$destinataires = sql_allfetsel(
			'email',
			'spip_auteurs',
			'id_auteur IN ('.join(', ', $destinataires).')'
		);
		$destinataires = array_map('reset', $destinataires);
		
		// On enlève ce champ du texte à générer
		unset($saisies[$options['champ_destinataires']]);
		
		// On récupère le courriel de l'envoyeur
		$courriel_envoyeur = _request($options['champ_courriel']);
		
		// On récupère le nom de l'envoyeur
		$nom_envoyeur = $options['champ_nom'] ? _request($options['champ_nom']) : $courriel_envoyeur;
		
		// On récupère le sujet s'il existe sinon on le construit
		$sujet = $options['champ_sujet'] ? _request($options['champ_sujet']) : _T('formidable:traiter_email_sujet', array('nom'=>$nom_envoyeur));
		$sujet = filtrer_entites($sujet);
		
		// Maintenant on parcourt les champs pour générer le texte du message
		$texte = '';
		foreach ($saisies as $saisie){
			$options_saisie = $saisie['options'];
			
			// On ne prend pas en compte le champ du destinataire
			if ($options_saisie['nom'] != $options['champ_destinataires']){
				$label = $options_saisie['label'] ? '[ '.trim(_T_ou_typo($options_saisie['label']))." ]\n" : '';
				$label = filtrer_entites($label);
				$texte .= $label;
				$texte .= _request($options_saisie['nom'])."\n\n";
			}
		}
		
		// horodatons
		$date = date("d/m/y");
		$heure = date("H:i:s");
		$contexte = "\n\n"
			._T('formidable:traiter_email_horodatage', array('date'=>$date, 'heure'=>$heure))
			."\n"
			._T('formidable:traiter_email_page', array('url'=>self()))
			."\n\n";
		$texte = $contexte.$texte;
		
		// On finit par le nom du site
		$nom_site = supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']));
		$texte .= "\n\n-- "._T('envoi_via_le_site')." ".$nom_site." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	
		// On formate pour les accents
		$texte = filtrer_entites($texte);
		
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
