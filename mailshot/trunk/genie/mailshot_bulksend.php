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
		include_spip('inc/config');
		$boost = (lire_config("mailshot/boost_send")=='oui'?true:false);

		$nb = 0;
		$f_relance = _DIR_TMP."bulksend_relance.txt";
		$relance = true;
		if (!$boost){
			lire_fichier($f_relance,$nb);
		}

		if (!$nb=intval($nb)){
			$relance = false;
			list($periode,$nb) = mailshot_cadence();

			// corriger par le nombre que l'on envoi
			// par le ratio de delta de time effectif depuis le dernier cron
			// sur la periode visee
			$f_last = _DIR_TMP."bulksend_last.txt";
			$now = time();
			lire_fichier($f_last,$last);
			if ($last=intval($last)
			  AND ($dt = $now-$last)>0){
				$c = min(2,$dt/$periode);
				$nb = intval(round($nb*$c,0));
				spip_log("Correction sur nb : $c ($dt au lieu de $periode) => $nb","mailshot");
			}
			ecrire_fichier($f_last,$now);
		}


		$restant = mailshot_envoyer_lot($nb);
		if ($restant>0 AND !$boost){
			ecrire_fichier($f_relance,$restant);
			$boost = true;
		}
		elseif($relance)
			@unlink($f_relance);

		// dire qu'on a pas fini si mode boost pour se relancer aussi vite que possible
		if ($boost)
			return -($t-$periode);
	}
	return 0;
}
