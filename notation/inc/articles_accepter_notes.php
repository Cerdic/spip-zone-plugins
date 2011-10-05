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
	if ($art_accepter_note and in_array($art_accepter_note,array('oui','non'))) {
		spip_log("l'article $id_objet accepte les notes : $art_accepter_note",'notation');
		$accepter_note = $art_accepter_note;
	}
	return substr($accepter_note, 0, 3);
}


?>
