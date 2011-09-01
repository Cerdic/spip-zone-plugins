<?php

/**
 * Fonction permettant de tester la connexion au serveur Soap
 *
 * @param string $serveur : adresse du serveur
 * @param string $ident : identifiant de connexion
 * @param string $psw : mot de passe de connexion
 *
 */
function gestionml_api_tester($serveur, $ident, $psw) {
	$retour = array() ;
	try {
		$soap = new SoapClient($serveur);
		$session = $soap->login($ident, $psw,"fr", false);
		$soap->logout($session);
	} catch(SoapFault $fault) {
		$retour['message_erreur'] .= $fault->faultstring;
	}
	return ($retour);
}

/**
 * Fonction permettant de recuperer toutes les listes du domaine
 *
 * @param array $tab : non utilise
 *
*/
function gestionml_api_listes($tab) {
	return(gestionml_api_traiter_ovh(false)) ;
}

/**
 * Fonction permettant de recuperer les listes autorisees 
 * pour l'utilisateur courant
 *
 * @param array $tab : non utilise
 *
*/
function gestionml_api_listes_toutes($tab) {
	return(gestionml_api_traiter_ovh(true)) ;
}

/**
 * Fonction de connexion a l'api soap d'OVH
 *
 * @param boolean $toutes : Recuperer toutes les listes ou uniquement celles autorisees
 * @param string $ovhaction : action ovh a effectuer (info, sendlist, users, usersdel, useradd)
 * @param string $nameML : nom de la liste
 * @param string $email : Email a traiter
 *
*/
function gestionml_api_traiter_ovh($toutes,$ovhaction='', $nameML='', $email='') {
	$retour = array() ;
	if( $ovhaction == '' ) $ovhaction = _request('ovhaction') ;
	if( $nameML == '' ) $nameML = _request('nameML') ;
	if( $email == '' ) $email = _request('email') ;

	$config = lire_config('gestionml',array());

	try {
		if (($config['hebergeur'] == "0" ) || preg_match(',\.loc\.,', $_SERVER['HTTP_HOST'])){
			include_spip('inc/soap_local');
			$soap = new SoapClientLocal(NULL, array('location' => _DIR_PLUGIN_GESTIONML."/inc/soap_local.php",
                                     'uri'      => "http://".$_SERVER["HTTP_HOST"]));
		} else {
			$soap = new SoapClient($config['serveur_distant']);
		}

		//login
		$session = $soap->login($config['identifiant'], $config['mot_de_passe'],"fr", false);
      $retour['listes'] = gestionml_api_liste_des_listes($soap->mailingListList($session, $config['domaine']),$toutes);

		switch($ovhaction) {
			case "" :
			// On ne fait rien
			break ;
			case "infos" :
				$retour['infos'] = print_r( $soap->mailingListFullInfo($session, $config['domaine'], $nameML), true )  ;
				$retour['liste'] = $nameML ;
			break ;
			case "sendlist" :
				if ($email != '') {
					$soap->mailingListSubscriberListByEmail($session, $config['domaine'], $nameML, $email);
					$retour['message_ok'] = _T('gestionml:api_liste_envoyee',array('nameML'=>$nameML,'email'=>$email)) ; 
				} else {
					$retour['message_erreur'] = _T('gestionml:api_liste_envoyee_err') ; 
				}
			break ;
			case "users" :
				$retour['users'] = $soap->mailingListSubscriberList($session, $config['domaine'], $nameML) ;
				sort($retour['users']) ;
				$retour['liste'] = $nameML ;
			break ;
			case "usersdel" :
				foreach ($email as $un_email) {
					$soap->mailingListSubscriberDel($session, $config['domaine'], $nameML, $un_email) ;
				}
				$retour['editable'] = true ;
				$retour['message_ok'] = _T('gestionml:api_suppression_emails',array('str_emails'=>implode(", ",$email),'nameML'=>$nameML)) ; 
			break ;
			case "useradd" :
				$soap->mailingListSubscriberAdd($session, $config['domaine'], $nameML, $email) ;
				$retour['editable'] = true ;
				$retour['message_ok'] = _T('gestionml:api_ajout_email',array('email'=>$email,'nameML'=>$nameML)) ; 
			break ;
			default :
				$retour['message_erreur'] = _T('gestionml:api_action_erreur') ; 
		}

		//logout
		$soap->logout($session);
		
	} catch(SoapFault $fault) {
		$retour['message_erreur'] .= $fault->faultstring;
	}
	
	return ($retour);
}

/**
 * Fonction renvoyant le tableau des listes autorisees pour l'utilisateur courant
 *
 * @param array $tableau : tableau de toutes les listes disponibles
 * @param boolean $toutes : Recuperer toutes les listes ou uniquement celles autorisees
 *
*/
function gestionml_api_liste_des_listes($tableau,$toutes) {
	$listes = array() ;
	if(is_array($tableau)){
		foreach($tableau as $ligne){
			if($toutes)
				$listes[$ligne->ml] = $ligne->nbSubscribers ;
			elseif( autoriser('gerer','ml','','',array('ml'=>$ligne->ml)))
				$listes[$ligne->ml] = $ligne->nbSubscribers ;
		}
	}
	ksort($listes) ;
	return($listes);
}

/**
 * Fonction d'ajout d'un email
 *
 * @param string $nameML : nom de la liste
 * @param string $email : Email a traiter
 *
*/
function gestionml_api_ajouter_email($nameML, $email) {
	return( gestionml_api_traiter_ovh(false,"useradd", $nameML, $email) );
}

/**
 * Fonction de suppression d'un ou plusieurs emails
 *
 * @param string $nameML : nom de la liste
 * @param array $tab_email : tableau des email a traiter
 *
*/
function gestionml_api_supprimer_emails($nameML, $tab_email) {
	return( gestionml_api_traiter_ovh(false,"usersdel", $nameML, $tab_email) );
}

?>