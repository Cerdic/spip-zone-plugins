<?php

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

function gestionml_api_listes($tab) {
	return(gestionml_api_traiter_ovh(false)) ;
}

function gestionml_api_listes_toutes($tab) {
	return(gestionml_api_traiter_ovh(true)) ;
}

function gestionml_api_traiter_ovh($toutes,$ovhaction='', $nameML='', $email='') {
	$retour = array() ;
	if( $ovhaction == '' ) $ovhaction = _request('ovhaction') ;
	if( $nameML == '' ) $nameML = _request('nameML') ;
	if( $email == '' ) $email = _request('email') ;

	$config = lire_config('gestionml',array());

	try {
		if ($config['hebergeur'] == "0" ) {
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
			break ;
			case "sendlist" :
				if ($email != '') {
					$soap->mailingListSubscriberListByEmail($session, $config['domaine'], $nameML, $email);
					$retour['message_ok'] = 'La liste des abonn&eacute;s de la liste '.$nameML.' a &eacute;t&eacute; envoy&eacute;e &agrave; '.$email ; 
				} else {
					$retour['message_erreur'] = 'L\'adresse email de votre compte n\'est pas renseign&eacute;e' ;
				}
			break ;
			case "users" :
				$retour['users'] = $soap->mailingListSubscriberList($session, $config['domaine'], $nameML) ;
				sort($retour['users']) ;
				$retour['liste'] = $nameML ;
			break ;
			case "userdel" :
				$soap->mailingListSubscriberDel($session, $config['domaine'], $nameML, $email) ;
				$retour['message_ok'] = 'La suppression de '.$email.' de la liste '.$nameML.' sera prise en compte dans quelques instants' ;
			break ;
			case "useradd" :
				$soap->mailingListSubscriberAdd($session, $config['domaine'], $nameML, $email) ;
				$retour['message_ok'] = 'Le rajout de '.$email.' dans la liste '.$nameML.' sera pris en compte dans quelques instants' ;
			break ;
			default :
				$retour['message_erreur'] = 'Action demand&eacute; non prise en compte' ;
		}

		//logout
		$soap->logout($session);
		
	} catch(SoapFault $fault) {
		$retour['message_erreur'] .= $fault->faultstring;
	}
	
	return ($retour);
}

function gestionml_api_liste_des_listes($tableau,$toutes) {
	$listes = array() ;
	if(is_array($tableau)){
		foreach($tableau as $ligne){
			if($toutes)
				$listes[$ligne->ml] = $ligne->nbSubscribers ;
			elseif( autoriser('gerer','gestionml','','',array('ml'=>$ligne->ml)))
				$listes[$ligne->ml] = $ligne->nbSubscribers ;
		}
	}
	ksort($listes) ;
	return($listes);
}

function gestionml_api_ajouter_email($nameML, $email) {
	return( gestionml_api_traiter_ovh(false,"useradd", $nameML, $email) );
}

?>