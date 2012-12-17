<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Mettre a jour la meta qui indique qu'au moins un envoi est en cours
 * evite un acces sql a chaque hit du cron
 *
 * @param bool $force
 * @return bool
 */
function mailshot_update_meta_processing($force = false){
	$current = ((isset($GLOBALS['meta']['mailshot_processing']) AND $GLOBALS['meta']['mailshot_processing'])?true:false);

	$new = false;
	if ($force OR sql_countsel("spip_mailshot","statut=".sql_quote('processing')))
		$new = true;

	if ($new OR $new!==$current){
		if ($new) {
			ecrire_meta("mailshot_processing",'oui');
			// reprogrammer le cron
			include_spip('inc/genie');
	    genie_queue_watch_dist();
		}
		else
			effacer_meta('mailshot_processing');
	}

	return $new;
}


/**
 * Envoyer une serie de mails
 * @param int $nb_max
 * @return int
 *   nombre de mails envoyes
 */
function mailshot_envoyer_lot($nb_max=5){
	$nb = 0;

	spip_log("envoyer_lot $nb_max","mailshot");


	return $nb;
}