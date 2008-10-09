<?php
function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
	$s = spip_query(
	"SELECT id_rubrique,statut FROM spip_articles WHERE id_article="._q($id));
	$r = spip_fetch_array($s);
	include_spip('inc/auth');
	return
		autoriser('publierdans', 'rubrique', $r['id_rubrique'], $qui, $opt)
		OR (
			in_array($qui['statut'], array('0minirezo', '1comite'))
			//AND in_array($r['statut'], array('prop','prepa', 'poubelle'))
			AND spip_num_rows(auteurs_article($id, "id_auteur=".$qui['id_auteur']))
		);
}

function exec_dater()
{
	$type = _request('type');
	if (!preg_match('/^\w+$/',$type)) // securite
		die('XSS');

	$id = intval(_request('id'));
/*
	if (($GLOBALS['auteur_session']['statut'] != '0minirezo')
	OR ($type == 'article' AND    !acces_article($id))) {
		spip_log("Tentative d'intrusion du " . $GLOBALS['auteur_session']['statut'] . ' ' . $GLOBALS['auteur_session']['nom'] . " dans " . $GLOBALS['exec'] . " sur $type $id.");
		include_spip('inc/minipres');
		echo minipres(_T('info_acces_interdit'));
		exit;
	}*/

	$table = ($type=='syndic') ? 'syndic' : ($type . 's');
	$row = spip_fetch_array(spip_query("SELECT * FROM spip_$table WHERE id_$type=$id"));

	$statut = $row['statut'];
	$date = $row[($type!='breve')?"date":"date_heure"];
	$date_redac = $row["date_redac"];

	$script = ($type=='article')? 'articles' : ($type == 'breve' ? 'breves_voir' : 'sites');
	$dater = charger_fonction('dater', 'inc');
	ajax_retour($dater($id, 'ajax', $statut, $type, $script, $date, $date_redac));
}
?>