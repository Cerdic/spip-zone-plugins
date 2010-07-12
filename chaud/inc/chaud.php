<?php
/*
 * Plugin C'est chaud
 * (c) 2010 Fil
 * Distribue sous licence GPL
 *
 */


function chaud_notifier() {
	spip_log('notification', 'chaud');
	if ($a = chaud_articles(1.5)) {
		spip_log(var_export($a, true), 'chaud');
		$chauds = recuperer_fond('notification_chauds', array('chauds' => array_keys($a)));
		if ($chauds) {
			spip_log($chauds, 'chaud');
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			$envoyer_mail($GLOBALS['meta']['email_webmaster'],
				_L('Articles chauds'),
				$chauds
			);
		}
	}
}


function chaud_articles($seuil = 1.0) {
	$chaud = array();

	include_spip('base/abstract_sql');
	if ($s = sql_query("SELECT
		id_article,
		popularite * popularite
		* DATEDIFF(NOW(),date_modif)
		/ ( visites + 10 * popularite )
		/ ( select SUM(popularite) from spip_articles )
		* SQRT( ( select count(*) from spip_articles where statut='publie' ) )
			as s
		FROM spip_articles
		WHERE statut='publie'
		ORDER BY s DESC
		LIMIT 50")
	) {
		while ($t = sql_fetch($s)
		AND $t['s'] > $seuil
		) {
			$chaud[$t['id_article']] = $t['s'];
		}
	}

	return $chaud;
}

