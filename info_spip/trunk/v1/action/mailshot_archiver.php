<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function action_mailshot_archiver($arg=null){

	if (is_null($arg)){
		$securiser_action = charger_fonction("securiser_action","inc");
		$arg = $securiser_action();
	}

	$id_mailshot = intval($arg);
	include_spip('inc/autoriser');
	if (autoriser('archiver','mailshot',$id_mailshot)){
		include_spip('inc/mailshot');
		mailshot_archiver($id_mailshot);
	}
}
