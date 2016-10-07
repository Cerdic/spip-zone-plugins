<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


function genie_mailshot_bulksend_dist($t){
	spip_log("bulksend:meta_processing:".$GLOBALS['meta']['mailshot_processing'],"mailshot");

	// un mailshot programme dans le futur, en attente d'init
	if ($id_mailshot = sql_getfetsel('id_mailshot','spip_mailshots','statut='.sql_quote('init').' AND date_start<'.sql_quote(date('Y-m-d H:i:s')),'','','0,1')){
		// passer en processing l'id_mailshot concerne et initialiser le mailer
		sql_updateq('spip_mailshots',array('statut'=>'processing'),'id_mailshot='.intval($id_mailshot));
		// initialiser le mailer si necessaire
		// On cree l'objet Mailer (PHPMailer) pour le manipuler ensuite
		if ($mailer = lire_config("mailshot/mailer")
			AND charger_fonction($mailer,'bulkmailer',true)
			AND $init = charger_fonction($mailer."_init",'bulkmailer',true)){
			$init($id_mailshot);
		}
	}
	
	
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
			if (!$boost){
				lire_fichier($f_last,$last);
				if ($last=intval($last)
				  AND ($dt = $now-$last)>0){
					$c = min(2,$dt/$periode);
					$nb = intval(round($nb*$c,0));
					spip_log("Correction sur nb : $c ($dt au lieu de $periode) => $nb","mailshot");
				}
			}
			ecrire_fichier($f_last,$now);
		}

		// si mode boost est qu'on a *beaucoup* de destinataires, lancer des actions concourantes
		// si on est sur de pas ecrouler le serveur
		if ($boost
		  and function_exists('sys_getloadavg')
		  and $load = sys_getloadavg()
		  and is_array($load)
		  and $load = array_shift($load)) {
			if (!defined('_ECRAN_SECURITE_LOAD')) {
				define('_ECRAN_SECURITE_LOAD', 4);
			}
			if ($load < _ECRAN_SECURITE_LOAD) {

				$next = sql_fetsel("*", "spip_mailshots", "statut=" . sql_quote('processing'), '', 'id_mailshot', '0,1');
				if (!defined('_MAILSHOT_SEND_PER_PROCESS')) {
					define('_MAILSHOT_SEND_PER_PROCESS', 10000);
				}
				// si le total est superieur au seuil configure, ET que la liste des destinataires est initialisee
				if (($total = $next['total']) > _MAILSHOT_SEND_PER_PROCESS
				  and sql_countsel('spip_mailshots_destinataires','id_mailshot='.intval($next['id_mailshot'])) >= $total) {
					$nb_process = floor($total / _MAILSHOT_SEND_PER_PROCESS);
					$nb_process = max($nb_process, 0);
					$nb_process = min($nb_process, defined('_MAILSHOT_MAX_PROCESS') ? _MAILSHOT_MAX_PROCESS : 10);

					$decalage = 5 * $nb;
					$restant = $next['total'] - $next['current'];
					$nb_process = min($nb_process, floor(($restant - $nb) / $decalage));
					spip_log("BOOST : $total destinataires, lancement de $nb_process processus supplementaires", "mailshot");
					if ($nb_process > 0) {
						include_spip('inc/actions');
						while ($nb_process) {
							$offset = $nb_process * $decalage;
							$arg = $next['id_mailshot'] . "-$nb-" . $offset;
							$url = generer_action_auteur("mailshot_boost_send", $arg, "", true, 0);
							mailshot_call_url_async($url);
							$nb_process--;
						}
					}
				}
			}
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


function mailshot_call_url_async($url){
	spip_log("Call URL async $url","mailshot");
	$parts = parse_url($url);

	switch ($parts['scheme']) {
		case 'https':
			$scheme = 'tls://';
			$port = 443;
			break;
		case 'http':
		default:
			$scheme = '';
			$port = 80;
	}

	$fp = @fsockopen($scheme . $parts['host'],
		isset($parts['port']) ? $parts['port'] : $port,
		$errno, $errstr, 1);

	if ($fp){
		$timeout = 200; // ms
		stream_set_timeout($fp, 0, $timeout*1000);
		$query = $parts['path'] . ($parts['query'] ? "?" . $parts['query'] : "");
		$out = "GET " . $query . " HTTP/1.1\r\n";
		$out .= "Host: " . $parts['host'] . "\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		spip_timer('read');
		$t = 0;
		// on lit la reponse si possible pour fermer proprement la connexion
		// avec un timeout total de 200ms pour ne pas se bloquer
		while (!feof($fp) AND $t<$timeout){
			@fgets($fp, 1024);
			$t += spip_timer('read', true);
			spip_timer('read');
		}
		fclose($fp);
	}
}