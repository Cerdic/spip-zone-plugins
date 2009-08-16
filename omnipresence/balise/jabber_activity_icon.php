<?php
include_spip('inc/omnipresence');

function balise_JABBER_ACTIVITY_ICON($p) {
	return calculer_balise_dynamique($p, 'JABBER_ACTIVITY_ICON', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE, 'lang'));
}

function balise_JABBER_ACTIVITY_ICON_stat($args, $filtres) {
	return array(
		isset($args[3]) ? $args[3] : $args[0],
		$args[1],
		$args[2],
	);
}

function balise_JABBER_ACTIVITY_ICON_dyn($jid, $host, $locale) {
	include_spip('inc/filtres');
	$status = demander_action("pep/activity/value-$locale.txt", $jid, $host);
	return inserer_attribut(demander_action('pep/activity/image.png'.$theme, $jid, $host), "alt", $status);
}
?>
