<?php
include_spip('inc/omnipresence');

function balise_JABBER_ACTIVITY_TEXT($p) {
	return calculer_balise_dynamique($p, 'JABBER_ACTIVITY_TEXT', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_ACTIVITY_TEXT_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_ACTIVITY_TEXT_dyn($jid, $host) {
	return demander_action('pep/activity/text.txt', $jid, $host);
}
?>
