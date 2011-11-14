<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Moderation des forums specifique a un ticket
 * @param int $id_objet identifiant du ticket
 * @return string  : "non", "pos"(teriori), "pri"(ori), "abo"(nnement)
 */
function inc_ticket_accepter_forums_publics_dist($id_ticket){
	$accepter_forum = lire_config("tickets/general/forums_publics", "posteriori");
	return substr($accepter_forum, 0, 3);
}

?>
