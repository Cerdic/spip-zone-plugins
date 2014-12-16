<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_mailshot_boost_send($arg=null){

	if (is_null($arg)){
		$securiser_action = charger_fonction("securiser_action","inc");
		$arg = $securiser_action();
	}

	include_spip('inc/headers');
	http_status(204); // No Content
	header("Connection: close");
	flush();
	ob_flush();
	ob_end_flush();

	spip_log("BOOST $arg","mailshot");
	list($id_mailshot,$nb,$offset) = explode("-",$arg);
	$next = sql_fetsel("id_mailshot","spip_mailshots","statut=".sql_quote('processing'),'','id_mailshot','0,1');
	if ($next['id_mailshot']==$id_mailshot){
		include_spip("inc/mailshot");
		mailshot_envoyer_lot($nb,$offset);
	}

	exit;
}
