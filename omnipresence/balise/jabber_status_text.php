<?php
include_spip('inc/omnipresence');

function balise_JABBER_STATUS_TEXT($p) {
	return calculer_balise_dynamique($p, 'JABBER_STATUS_TEXT', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_STATUS_TEXT_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_STATUS_TEXT_dyn($jid, $host) {
	return demander_action('message', $jid, $host);
}
?>
