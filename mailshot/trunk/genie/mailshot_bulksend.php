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

		include_spip('inc/mailshot');
		list($periode,$nb) = mailshot_cadence();
		$boost = (lire_config("mailshot/boost_send")=='oui'?true:false);
		mailshot_envoyer_un_lot_par_morceaux($nb,!$boost);
		// dire qu'on a pas fini si mode boost pour se relancer aussi vite que possible
		if ($boost)
			return -($t-$periode);
	}
	return 0;
}
