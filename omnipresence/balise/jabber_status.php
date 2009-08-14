<?php
include_spip('inc/omnipresence');

function balise_JABBER_STATUS($p) {
	return calculer_balise_dynamique($p, 'JABBER_STATUS', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE, 'lang'));
}

function balise_JABBER_STATUS_stat($args, $filtres) {
	return array(
		isset($args[3]) ? $args[3] : $args[0],
		$args[1],
		isset($args[4]) ? $args[4] : $args[2],
	);
}

function balise_JABBER_STATUS_dyn($jid, $host, $locale) {
	return demander_action("text-$locale.txt", $jid, $host);
}
?>
