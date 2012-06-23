<?php
/*
 * Plugin Proposer Evenement
 * (c) 2010 Cedric Morin
 * Distribue sous licence GPL
 *
 */

function propevent_rubrique(){
	$rubrique = lire_config('propevent/rubrique');
	while (count($rubrique)
		AND $t=array_shift($rubrique)
		AND $t = explode('|',$t)
		AND reset($t)!=='rubrique');
	$id_rubrique = 0;
	if (reset($t)=='rubrique')
		$id_rubrique = intval(end($t));
	return $id_rubrique;
}

/**
 * Charger le formulaire de proposition d'evenement
 * @return mixed
 */
function formulaires_proposer_evenement_charger_dist(){
	// verifier que le plugin est configure
	if (!function_exists('lire_config')
	  OR !lire_config('propevent/rubrique')
		OR !lire_config('propevent/etat_contribution'))
		return false;

	$valeurs = array(
		'nom'=>'',
		'prenom'=>'',
		'email'=>'',
		'telephone'=>'',

		'titre'=>'',
		'descriptif'=>'',
		'texte'=>'',

		'horaire'=>'',
		'date_debut'=>'',
		'heure_debut'=>'',
		'date_fin'=>'',
		'heure_fin'=>'',

		'lieu'=>'',
		'id_categorie'=>'',
		'mots'=>'',

		'id_rubrique' => propevent_rubrique(),
	);

	return $valeurs;
}

/**
 * Verifier le numero de telephone, tire du plugin verifier
 * @param string $valeur
 * @param array $options
 * @return string
 */
function verifier_telephone_fr($valeur, $options=array()){
	$erreur = _T('propevent:erreur_telephone');
	$ok = '';
	// On accepte differentes notations, les points, les tirets, les espaces, les slashes
	$tel = preg_replace("#\.|/|-| #i",'',$valeur);

	// On interdit les 000 etc. mais je pense qu'on peut faire plus malin
	// TODO finaliser les num√©ros √† la con
	if($tel == '0000000000') return $erreur;

	if(!preg_match("/^0[0-9]{9}$/",$tel)) return $erreur;

	return $ok;
}

/**
 * Verifier toute la saisie
 * 
 * @return array
 */
