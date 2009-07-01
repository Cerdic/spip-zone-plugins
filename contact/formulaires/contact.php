<?php

function formulaires_contact_charger_dist($id_auteur=''){	
	$valeurs = array();
	
	$valeurs['email_contact'] = '';
	$valeurs['sujet_contact'] = '';
	$valeurs['texte_contact'] = '';
	$valeurs['destinataire'] = array();
	$valeurs['choix_destinataires'] = '';
	
	// La liste dans laquelle on pourra éventuellement choisir
	$choix_destinataires = lire_config('contact/choix_destinataires');
	// Le type de choix
	$valeurs['type_choix'] = $type_choix = lire_config('contact/type_choix');
	
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
	$champs_possibles = contact_infos_supplementaires();
	if (!is_array($champs_choisis = lire_config('contact/champs')))
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
	if (!is_array($champs_obligatoires = lire_config('contact/obligatoires')))
		$valeurs['obligatoires'] = false;
	else
		$valeurs['obligatoires'] = $champs_obligatoires;
	
	// Infos sur l'ajout de pièces jointes ou non
	$autoriser_pj = (lire_config('contact/autoriser_pj') == 'true');
	$valeurs['autoriser_pj'] = $autoriser_pj;
	
	// Si on autorise les pièces jointes, on regarde quel est le nombre max de pj.
	if ($autoriser_pj) {
		$nb_max_pj = lire_config('contact/nb_max_pj');
		$valeurs['nb_max_pj'] = $nb_max_pj;
		// On pré-remplit un tableau pour pouvoir boucler dessus le bon nombre de fois
		$valeurs['pj_fichiers'] = array_fill(0, $nb_max_pj, '');
	}
	
	//Sert à stocker les informations des fichiers plus ou moins bien uploadés lorsqu'il y a des erreurs.
	$valeurs['pj_nom_enregistrees'] = array();
	$valeurs['pj_cle_enregistrees'] = array();
	$valeurs['pj_mime_enregistrees'] = array();
	
	return $valeurs;
}

