<?php
include_spip('inc/omnipresence');

function balise_JABBER_TUNE_TITLE($p) {
	return calculer_balise_dynamique($p, 'JABBER_TUNE_TITLE', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_TUNE_TITLE_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_TUNE_TITLE_dyn($jid, $host) {
	return demander_action('pep/tune/title.txt', $jid, $host);
}
?>