function formulaires_proposer_evenement_verifier_dist(){
	$erreurs = array();

	$oblis = array('nom','email','titre','descriptif','texte','date_debut','date_fin');
	if (lire_config('propevent/proposer_thematique')=='oui')
		$oblis[] = 'id_categorie';

	$horaire = _request('horaire')=='non'?false:true;
	if ($horaire){
		$oblis[] = 'heure_debut';
		$oblis[] = 'heure_fin';
	}
	if (!_request('mots') OR !trim(implode('',_request('mots'))))
		$oblis[] = 'lieu';
	
	foreach($oblis as $obli){
		if (!_request($obli))
			$erreurs[$obli] = _T('info_obligatoire');
	}

	if ($email = _request('email') AND !email_valide($email)){
		$erreurs['email'] = _T('form_email_non_valide');
	}
	if ($tel = _request('telephone') AND $err = verifier_telephone_fr($tel))
		$erreurs['telephone'] = $err;

	include_spip('inc/agenda_gestion');

	if (_request('date_debut'))
		$date_debut = agenda_verifier_corriger_date_saisie('debut',$horaire,$erreurs);
	if (_request('date_fin'))
		$date_fin = agenda_verifier_corriger_date_saisie('fin',$horaire,$erreurs);

	if ($date_debut AND $date_fin AND $date_fin<$date_debut)
		$erreurs['date_fin'] = _T('agenda:erreur_date_avant_apres');

	if (_request('antibot') OR _request('bot')!=="Je suis un humain")
		$erreurs['message_erreur'] = _T('propevent:erreur_no_bot');

	include_spip('base/abstract_sql');
	// controler la saisie de la categorie
	// protection contre hack
	if ($id = _request('id_categorie')
		AND (intval(sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=".intval($id)))!==propevent_rubrique())){
		$erreurs['id_categorie'] = _T('propevent:erreur_categorie_interdite');
	}

	// controler la saisie des mots
	// protection contre hack
	if ($mots = _request('mots')){
		$mots = array_map('intval',$mots);
		$mots = array_diff($mots,array(0));
		$verif = sql_allfetsel('id_mot', "spip_mots", array(sql_in("id_groupe",lire_config('propevent/groupes')),sql_in('id_mot',$mots)));
		$verif = array_map('reset',$verif);
		$verif = array_map('intval',$verif);
		if (count(array_diff($mots, $verif)) OR count(array_diff($verif, $mots)))
		$erreurs['message_erreur'] = _T('propevent:erreur_mots_cles_interdits');
	}

	if (count($erreurs) AND !isset($erreurs['message_erreur']))
		$erreurs['message_erreur'] = _T('propevent:erreurs_verifier');
	return $erreurs;
}


function formulaires_proposer_evenement_traiter_dist(){
	include_spip('base/abstract_sql');
	$res = array();
	$set_article = array(
		'titre' => _request('titre'),
		#'chapo' => _request('descriptif'),
		'texte' => _request('texte'),
		'statut' => lire_config('propevent/etat_contribution'),
	);

	$id_auteur = propevent_retrouver_auteur(_request('nom'),_request('prenom'),_request('email'),_request('telephone'));
	// si l'auteur n'a pu etre cree, indiquons ses infos dans le PS
	// c'est un pis aller au cas ou
	if (is_string($id_auteur)){
		$set_article['ps'] = "Propose par : $id_auteur";
		$id_auteur = 0;
	}
	$id_rubrique = intval(propevent_rubrique());
	if ($id = intval(_request('id_categorie')))
		$id_rubrique = $id;
	include_spip('action/editer_article');
	include_spip('inc/autoriser');

	autoriser_exception('creerarticledans', 'rubrique', $id_rubrique);
	if ($id_article = insert_article($id_rubrique)){
		// enlever le lien auteur_article cree a tort !
		sql_delete("spip_auteurs_articles", "id_article=".intval($id_article));

		// autoriser la modif par auteur anonyme
		autoriser_exception('modifier', 'article', $id_article);
		if ($set_article['statut']=='publie'){
			autoriser_exception('publierdans', 'rubrique', $id_rubrique);
		}
		// vilain hack pour SPIP 2.0 ...
		foreach($set_article as $k=>$v)
			set_request($k,$v);
		articles_set($id_article, $set_article);
		// associer l'auteur
		if ($id_auteur){
			include_spip('action/editer_auteurs');
			ajouter_auteur_et_rediriger('article', $id_article, $id_auteur, '');
		}	

		// creer l'evenement qui va avec !
		// en commencant par autoriser a titre derogatoire
		autoriser_exception('creerevenementdans','article',$id_article);
		include_spip('action/editer_evenement');
		if ($id_evenement = agenda_action_insert_evenement($id_article)){
			$set = array('titre' => $set_article['titre']);
			$set['horaire'] = _request('horaire')=='non'?'non':'oui';
			include_spip('inc/agenda_gestion');
			$date_debut = agenda_verifier_corriger_date_saisie('debut',$set['horaire']=='oui',$erreurs);
			$date_fin = agenda_verifier_corriger_date_saisie('fin',$set['horaire']=='oui',$erreurs);

			$set['date_debut'] = date('Y-m-d H:i:s',$date_debut);
			$set['date_fin'] = date('Y-m-d H:i:s',$date_fin);
			$set['descriptif'] = _request('descriptif');

			$set['mots'] = _request('mots');
			action_evenement_set($id_evenement,$set);

			// succes
			if ($set_article['statut']=='publie')
				$res['message_ok'] = _T('propevent:ok_proposition_publiee_moderation_posteriori');
			else
				$res['message_ok'] = _T('propevent:ok_proposition_attente_moderation');
			$res['id_article'] = $id_article;
			$res['id_evenement'] = $id_evenement;

			// notifier
			propevent_email_confirmation(_request('email'),$id_article,$id_evenement,$id_auteur);

		}
		else
			$res['message_erreur'] = _T('propevent:erreur_enregistrement_evenement_impossible');

	}
	else
		$res['message_erreur'] = _T('propevent:erreur_enregistrement_article_impossible');



	return $res;
}


function propevent_retrouver_auteur($nom,$prenom,$email,$telephone){
	include_spip('base/abstract_sql');
	// retrouver l'auteur sur la foi du mail
	if (!$auteur = sql_fetsel("*",'spip_auteurs','email='.sql_quote($email))){
		// il n'existe pas, on le cree en visiteur, sans login ni pass
		$id_auteur = sql_insertq('spip_auteurs', array('nom'=>"$nom $prenom",'bio'=>"Tel : $telephone",'email'=>$email,'statut'=>'6forum','source'=>'spip'));
		if (!$id_auteur){
			spip_log('Creation auteur impossible, enregistrement dans l\'article');
			return "$nom $prenom / $email / Tel : $telephone";
		}
		$auteur = sql_fetsel("*", "spip_auteurs", "id_auteur=".intval($id_auteur));
	}
	$id_auteur = intval($auteur['id_auteur']);
	// verifier si les infos sont a jour
	// si c'est un visiteur, sinon on le laisse maitre de ses infos
	if ($auteur['statut']=='6forum'){
		$set = array();
		if ($auteur['nom']!=="$nom $prenom"){
			$set['nom'] = "$nom $prenom";
		}
		if ($telephone AND trim($auteur['bio'])!=="Tel : $telephone"){
			$set['bio'] = "Tel : $telephone";
		}
		if (count($set))
			sql_updateq("spip_auteurs", $set, "id_auteur=".intval($id_auteur));
	}

	return $id_auteur;
}

function propevent_email_confirmation($email,$id_article,$id_evenement,$id_auteur){
	if (!charger_fonction("notifications", "inc",true) OR !function_exists("notifications_envoyer_mails"))
		return;

	$article = sql_fetsel("*", "spip_articles", "id_article=".intval($id_article));
	$event = sql_fetsel("*", "spip_evenements", "id_evenement=".intval($id_evenement));
	$auteur = sql_fetsel("*", "spip_auteurs", "id_auteur=".intval($id_auteur));
	$parauteur = $auteur?
		(_T('forum_par_auteur', array('auteur' => $auteur['nom'])) .
			 ($auteur['email'] ? ' <' . $auteur['email'] . '>' : '')):'';

	$contexte = array(
		'titre' => $article['titre'],
		'descriptif' => $article['chapo'],
		'texte' => $article['texte'],
		'statut' => $article['statut'],
		'par_auteur' => $parauteur,
		'date_debut' => $event['date_debut'],
		'date_fin' => $event['date_fin'],
		'horaire' => $event['horaire'],
		'lieu' => $event['lieu'],
		'mots' => array_map('reset',sql_allfetsel("id_mot", "spip_mots_evenements", "id_evenement=".intval($id_evenement))),
		'theme' => lire_config('propevent/proposer_thematique')?sql_getfetsel("titre", "spip_rubriques", "id_rubrique=".intval($article['id_rubrique'])):'',
	);

	$contexte['url_moderation'] = url_absolue(generer_url_entite($id_article, 'article', '', '', false));
	// envoyer a celui qui a propose
	$corps = recuperer_fond("notifications/evenement_propose",$contexte);
	notifications_envoyer_mails($email,$corps);
	// envoyer aux modos !
	$corps_modo = recuperer_fond("notifications/evenement_propose_modo",$contexte);
	$email_modo = lire_config('propevent/email_moderateur');
	notifications_envoyer_mails($email_modo,$corps_modo);
	
}
?>