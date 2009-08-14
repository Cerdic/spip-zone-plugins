<?php
include_spip('inc/omnipresence');

function balise_JABBER_AVATAR($p) {
	return calculer_balise_dynamique($p, 'JABBER_AVATAR', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_AVATAR_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_AVATAR_dyn($jid, $host) {
	return inserer_attribut(demander_action('avatar',$jid, $host), "alt", "");
}
?>
