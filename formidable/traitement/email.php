<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function traitement_email_dist($contenu, $options, $retours){
	$saisies = formidable_chercher_saisies($contenu);
	
	// On récupère les destinataires
	$destinataires = _request($options['champ_destinataires']);
	if (is_array($destinataires)){
		include_spip('saisies_fonctions');
		
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
		$sujet = $options['champ_sujet'] ? _request($options['champ_sujet']) : "$nom_envoyeur vous a écrit.";
		
		// Maintenant on parcourt les champs pour générer le texte du message
		$texte = '';
		foreach ($saisies as $saisie){
			$options_saisie = $saisie['options'];
			
			// On ne prend pas en compte le champ du destinataire
			if ($options_saisie['nom'] != $options['champ_destinataires']){
				$label = $options_saisie['label'] ? saisies_transformer_langue($options_saisie['label'])." :\n" : '';
				$texte .= $label;
				$texte .= _request($options_saisie['nom'])."\n\n";
			}
		}
		
		// horodatons
		$horodatage = date("d / m / y à H:i:s");
		$horodatage = "\n\n"._T('contact:horodatage', array('horodatage'=>$horodatage))."\n\n";
		$texte = $horodatage.$texte;
		
		// On finit par le nom du site
		$nom_site = supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']));
		$texte .= "\n\n-- "._T('envoi_via_le_site')." ".$nom_site." (".$GLOBALS['meta']['adresse_site']."/) --\n";
	
		// On formate pour les accents
		$texte = filtrer_entites($texte);
		
		// On envoie enfin le message
		$envoyer_mail = charger_fonction('envoyer_mail','inc');
		$ok = $envoyer_mail($destinataires, $sujet, $texte, $courriel_envoyeur, "X-Originating-IP: ".$GLOBALS['ip']);
		
		if ($ok){
			$retours['message_ok'] .= "\nSuper, ton message a été envoyé correctement.\n";
		}
		else{
			$retours['message_erreur'] .= "\nEt mince, une erreur. Essayes encore !\n";
		}
	}
	
	return $retours;
}

?>
