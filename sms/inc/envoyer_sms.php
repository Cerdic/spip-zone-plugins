<?php

/*
 Vérifier les parametre et faire la requete d'envoi du sms
	$contexte est un tableau (nom=>valeur) qui sera enrichi
	Retourne true si tout s'est bien passé , message d'erreur sinon
	$connexion est un array qui defini :
		prestataire : clickatell_http,sms2email_http
		user
		password
		api_id
	$message est un array qui defini le message a envoyer :
		to
		from
		id
		text
*/
function inc_envoyer_sms($connexion,$message){
	$resultat = true;
	
	include_spip('inc/sms');

  $sender =& Net_SMS::factory($connexion['prestataire'],
                       array(	'user' => $connexion['user'],
							'password' => $connexion['password'],
							'api_id' => $connexion['api_id'] ));
  if (c_pear::isError($sender)) {
		$resultat = _L('factory SMS failed') . '<br />' .	print_r($sender, true);
		return $resultat;
  }
  
	//send message and return result
	// un peu d'ordre dans le message
	if (isset($message('from'))) $message['send_params']['from'] = $message['from'];

	$e = $sender->send($message);
  if (c_pear::isError($e))   {
	$resultat = _L('transmission_loupee') .
	   '<br />' . print_r($msg, true) .
	   '<br />' . print_r($e, true);
  }
	return $resultat;
}

?>