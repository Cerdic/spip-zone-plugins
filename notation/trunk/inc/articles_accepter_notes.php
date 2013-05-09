<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Modération des notes spécifique a un article
 * @param int $id_objet 
 * 		identifiant de l'article
 * @return string
 * 		"non", "oui"
 */
function inc_articles_accepter_notes_dist($id_objet) {
	$accepter_note = $GLOBALS['meta']["notations_publics"];
	$art_accepter_note = sql_getfetsel('accepter_note', 'spip_articles', array(
		"id_article = ". intval($id_objet)
	));
	if ($art_accepter_note and in_array($art_accepter_note,array('oui','non'))) {
		$accepter_note = $art_accepter_note;
	}
	return substr($accepter_note, 0, 3);
}


?>