function formulaires_contact_verifier_dist($id_auteur=''){
	$erreurs = array();
	include_spip('inc/filtres');
	include_spip('inc/documents');
	
	if (!_request('destinataire'))
		$erreurs['destinataire'] = _T("info_obligatoire");
	if (!$adres = _request('email_contact'))
		$erreurs['email_contact'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_contact'] = _T('form_prop_indiquer_email');
	
	$champs_choisis = lire_config('contact/champs');
	$champs_obligatoires = lire_config('contact/obligatoires');
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
	
	// On s'occupe des pièces jointes.
	$pj_fichiers = $_FILES['pj_fichiers'];

	//Si le répertoire temporaire n'existe pas encore, il faut le créer.
	$repertoire_temp_pj = _DIR_TMP.'/contact_pj/';
	if (!is_dir($repertoire_temp_pj)) mkdir($repertoire_temp_pj);
	
	//Pour les nouvelles pj uploadées
	if ($pj_fichiers != null) {
		foreach ($pj_fichiers['name'] as $cle => $nom_pj) {
			// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			if (($nom_pj != null) && ($pj_fichiers['error'][$cle] == 0)) {
				//On vérifie qu'un fichier ne porte pas déjà le même nom, sinon on lui donne un nom aléatoire + nom original
				if (file_exists($repertoire_temp_pj.$nom_pj))
					$nom_pj = rand().$nom_pj;
				
				//déplacement du fichier vers le dossier de réception temporaire	
				if (move_uploaded_file($pj_fichiers['tmp_name'][$cle], $repertoire_temp_pj.$nom_pj)) {
					$infos_pj[$cle]['message'] = 'ajout fichier';
					$infos_pj[$cle]['nom'] = $nom_pj;
					// On en déduit l'extension et du coup la vignette
					$infos_pj[$cle]['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $nom_pj));
					$infos_pj[$cle]['vignette'] = vignette_par_defaut($infos_pj[$cle]['extension'], false, true);
					//On récupère le tye MIME du fichier aussi
					$infos_pj[$cle]['mime'] = $pj_fichiers['type'][$cle];
				}
			}
		}
	}
	
	//Pour les pj qui ont déjà été récupérées avec succes, on remet le tableau des informations sur les pj à jour
	$pj_enregistrees_nom = _request('pj_enregistrees_nom');
	$pj_enregistrees_mime = _request('pj_enregistrees_mime');
	$pj_enregistrees_extension = _request('pj_enregistrees_extension');
	$pj_enregistrees_vignette = _request('pj_enregistrees_vignette');
	
	if (is_array($pj_enregistrees_nom))
		foreach ($pj_enregistrees_nom as $cle => $nom){
			$infos_pj[$cle]['message'] = 'ajout fichier';
			$infos_pj[$cle]['nom'] = $nom;
			$infos_pj[$cle]['mime'] = $pj_enregistrees_mime[$cle];
			$infos_pj[$cle]['extension'] = $pj_enregistrees_extension[$cle];
			$infos_pj[$cle]['vignette'] = $pj_enregistrees_vignette[$cle];
		}
	
	//Maintenant on vérifie s'il n'y a pas eu une suppression de fichiers
	$nb_max_pj = lire_config('contact/nb_max_pj');
	for ($cle=0 ; $cle<$nb_max_pj ; $cle++) {
		if (_request('pj_supprimer_'.$cle)) {
			//On récupère le nom de la pièce jointe à supprimer
			$nom_pj_supprimer = $infos_pj[$cle]['nom'];
			//On supprime le fichier portant ce nom
			unlink($repertoire_temp_pj.$nom_pj_supprimer);
			//On re-propose la possibilité de télécharger un fichier en supprimant les infos du fichier
			unset($infos_pj[$cle]);
		}
	}
	
	// Si on est pas dans une confirmation et qu'il n'y pas de vraies erreurs on affiche la prévisu du message
	if (!_request('confirmer') AND !count($erreurs))
		$erreurs['previsu']=' ';
	
	// Si on est pas dans une confirmation, on ajoute au contexte les infos des fichiers déjà téléchargés
	if (!_request('confirmer'))
		 $erreurs['infos_pj'] = $infos_pj;
	
	return $erreurs;
}

function formulaires_contact_traiter_dist($id_auteur=''){
	
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
	// S'il n'y a pas le plugin facteur, on met l'(es) adresse(s) sous forme de chaine de caractères.
	if (!defined("_DIR_PLUGIN_FACTEUR"))
		$mail = join(', ', $mail);
	
	// Les infos supplémentaires
	$champs_possibles = contact_infos_supplementaires();
	$champs_choisis = lire_config('contact/champs');
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
	
	// On va vérifie s'il y a des pièces jointes
	$pj_enregistrees_nom = _request('pj_enregistrees_nom');
	$pj_enregistrees_mime = _request('pj_enregistrees_mime');
	$pj_enregistrees_extension = _request('pj_enregistrees_extension');
	$repertoire_temp_pj = _DIR_TMP.'/contact_pj/';
	
	// Si oui on les ajoute avec le plugin Facteur
	if ($pj_enregistrees_nom != null) {
		//On rajoute des sauts de ligne pour différencier du message.
		$texte .= "\n\n";
		$texte = array(
			'texte' => $texte
		);
		foreach ($pj_enregistrees_nom as $cle => $nom_pj) {
			$texte['pieces_jointes'][$cle] = array(
				'chemin' => $repertoire_temp_pj.$nom_pj,
				'nom' => $nom_pj,
				'encodage' => 'base64',
				'mime' => $pj_enregistrees_mime[$cle]
			);
		}
	}
	
	spip_log($texte);
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $sujet, $texte, $adres, "X-Originating-IP: ".$GLOBALS['ip']);
	$message = _T("form_prop_message_envoye");

	return array('message_ok'=>$message);
}

?>
