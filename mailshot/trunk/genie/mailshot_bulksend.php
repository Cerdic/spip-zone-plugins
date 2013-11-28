<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_mailshot_bulksend_dist($t){
	spip_log("bulksend:meta_processing:".$GLOBALS['meta']['mailshot_processing'],"mailshot");
	// Rien a faire si la meta pas de mailshots en cours
	// ne pas se fier a la meta ici pour des raisons de concurrence au demarrage d'un envoi
	if (sql_countsel("spip_mailshots","statut=".sql_quote('processing'))){
		// securite pour que le cron se relance
		// sera effacee dans mailshot_envoyer_lot si envoi fini
		$GLOBALS['meta']['mailshot_processing'] = 'oui';

		include_spip('inc/mailshot');
		include_spip('inc/config');
		$boost = (lire_config("mailshot/boost_send")=='oui'?true:false);

		$nb = 0;
		$f_relance = _DIR_TMP."bulksend_relance.txt";
		$f_last = _DIR_TMP."bulksend_last.txt";
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
		elseif($relance){
			@unlink($f_relance);
			// regarder si par hasard on a pas deja depasse le temps prevu par la cadence normale
			// dans ce cas on redemande la main aussitot
			// concerne les cas ou le smtp fait tellement attendre qu'on peine a respecter le rythme
			list($periode,$nb) = mailshot_cadence();
			$now = time();
			lire_fichier($f_last,$last);
			if ($last=intval($last)
			  AND ($dt = $now-$last)>$periode){
				$boost = true;
			}
		}

		// dire qu'on a pas fini si mode boost pour se relancer aussi vite que possible
		if ($boost)
			return -($t-$periode);
	}
	else {
		if (!function_exists("mailshot_update_meta_processing"))
			include_spip("inc/mailshot");
		mailshot_update_meta_processing();
	}
	return 0;
}
