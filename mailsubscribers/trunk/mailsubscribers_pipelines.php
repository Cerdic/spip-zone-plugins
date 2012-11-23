<?php
/**
 * Plugin mailsubscribers
 * (c) 2012 Cédric Morin
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajouter un jeton unique sur chaque inscrit (sert aux signatures d'action)
 * @param $flux
 * @return mixed
 */
function mailsubscribers_pre_insertion($flux){
	if ($flux['args']['table']=='spip_mailsubscribers'
	  AND !isset($flux['data']['jeton'])){
		include_spip("inc/acces");
		$flux['data']['jeton'] = creer_uniqid();
		include_spip("inc/mailsubscribers");
		if (!isset($flux['data']['listes']))
			$flux['data']['listes'] = mailsubscribers_normaliser_nom_liste();
		if (!isset($flux['data']['email'])){
			include_spip("inc/acces");
			$flux['data']['email'] = creer_uniqid(); // eviter l'eventuel echec unicite sur email vide
		}
	}
	return $flux;
}

/**
 * Quand le statut de l'abonnement est change, tracer par qui (date, ip, #id si auteur loge, nom/email si en session)
 * Permet d'opposer l'optin d'un internaute a son abonnement
 * (et a contrario de tracer que l'abonnement n'a pas ete fait par lui si c'est le cas...)
 * @param $flux
 * @return mixed
 */
function mailsubscribers_pre_edition($flux){
	if ($flux['args']['table']=='spip_mailsubscribers'
	  AND $flux['args']['action']=='instituer'
	  AND $id_mailsubscriber = $flux['args']['id_objet']
	  AND $statut_ancien = $flux['args']['statut_ancien']
	  AND isset($flux['data']['statut'])
	  AND $statut = $flux['data']['statut']
	  AND $statut != $statut_ancien
	  AND ($statut=='valide' OR $statut_ancien=='valide')){

		// on change le statut : logons date et par qui dans le champ optin
		$optin = sql_getfetsel("optin","spip_mailsubscribers","id_mailsubscriber=".intval($id_mailsubscriber));
		$optin = trim($optin);
		$optin .=
		  "\n"
		  . _T('mailsubscriber:info_statut_'.$statut)." : "
			. date('Y-m-d H:i:s').", "
		  . _T('public:par_auteur').' '
			  . (isset($GLOBALS['visiteur_session']['id_auteur'])?"#".$GLOBALS['visiteur_session']['id_auteur'].' ':'')
			  . (isset($GLOBALS['visiteur_session']['nom'])?$GLOBALS['visiteur_session']['nom'].' ':'')
			  . (isset($GLOBALS['visiteur_session']['session_nom'])?$GLOBALS['visiteur_session']['session_nom'].' ':'')
			  . (isset($GLOBALS['visiteur_session']['session_email'])?$GLOBALS['visiteur_session']['session_email'].' ':'')
		    . '('.$GLOBALS['ip'].')'
		;
		$optin = trim($optin);
		$flux['data']['optin'] = $optin;
	}
	return $flux;
}


/**
 * Optimiser la base de donnee en supprimant inscriptions non confirmees
 * ainsi que les inscriptions a la poubelle
 *
 * @param array $flux
 * @return array
 */
function mailsubscribers_optimiser_base_disparus($flux){
	$n = &$flux['data'];
	$mydate = $flux['args']['date'];


	# passer en refuser les inscriptions en attente non confirmees
	sql_updateq("spip_mailsubscribers",array("statut"=>"refuse"), "statut=".sql_quote('prepa')." AND date < ".sql_quote($mydate));

	# supprimer les inscriptions a la poubelle
	sql_delete("spip_mailsubscribers", "statut=".sql_quote('poubelle')." AND date < ".sql_quote($mydate));

	return $flux;

}

/**
 * Ajout de la coche d'optin sur le formulaire inscription
 *
 * @param array $flux
 * @return array
 */
function mailsubscribers_formulaire_charger($flux){
	if ($flux['args']['form']=="inscription"){
		// ici on ne lit pas la config pour aller plus vite (pas grave si on a ajoute le champ sans l'utiliser)
		$flux['data']['mailsubscriber_optin'] = '';
	}
	return $flux;
}

/**
 * Ajout de la coche d'optin sur le formulaire inscription
 *
 * @param array $flux
 * @return array
 */
function mailsubscribers_formulaire_fond($flux){
	if ($flux['args']['form']=="inscription"){
		include_spip('inc/config');
		if (lire_config("mailsubscribers/proposer_signup_optin",0)){
			if (($p = strpos($flux['data'],"</ul>"))!==false){
				$input = recuperer_fond("formulaires/inc-optin-subscribe",$flux['args']['contexte']);
				$flux['data'] = substr_replace($flux['data'],$input,$p,0);
			}
		}
	}
	return $flux;
}

/**
 * Ajout de la coche d'optin sur le formulaire inscription
 *
 * @param array $flux
 * @return array
 */
function mailsubscribers_formulaire_traiter($flux){
	if ($flux['args']['form']=="inscription"
	  AND _request('mailsubscriber_optin')
	  AND isset($flux['data']['id_auteur'])
		AND $id_auteur = $flux['data']['id_auteur']){
		// si on a poste l'optin et auteur inscrit en base
		// verifier quand meme que la config autorise cet optin, et que l'inscription s'est bien faite)
		include_spip('inc/config');
		if (lire_config("mailsubscribers/proposer_signup_optin",0)){
			$row = sql_fetsel('nom,email','spip_auteurs','id_auteur='.intval($id_auteur));
			if ($row){
				// inscrire le nom et email
				$newsletter_subscribe = charger_fonction('subscribe','newsletter');
				$newsletter_subscribe($row['email'],array('nom'=>$row['nom']));
			}
		}
	}
	return $flux;
}

?>