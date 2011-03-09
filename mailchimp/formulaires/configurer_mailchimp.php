<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/editer');
# API mailchimp
include_spip('inc/1.3/MCAPI.class');
// necessaire pour utiliser lire_config
include_spip('inc/config');


function formulaires_configurer_mailchimp_verifier_dist()
{
	$res = array();


	#recuperation de la config
	$apiKey = _request("apiKey");
	$listId = _request("listId");

	spip_log(__FILE__."  ".__LINE__);
	spip_log($apiKey);
	spip_log($listId);

	// initialisation d'un objet mailchimp
	$api = new MCAPI($apiKey);
	$retval = $api->listMembers($listId, 'subscribed', null, 0, 5);

	if ($api->errorCode){
		spip_log(__FILE__."  ".__LINE__);
		$res = array('message_erreur' => _T('mailchimp:configurer_erreur_api')."<br/>"._T('mailchimp:api_errorcode')."<br/><b>".$api->errorCode."</b><br/><b>".$api->errorMessage ."</b>");
	} else {
		spip_log(__FILE__."  ".__LINE__);
		$chaine=_T('mailchimp:retour_test_api')."<br/><br/>";
		foreach ($retval['data'] as $member){
			spip_log(__FILE__."  ".__LINE__);
			$chaine .= $member['email']." - ".$member['timestamp']."<br/>";
		}
		$res = array('message_ok' => $chaine);
	}
	spip_log(__FILE__."  ".__LINE__);
	return $res;
}
?>
