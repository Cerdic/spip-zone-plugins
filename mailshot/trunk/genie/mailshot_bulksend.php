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

		// corriger par le nombre que l'on envoi
		// par le ratio de delta de time effectif depuis le dernier cron
		// sur la periode visee
		$f = _DIR_TMP."bulksend_last.txt";
		$now = time();
		lire_fichier($f,$last);
		if ($last=intval($last)
		  AND ($dt = $now-$last)>0){
			$c = min(2,$dt/$periode);
			$nb = intval(round($nb*$c,0));
			spip_log("Correction sur nb : $c ($dt au lieu de $periode) => $nb","mailshot");
		}
		ecrire_fichier($f,$now);

		$boost = (lire_config("mailshot/boost_send")=='oui'?true:false);
		mailshot_envoyer_un_lot_par_morceaux($nb,!$boost);
		// dire qu'on a pas fini si mode boost pour se relancer aussi vite que possible
		if ($boost)
			return -($t-$periode);
	}
	return 0;
}
