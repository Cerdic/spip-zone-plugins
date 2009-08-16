<?php
include_spip('inc/omnipresence');

function balise_JABBER_STATUS_ICON($p) {
	return calculer_balise_dynamique($p, 'JABBER_STATUS_ICON', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE, 'lang'));
}

function balise_JABBER_STATUS_ICON_stat($args, $filtres) {
	return array(
		isset($args[3]) ? $args[3] : $args[0],
		$args[1],
		isset($args[4]) ? '-'.$args[4] : '',
		$args[2],
	);
}

function balise_JABBER_STATUS_ICON_dyn($jid, $host, $theme, $locale) {
	include_spip('inc/filtres');
	$status = demander_action("text-$locale.txt", $jid, $host);
	return inserer_attribut(demander_action('image'.$theme, $jid, $host), "alt", $status);
}
?>
