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
		$nb_sent = mailshot_envoyer_lot($nb);
		// dire qu'on a pas fini si mode boost pour se relancer aussi vite que possible
		if (lire_config("mailshot/boost_send")=='oui')
			return -($t-$periode);
		elseif ($nb_sent<$nb){
			// il faudrait lancer un jobpour finir en async l'envoi du nombre exact demande ?
			// pour le moment on log pour voir
			spip_log("envoi incomplet : $nb_sent au lieu de $nb","bulksend"._LOG_INFO_IMPORTANTE);
		}
	}
	return 0;
}