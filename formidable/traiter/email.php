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
		
		// On récupère le courriel de l'envoyeur s'il existe
		if ($options['champ_courriel']){
			$courriel_envoyeur = _request($options['champ_courriel']);
		}
		if (!$courriel_envoyeur) $courriel_envoyeur = '';
		
		// On parcourt les champs pour générer le tableau des valeurs
		$valeurs = array();
		foreach ($champs as $champ){
			$valeurs[$champ] = _request($champ);
		}
		
		// On récupère le nom de l'envoyeur
		if ($options['champ_nom']){
			$a_remplacer = array();
			if (preg_match_all('/@[\w]+@/', $options['champ_nom'], $a_remplacer)){
				$a_remplacer = $a_remplacer[0];
				foreach ($a_remplacer as $cle=>$val) $a_remplacer[$cle] = trim($val, '@');
				$a_remplacer = array_flip($a_remplacer);
				$a_remplacer = array_intersect_key($valeurs, $a_remplacer);
				$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => $GLOBALS['meta']['nom_site']));
			}
			$nom_envoyeur = trim(_L($options['champ_nom'], $a_remplacer));
		}
		if (!$nom_envoyeur) $nom_envoyeur = $GLOBALS['meta']['nom_site'];
		
		// On récupère le sujet s'il existe sinon on le construit
		if ($options['champ_sujet']){
			$a_remplacer = array();
			if (preg_match_all('/@[\w]+@/', $options['champ_sujet'], $a_remplacer)){
				$a_remplacer = $a_remplacer[0];
				foreach ($a_remplacer as $cle=>$val) $a_remplacer[$cle] = trim($val, '@');
				$a_remplacer = array_flip($a_remplacer);
				$a_remplacer = array_intersect_key($valeurs, $a_remplacer);
				$a_remplacer = array_merge($a_remplacer, array('nom_site_spip' => $GLOBALS['meta']['nom_site']));
			}
			$sujet = trim(_L($options['champ_sujet'], $a_remplacer));
		}
		if (!$sujet) $sujet = _T('formidable:traiter_email_sujet', array('nom'=>$nom_envoyeur));
		$sujet = filtrer_entites($sujet);
		
		// On génère la vue HTML
		$html = recuperer_fond(
			'inclure/voir_saisies',
			array(
				'saisies' => $saisies,
				'valeurs' => $valeurs
			)
		);
		
		// Horodatons au début
		$date = date('d/m/y');
		$heure = date('H:i:s');
		$contexte = '<p>'
			._T('formidable:traiter_email_horodatage', array('formulaire'=>_T_ou_typo($formulaire['titre']), 'date'=>$date, 'heure'=>$heure))
			.'<br/>'
			._T('formidable:traiter_email_page', array('url'=>url_absolue(self('&', true))))
			.'</p>';
		$html = $contexte.$html;
		
		// On finit par le nom du site
		$nom_site = supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']));
		$html .= '<p>-- '._T('envoi_via_le_site').' <a href="'.$GLOBALS['meta']['adresse_site'].'">'.$nom_site.'</a> --</p>';
		
		// On génère le texte brut
		include_spip('classes/facteur');
		$texte = Facteur::html2text($html);
		
		// On utilise la forme avancé de Facteur
		$corps = array(
			'html' => $html,
			'texte' => $texte,
			'nom_envoyeur' => $nom_envoyeur
		);
		
		// On envoie enfin le message
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$ok = $envoyer_mail($destinataires, $sujet, $corps, $courriel_envoyeur, "X-Originating-IP: ".$GLOBALS['ip']);
		
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
