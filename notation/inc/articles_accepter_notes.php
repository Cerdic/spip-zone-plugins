<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Moderation des notes specifique a un article
 * @param int $id_objet identifiant de l'article
 * @return string  : "non", "oui"
 */
function inc_articles_accepter_notes_dist($id_objet) {
	$accepter_note = $GLOBALS['meta']["notations_publics"];
	$art_accepter_note = sql_getfetsel('accepter_note', 'spip_articles', array(
		"id_article = ". intval($id_objet)
	));
	spip_log($art_accepter_note,'notation');
	if (in_array($art_accepter_note,array('oui','non'))) {
		$accepter_note = $art_accepter_note;
	}
	return substr($accepter_note, 0, 3);
}


?>
