<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Moderer le forum d'un article ?
// = modifier l'article
// = OU en etre un des auteurs
function autoriser_moderer_forum_article($faire, $type, $id, $qui, $opt) {
	return
		autoriser('modifier', $type, $id, $qui, $opt)
		OR spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']));
}


?>