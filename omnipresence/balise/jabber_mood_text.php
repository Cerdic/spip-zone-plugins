<?php
include_spip('inc/omnipresence');

function balise_JABBER_MOOD_TEXT($p) {
	return calculer_balise_dynamique($p, 'JABBER_MOOD_TEXT', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_MOOD_TEXT_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_MOOD_TEXT_dyn($jid, $host) {
	return demander_action('pep/mood/text.txt', $jid, $host);
}
?>
