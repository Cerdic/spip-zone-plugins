<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('configurer/pipelines');

function formulaires_contact_charger_dist($id_auteur='',$tracer=''){
	$valeurs = array();

	$valeurs['destinataire'] = array();
	$valeurs['choix_destinataires'] = '';

	// La liste dans laquelle on pourra éventuellement choisir
	$choix_destinataires = ($tmp = lire_config('contact/choix_destinataires')) ? $tmp : array();
	$choix_destinataires = array_map('intval',$choix_destinataires);

	// tableau des type_choix necessitant la prise en compte de $id_auteur
	$t_c = array('tous_et', 'tous_ou', 'un_et', 'un_ou', 'plusieurs_et', 'plusieurs_ou');
	$valeurs['type_choix'] = $type_choix = lire_config('contact/type_choix');
	if( in_array($type_choix,$t_c) ){
		if(!is_array($id_auteur)){
			$id_auteur = explode(',',$id_auteur);
			if(!is_numeric($id_auteur[0]))$id_auteur=array();
		}
	}

	$nb_d = count($choix_destinataires);
	// Rien n'a été défini, on utilise l'auteur 1
	if ($nb_d == 0){
		$valeurs['destinataire'][] = 1;
	}else{
		$valeurs['destinataire'] = $choix_destinataires;
	}

	if ($type_choix == 'tous_ou' and $id_auteur){
		$valeurs['destinataire'] = $id_auteur;
	}else if ($type_choix == 'tous_et' and $id_auteur){
		$valeurs['destinataire'] = array_unique(array_merge($valeurs['destinataire'],$id_auteur));
	}else if (($type_choix == 'un' or $type_choix == 'plusieurs') and $nb_d>1){
	$valeurs['choix_destinataires'] = $valeurs['destinataire'];
	}else if ($type_choix == 'un_et' or $type_choix == 'plusieurs_et'){
			$c_d = array_unique(array_merge($valeurs['destinataire'],$id_auteur));
		if(count($c_d)>1){
			$valeurs['choix_destinataires'] = $c_d;
		}else{
			$valeurs['destinataire'] = $c_d;
		}
	}else if ($type_choix == 'un_ou' or $type_choix == 'plusieurs_ou'){
		if(count($id_auteur)>1){
			$valeurs['choix_destinataires'] = $id_auteur;
		}elseif(count($id_auteur)==1){
			$valeurs['destinataire'] = $id_auteur;
		}elseif(count($valeurs['destinataire'])>1){
			$valeurs['choix_destinataires'] = $valeurs['destinataire'];
		}
	}
	$valeurs['destinataire_selection'] = _request('destinataire');

	// Les infos supplémentaires
	$champs_possibles = contact_infos_supplementaires();
	$champs_mini_config = array('mail', 'sujet', 'texte');

		$champs_choisis = lire_config('contact/champs',$champs_mini_config);

		// On envoie un tableau contenant tous les champs choisis et leur titre
		// DANS L'ORDRE de ce qu'on a récupéré de CFG
		$champs_choisis = array_flip($champs_choisis);
		foreach ($champs_choisis as $cle => $valeur){
			$champs_choisis[$cle] = $champs_possibles[$cle];
		}
		$valeurs['_champs'] = $champs_choisis;
		// Mais aussi tous les champs un par un
		$valeurs = array_merge(
			$valeurs,
			array_map(
				create_function('', 'return "";'),
				$champs_choisis
			)
		);


		$valeurs['_obligatoires'] = $champs_obligatoires = lire_config('contact/obligatoires',$champs_mini_config);

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

function formulaires_contact_verifier_dist($id_auteur='',$tracer=''){
	$erreurs = array();
	include_spip('inc/filtres');
	include_spip('inc/documents');
	include_spip('inc/charsets');
	if($tracer){
		$trace=explode('-',$tracer);
		if( !(count($trace)==2) or !(intval($trace[1])>0) ){
			$erreurs['message_erreur'] = 'Une erreur de transmission s\'est produite, merci de renouveller vôtre demande.';
		}
	}
	if (!_request('destinataire'))
		$erreurs['destinataire'] = _T("info_obligatoire");
	if (!$adres = _request('mail'))
		$erreurs['mail'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['mail'] = _T('form_prop_indiquer_email');

	$champs_mini_config = array('mail', 'sujet', 'texte');
	$champs_choisis = lire_config('contact/champs',$champs_mini_config );
	$champs_obligatoires = lire_config('contact/obligatoires',$champs_mini_config);
	if (is_array($champs_choisis) and is_array($champs_obligatoires)){
		foreach($champs_choisis as $champ){
			if (!_request($champ) and in_array($champ, $champs_obligatoires))
				$erreurs[$champ] = _T("info_obligatoire");
		}
	}

	if(!(strlen(_request('sujet'))>3))
		$erreurs['sujet'] = _T('forum_attention_trois_caracteres');

	$texte_min = !defined('_TEXTE_MIN')?10:_TEXTE_MIN;
	if(!(strlen(_request('texte'))>$texte_min) && !$erreurs['texte'])
		$erreurs['texte'] = _T('contact:forum_attention_nbre_caracteres',array('nbre_caract'=>$texte_min));

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
			// On commence par transformer le nom du fichier pour éviter les conflits
			$nom_pj = trim(preg_replace('/[\s]+/', '_', strtolower(translitteration($nom_pj))));
			// Si le fichier a bien un nom et qu'il n'y a pas d'erreur associé à ce fichier
			if (($nom_pj != null) && ($pj_fichiers['error'][$cle] == 0)) {
				//On vérifie qu'un fichier ne porte pas déjà le même nom, sinon on lui donne un nom aléatoire + nom original
				if (file_exists($repertoire_temp_pj.$nom_pj))
					$nom_pj = $nom_pj.'_'.rand();

				//déplacement du fichier vers le dossier de réception temporaire
				if (move_uploaded_file($pj_fichiers['tmp_name'][$cle], $repertoire_temp_pj.$nom_pj)) {
					$infos_pj[$cle]['message'] = 'ajout fichier';
					$infos_pj[$cle]['nom'] = $nom_pj;
					// On en déduit l'extension et du coup la vignette
					$infos_pj[$cle]['extension'] = strtolower(preg_replace('/^.*\.([\w]+)$/i', '$1', $nom_pj));
					if (function_exists('vignette_par_defaut'))
						$vignette = 'vignette_par_defaut';
					else
						$vignette = charger_fonction('vignette', 'inc');
					$infos_pj[$cle]['vignette'] = $vignette($infos_pj[$cle]['extension'], false, true);
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

function formulaires_contact_traiter_dist($id_auteur='',$tracer=''){

	include_spip('base/abstract_sql');
	include_spip('inc/texte');

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
	$champs_possibles = contact_infos_supplementaires();
	$champs_mini_config = array('mail', 'sujet', 'texte');
	$champs_choisis = lire_config('contact/champs',$champs_mini_config);
	if (is_array($champs_choisis)){
		foreach($champs_choisis as $champ){
			if ($reponse_champ = _request($champ)){
				if( ($champ=='mail') OR ($champ=='sujet') OR ($champ=='texte') ){
					$posteur[$champ] = $reponse_champ;
				}else{
					$infos .= "\n".$champs_possibles[$champ]." : ".$reponse_champ;
				}
			}
		}
	}
	if($tracer){
		$trace=explode('-',$tracer);
		if( (count($trace)==2) and (intval($trace[1])>0) ){
			$url = generer_url_entite(intval($trace[1]),$trace[0]);
			if($url){
				$inforigine= $GLOBALS['meta']['adresse_site']."/".$url;
			}else{
				$inforigine= 'info trace non comprise';
			}
		}else{
			$inforigine= 'info trace non comprise';
		}
			$inforigine= _T('contact:inforigine')."\n".$inforigine."\n\n";

	}

	// horodatons
	$horodatage = affdate_heure (date ("Y-m-d H:i:s"));
	$horodatage = "\n\n"._T('contact:horodatage', array('horodatage'=>$horodatage))."\n\n";
	$par = _T('contact:par_qui').$posteur['mail']."\n\n";

	$texte = $horodatage.$par.$inforigine.$infos."\n\n".$posteur['texte'];
	$nom_site = supprimer_tags(extraire_multi($GLOBALS['meta']['nom_site']));
	$texte .= "\n\n "._T('envoi_via_le_site')." ".$nom_site." ( ".$GLOBALS['meta']['adresse_site']."/ ) \n";

	// Texte a envoyer par mail, sans raccourcis SPIP
	$texte_final = propre($texte);
	// Eviter que le facteur machouille les apostrophes
	if ($GLOBALS['meta']['facteur_filtre_iso_8859']){
		$texte_final = preg_replace(',&#8217;,',"'",$texte_final);
	}
	// Sauvegarder un soupcon de liste dans le mail
	$texte_final = preg_replace (array('/<li>/','/<\/li>/','/<\/ul>/'), array("- ","\n","\n"), $texte_final);
	$texte_final = supprimer_tags($texte_final);

	// On formate pour les accents
	// Texte a mettre en base
	$texte = filtrer_entites($texte);

	// On va vérifie s'il y a des pièces jointes
	$pj_enregistrees_nom = _request('pj_enregistrees_nom');
	$pj_enregistrees_mime = _request('pj_enregistrees_mime');
	$pj_enregistrees_extension = _request('pj_enregistrees_extension');
	$repertoire_temp_pj = _DIR_TMP.'/contact_pj/';

	// Si oui on les ajoute avec le plugin Facteur
	if ($pj_enregistrees_nom != null) {
		//On rajoute des sauts de ligne pour différencier du message.
		$texte_final = array(
			'texte' => $texte_final
		);
		foreach ($pj_enregistrees_nom as $cle => $nom_pj) {
			$texte_final['pieces_jointes'][$cle] = array(
				'chemin' => $repertoire_temp_pj.$nom_pj,
				'nom' => $nom_pj,
				'encodage' => 'base64',
				'mime' => $pj_enregistrees_mime[$cle]
			);
		}
	}

	// Enregistrement des messages en base de données si on l'a demandé
	if (lire_config('contact/sauvegarder_contacts')) {
		// Il s'agit d'un visiteur : on va donc l'enregistrer dans la table auteur pour garder son mail.
		// Sauf s'il existe déjà.
		$id_aut = sql_getfetsel(
			'id_auteur',
			'spip_auteurs',
			'email = '.sql_quote($posteur['mail'])
		);
		if (!$id_aut){
			$nom_auteur = trim(_request('prenom').' '._request('nom'));
			if (!$nom_auteur) $nom_auteur = $posteur['mail'];
			$id_aut = sql_insertq(
				'spip_auteurs',
				array(
					'nom' => $nom_auteur,
					'email' => $posteur['mail'],
					'statut' => 'contact'
				)
			);
		}

		// Ensuite on ajoute le message dans la base
		$id_message = sql_insertq(
			'spip_messages',
			array(
				'titre' => $posteur['sujet'],
				'statut' => 'publie',
				'type' => 'contac',
				'id_auteur' => $id_aut,
				'date_heure' => date('Y-m-d H:i:s'),
				'texte' => $texte,
				'rv' => 'non'
			)
		);

		// S'il y a des pièces jointes on les ajoute aux documents de SPIP.
		if ($pj_enregistrees_nom != null) {
			//On charge la fonction pour ajouter le document là où il faut
			$ajouter_document = charger_fonction('ajouter_documents', 'inc');
			foreach ($pj_enregistrees_nom as $nom_pj) {
				$id_doc = $ajouter_document($repertoire_temp_pj.$nom_pj, $nom_pj, 'message', $id_message, 'document', $id_document, $titrer=false);
			}
		}

		// On lie le message au(x) destinataire(s) concerné(s)
		foreach ($destinataire as $id_destinataire) {
			sql_insertq(
				'spip_auteurs_messages',
				array(
					'id_auteur' => $id_destinataire,
					'id_message' => $id_message,
					'vu' =>'non')
			);
		}

		$memoire = generer_url_ecrire('contact_un_message', 'id_message='.$id_message);
		if ($pj_enregistrees_nom != null) {
			$texte_final['texte'] .= "\n\n"._T('contact:consulter_memoire')."\n".$memoire;
		}
		else{
			$texte_final .= "\n\n"._T('contact:consulter_memoire')."\n".$memoire;
		}
	}
	// envoyer le mail maintenant
	if (!is_array($texte_final)) {
		$texte_final = array(
			'texte' => $texte_final
		);
	}
	$texte_final['repondre_a'] = $posteur['mail'];
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$envoyer_mail($mail, $posteur['sujet'], $texte_final , '', "X-Originating-IP: ".$GLOBALS['ip']);

	// Maintenant que tout a été envoyé ou enregistré, s'il y avait des PJ il faut supprimer les fichiers
	if ($pj_enregistrees_nom != null) {
		foreach ($pj_enregistrees_nom as $cle => $nom_pj) {
			unlink($repertoire_temp_pj.$nom_pj);
		}
	}

	$message = _T('contact:succes', array("equipe_site" => $nom_site));
	return array('message_ok'=>$message);
}

?>
