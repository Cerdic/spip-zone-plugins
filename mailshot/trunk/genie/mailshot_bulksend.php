<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_mailshot_bulksend_dist($t){
	// Rien a faire si la meta n'est pas la
	if (isset($GLOBALS['meta']['mailshot_processing'])){
		// TODO : limiter la cadence en fonction de la config
		include_spip('inc/mailshot');
		list($periode,$nb) = mailshot_cadence();
		mailshot_envoyer_lot($nb);
	}
	return 0;
}