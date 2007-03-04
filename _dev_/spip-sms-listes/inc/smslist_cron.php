<?php

/*
 * Spip SMS Liste
 * Gestion de liste de diffusion de SMS
 *
 * Auteur :
 * Cedric Morin
 * (c) 2007 - Distribue sous licence GNU/GPL
 *
 */

function cron_smslist_cron($t){

	$smslist_envoyer = charger_fonction('inc/smslist_envoyer');
	
	$time = time();
	spip_log("sms-list meleuse cron");
	$encore = $smslist_envoyer();
	if ($encore){
		spip_log("sms-list cron : il reste des SMS a envoyer !");
		return (0 - $t);
	}
	return 1; 
}

?>