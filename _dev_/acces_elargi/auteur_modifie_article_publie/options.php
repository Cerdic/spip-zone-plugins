<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Autoriser a modifier l'article $id
// = publier_dans rubrique parente
// = ou statut 'prop,prepa' et $qui est auteur
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$s = spip_query(
	"SELECT id_rubrique,statut FROM spip_articles WHERE id_article="._q($id));
	$r = spip_fetch_array($s);
	return
		autoriser('publier_dans', 'rubrique', $r['id_rubrique'], $qui, $opt)
		OR (
			in_array($qui['statut'], array('0minirezo', '1comite'))
/*
			# on commente cette ligne : tous les articles sont modifiables
			AND in_array($r['statut'], array('prop','prepa', 'poubelle'))
*/
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
		);
}
?>