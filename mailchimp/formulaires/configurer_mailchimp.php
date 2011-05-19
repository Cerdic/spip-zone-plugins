<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
# API mailchimp
include_spip('inc/1.3/MCAPI.class');

 
/**
 * Formulaire de configuration du plugin Mailchimp
 * On vérifie juste que l'on peut se connecter à l'API mailchimp   
 * ( et on stocke les 5 premiers abonnés de la liste pour passer à traiter) 
 */
function formulaires_configurer_mailchimp_verifier_dist($chaine='')
{
	$res = array();

	$chaine="<br/>";
	#recuperation de la config
	$apiKey = _request("apiKey");
	$listId = _request("listId");

	spip_log(__FILE__." ".__LINE__. " $apiKey - $listId" ) ;

	// initialisation d'un objet mailchimp
	$api = new MCAPI($apiKey);
	
	// appel de la méthode Suscribed qui renvoie les 5 premiers inscrits 
	$retval = $api->listMembers($listId, 'subscribed', null, 0, 5);

	// L'api a retourné une erreur 
	if ($api->errorCode)
	{
		$res = array('message_erreur' => _T('mailchimp:configurer_erreur_api')."<br/>"._T('mailchimp:api_errorcode')."<br/><b>".$api->errorCode."</b><br/><b>".$api->errorMessage ."</b>");
	}
	else 
	{
		// On récupère les 5 derniers 
		foreach ($retval['data'] as $member)
		{
			$chaine .= $member['email']." - ".$member['timestamp']."<br/> ";
		}
	}
	//C'est pas beau mais bon, pas de possibilité de passer une variable de vérifier à traiter 	
	define('_MAILCHIMP_CONF_LISTE_ABONNES',$chaine);


	return $res;
}



/**
 * Formulaire de configuration du plugin Mailchimp
 * On traite l'information : sauvegarde dans une meta et 
 * affichage du succes dans une belle boite .     
 * 
 */


function formulaires_configurer_mailchimp_traiter_dist($chaine)
{
	$res = array();

	#Ecriture des parametres dans META 
	ecrire_meta("mailchimp/apiKey", _request("apiKey") );
	ecrire_meta("mailchimp/listId", _request("listId") );

	#Retour succes 
	$res = array('message_ok' => _T('mailchimp:retour_test_api')._MAILCHIMP_CONF_LISTE_ABONNES  );

	return $res;
}

?>
