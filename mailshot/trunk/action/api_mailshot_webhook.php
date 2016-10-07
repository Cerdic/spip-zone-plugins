<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_api_mailshot_webhook($arg=null){

	if (is_null($arg))
		$arg =_request('arg');

	spip_log("action_api_mailshot_webhook $arg","mailshot_feedback");

	$mailer = explode("/",$arg);
	$mailer = reset($mailer);

	// appeler le mailer si necessaire
	if ($mailer
		AND charger_fonction($mailer,'bulkmailer',true)
		AND $webhook = charger_fonction($mailer."_webhook",'bulkmailer',true)){
		$webhook($arg);
	}

}