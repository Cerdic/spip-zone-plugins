<?php
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$r = sql_fetsel("id_rubrique,statut", "spip_articles", "id_article=".sql_quote($id));

	include_spip('inc/auth'); // pour auteurs_article si espace public

	return
		$r
		AND
		autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
		OR (
			in_array($qui['statut'], array('0minirezo', '1comite'))
			//AND in_array($r['statut'], array('prop','prepa', 'poubelle'))
			AND auteurs_article($id, "id_auteur=".$qui['id_auteur'])
		);
}

/*
// Autoriser a publier dans la rubrique $id
// http://doc.spip.org/@autoriser_rubrique_publierdans_dist
function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {
	return
		($qui['statut'] == '0minirezo')
		AND (
			!$qui['restreint'] OR !$id
			OR in_array($id, $qui['restreint'])
		);
}
*/
?>
