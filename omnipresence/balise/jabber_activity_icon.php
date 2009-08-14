<?php
include_spip('inc/omnipresence');

function balise_JABBER_ACTIVITY_ICON($p) {
	return calculer_balise_dynamique($p, 'JABBER_ACTIVITY_ICON', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE));
}

function balise_JABBER_ACTIVITY_ICON_stat($args, $filtres) {
	return array(
		isset($args[2]) ? $args[2] : $args[0],
		$args[1],
	);
}

function balise_JABBER_ACTIVITY_ICON_dyn($jid, $host) {
	#TODO: Request value in the context's locale
	$status = demander_action('pep/activity/value.txt', $jid, $host);
	return inserer_attribut(demander_action('pep/activity/image.png'.$theme, $jid, $host), "alt", $status);
}
?>
