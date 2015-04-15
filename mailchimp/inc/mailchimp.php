<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
# API mailchimp
include_spip('inc/1.3/MCAPI.class');
// necessaire pour utiliser lire_config
include_spip('inc/config');

function mailchimp_subscribe($email, $res=array()){

	#recuperation de la config
	$apiKey = lire_config("mailchimp/apiKey");
	$listId = lire_config("mailchimp/listId");

	// ne rien faire si erreur
	if (isset($res['message_erreur'])){
		return $res;
	}

	$message_ok = $res['message_ok'];

	if (!$apiKey OR !$listId){
		// n'effrayons pas l utilisateur classique
		#spip_log(__LINE__);
		if (autoriser("configurer", "mailchimp")){
			#spip_log(__LINE__);
			//erreur il faut configurer le plugin mailchimp
			spip_log("Admin "._T('mailchimp:config_erreur'),"mailchimp"._LOG_ERREUR);
			$res = array('message_erreur' => _T('mailchimp:config_erreur'));
		}
		else {
			#spip_log(__LINE__);
			// que le spiplog si on est juste un user
			spip_log(_T('mailchimp:config_erreur'),"mailchimp"._LOG_ERREUR);
			$res = array('message_erreur' => _T('mailchimp:api_error'));
		} // autoriser
		return $res;
	}


	// initialisation d'un objet mailchimp
	$api = new MCAPI($apiKey);

	#spip_log(__LINE__);
	// By default this sends a confirmation email - you will not see new members
	// until the link contained in it is clicked!
	$retval = $api->listSubscribe($listId, $email);

	if ($api->errorCode){
		#spip_log(__LINE__);
		$messageErreur = _T('mailchimp:api_errorcode')."<br/><b>".$api->errorCode."</b><br/>".$api->errorMessage;
		if (autoriser("configurer", "mailchimp")){
			#spip_log(__LINE__);
			$res = array('message_erreur' => "Formulaire : $message_ok <br/><br/>API Mailchimp: $messageErreur");
			spip_log("Admin $messageErreur","mailchimp"._LOG_ERREUR);
		} // fin message pour admin
		else {
			// que le spiplog si on est juste un user
			spip_log("$messageErreur","mailchimp"._LOG_ERREUR);
			// message erreur succint a l'utilisateur
			$res = array('message_erreur' => _T('mailchimp:api_error'));
			#spip_log(__LINE__);
		} // autoriser
	}
	else {
		#spip_log(__LINE__);
		$message_ok .="<br/><br/>"._T('mailchimp:demande_inscription_envoyee1', array('email' => "$email"));
		$message_ok .="<br/><br/>"._T('mailchimp:demande_inscription_envoyee2');
		$message_ok .="<br/><br/><i>"._T('mailchimp:demande_inscription_envoyee3')."</i>";
		$res['message_ok'] = $message_ok ;
	}

	return $res;
}

function mailchimp_unsubscribe($email, $res=array()){

	#recuperation de la config
	$apiKey = lire_config("mailchimp/apiKey");
	$listId = lire_config("mailchimp/listId");

	// ne rien faire si erreur
	if (isset($res['message_erreur'])){
		return $res;
	}

	$message_ok = $res['message_ok'];

	if (!$apiKey OR !$listId){
		// n'effrayons pas l utilisateur classique
		#spip_log(__LINE__);
		if (autoriser("configurer", "mailchimp")){
			#spip_log(__LINE__);
			//erreur il faut configurer le plugin mailchimp
			spip_log("Admin "._T('mailchimp:config_erreur'),"mailchimp"._LOG_ERREUR);
			$res = array('message_erreur' => _T('mailchimp:config_erreur'));
		}
		else {
			#spip_log(__LINE__);
			// que le spiplog si on est juste un user
			spip_log(_T('mailchimp:config_erreur'),"mailchimp"._LOG_ERREUR);
			$res = array('message_erreur' => _T('mailchimp:api_error'));
		} // autoriser
		return $res;
	}

	// initialisation d'un objet mailchimp
	$api = new MCAPI($apiKey);

	#spip_log(__LINE__);
	$retval = $api->listUnSubscribe($listId, $email);

	if ($api->errorCode){
		#spip_log(__LINE__);
		$messageErreur = _T('mailchimp:api_errorcode')."<br/><b>".$api->errorCode."</b><br/>".$api->errorMessage;
		if (autoriser("configurer", "mailchimp")){
			#spip_log(__LINE__);
			$res = array('message_erreur' => "Formulaire : $message_ok <br/>API Mailchimp: $messageErreur");
			spip_log("Admin $messageErreur","mailchimp"._LOG_ERREUR);
		} // fin message pour admin
		else {
			// que le spiplog si on est juste un user
			spip_log("$messageErreur","mailchimp"._LOG_ERREUR);
			// message erreur succint a l'utilisateur
			$res = array('message_erreur' => _T('mailchimp:api_error'));
			#spip_log(__LINE__);
		} // autoriser
	}
	else {
		#spip_log(__LINE__);
		$message_ok .="<br>"._T('mailchimp:demande_desincription_ok', array('email' => "$email"));
		$res['message_ok']=$message_ok ;
	}

	return $res;
}
