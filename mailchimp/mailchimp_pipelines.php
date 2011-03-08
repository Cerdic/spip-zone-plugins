<?php

/**
var_dump($flux);
die ("plouf $message_ok");
 * Proposer un traitement par defaut pour les #FORMULAIRE_CONFIGURER_XXX
 *
 * @param array $flux
 * @return array
 */


function mailchimp_formulaire_traiter($flux)
{

	// on recupere d'abord le nom du formulaire .
	// car c'est un pipeline donc tout formulaire passe dedans ( prive ou public)
	$formulaire = $flux['args']['form'];
	spip_log(__LINE__);

	//dans notre cas c'est le formulaire mesabonnes (du plugin mes_abonnes) qui nous interesse
	if ($formulaire=="mesabonnes"){
		// necessaire pour utiliser les autorisations
		include_spip('inc/autoriser');
		spip_log(__LINE__);

		# API mailchimp
		require_once 'inc/1.3/MCAPI.class.php';

		// necessaire pour utiliser lire_config
		include_spip('inc/config');

		#recuperation de la config
		$apiKey = lire_config("mailchimp/apiKey");
		$listId = lire_config("mailchimp/listId");

		//on verifie que les parametres du plugin mailchimp sont initialisées
		if ($apiKey and $listId){
			spip_log(__LINE__);
			spip_log($apiKey);
			spip_log($listId);

			// initialisation d'un objet mailchimp
			$api = new MCAPI($apiKey);


			// 2 cas possibles : inscription ou desinscription
			$id_abonne = $flux['data']['id_abonne'];
			$message_ok = $flux['data']['message_ok'];

			$statut = sql_getfetsel('statut', 'spip_mesabonnes', 'id_abonne='.intval($id_abonne));
			$email = sql_getfetsel('email', 'spip_mesabonnes', 'id_abonne='.intval($id_abonne));

			if ($statut=='publie'){
				spip_log(__LINE__);
				// By default this sends a confirmation email - you will not see new members
				// until the link contained in it is clicked!
				$retval = $api->listSubscribe($listId, $email);

				if ($api->errorCode){
					spip_log(__LINE__);
					$messageErreur = _T('mailchimp:api_errorcode')." : ErroCode Mailchimp:".$api->errorCode." / Error message".$api->errorMessage;
					if (autoriser("configurer", "mailchimp")){
						spip_log(__LINE__);
						$flux['data'] = array('message_erreur' => "Plugin mes_abonnes : $message_ok <br/> Plugin Mailchimp: $messageErreur");
						spip_log("Admin $messageErreur");
						return $flux;
					} // fin message pour admin
					else {
						spip_log(__LINE__);
						// que le spiplog si on est juste un user
						spip_log("$messageErreur");
						return $flux;
					} // autoriser
				} else {
					spip_log(__LINE__);
					$message_ok .="<br>"._T('mailchimp:demande_inscription_envoyee', array('email' => "$email",'from'=>'pas dispo par api'));
					$flux['data']['message_ok']=$message_ok ;
					return $flux;
				}

			} // $statut=='subscribe'

			else if ($statut=='poubelle'){
				spip_log(__LINE__);
				$retval = $api->listUnSubscribe($listId, $email);

				if ($api->errorCode){
					spip_log(__LINE__);
					$messageErreur = _T('mailchimp:api_errorcode')." : ErroCode Mailchimp:".$api->errorCode." / Error message".$api->errorMessage;
					if (autoriser("configurer", "mailchimp")){
						spip_log(__LINE__);
						$flux['data'] = array('message_erreur' => "Plugin mes_abonnes : $message_ok <br/> Plugin Mailchimp: $messageErreur");
						spip_log("Admin $messageErreur");
						return $flux;
					} // fin message pour admin
					else {
						spip_log(__LINE__);
						// que le spiplog si on est juste un user
						spip_log(" $messageErreur");
						return $flux;
					} // autoriser
				} else {
					spip_log(__LINE__);
					$message_ok .="<br>"._T('mailchimp:demande_desincription_ok', array('email' => "$email"));
					$flux['data']['message_ok']=$message_ok ;
					return $flux;
				}

			}
			else
			{
				spip_log(__LINE__);
				// ne doit pas arriver normallement

			}

		} //($apiKey and $listId)
		else {
			// n'effrayons pas l utilisateur classique
			spip_log(__LINE__);
			if (autoriser("configurer", "mailchimp")){
				spip_log(__LINE__);
				//erreur il faut configurer le plugin mailchimp
				$flux['data'] = array('message_erreur' => _T('mailchimp:config_erreur'));
				spip_log("Admin"._T('mailchimp:config_erreur'));
				return $flux;
			}
			else {
				spip_log(__LINE__);
				// que le spiplog si on est juste un user
				spip_log(_T('mailchimp:config_erreur'));
				return $flux;
			} // autoriser

			spip_log(__LINE__);
		} // if ( $apiKey and $listId )	{

		spip_log(__LINE__);
	}
	spip_log(__LINE__);
}


?>
