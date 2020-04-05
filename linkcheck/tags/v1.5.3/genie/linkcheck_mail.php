<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function genie_linkcheck_mail_dist() {
	include_spip('inc/config');

	if (lire_config('linkcheck/notifier_courriel')) {
		$sql = sql_allfetsel(
			'COUNT(id_linkcheck) AS c, etat',
			'spip_linkchecks',
			'etat IN ("mort", "malade") AND date > subdate(current_date, 1)',
			'etat'
		);

		if (is_array($sql) && count($sql) > 0) {
			$sql = sql_allfetsel('COUNT(id_linkcheck) AS c, etat', 'spip_linkchecks', '', 'etat');
			foreach ($sql as $valeur) {
				$msg_resultat .= '<li>'.$valeur['c'].' lien(s) '.$valeur['etat'].'.</li>';
			}

			$cont = _T('linkcheck:mail_notification1');
			$cont .= '<ul>'.$msg_resultat.'</ul><br/>';
			$cont .= _T('linkcheck:mail_notification2');

			$email = lire_config('email_webmaster');

			$nsite = lire_config('nom_site');

			$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');

			$ok = $envoyer_mail(
				$email,
				'Liens cassés sur '.$nsite,
				array('html' => $cont,
				'texte' => strip_tags($cont),
				'nom_envoyeur' => 'Linkcheck')
			);

			if ($ok) {
				return 1;
			}
		}
	}
	return 0;
}
