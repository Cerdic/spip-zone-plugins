<?php
include_spip('inc/omnipresence');

function balise_JABBER_MOOD($p) {
	return calculer_balise_dynamique($p, 'JABBER_MOOD', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE, 'lang'));
}

function balise_JABBER_MOOD_stat($args, $filtres) {
	return array(
		isset($args[3]) ? $args[3] : $args[0],
		$args[1],
		isset($args[4]) ? $args[4] : $args[2],
	);
}

function balise_JABBER_MOOD_dyn($jid, $host, $locale) {
	return demander_action("pep/mood/value-$locale.txt", $jid, $host);
}
?>
