<?php
include_spip('inc/omnipresence');

function balise_JABBER_MOOD_ICON($p) {
	return calculer_balise_dynamique($p, 'JABBER_MOOD_ICON', array(CHAMP_JID, CHAMP_SERVEUR_OMNIPRESENCE, 'lang'));
}

function balise_JABBER_MOOD_ICON_stat($args, $filtres) {
	return array(
		isset($args[3]) ? $args[3] : $args[0],
		$args[1],
		$args[2],
	);
}

function balise_JABBER_MOOD_ICON_dyn($jid, $host, $locale) {
	$status = demander_action("pep/mood/value-$locale.txt", $jid, $host);
	return inserer_attribut(demander_action('pep/mood/image.png'.$theme, $jid, $host), "alt", $status);
}
?>
